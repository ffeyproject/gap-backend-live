<?php

namespace backend\controllers;

use kartik\mpdf\Pdf;
use Yii;
use common\models\ar\TrnMoMemo;
use common\models\ar\TrnMoMemoSearch;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnMoMemoController implements the CRUD actions for TrnMoMemo model.
 */
class TrnMoMemoController extends Controller
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
     * Lists all TrnMoMemo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnMoMemoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnMoMemo model.
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
     * Creates a new TrnScAgen model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $moId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionCreate($moId)
    {
        if(Yii::$app->request->isAjax){
            $model = new TrnMoMemo(['mo_id'=>$moId]);

            if ($model->load(Yii::$app->request->post())) {
                $mo = $model->mo;

                if($mo->status != $mo::STATUS_APPROVED){
                    throw new ForbiddenHttpException('Status MO tidak valid, memo perubahan tidak bisa ditambahkan.');
                }

                if($model->validate()){
                    $model->created_at = time();
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
     * Updates an existing TrnMoMemo model.
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
     * @param $id
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $mo = $model->mo;

        if($mo->status != $mo::STATUS_APPROVED){
            throw new ForbiddenHttpException('Status MO tidak valid, memo perubahan tidak bisa dihapus.');
        }

        $model->delete();

        return $this->redirect(['/trn-mo/view', 'id'=>$mo->id]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDisplay($id){
        return $this->renderPartial('display-memo', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionPrint($id){
        $model = $this->findModel($id);
        $mo = $model->mo;

        $content = $this->renderPartial('print', ['model'=>$model, 'mo'=>$mo]);

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
            //'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
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
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=>['Krajee Report Header'],
                'SetFooter'=>['Page {PAGENO}'],
                'SetTitle'=>'MEMO PERUBAHAN - '.$model->id,
            ]
        ]);

        $pdf->methods['SetHeader'] = 'MEMO PERUBAHAN | '.$model->id.' | MO: '.$mo->no;

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * Finds the TrnMoMemo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnMoMemo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnMoMemo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
