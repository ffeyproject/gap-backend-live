<?php
namespace backend\controllers;

use common\models\ar\TrnKartuProsesPrinting;
use common\models\ar\TrnKartuProsesPrintingItem;
use common\models\ar\TrnKartuProsesPrintingItemSearch;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnStockPfp;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnKartuProsesPrintingItemController implements the CRUD actions for TrnKartuProsesPrintingItem model.
 */
class TrnKartuProsesPrintingItemController extends Controller
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
                ],
            ],
        ];
    }

    /**
     * Lists all TrnKartuProsesPrintingItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnKartuProsesPrintingItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnKartuProsesPrintingItem model.
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
     * Creates a new TrnKartuProsesPrintingItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $processId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionCreate($processId)
    {
        if(Yii::$app->request->isAjax){
            $model = new TrnKartuProsesPrintingItem([
                'kartu_process_id'=>$processId,
                'date'=>date('Y-m-d')
            ]);

            $kartuProses = $model->kartuProcess;

            if($kartuProses === null){
                throw new ForbiddenHttpException('ID Kartu Proses Tidak Valid.');
            }

            if($kartuProses->status != $kartuProses::STATUS_DRAFT){
                throw new ForbiddenHttpException('Status Kartu Proses Tidak Valid.');
            }

            $model->wo_id = $kartuProses->wo_id;
            $model->mo_id = $kartuProses->mo_id;
            $model->sc_greige_id = $kartuProses->sc_greige_id;
            $model->sc_id = $kartuProses->sc_id;
            $greige = $model->wo->greige;

            $perBatchToleransiAtas = 0;

            if(!$kartuProses->no_limit_item){
                $perBatch = (float)$greige->group->qty_per_batch;
                $perBatchInPercent = 0.02 * $perBatch; //dua persen dari satu batch
                $perBatchToleransiAtas = $perBatch + $perBatchInPercent;
            }

            if ($model->load(Yii::$app->request->post())) {
                $stockIds = $model->stock_ids;
                if (!is_array($stockIds)) {
                    $stockIds = [$stockIds];
                }

                $customQtys = Yii::$app->request->post('custom_qty', []);
                $isDigital = ($kartuProses->jenis_printing == TrnKartuProsesPrinting::JENIS_PRINTING_DIGITAL);

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $successCount = 0;
                    $hasError = false;
                    $result = [];

                    $currentTotal = (float)$kartuProses->getTrnKartuProsesPrintingItems()->sum('panjang_m');

                    foreach ($stockIds as $stockId) {
                        if (empty($stockId)) continue;

                        $originalStock = TrnStockGreige::findOne($stockId);
                        if ($originalStock === null || $originalStock->status != TrnStockGreige::STATUS_VALID) {
                            throw new ForbiddenHttpException("Stock dengan ID {$stockId} tidak ditemukan atau status tidak valid.");
                        }

                        $panjangAsli = (float)$originalStock->panjang_m;
                        $usedQty = ($isDigital && isset($customQtys[$stockId])) ? (float)$customQtys[$stockId] : $panjangAsli;

                        if ($usedQty <= 0 || $usedQty > $panjangAsli) {
                            throw new ForbiddenHttpException("Jumlah pemakaian untuk stock {$originalStock->no_document} tidak valid (harus > 0 dan <= {$panjangAsli}).");
                        }

                        $stockToUseId = $originalStock->id;
                        $panjangToUse = $panjangAsli;

                        // Jika digital printing dan qty yang dipakai kurang dari panjang asli -> lakukan pemotongan
                        if ($isDigital && $usedQty < $panjangAsli) {
                            $sisaQty = round($panjangAsli - $usedQty, 2);

                            // 1. Ubah status stock asal menjadi DIPOTONG
                            $originalStock->status = TrnStockGreige::STATUS_DIPOTONG;
                            if (!$originalStock->save(false, ['status'])) {
                                throw new \Exception("Gagal mengubah status stock asal.");
                            }

                            // 2. Buat stock baru untuk yang dipakai
                            $stockDipakai = new TrnStockGreige();
                            $attribs = $originalStock->attributes;
                            unset($attribs['id'], $attribs['created_at'], $attribs['created_by'], $attribs['updated_at'], $attribs['updated_by']);
                            $stockDipakai->setAttributes($attribs, false);
                            $stockDipakai->panjang_m = $usedQty;
                            $stockDipakai->is_pemotongan = true;
                            $stockDipakai->is_hasil_mix = !empty($originalStock->is_hasil_mix);
                            $stockDipakai->is_hasil_setting = !empty($originalStock->is_hasil_setting);
                            $stockDipakai->status = TrnStockGreige::STATUS_VALID;
                            $stockDipakai->note = ($originalStock->note ? $originalStock->note . ' | ' : '') . 'Potong untuk KP: ' . $kartuProses->nomor_kartu;
                            if (!$stockDipakai->save(false)) {
                                throw new \Exception("Gagal membuat stock hasil pemotongan yang dipakai.");
                            }

                            // 3. Buat stock baru untuk sisa pemotongan (tetap di Gudang 2)
                            $stockSisa = new TrnStockGreige();
                            $stockSisa->setAttributes($attribs, false);
                            $stockSisa->panjang_m = $sisaQty;
                            $stockSisa->is_pemotongan = true;
                            $stockSisa->is_hasil_mix = !empty($originalStock->is_hasil_mix);
                            $stockSisa->is_hasil_setting = !empty($originalStock->is_hasil_setting);
                            $stockSisa->status = TrnStockGreige::STATUS_VALID;
                            $stockSisa->jenis_gudang = $originalStock->jenis_gudang;
                            $stockSisa->pfp_jenis_gudang = $originalStock->pfp_jenis_gudang;
                            $stockSisa->jenis_printing = $originalStock->jenis_printing;
                            $stockSisa->note = ($originalStock->note ? $originalStock->note . ' | ' : '') . 'Sisa Potong dari KP: ' . $kartuProses->nomor_kartu;
                            if (!$stockSisa->save(false)) {
                                throw new \Exception("Gagal membuat stock sisa pemotongan.");
                            }

                            $stockToUseId = $stockDipakai->id;
                            $panjangToUse = $usedQty;
                        }

                        $itemModel = new TrnKartuProsesPrintingItem([
                            'kartu_process_id' => $processId,
                            'date' => $model->date,
                            'wo_id' => $kartuProses->wo_id,
                            'mo_id' => $kartuProses->mo_id,
                            'sc_greige_id' => $kartuProses->sc_greige_id,
                            'sc_id' => $kartuProses->sc_id,
                            'note' => $model->note,
                            'stock_id' => $stockToUseId,
                            'stock_ids' => [$stockToUseId],
                            'panjang_m' => $panjangToUse
                        ]);

                        if(!$itemModel->validate()){
                            foreach ($itemModel->getErrors() as $attribute => $errors) {
                                $result[Html::getInputId($model, 'stock_ids')] = $errors;
                            }
                            $hasError = true;
                            break;
                        }

                        $currentTotal += $panjangToUse;

                        if(!$kartuProses->no_limit_item){
                            if($currentTotal > $perBatchToleransiAtas){
                                //kelebihan diatas 2% dari panjang per batch dibagi dua diizinkan.
                                throw new ForbiddenHttpException('Jumlah greige tidak valid, hanya kelebihan sebanyak 2% dari per batch yang diizinkan.');
                            }
                        }

                        if($itemModel->save(false)){
                            $successCount++;
                        }
                    }

                    if($hasError){
                        $transaction->rollBack();
                        return $this->asJson(['validation' => $result]);
                    }

                    if($successCount > 0){
                        $transaction->commit();
                        return $this->asJson(['success' => true]);
                    } else {
                        $transaction->rollBack();
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }
            }

            $asalGreige = TrnStockGreige::asalGreigeOptions()[$kartuProses->asal_greige];
            $jenisGudang = TrnStockGreige::jenisGudangOptions()[$model->wo->mo->jenis_gudang];
            $jenisPrintingText = ($kartuProses->jenis_printing !== null && isset(TrnKartuProsesPrinting::jenisPrintingOptions()[$kartuProses->jenis_printing])) ? TrnKartuProsesPrinting::jenisPrintingOptions()[$kartuProses->jenis_printing] : '-';
            $searchHint = "Mencari Greige {$greige->nama_kain}, Status Valid, Asal Greige {$asalGreige}, Jenis Gudang {$jenisGudang}. (Gudang 2 difilter Jenis Printing {$jenisPrintingText})";
            if ($kartuProses->jenis_printing == TrnKartuProsesPrinting::JENIS_PRINTING_DIGITAL) {
                $searchHint .= " | Pencarian Digital Printing: ketik No. Kartu PFP atau Qty (format: {no_kartu_pfp}*{qty}).";
            }

            return $this->renderAjax('create', [
                'model'=>$model,
                'searchHint'=>$searchHint
            ]);
        }

        throw new ForbiddenHttpException('Not allowed');
    }

    /**
     * Updates an existing TrnKartuProsesPrintingItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }*/

    /**
     * Deletes an existing TrnKartuProsesPrintingItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $kartuProses = $model->kartuProcess;
        if($kartuProses->status != $kartuProses::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status Kartu Proses ini tidak valid untuk dihapus.');
            return $this->redirect(['/trn-kartu-proses-printing/view', 'id'=>$kartuProses->id]);
        }

        $model->delete();

        Yii::$app->session->setFlash('success', 'Item berhasil dihapus.');
        return $this->redirect(['/trn-kartu-proses-printing/view', 'id'=>$kartuProses->id]);
    }

    /**
     * Finds the TrnKartuProsesPrintingItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnKartuProsesPrintingItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnKartuProsesPrintingItem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
