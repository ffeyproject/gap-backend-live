<?php

namespace backend\controllers;

use common\models\ar\KartuProsesDyeingItem;
use common\models\ar\TrnStockGreige;
use Yii;
use common\models\ar\TrnKartuProsesDyeingItem;
use common\models\ar\TrnKartuProsesDyeingItemSearch;
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
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $kartuProses = $model->kartuProcess;
        if($kartuProses->status != $kartuProses::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status Kartu Proses ini tidak valid untuk dihapus.');
            return $this->redirect(['/trn-kartu-proses-dyeing/view', 'id'=>$kartuProses->id]);
        }

        $model->delete();

        Yii::$app->session->setFlash('success', 'Item berhasil dihapus.');
        return $this->redirect(['/trn-kartu-proses-dyeing/view', 'id'=>$kartuProses->id]);
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

    /**
     * Edit Qty via modal form
     */
//   public function actionEditQty($id)
// {
//     $model = $this->findModel($id);
//     $oldStockId = $model->stock_id; // simpan stock lama

//     if ($model->load(Yii::$app->request->post())) {
//         if ($model->save(false)) {

//             // Jika ada stock lama dan berbeda → ubah jadi VALID
//             if ($oldStockId && $oldStockId != $model->stock_id) {
//                 $oldStock = \common\models\ar\TrnStockGreige::findOne($oldStockId);
//                 if ($oldStock) {
//                     $oldStock->status = \common\models\ar\TrnStockGreige::STATUS_VALID;
//                     $oldStock->save(false);
//                 }
//             }

//             // Ubah stock baru jadi ON_PROCESS_CARD
//             if ($model->stock_id) {
//                 $newStock = \common\models\ar\TrnStockGreige::findOne($model->stock_id);
//                 if ($newStock) {
//                     $newStock->status = \common\models\ar\TrnStockGreige::STATUS_ON_PROCESS_CARD;
//                     $newStock->save(false);
//                 }
//             }

//             Yii::$app->session->setFlash('success', 'Qty & Stock berhasil diperbarui.');

//             return $this->redirect([
//                 '/processing-dyeing/view',
//                 'id' => $model->kartu_process_id,
//             ]);
//         }
//     }

//     $stocks = new \yii\data\ActiveDataProvider([
//         'query' => \common\models\ar\TrnStockGreige::find()
//             ->where(['status' => \common\models\ar\TrnStockGreige::STATUS_VALID]),
//         'pagination' => ['pageSize' => 10],
//     ]);

//     return $this->renderAjax('@app/views/trn-kartu-proses-dyeing/child/_form_edit_qty', [
//         'model' => $model,
//         'stocks' => $stocks,
//     ]);
// }

    // public function actionEditQty($id)
    // {
    //     $model = $this->findModel($id);

    //     $oldStockId = $model->stock_id;
    //     $oldPanjang = $model->panjang_m;

    //     if ($model->load(Yii::$app->request->post())) {

    //         $transaction = Yii::$app->db->beginTransaction();
    //         try {
    //             if ($model->save(false)) {

    //                 // ================================
    //                 // 1. Update stock lama → VALID
    //                 // ================================
    //                 if ($oldStockId && $oldStockId != $model->stock_id) {
    //                     $oldStock = \common\models\ar\TrnStockGreige::findOne($oldStockId);
    //                     if ($oldStock) {
    //                         $oldStock->status = \common\models\ar\TrnStockGreige::STATUS_VALID;
    //                         $oldStock->save(false);
    //                     }
    //                 }

    //                 // ================================
    //                 // 2. Update stock baru → ON_PROCESS_CARD
    //                 // ================================
    //                 if ($model->stock_id) {
    //                     $newStock = \common\models\ar\TrnStockGreige::findOne($model->stock_id);
    //                     if ($newStock) {
    //                         $newStock->status = \common\models\ar\TrnStockGreige::STATUS_ON_PROCESS_CARD;
    //                         $newStock->save(false);
    //                     }
    //                 }

    //                 // ================================
    //                 // 3. Update MstGreige stock & available
    //                 // ================================
    //                 $greige = $model->stock ? $model->stock->greige : null;

    //                 if ($greige) {
    //                     $selisih = $model->panjang_m - $oldPanjang;

    //                     if ($selisih > 0) {
    //                         // Panjang baru lebih besar → kurangi stock & available
    //                         $greige->stock -= $selisih;
    //                         $greige->available -= $selisih;
    //                     } elseif ($selisih < 0) {
    //                         // Panjang baru lebih kecil → tambahkan stock & available
    //                         $greige->stock += abs($selisih);
    //                         $greige->available += abs($selisih);
    //                     }

    //                     if (!$greige->save(false, ['stock','available'])) {
    //                         throw new \Exception("Gagal update stock MstGreige: " . json_encode($greige->getErrors()));
    //                     }
    //                 } else {
    //                     throw new \Exception("Relasi greige tidak ditemukan untuk stock ini.");
    //                 }

    //                 $transaction->commit();

    //                 Yii::$app->session->setFlash('success', 'Qty & Stock berhasil diperbarui.');
    //                 return $this->redirect(['/processing-dyeing/view', 'id' => $model->kartu_process_id]);
    //             }
    //         } catch (\Exception $e) {
    //             $transaction->rollBack();
    //             Yii::$app->session->setFlash('error', 'Update gagal: ' . $e->getMessage());
    //         }
    //     }

    //     // ================================
    //     // 4. Data provider stocks VALID
    //     // ================================
    //     $stocks = new \yii\data\ActiveDataProvider([
    //         'query' => \common\models\ar\TrnStockGreige::find()
    //             ->where(['status' => \common\models\ar\TrnStockGreige::STATUS_VALID]),
    //         'pagination' => ['pageSize' => 10],
    //     ]);

    //      // ================================
    //     // 5. Filter stock sesuai greige kartu proses
    //     // ================================
    //     $greigeId = $model->stock ? $model->stock->greige_id : null;

    //     $searchModel = new \common\models\search\TrnStockGreigeSearch();
    //     $stocks = $searchModel->search(Yii::$app->request->queryParams);
        
    //     if ($greigeId) {
    //         $stocks->query->andWhere(['greige_id' => $greigeId]);
    //     }

    //     // ================================
    //     // Data provider & search model untuk GridView stock
    //     // ================================
    //     $searchModel = new TrnStockGreigeSearch();
    //     $stocks = $searchModel->search(Yii::$app->request->queryParams);

    //         return $this->renderAjax('@app/views/trn-kartu-proses-dyeing/child/_form_edit_qty', [
    //             'model'  => $model,
    //             'stocks' => $stocks,
    //             'searchModel' => $searchModel,
    //         ]);
    // }

    public function actionEditQty($id)
{
    $model = $this->findModel($id);
    $oldStockId = $model->stock_id;
    $oldPanjang = $model->panjang_m;

    if ($model->load(Yii::$app->request->post())) {

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($model->save(false)) {
                // Update stock lama
                if ($oldStockId && $oldStockId != $model->stock_id) {
                    $oldStock = \common\models\ar\TrnStockGreige::findOne($oldStockId);
                    if ($oldStock) {
                        $oldStock->status = \common\models\ar\TrnStockGreige::STATUS_VALID;
                        $oldStock->save(false);
                    }
                }

                // Update stock baru
                if ($model->stock_id) {
                    $newStock = \common\models\ar\TrnStockGreige::findOne($model->stock_id);
                    if ($newStock) {
                        $newStock->status = \common\models\ar\TrnStockGreige::STATUS_ON_PROCESS_CARD;
                        $newStock->save(false);
                    }
                }

                // Update greige stock & available
                $greige = $model->stock ? $model->stock->greige : null;
                if ($greige) {
                    $selisih = $model->panjang_m - $oldPanjang;
                    $greige->stock -= max($selisih, 0);
                    $greige->available -= max($selisih, 0);
                    $greige->stock += max(-$selisih, 0);
                    $greige->available += max(-$selisih, 0);

                    if (!$greige->save(false, ['stock','available'])) {
                        throw new \Exception("Gagal update stock MstGreige: " . json_encode($greige->getErrors()));
                    }
                } else {
                    throw new \Exception("Relasi greige tidak ditemukan untuk stock ini.");
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

    // ================================
    // Data provider & search model untuk GridView stock
    // ================================
    $searchModel = new \common\models\search\TrnStockGreigeSearch();
    $stocks = $searchModel->search(Yii::$app->request->queryParams);

    // Filter stock sesuai greige kartu proses
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



}