<?php

namespace backend\controllers;

use common\models\ar\GudangJadiMutasiItem;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnGudangJadiSearch;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnStockGreigeSearch;
use kartik\mpdf\Pdf;
use Yii;
use common\models\ar\GudangJadiMutasi;
use common\models\ar\GudangJadiMutasiSearch;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * GudangJadiMutasiController implements the CRUD actions for GudangJadiMutasi model.
 */
class GudangJadiMutasiController extends Controller
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
     * Lists all GudangJadiMutasi models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GudangJadiMutasiSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GudangJadiMutasi model.
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
     * Creates a new GudangJadiMutasi model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws HttpException
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $searchModel = new TrnGudangJadiSearch(['status'=>TrnGudangJadi::STATUS_STOCK]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $model = new GudangJadiMutasi(['date'=>date('Y-m-d')]);

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->load(Yii::$app->request->post())) {
                if($model->validate()){
                    $transaction = Yii::$app->db->beginTransaction();

                    try {
                        if ($model->save(false)) {
                            foreach (Json::decode(Yii::$app->request->post('items')) as $item) {
                                $modelItem = new GudangJadiMutasiItem([
                                    'mutasi_id' => $model->id,
                                    'stock_id' => $item['stock_id'],
                                ]);
                                if (!$modelItem->save(false)) {
                                    $transaction->rollBack();
                                    throw new HttpException(500, 'Gagal, coba lagi. (2)');
                                }
                            }
                        }else{
                            $transaction->rollBack();
                            throw new HttpException(500, 'Gagal, coba lagi. (1)');
                        }

                        $transaction->commit();
                        return ['success'=>true, 'redirect'=>Url::to(['view', 'id'=>$model->id])];
                    } catch (\Throwable $t) {
                        $transaction->rollBack();
                        throw $t;
                    }
                }

                $result = [];
                // The code below comes from ActiveForm::validate(). We do not need to validate the model
                // again, as it was already validated by save(). Just collect the messages.
                foreach ($model->getErrors() as $attribute => $errors) {
                    $result[Html::getInputId($model, $attribute)] = $errors;
                }

                return ['validation' => $result];
            }
        }

        return $this->render('create', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing GudangJadiMutasi model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if($model->status != $model::STATUS_DRAFT){
            throw new ForbiddenHttpException('Status tidak valid. Tidak bisa dirubah.');
        }

        $searchModel = new TrnGudangJadiSearch(['status'=>TrnGudangJadi::STATUS_STOCK]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $modelsItem = $model->gudangJadiMutasiItems;

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->load(Yii::$app->request->post())) {
                if($model->validate()){
                    $transaction = Yii::$app->db->beginTransaction();

                    try {
                        if ($model->save(false)) {
                            GudangJadiMutasiItem::deleteAll(['mutasi_id'=>$model->id]);

                            foreach (Json::decode(Yii::$app->request->post('items')) as $item) {
                                $modelItem = new GudangJadiMutasiItem([
                                    'mutasi_id' => $model->id,
                                    'stock_id' => $item['stock_id'],
                                ]);
                                if (!$modelItem->save(false)) {
                                    $transaction->rollBack();
                                    throw new HttpException(500, 'Gagal, coba lagi. (2)');
                                }
                            }
                        }else{
                            $transaction->rollBack();
                            throw new HttpException(500, 'Gagal, coba lagi. (1)');
                        }

                        $transaction->commit();
                        return ['success'=>true, 'redirect'=>Url::to(['view', 'id'=>$model->id])];
                    } catch (\Throwable $t) {
                        $transaction->rollBack();
                        throw $t;
                    }
                }

                $result = [];
                // The code below comes from ActiveForm::validate(). We do not need to validate the model
                // again, as it was already validated by save(). Just collect the messages.
                foreach ($model->getErrors() as $attribute => $errors) {
                    $result[Html::getInputId($model, $attribute)] = $errors;
                }

                return ['validation' => $result];
            }
        }

        return $this->render('update', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'modelsItem' => $modelsItem
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUbahNomor($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'ubah_nomor';

        if($model->status != $model::STATUS_POSTED){
            throw new ForbiddenHttpException('Status tidak valid. Tidak bisa ubah nomor.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save(false, ['no_urut', 'nomor']);
            return $this->redirect(['view', 'id'=>$id]);
        }

        return $this->render('ubah-nomor', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing GudangJadiMutasi model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model->status != $model::STATUS_DRAFT){
            throw new ForbiddenHttpException('Status tidak valid. Tidak bisa dihapus.');
        }

        GudangJadiMutasiItem::deleteAll(['mutasi_id'=>$model->id]);

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing TrnPotongGreige model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPosting($id)
    {
        $model = $this->findModel($id);
        if ($model->status != $model::STATUS_DRAFT) {
            throw new ForbiddenHttpException('Status tidak valid. Tidak bisa diposting.');
        }

        $model->status = $model::STATUS_POSTED;
        $model->setNomor();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if($model->save(false, ['status', 'no_urut', 'nomor'])){
                //TODO: sesuaikan status gudang jadi
                foreach ($model->gudangJadiMutasiItems as $item) {
                    $stock = $item->stock;
                    $stock->status = $stock::STATUS_PINDAH_GUDANG;
                    $stock->save(false, ['status']);
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Posting berhasil.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Gagal, coba lagi.');
            return $this->redirect(['view', 'id' => $model->id]);
        }catch (\Throwable $t){
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $t->getMessage());
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDisplay($id){
        return $this->renderPartial('display', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionPrint($id){
        $model = $this->findModel($id);

        $content = $this->renderPartial('_print', ['model' => $model]);

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_BLANK,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            //'format' => [210,148], //A5 210mm x 148mm
            // portrait orientation
            //'orientation' => Pdf::ORIENT_LANDSCAPE,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            //'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssFile' => Yii::$app->vendorPath.'/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            //'cssFile' => '@webroot/css/mpdf/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '
                .row {
                    margin: 0px 0px 0px 0px !important;
                    padding: 0px !important;
                }
                
                .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
                    border:0;
                    padding:5px 0 5px 0;
                    margin-left:-0.00001;
                }
            ',
            // set mPDF properties on the fly
            'options' => ['title' => 'Sales Contract - '.$model->id],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=>['Krajee Report Header'],
                'SetTitle'=>'MUTASI GUDANG JADI - '.$model->id,
            ]
        ]);

        if($model->status == $model::STATUS_DRAFT){
            $pdf->methods['SetHeader'] = 'MUTASI GUDANG JADI | DRAFT | ';
        }else{
            $pdf->methods['SetHeader'] = 'MUTASI GUDANG JADI | '.$model->no_urut.' | '.$model->nomor;
        }

        $pdf->methods['SetFooter'] = 'Page {PAGENO}';

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * Finds the GudangJadiMutasi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GudangJadiMutasi the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GudangJadiMutasi::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
