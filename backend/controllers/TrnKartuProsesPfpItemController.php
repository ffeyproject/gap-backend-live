<?php

namespace backend\controllers;

use common\models\ar\TrnStockGreige;
use Yii;
use common\models\ar\TrnKartuProsesPfpItem;
use common\models\ar\TrnKartuProsesPfpItemSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TrnKartuProsesPfpItemController implements the CRUD actions for TrnKartuProsesPfpItem model.
 */
class TrnKartuProsesPfpItemController extends Controller
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
     * Lists all TrnKartuProsesPfpItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnKartuProsesPfpItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnKartuProsesPfpItem model.
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
     * Total item pada kartu proses tidak boleh lebih dari panjang 1 batch greige nya, kecuali kelebihan pertama.
     * Masing2 total tube kiri dan tube kanan tidak boleh lebih dari setengah panjang 1 batch greige nya, kecuali kelebihan pertama.
     * Pemeriksaan diatas dilakukan setiap penambahan item kartu proses
     * @param $processId
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws HttpException
     */
    public function actionCreate($processId)
    {   
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = new TrnKartuProsesPfpItem([
                'kartu_process_id'=>$processId,
                'date'=>date('Y-m-d'),
                'created_at'=>time(),
                'note'=>'-'
            ]);

            $kartuProses = $model->kartuProcess;

            if($kartuProses === null){
                throw new ForbiddenHttpException('ID Kartu Proses Tidak Valid.');
            }

            if($kartuProses->status != $kartuProses::STATUS_DRAFT){
                throw new ForbiddenHttpException('Status Kartu Proses Tidak Valid.');
            }

            $model->greige_group_id = $kartuProses->greige_group_id;
            $model->greige_id = $kartuProses->greige_id;
            $model->order_pfp_id = $kartuProses->order_pfp_id;
            $perBatch = (float)$model->greigeGroup->qty_per_batch;
            $perBatchHalf = $perBatch / 2;
            $perBatchHalfInPercent = 0.02 * $perBatchHalf; //dua persen dari setengah batch
            $perBatchHalfToleransiAtas = $perBatchHalf + $perBatchHalfInPercent;
            //$perBatchHalfToleransiBawah = $perBatchHalf - $perBatchHalfInPercent;

            if ($model->load(Yii::$app->request->post())) {
                if($model->validate()){
                    $model->panjang_m = (float)$model->stock->panjang_m;

                    if(!$kartuProses->no_limit_item){
                        switch ($model->tube){
                            case $model::TUBE_KIRI:
                                $totalTubeKiri = $kartuProses->getTrnKartuProsesPfpItemsTubeKiri()->sum('panjang_m');
                                $totalTubeKiri = $totalTubeKiri !== null ? $totalTubeKiri : 0;
                                $totalTubeKiri += $model->panjang_m;
                                if(($totalTubeKiri > $perBatchHalfToleransiAtas)){
                                    //kelebihan diatas 2% dari panjang per batch dibagi dua tidak diizinkan.
                                    throw new ForbiddenHttpException('Jumlah tube kiri tidak valid, hanya kelebihan sebanyak 2% dari per batch dibagi dua yang diizinkan.');
                                }
                                break;
                            case $model::TUBE_KANAN:
                                $totalTubeKanan = $kartuProses->getTrnKartuProsesPfpItemsTubeKanan()->sum('panjang_m');
                                $totalTubeKanan = $totalTubeKanan !== null ? $totalTubeKanan : 0;
                                $totalTubeKanan += $model->panjang_m;
                                if(($totalTubeKanan > $perBatchHalfToleransiAtas)){
                                    //kelebihan diatas 2% dari panjang per batch dibagi dua tidak diizinkan.
                                    throw new ForbiddenHttpException('Jumlah tube kanan tidak valid, hanya kelebihan sebanyak 2% dari per batch dibagi dua yang diizinkan.');
                                }
                                break;
                        }
                    }

                    if($model->save(false)){
                        return ['success' => true, 'tube'=>$model->tube];
                    }

                    throw new HttpException(500, 'Gagal memproses, coba lagi.');
                }

                $result = [];
                // The code below comes from ActiveForm::validate(). We do not need to validate the model
                // again, as it was already validated by save(). Just collect the messages.
                foreach ($model->getErrors() as $attribute => $errors) {
                    $result[Html::getInputId($model, $attribute)] = $errors;
                }

                return ['validation' => $result];
            }

            $asalGreige = TrnStockGreige::asalGreigeOptions()[$kartuProses->asal_greige];
            $jenisGudang = TrnStockGreige::jenisGudangOptions()[$model->orderPfp->jenis_gudang];
            $searchHint = "Mencari Greige {$model->greige->nama_kain}, Status Valid, Asal Greige {$asalGreige}, Jenis Gudang {$jenisGudang}.";

            return $this->renderAjax('create', [
                'model'=>$model,
                'searchHint'=>$searchHint
            ]);
        }

        throw new ForbiddenHttpException('Not allowed');
    }

    /**
     * Updates an existing TrnKartuProsesPfpItem model.
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
     * Deletes an existing TrnKartuProsesPfpItem model.
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
        $kp = $model->kartuProcess;
        if($kp->status != $kp::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk dihapus.');
            return $this->redirect(['/trn-kartu-proses-pfp/view', 'id'=>$kp->id]);
        }

        $model->delete();

        Yii::$app->session->setFlash('success', 'Item berhasil dihapus.');
        return $this->redirect(['/trn-kartu-proses-pfp/view', 'id'=>$kp->id]);
    }

    /**
     * Finds the TrnKartuProsesPfpItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnKartuProsesPfpItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnKartuProsesPfpItem::findOne($id)) !== null) {
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

                    // Update stock lama
                    if ($oldStockId && $oldStockId != $model->stock_id) {
                        $oldStock = \common\models\ar\TrnStockGreige::findOne($oldStockId);
                        if ($oldStock) {
                            $oldStock->status = \common\models\ar\TrnStockGreige::STATUS_VALID;
                            $oldStock->save(false);

                            \common\models\ar\TrnStockGreigeOpname::updateAll(
                                ['status' => \common\models\ar\TrnStockGreigeOpname::STATUS_VALID],
                                ['stock_greige_id' => $oldStock->id]
                            );
                        }
                    }

                    // Update stock baru
                    if ($model->stock_id) {
                        $newStock = \common\models\ar\TrnStockGreige::findOne($model->stock_id);
                        if ($newStock) {
                            $newStock->status = \common\models\ar\TrnStockGreige::STATUS_ON_PROCESS_CARD;
                            $newStock->save(false);

                            \common\models\ar\TrnStockGreigeOpname::updateAll(
                                ['status' => \common\models\ar\TrnStockGreigeOpname::STATUS_ON_PROCESS_CARD],
                                ['stock_greige_id' => $newStock->id]
                            );
                        }
                    }

                    // Update greige stock, available & stock_opname
                    $greige = $model->stock ? $model->stock->greige : null;
                    if ($greige) {
                        $selisih = $model->panjang_m - $oldPanjang;

                        $greige->stock     -= max($selisih, 0);
                        $greige->available -= max($selisih, 0);
                        $greige->stock     += max(-$selisih, 0);
                        $greige->available += max(-$selisih, 0);

                        if (\common\models\ar\TrnStockGreigeOpname::adaOpnameUntuk($model->stock_id)) {
                            $greige->stock_opname -= max($selisih, 0);
                            $greige->stock_opname += max(-$selisih, 0);
                        }

                        if (!$greige->save(false, ['stock','available','stock_opname'])) {
                            throw new \Exception("Gagal update stock MstGreige: " . json_encode($greige->getErrors()));
                        }
                    } else {
                        throw new \Exception("Relasi greige tidak ditemukan untuk stock ini.");
                    }

                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Qty & Stock berhasil diperbarui.');

                    // redirect ke view kartu proses PFP
                    return $this->redirect(['/processing-pfp/view', 'id' => $model->kartu_process_id]);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Update gagal: ' . $e->getMessage());
            }
        }

        // Data provider untuk modal pilih stock
        $searchModel = new \common\models\search\TrnStockGreigeSearch();
        $stocks = $searchModel->search(Yii::$app->request->queryParams);

        $greigeId = $model->stock ? $model->stock->greige_id : null;
        if ($greigeId) {
            $stocks->query->andWhere(['greige_id' => $greigeId]);
        }

        return $this->renderAjax('@app/views/trn-kartu-proses-pfp/child/_form_edit_qty', [
            'model' => $model,
            'stocks' => $stocks,
            'searchModel' => $searchModel,
        ]);
    }



    public function actionEditMesin($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(false, ['mesin'])) {
                Yii::$app->session->setFlash('success', 'Nomer Mesin berhasil diperbarui.');
                return $this->redirect(['/processing-pfp/view', 'id' => $model->kartu_process_id]);
            }
            Yii::$app->session->setFlash('error', 'Gagal menyimpan perubahan mesin.');
        }

        return $this->renderAjax('@app/views/trn-kartu-proses-pfp/child/_form_edit_mesin', [
            'model' => $model,
        ]);
    }


}