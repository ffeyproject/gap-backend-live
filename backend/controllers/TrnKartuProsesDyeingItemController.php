<?php

namespace backend\controllers;

use common\models\ar\KartuProsesDyeingItem;
use common\models\ar\MstGreige;
use common\models\ar\TrnStockGreige;
use Yii;
use common\models\ar\TrnKartuProsesDyeingItem;
use common\models\ar\TrnKartuProsesDyeingItemSearch;
use common\models\ar\TrnStockGreigeOpname;
use common\models\ar\TrnKartuProsesDyeingItemLog;
use common\models\search\TrnStockGreigeSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\data\ActiveDataProvider;

/**
 * TrnKartuProsesDyeingItemController implements the CRUD actions for TrnKartuProsesDyeingItem model.
 */
class TrnKartuProsesDyeingItemController extends Controller
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
     * Lists all TrnKartuProsesDyeingItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnKartuProsesDyeingItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnKartuProsesDyeingItem model.
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
     * Creates a new KartuProsesDyeingItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $processId
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws HttpException
     */
    public function actionCreate($processId)
    {
        if(Yii::$app->request->isAjax){
            $model = new TrnKartuProsesDyeingItem([
                'kartu_process_id'=>$processId,
                'date'=>date('Y-m-d')
            ]);

            $kartuProses = $model->kartuProcess;
            if($kartuProses->status != $kartuProses::STATUS_DRAFT){
                throw new ForbiddenHttpException('Status Kartu Proses Tidak Valid.');
            }

            $model->wo_id = $kartuProses->wo_id;
            $model->mo_id = $kartuProses->mo_id;
            $model->sc_greige_id = $kartuProses->sc_greige_id;
            $model->sc_id = $kartuProses->sc_id;
            $greige = $model->wo->greige;

            $perBatchHalfToleransiAtas = 0;

            if(!$kartuProses->no_limit_item){
                $perBatch = $greige->group->qty_per_batch;
                $perBatchHalf = $perBatch / 2; // setengah batch
                $perBatchHalfInPercent = 0.02 * $perBatchHalf; //dua persen dari setengah batch
                $perBatchHalfToleransiAtas = $perBatchHalf + $perBatchHalfInPercent;
            }

            if ($model->load(Yii::$app->request->post())) {
                if($model->validate()){
                    $model->panjang_m = (float)$model->stock->panjang_m;

                    if(!$kartuProses->no_limit_item){
                        switch ($model->tube){
                            case $model::TUBE_KIRI:
                                $totalTubeKiri = $kartuProses->getTrnKartuProsesDyeingItemsTubeKiri()->sum('panjang_m');
                                $totalTubeKiri = $totalTubeKiri !== null ? $totalTubeKiri : 0;
                                $totalTubeKiri += $model->panjang_m;

                                if(!$kartuProses->no_limit_item){
                                    if(($totalTubeKiri > $perBatchHalfToleransiAtas)){
                                        //kelebihan diatas 2% dari panjang per batch dibagi dua tidak diizinkan.
                                        throw new ForbiddenHttpException('Jumlah tube kiri tidak valid, hanya kelebihan sebanyak 2% dari per batch dibagi dua yang diizinkan.');
                                    }
                                }
                                break;
                            case $model::TUBE_KANAN:
                                $totalTubeKanan = $kartuProses->getTrnKartuProsesDyeingItemsTubeKanan()->sum('panjang_m');
                                $totalTubeKanan = $totalTubeKanan !== null ? $totalTubeKanan : 0;
                                $totalTubeKanan += $model->panjang_m;

                                if(!$kartuProses->no_limit_item){
                                    if(($totalTubeKanan > $perBatchHalfToleransiAtas)){
                                        //kelebihan diatas 2% dari panjang per batch dibagi dua tidak diizinkan.
                                        throw new ForbiddenHttpException('Jumlah tube kanan tidak valid, hanya kelebihan sebanyak 2% dari per batch dibagi dua yang diizinkan.');
                                    }
                                }
                                break;
                        }
                    }

                    if($model->save(false)){
                        return $this->asJson(['success' => true, 'tube'=>$model->tube]);
                    }

                    throw new HttpException(500, 'Gagal memproses, coba lagi.');
                }

                $result = [];
                // The code below comes from ActiveForm::validate(). We do not need to validate the model
                // again, as it was already validated by save(). Just collect the messages.
                foreach ($model->getErrors() as $attribute => $errors) {
                    $result[Html::getInputId($model, $attribute)] = $errors;
                }

                return $this->asJson(['validation' => $result]);
            }

            $asalGreige = TrnStockGreige::asalGreigeOptions()[$kartuProses->asal_greige];
            $jenisGudang = TrnStockGreige::jenisGudangOptions()[$model->wo->mo->jenis_gudang];
            $searchHint = "Mencari Greige {$greige->nama_kain}, Status Valid, Asal Greige {$asalGreige}, Jenis Gudang {$jenisGudang}";

            return $this->renderAjax('create', [
                'model'=>$model,
                'searchHint'=>$searchHint
            ]);
        }

        throw new ForbiddenHttpException('Not allowed');
    }

    /**
     * Updates an existing KartuProsesDyeingItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionUpdate($id)
    {
        if(Yii::$app->request->isAjax){
            $model = $this->findModel($id);

            if ($model->load(Yii::$app->request->post())) {
                if($model->validate()){
                    if($model->save(false)){
                        return $this->asJson(['success' => true]);
                    }

                    throw new HttpException(500, 'Gagal memproses, coba lagi.');
                }

                $result = [];
                // The code below comes from ActiveForm::validate(). We do not need to validate the model
                // again, as it was already validated by save(). Just collect the messages.
                foreach ($model->getErrors() as $attribute => $errors) {
                    $result[Html::getInputId($model, $attribute)] = $errors;
                }

                return $this->asJson(['validation' => $result]);
            }

            return $this->renderAjax('update', [
                'model' => $model,
            ]);
        }

        throw new ForbiddenHttpException('Not allowed');
    }*/

    /**
     * Deletes an existing KartuProsesDyeingItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    // public function actionDelete($id)
    // {
    //     $model = $this->findModel($id);

    //     $kartuProses = $model->kartuProcess;
    //     if($kartuProses->status != $kartuProses::STATUS_DRAFT){
    //         Yii::$app->session->setFlash('error', 'Status Kartu Proses ini tidak valid untuk dihapus.');
    //         return $this->redirect(['/trn-kartu-proses-dyeing/view', 'id'=>$kartuProses->id]);
    //     }

    //     $model->delete();

    //     Yii::$app->session->setFlash('success', 'Item berhasil dihapus.');
    //     return $this->redirect(['/trn-kartu-proses-dyeing/view', 'id'=>$kartuProses->id]);
    // }


    public function actionDelete($id)
    {
        $model = $this->findModel($id); // TrnKartuProsesDyeingItem
        $kartuProses = $model->kartuProcess;

        if ($kartuProses->status != $kartuProses::STATUS_DRAFT) {
            Yii::$app->session->setFlash('error', 'Status Kartu Proses ini tidak valid untuk dihapus.');
            return $this->redirect(['/trn-kartu-proses-dyeing/view', 'id' => $kartuProses->id]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            /** @var \common\models\ar\TrnStockGreige $stockItem */
            $stockItem = $model->stock;

            if ($stockItem !== null) {
                // kembalikan status stock greige (sesuaikan konstanta jika beda)
                $stockItem->status = $stockItem::STATUS_VALID;
                if (!$stockItem->save(false, ['status'])) {
                    throw new \Exception('Gagal mengubah status stock greige (ID: ' . $stockItem->id . ').');
                }

                // tambahkan kembali stock_opname di mst_greige
                /** @var \common\models\ar\MstGreige $mstGreige */
                $mstGreige = \common\models\ar\MstGreige::findOne($stockItem->greige_id);
                if ($mstGreige !== null) {
                    $newStockOpname = (float)$mstGreige->stock_opname + (float)$stockItem->panjang_m;
                    Yii::$app->db->createCommand()->update(
                        \common\models\ar\MstGreige::tableName(),
                        ['stock_opname' => $newStockOpname],
                        ['id' => $mstGreige->id]
                    )->execute();
                }

                // ubah status TrnStockGreigeOpname yang terkait (sesuaikan konstanta jika berbeda)
                \common\models\ar\TrnStockGreigeOpname::updateAll(
                    ['status' => \common\models\ar\TrnStockGreigeOpname::STATUS_VALID],
                    ['stock_greige_id' => $stockItem->id]
                );
            }

            if ($model->delete() === false) {
                throw new \Exception('Gagal menghapus item kartu proses.');
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Item berhasil dihapus dan stok dikembalikan.');
            return $this->redirect(['/trn-kartu-proses-dyeing/view', 'id' => $kartuProses->id]);
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['/trn-kartu-proses-dyeing/view', 'id' => $kartuProses->id]);
        }
    }


    /**
     * Finds the TrnKartuProsesDyeingItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnKartuProsesDyeingItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnKartuProsesDyeingItem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionEditQty($id)
    {
        $model = $this->findModel($id);
        $oldStockId = $model->stock_id;
        $oldPanjang = $model->panjang_m;

        if ($model->load(Yii::$app->request->post())) {

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save(false)) {
                    // --- Update stock lama ---
                    if ($oldStockId && $oldStockId != $model->stock_id) {
                        $oldStock = TrnStockGreige::findOne($oldStockId);
                        if ($oldStock) {
                            $oldStock->status = TrnStockGreige::STATUS_VALID;
                            $oldStock->save(false);

                            TrnStockGreigeOpname::updateAll(
                                ['status' => TrnStockGreigeOpname::STATUS_VALID],
                                ['stock_greige_id' => $oldStock->id]
                            );
                        }
                    }

                    // --- Update stock baru ---
                    if ($model->stock_id) {
                        $newStock = TrnStockGreige::findOne($model->stock_id);
                        if ($newStock) {
                            $newStock->status = TrnStockGreige::STATUS_ON_PROCESS_CARD;
                            $newStock->save(false);

                            TrnStockGreigeOpname::updateAll(
                                ['status' => TrnStockGreigeOpname::STATUS_ON_PROCESS_CARD],
                                ['stock_greige_id' => $newStock->id]
                            );
                        }
                    }

                    // --- Update stock greige total ---
                    $greige = $model->stock ? $model->stock->greige : null;
                    if ($greige) {
                        $selisih = $model->panjang_m - $oldPanjang;

                        $greige->stock     -= max($selisih, 0);
                        $greige->available -= max($selisih, 0);
                        $greige->stock     += max(-$selisih, 0);
                        $greige->available += max(-$selisih, 0);

                        if (TrnStockGreigeOpname::adaOpnameUntuk($model->stock_id)) {
                            $greige->stock_opname -= max($selisih, 0);
                            $greige->stock_opname += max(-$selisih, 0);
                        }

                        if (!$greige->save(false, ['stock','available','stock_opname'])) {
                            throw new \Exception("Gagal update stock MstGreige: " . json_encode($greige->getErrors()));
                        }
                    } else {
                        throw new \Exception("Relasi greige tidak ditemukan untuk stock ini.");
                    }

                    // --- Catat Log ke trn_kartu_proses_dyeing_item_log ---
                    $log = new TrnKartuProsesDyeingItemLog([
                        'kartu_process_id' => $model->kartu_process_id,
                        'item_id'          => $model->id,
                        'stock_id'         => $model->stock_id,
                        'action_type'      => TrnKartuProsesDyeingItemLog::ACTION_UBAH_QTY,
                        'qty_before'       => $oldPanjang,
                        'qty_after'        => $model->panjang_m,
                        'alasan'           => 'Pergantian qty roll',
                    ]);

                    if (!$log->save()) {
                        throw new \Exception('Gagal mencatat log: ' . json_encode($log->getErrors()));
                    }

                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Qty & Stock berhasil diperbarui.');
                    return $this->redirect(['/processing-dyeing/view', 'id' => $model->kartu_process_id]);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Update gagal: ' . $e->getMessage());
            }
        }

        // === Tampilkan form & data stock ===
        $searchModel = new \common\models\search\TrnStockGreigeSearch();
        $stocks = $searchModel->search(Yii::$app->request->queryParams);

        $greigeId = $model->stock ? $model->stock->greige_id : null;
        if ($greigeId) {
            $stocks->query->andWhere(['greige_id' => $greigeId]);
        }

        return $this->renderAjax('@app/views/trn-kartu-proses-dyeing/child/_form_edit_qty', [
            'model' => $model,
            'stocks' => $stocks,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionEditMesin($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        Yii::$app->session->setFlash('success', 'Data mesin berhasil diperbarui.');
            return $this->redirect(['/processing-dyeing/view', 'id' => $model->kartu_process_id]);
        }

        return $this->renderAjax('@app/views/trn-kartu-proses-dyeing/child/_form_edit_mesin', [
            'model' => $model,
        ]);
    }

    protected function findItemModel($id)
    {
        $model = TrnKartuProsesDyeingItem::findOne($id);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('Item Kartu Proses tidak ditemukan.');
        }
        return $model;
    }

    public function actionDeleteItem($id)
    {
        $model = $this->findModel($id);
        $kartuProses = $model->kartuProcess;

        // Jika modal dibuka (GET / AJAX)
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_form_delete_item', [
                'model' => $model,
            ]);
        }

        // Jika submit form POST
        if (Yii::$app->request->isPost) {
            $alasan = Yii::$app->request->post('DynamicModel')['alasan'] ?? '(tanpa alasan)';

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $itemId = $model->id;
                $stockId = $model->stock_id;
                $qtyBefore = $model->panjang_m;

                // Kembalikan stock
                if ($stockId) {
                    $stock =TrnStockGreige::findOne($stockId);
                    if ($stock) {
                        $stock->status = TrnStockGreige::STATUS_KELUAR_GUDANG;
                        $stock->save(false, ['status']);

                    TrnStockGreigeOpname::updateAll(
                            ['status' => TrnStockGreigeOpname::STATUS_KELUAR_GUDANG],
                            ['stock_greige_id' => $stockId]
                        );

                        if ($stock->greige_id) {
                            $mstGreige = MstGreige::findOne($stock->greige_id);
                            if ($mstGreige) {
                                $mstGreige->stock_opname += (float)$stock->panjang_m;
                                $mstGreige->save(false, ['stock_opname']);
                            }
                        }
                    }
                }

                // Simpan log penghapusan
                Yii::$app->db->createCommand()->insert('trn_kartu_proses_dyeing_item_log', [
                    'kartu_process_id' => $model->kartu_process_id,
                    'item_id'          => $itemId,
                    'stock_id'         => $stockId,
                    'action_type'      => TrnKartuProsesDyeingItemLog::ACTION_HAPUS,
                    'qty_before'       => $qtyBefore,
                    'qty_after'        => 0,
                    'alasan'           => $alasan,
                    'created_at'       => time(),
                    'updated_at'       => time(),
                    'created_by'       => Yii::$app->user->id ?? null,
                    'updated_by'       => Yii::$app->user->id ?? null,
                ])->execute();

                $model->delete();

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Roll berhasil dihapus.');
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal menghapus roll: ' . $e->getMessage());
            }

            return $this->redirect(['/processing-dyeing/view', 'id' => $kartuProses->id]);
        }

        throw new \yii\web\BadRequestHttpException();
    }
    


    public function actionAddCreate($processId)
    {
        $model = new TrnKartuProsesDyeingItem([
            'kartu_process_id' => $processId,
            'date' => date('Y-m-d'),
        ]);

        $kartuProses = $model->kartuProcess;
        if (!$kartuProses) {
            throw new \yii\web\NotFoundHttpException('Kartu Proses tidak ditemukan.');
        }

        // Lengkapi relasi wajib
        $model->wo_id         = $kartuProses->wo_id;
        $model->mo_id         = $kartuProses->mo_id;
        $model->sc_greige_id  = $kartuProses->sc_greige_id;
        $model->sc_id         = $kartuProses->sc_id;

        // Ambil data stock valid
        $searchModel = new TrnStockGreigeSearch();
        $stocks = $searchModel->search(Yii::$app->request->queryParams);
        $stocks->query->andWhere(['status' => TrnStockGreige::STATUS_VALID]);

        $greigeName = isset($model->wo->greige->nama_kain) ? $model->wo->greige->nama_kain : '-';
        $searchHint = "Mencari roll greige untuk kain {$greigeName} dengan status VALID.";

        if ($model->load(Yii::$app->request->post())) {
            $model->wo_id         = $kartuProses->wo_id;
            $model->mo_id         = $kartuProses->mo_id;
            $model->sc_greige_id  = $kartuProses->sc_greige_id;
            $model->sc_id         = $kartuProses->sc_id;

            $alasan = trim(Yii::$app->request->post('alasan', ''));
            if ($alasan === '') {
                Yii::$app->session->setFlash('error', 'Alasan penambahan roll wajib diisi.');
                if (Yii::$app->request->isAjax) {
                    return $this->asJson(['success' => false, 'message' => 'Alasan wajib diisi.']);
                }
                return $this->redirect(Yii::$app->request->referrer);
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save(false)) {

                    // ✅ 1. Update status TrnStockGreige menjadi ON_PROCESS_CARD
                    $stock = TrnStockGreige::findOne($model->stock_id);
                    if ($stock !== null) {
                        $stock->status = TrnStockGreige::STATUS_ON_PROCESS_CARD;
                        $stock->save(false, ['status']);

                        // ✅ 2. Update juga status TrnStockGreigeOpname yang terkait
                        TrnStockGreigeOpname::updateAll(
                            ['status' => TrnStockGreigeOpname::STATUS_ON_PROCESS_CARD],
                            ['stock_greige_id' => $stock->id]
                        );

                         // ✅ 3. Cek apakah roll ada di tabel opname
                        $hasOpname = TrnStockGreigeOpname::find()
                            ->where(['stock_greige_id' => $stock->id])
                            ->exists();

                        // ✅ 4. Kurangi stok di MstGreige sesuai kondisi
                        if ($stock->greige) {
                            if ($hasOpname) {
                                // Ada di opname → kurangi stock, available, dan stock_opname
                                $stock->greige->minusStockOpname($model->panjang_m);
                            } else {
                                // Tidak ada di opname → kurangi stock dan available saja
                                $stock->greige->minusAddNewStock($model->panjang_m);
                            }
                        }
                    
                    }

                    // ✅ 5. Catat log aksi tambah roll
                    $log = new TrnKartuProsesDyeingItemLog([
                        'kartu_process_id' => $model->kartu_process_id,
                        'item_id'          => $model->id,
                        'stock_id'         => $model->stock_id,
                        'action_type'      => TrnKartuProsesDyeingItemLog::ACTION_TAMBAH,
                        'qty_before'       => 0,
                        'qty_after'        => $model->panjang_m,
                        'alasan'           => $alasan ?: 'Menambahkan roll baru ke kartu proses.',
                    ]);
                    $log->save(false);

                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Roll berhasil ditambahkan. Status stock & opname diperbarui.');

                    if (Yii::$app->request->isAjax) {
                        return $this->asJson(['success' => true]);
                    }

                    return $this->redirect(['/processing-dyeing/view', 'id' => $processId]);
                }
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal menyimpan data: ' . $e->getMessage());
            }
        }

        // Render form
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('@app/views/trn-kartu-proses-dyeing-item/_form2', [
                'model' => $model,
                'searchHint' => $searchHint,
                'searchModel' => $searchModel,
                'stocks' => $stocks,
            ]);
        }

        return $this->render('@app/views/trn-kartu-proses-dyeing-item/_form2', [
            'model' => $model,
            'searchHint' => $searchHint,
            'searchModel' => $searchModel,
            'stocks' => $stocks,
        ]);
    }





}