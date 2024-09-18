<?php

namespace backend\controllers;

use kartik\mpdf\Pdf;
use Yii;
use common\models\ar\InspectingPrintingReject;
use common\models\ar\InspectingPrintingRejectSearch;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * InspectingPrintingRejectController implements the CRUD actions for InspectingPrintingReject model.
 */
class InspectingPrintingRejectController extends Controller
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
     * Lists all InspectingPrintingReject models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InspectingPrintingRejectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single InspectingPrintingReject model.
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
     * Creates a new InspectingPrintingReject model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $kartu_proses_id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionCreate($kartu_proses_id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = new InspectingPrintingReject([
                'kartu_proses_id'=>$kartu_proses_id,
                'date' => date('Y-m-d'),
                'created_at' => time(),
                'created_by' => Yii::$app->user->id
            ]);

            if ($model->load(Yii::$app->request->post())) {
                if($model->validate()){
                    $model->setNomor();

                    $kartuProses = $model->kartuProses;
                    $kartuProses->status = $kartuProses::STATUS_DELIVERED;
                    $kartuProses->approved_at = null;
                    $kartuProses->approved_by = null;

                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if(! ($flag = $model->save(false))){
                            $transaction->rollBack();
                            throw new HttpException(500, 'Gagal menyimpan, coba lagi. (1)');
                        }

                        if(! ($flag = $kartuProses->save(false, ['status', 'approved_at', 'approved_by']))){
                            $transaction->rollBack();
                            throw new HttpException(500, 'Gagal menyimpan, coba lagi. (2)');
                        }

                        if($flag){
                            $transaction->commit();
                            return ['success' => true, 'data'=>Url::to(['view', 'id'=>$model->id], true)];
                        }
                    }catch (\Throwable $t){
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

            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }

        throw new ForbiddenHttpException('Not allowed');
    }

    /**
     * Updates an existing InspectingPrintingReject model.
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
     * Deletes an existing InspectingPrintingReject model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionPrint($id){
        $model = $this->findModel($id);

        $content = $this->renderPartial('_print', ['model' => $model]);

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
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '
                body{font-family: sans-serif; font-size:12px; letter-spacing: 0px;}
                table {font-family: sans-serif; width: 100%; font-size:12px; border-spacing: 0; letter-spacing: 0px;} th, td {padding: 0.5em 0.5em; vertical-align: top;}
                table.bordered th, table.bordered td {padding: 0.5em 0.2em; border: 1px solid black;}
            ',
            // set mPDF properties on the fly
            //'options' => ['title' => 'Sales Contract - '.$model->id],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHTMLHeader'=>$this->renderPartial('print/header', ['model' => $model, 'config'=>$config]),
                'SetTitle'=>'SURAT PENGANTAR - '.$model->id,
                'SetFooter'=>['Page {PAGENO}'],
            ],
            'options' => [
                'setAutoTopMargin' => 'stretch'
            ],
            // call mPDF methods on the fly
        ]);

        $pdf->methods['SetHeader'] = 'SURAT PENGANTAR | '.$model->id.' | '.$model->no;

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * Finds the InspectingPrintingReject model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InspectingPrintingReject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InspectingPrintingReject::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
