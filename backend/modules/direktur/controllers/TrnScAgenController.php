<?php

namespace backend\modules\direktur\controllers;

use kartik\mpdf\Pdf;
use Yii;
use common\models\ar\TrnScAgen;
use common\models\ar\TrnScAgenSearch;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnScAgenController implements the CRUD actions for TrnScAgen model.
 */
class TrnScAgenController extends Controller
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
     * Lists all TrnScAgen models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnScAgenSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnScAgen model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TrnScAgen model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $scId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionCreate($scId)
    {
        if(Yii::$app->request->isAjax){
            $model = new TrnScAgen(['sc_id'=>$scId]);

            if ($model->load(Yii::$app->request->post())) {
                $sc = $model->sc;
                $model->date = $sc->date;

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
     * Deletes an existing TrnScAgen model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->delete();

        return $this->redirect(['trn-sc/view', 'id'=>$model->sc_id]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPrintLoa($id){
        return $this->renderPartial('print-loa', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionDisplayLoa($id){
        $model = $this->findModel($id);
        $sc = $model->sc;

        $content = $this->renderPartial('print/loa', ['model' => $model]);

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
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=>['Krajee Report Header'],
                'SetFooter'=>['Page {PAGENO}'],
                'SetTitle'=>'LETTER OF AGREEMENT - '.$model->id,
            ]
        ]);

        if($sc->status == $sc::STATUS_DRAFT){
            $pdf->methods['SetHeader'] = 'LETTER OF AGREEMENT | '.$model->id.' | DRAFT';
        }else{
            if($sc->status == $sc::STATUS_APPROVED){
                $pdf->methods['SetHeader'] = 'LETTER OF AGREEMENT | '.$model->id.' | '.$model->no;
            }else $pdf->methods['SetHeader'] = 'LETTER OF AGREEMENT | '.$model->id.' | MENUNGGU PERSETUJUAN';
        }

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * Finds the TrnScAgen model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnScAgen the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnScAgen::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
