<?php

namespace backend\controllers;

use backend\components\Converter;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnReturPengantar;
use common\models\ar\TrnReturPengantarItem;
use common\models\ar\TrnReturPengantarSearch;
use common\models\ar\TrnStockGreige;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * TrnReturPengantarController: surat pengantar stok hasil retur buyer ke bagian/divisi lain.
 */
class TrnReturPengantarController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'posting' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnReturPengantarSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnReturPengantar(['date' => date('Y-m-d')]);
        $selectedIds = [];

        if ($model->load(Yii::$app->request->post())) {
            $selectedIds = (array)Yii::$app->request->post('stock_ids', []);
            if ($this->saveWithItems($model, $selectedIds)) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'selectedIds' => $selectedIds,
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->status != $model::STATUS_DRAFT) {
            Yii::$app->session->setFlash('error', 'Status bukan draft, tidak bisa dirubah.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $selectedIds = ArrayHelper::getColumn($model->trnReturPengantarItems, 'gudang_jadi_id');

        if ($model->load(Yii::$app->request->post())) {
            $selectedIds = (array)Yii::$app->request->post('stock_ids', []);
            if ($this->saveWithItems($model, $selectedIds)) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'selectedIds' => $selectedIds,
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->status != $model::STATUS_DRAFT) {
            Yii::$app->session->setFlash('error', 'Status bukan draft, tidak bisa dihapus.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            TrnReturPengantarItem::deleteAll(['pengantar_id' => $model->id]);
            $model->delete();
            $transaction->commit();
        } catch (\Throwable $t) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $t->getMessage());
            return $this->redirect(['view', 'id' => $model->id]);
        }

        Yii::$app->session->setFlash('success', 'Berhasil dihapus.');
        return $this->redirect(['index']);
    }

    /**
     * Posting surat pengantar:
     * - Processing: stok gudang jadi -> Mutasi Ke Processing + stok Gudang Mutasi (dipakai di Kartu Proses Mutasi),
     * - bagian lain: stok gudang jadi -> Out.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     */
    public function actionPosting($id)
    {
        $model = $this->findModel($id);

        if ($model->status != $model::STATUS_DRAFT) {
            Yii::$app->session->setFlash('error', 'Status bukan draft, tidak bisa diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $items = $model->trnReturPengantarItems;
        if (empty($items)) {
            Yii::$app->session->setFlash('error', 'Item belum dipilih, tidak bisa diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->setNomor();
        $model->status = $model::STATUS_POSTED;
        $model->posted_at = time();
        $model->posted_by = Yii::$app->user->id;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save(false, ['status', 'no_urut', 'no', 'posted_at', 'posted_by'])) {
                throw new HttpException(500, 'Posting gagal, coba lagi. (1)');
            }

            foreach ($items as $item) {
                $gdJadi = $item->gudangJadi;
                if ($gdJadi->status != TrnGudangJadi::STATUS_STOCK) {
                    throw new HttpException(500, 'Stok retur #'.$gdJadi->id.' sudah tidak berstatus Stock ('.(TrnGudangJadi::statusOptions()[$gdJadi->status] ?? '-').'), keluarkan dari surat pengantar terlebih dahulu.');
                }

                if ($model->bagian == TrnReturPengantar::BAGIAN_PROCESSING) {
                    $gdJadi->status = TrnGudangJadi::STATUS_MUTASI_PROCESSING;

                    $stockGreige = $this->buildStockMutasi($model, $gdJadi);
                    if (!$stockGreige->save()) {
                        throw new HttpException(500, 'Gagal membuat stok Gudang Mutasi untuk stok retur #'.$gdJadi->id.': '.implode(', ', $stockGreige->getErrorSummary(false)));
                    }

                    $item->stock_greige_id = $stockGreige->id;
                    if (!$item->save(false, ['stock_greige_id'])) {
                        throw new HttpException(500, 'Posting gagal, coba lagi. (2)');
                    }
                } else {
                    $gdJadi->status = TrnGudangJadi::STATUS_OUT;
                }

                if (!$gdJadi->save(false, ['status'])) {
                    throw new HttpException(500, 'Posting gagal, coba lagi. (3)');
                }
            }

            $transaction->commit();
        } catch (\Throwable $t) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $t->getMessage());
            return $this->redirect(['view', 'id' => $model->id]);
        }

        Yii::$app->session->setFlash('success', $model->bagian == TrnReturPengantar::BAGIAN_PROCESSING
            ? 'Posting berhasil. Stok sudah masuk Gudang Mutasi dan bisa dipilih di Kartu Proses Mutasi (Dyeing / Printing).'
            : 'Posting berhasil. Stok retur sudah keluar dari gudang jadi.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Stok Gudang Mutasi dari stok retur, mengikuti pola TrnGudangJadiController::actionMutasiProcessing()
     * @param TrnReturPengantar $model
     * @param TrnGudangJadi $gdJadi
     * @return TrnStockGreige
     */
    private function buildStockMutasi($model, $gdJadi)
    {
        // panjang_m dalam meter
        $panjang = $gdJadi->qty;
        if ($gdJadi->unit == MstGreigeGroup::UNIT_YARD) {
            $panjang = Converter::yardToMeter($gdJadi->qty);
        }

        $color = $gdJadi->getColorResolved();
        $stockAsal = $gdJadi->returFrom;

        return new TrnStockGreige([
            'greige_id' => $gdJadi->wo->greige_id,
            'greige_group_id' => $gdJadi->wo->greige->group_id,
            'asal_greige' => TrnStockGreige::ASAL_GREIGE_MUTASI,
            'no_lapak' => '-',
            'grade' => $gdJadi->grade,
            'lot_lusi' => $stockAsal !== null ? $stockAsal->getNoLot() : '-',
            'lot_pakan' => '-',
            'no_set_lusi' => '-',
            'panjang_m' => $panjang,
            'status_tsd' => TrnStockGreige::STATUS_TSD_NORMAL,
            'no_document' => $model->no,
            'pengirim' => 'Gudang Jadi (Retur)',
            'mengetahui' => $model->penerima ?: '-',
            'note' => 'Retur buyer '.$gdJadi->source_ref.', surat pengantar '.$model->no.($model->note ? '. '.$model->note : ''),
            'status' => TrnStockGreige::STATUS_VALID,
            'date' => date('Y-m-d'),
            'jenis_gudang' => TrnStockGreige::JG_MUTASI_GD_JADI,
            'nomor_wo' => $gdJadi->wo->no,
            'color' => $color !== '-' ? $color : null,
        ]);
    }

    /**
     * Simpan header + item dari daftar stok retur yang dicentang.
     * @param TrnReturPengantar $model
     * @param array $selectedIds trn_gudang_jadi.id
     * @return bool
     */
    private function saveWithItems($model, $selectedIds)
    {
        $valid = $model->validate();

        if (empty($selectedIds)) {
            $model->addError('note', 'Pilih minimal satu stok retur.');
            $valid = false;
        }

        $stocks = TrnReturPengantar::availableStockQuery($model->id)->andWhere(['trn_gudang_jadi.id' => $selectedIds])->indexBy('id')->all();
        if (count($stocks) !== count(array_unique($selectedIds))) {
            $model->addError('note', 'Ada stok retur yang sudah tidak tersedia (sudah keluar atau dipakai surat pengantar lain), muat ulang halaman.');
            $valid = false;
        }

        if (!$valid) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save(false)) {
                throw new HttpException(500, 'Gagal menyimpan, coba lagi. (1)');
            }

            TrnReturPengantarItem::deleteAll(['pengantar_id' => $model->id]);
            foreach ($stocks as $stock) {
                $item = new TrnReturPengantarItem([
                    'pengantar_id' => $model->id,
                    'gudang_jadi_id' => $stock->id,
                    'qty' => $stock->qty,
                ]);
                if (!$item->save(false)) {
                    throw new HttpException(500, 'Gagal menyimpan, coba lagi. (2)');
                }
            }

            $transaction->commit();
            return true;
        } catch (\Throwable $t) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $t->getMessage());
            return false;
        }
    }

    /**
     * @param integer $id
     * @return TrnReturPengantar the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnReturPengantar::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
