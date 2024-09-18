<?php

namespace backend\controllers;

use common\models\ar\KartuProsesDyeingItem;
use common\models\ar\TrnStockGreige;
use Yii;
use common\models\ar\TrnKartuProsesDyeingItem;
use common\models\ar\TrnKartuProsesDyeingItemSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

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
}
