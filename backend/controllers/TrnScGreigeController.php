<?php

namespace backend\controllers;

use kartik\mpdf\Pdf;
use Yii;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnScGreigeSearch;
use yii\helpers\BaseVarDumper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TrnScGreigeController implements the CRUD actions for TrnScGreige model.
 */
class TrnScGreigeController extends Controller
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
     * Lists all TrnScGreige models.
     * @return mixed
     */
    public function actionTest()
    {
        $model = $this->findModel(1);
        $print = $model->trnKirimBuyerPosted;
        BaseVarDumper::dump($print, 10, true);Yii::$app->end();
    }

    /**
     * Lists all TrnScGreige models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnScGreigeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('format_ko_james', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnScGreige model.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if(Yii::$app->request->isAjax){
            return $this->renderAjax('view', [
                'model' => $this->findModel($id),
            ]);
        }

        throw new ForbiddenHttpException('Not allowed');
    }

    /**
     * Creates a new TrnScGreige model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $scId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionCreate($scId)
    {
        if(Yii::$app->request->isAjax){
            $model = new TrnScGreige(['sc_id' => $scId]);

            $sc = $model->sc;

            if($sc->status != $sc::STATUS_DRAFT){
                throw new ForbiddenHttpException('SC bukan draft, greige tidak bisa ditambahkan.');
            }

            if ($model->load(Yii::$app->request->post())) {
                if($model->validate()){
                    $model->save(false);
                    return $this->asJson(['success' => true]);
                    //$model->addError('merek','fsdfssdsf');
                }

                $result = [];
                // The code below comes from ActiveForm::validate(). We do not need to validate the model
                // again, as it was already validated by save(). Just collect the messages.
                foreach ($model->getErrors() as $attribute => $errors) {
                    $result[Html::getInputId($model, $attribute)] = $errors;
                }

                return $this->asJson(['validation' => $result]);
            }

            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }

        throw new ForbiddenHttpException('Not allowed');
    }

    /**
     * Updates an existing TrnScGreige model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if(Yii::$app->request->isAjax){
            $model = $this->findModel($id);

            $sc = $model->sc;

            if($sc->status != $sc::STATUS_DRAFT){
                throw new ForbiddenHttpException('SC bukan draft, greige tidak bisa dirubah.');
            }

            if ($model->load(Yii::$app->request->post())) {
                if($model->save()){
                    return $this->asJson(['success' => true]);
                    //$model->addError('merek', 'sdfsfsf');
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
    }

    /**
     * Deletes an existing TrnScGreige model.
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

        $sc = $model->sc;
        if($sc->status != $sc::STATUS_DRAFT){
            throw new ForbiddenHttpException('SC bukan, greige tidak bisa dihapus.');
        }

        $model->delete();

        return $this->redirect(['/trn-sc/view', 'id'=>$model->sc_id]);
    }

    /**
     * Displays a single TrnScGreige model.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionClose($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model  = $this->findModel($id);
            $sc = $model->sc;
            if($sc->status != $sc::STATUS_APPROVED){
                throw new ForbiddenHttpException('Status SC tidak valid, greige tidak bisa diclose.');
            }

            if($model->closed){
                throw new ForbiddenHttpException('Status Greige sudah close, greige tidak bisa diclose ulang.');
            }

            $postData = Yii::$app->request->post('data');
            if(empty($postData)){
                throw new ForbiddenHttpException('Keterangan closing harus diisi.');
            }

            $model->closed = true;
            $model->closing_note = $postData;
            $model->save(false, ['closed', 'closing_note']);

            if($sc->getTrnScGreiges()->where(['closed'=>false])->count('id') < 1){
                $sc->status = $sc::STATUS_CLOSED;
                $sc->closed_at = time();
                $sc->closed_by = Yii::$app->user->id;
                $sc->closed_note = 'Closed otomatis ketika SC GREIGE GROUP diclose.';
                $sc->save(false, ['status', 'closed_at', 'closed_by', 'closed_note']);
            }

            return true;
        }

        throw new MethodNotAllowedHttpException('Metode tidak diijinkan.');
    }

    /**
     * Displays a single TrnScGreige model.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionApprovePmc($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model  = $this->findModel($id);
            $sc = $model->sc;
            if($model->order_grege_approved !== false){
                throw new ForbiddenHttpException('Status tidak valid, order greige tidak bisa disetujui.');
            }

            if($model->closed){
                throw new ForbiddenHttpException('Status Greige sudah close, order greige tidak bisa disetujui.');
            }

            $postData = Yii::$app->request->post('data');
            if(empty($postData)){
                throw new ForbiddenHttpException('Keterangan harus diisi.');
            }

            $model->order_grege_approved = true;
            $model->order_grege_approved_at = time();
            $model->order_grege_approved_by = Yii::$app->user->id;
            $model->order_grege_approval_note = $postData;
            $model->save(false, ['order_grege_approved', 'order_grege_approved_at', 'order_grege_approved_by', 'order_grege_approval_note']);

            return true;
        }

        throw new MethodNotAllowedHttpException('Metode tidak diijinkan.');
    }

    /**
     * Displays a single TrnScGreige model.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionApproveDir($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model  = $this->findModel($id);
            if($model->order_grege_approved_dir !== false){
                throw new ForbiddenHttpException('Status tidak valid, order greige tidak bisa disetujui.');
            }

            if($model->closed){
                throw new ForbiddenHttpException('Status Greige sudah close, order greige tidak bisa disetujui.');
            }

            $sc = $model->sc;
            if(Yii::$app->user->id != $sc->direktur_id){
                throw new ForbiddenHttpException('Anda bukan Direktur yang ditentukan, order greige tidak bisa disetujui.');
            }

            $postData = Yii::$app->request->post('data');
            if(empty($postData)){
                throw new ForbiddenHttpException('Keterangan harus diisi.');
            }

            $model->order_grege_approved_dir = true;
            $model->order_grege_approved_at_dir = time();
            $model->order_grege_approval_note_dir = $postData;
            $model->save(false, ['order_grege_approved_dir', 'order_grege_approved_at_dir', 'order_grege_approval_note_dir']);

            return true;
        }

        throw new MethodNotAllowedHttpException('Metode tidak diijinkan.');
    }

    /**
     * Finds the TrnScGreige model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnScGreige the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnScGreige::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDisplayOrderGreige($id){
        return $this->renderPartial('display-order-greige', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionPrintOrderGreige($id){
        $model = $this->findModel($id);

        $content = $this->renderPartial('print/order-greige', ['model' => $model]);

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_BLANK,
            // A4 paper format
            'format' => Pdf::FORMAT_FOLIO,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => Yii::$app->vendorPath.'/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
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
            //'options' => ['title' => 'Order Greige - '.$model->no_order_greige],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=>['Krajee Report Header'],
                'SetTitle'=>'Order Greige - '.$model->no_order_greige,
                //'SetFooter'=>['Page {PAGENO}'],
            ]
        ]);

        $pdf->methods['SetHeader'] = 'ORDER GREIGE | No. SC: '.$model->sc->no.' | '.Yii::$app->params['kode_dokumen']['mo'];

        // return the pdf output as per the destination setting
        return $pdf->render();
    }
}
