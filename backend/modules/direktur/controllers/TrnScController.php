<?php
namespace backend\modules\direktur\controllers;

use backend\models\form\TrnScExportForm;
use backend\models\form\TrnScLocalForm;
use common\models\ar\MstBankAccount;
use common\models\ar\TrnNotif;
use kartik\mpdf\Pdf;
use Yii;
use common\models\ar\TrnSc;
use common\models\ar\TrnScSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseVarDumper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnScController implements the CRUD actions for TrnSc model.
 */
class TrnScController extends Controller
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
     * Lists all TrnSc models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnScSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnSc model.
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
     * Updates an existing TrnSc model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = TrnSc::findOne($id);
        if($model === null){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionClose($id){
        $model = $this->findModel($id);

        if(!$model->status == $model::STATUS_APPROVED){
            throw new ForbiddenHttpException('Status SC ini tidak valid, tidak bisa doclose.');
        }

        $model->status = $model::STATUS_CLOSED;
        $model->closed_at = time();
        $model->closed_by = Yii::$app->user->id;
        $model->closed_note = 'Diclose manual';
        $model->save(false, ['status', 'closed_at', 'closed_by', 'closed_note']);

        Yii::$app->session->setFlash('success', 'SC berhasil diclose.');

        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionPrintSc($id){
        $model = $this->findModel($id);

        // get your HTML raw content without any layouts or scripts
        switch ($model->tipe_kontrak){
            case TrnSc::TIPE_KONTRAK_EXPORT:
                $content = $this->renderPartial('print/export', ['model' => $model]);
                break;
            case TrnSc::TIPE_KONTRAK_LOKAL:
                $content = $this->renderPartial('print/lokal', ['model' => $model]);
                break;
            default:
                $content = '-';
        }

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
                'SetTitle'=>'SALES CONTRACT - '.$model->id,
            ]
        ]);

        if($model->status == $model::STATUS_DRAFT){
            $pdf->methods['SetHeader'] = 'SALES CONTRACT | ID:'.$model->id.' | DRAFT';
        }else{
            if($model->status == $model::STATUS_APPROVED){
                $pdf->methods['SetHeader'] = 'SALES CONTRACT - '.$model->jenisOrderName.' | ID:'.$model->id.' | NO:'.$model->no;
            }else $pdf->methods['SetHeader'] = 'SALES CONTRACT - '.$model->jenisOrderName.' | ID:'.$model->id.' | MENUNGGU PERSETUJUAN';
        }

        $pdf->methods['SetFooter'] = 'Page {PAGENO}';

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * Finds the TrnSc model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnSc the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnSc::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
