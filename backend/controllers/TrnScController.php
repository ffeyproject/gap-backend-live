<?php
namespace backend\controllers;

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
     * @param $action
     * @return bool
     * @throws HttpException
     * @throws BadRequestHttpException
     */
    public function beforeAction($action) {
        if(in_array($action->id, ['create-local', 'create-export'])){
            $formTokenName = Yii::$app->params['form_token_param'];

            if ($formTokenValue = Yii::$app->request->post($formTokenName)) {
                $sessionTokenValue = Yii::$app->session->get($formTokenName);

                if ($formTokenValue != $sessionTokenValue ) {
                    throw new HttpException(400, 'The form token could not be verified.');
                }

                Yii::$app->session->remove($formTokenName);
            }
        }

        //BaseVarDumper::dump($action, 10, true);Yii::$app->end();

        return parent::beforeAction($action);
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
     * Creates a new TrnSc model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    /*public function actionCreate()
    {
        $model = new TrnSc();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }*/

    /**
     * Creates a new TrnSc model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateLocal()
    {
        $model = new TrnScLocalForm([
            'tipe_kontrak'=>TrnSc::TIPE_KONTRAK_LOKAL,
            'currency'=>TrnSc::CURRENCY_IDR,
            'date'=>date('Y-m-d'),
            'disc_grade_b'=>0,
            'disc_piece_kecil'=>0,
        ]);

        $bankAcct = MstBankAccount::find()->orderBy('id')->asArray()->one();
        if(!empty($bankAcct)){
            $model->bank_acct_id = $bankAcct['id'];
        }

        $paramRoleMarketing = Yii::$app->params['rbac_roles']['marketing'];
        $userId = Yii::$app->user->id;
        $userRole = Yii::$app->authManager->getRolesByUser($userId);
        $userRole = ArrayHelper::toArray($userRole);
        $userRole = array_keys($userRole);
        $isMarketing = in_array($paramRoleMarketing, $userRole);
        if($isMarketing){
            $model->marketing_id = $userId;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //BaseVarDumper::dump($model, 10, true);Yii::$app->end();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create-local', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new TrnSc model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateExport()
    {
        $model = new TrnScExportForm([
            'tipe_kontrak'=>TrnSc::TIPE_KONTRAK_EXPORT,
            'currency'=>TrnSc::CURRENCY_USD,
            'date'=>date('Y-m-d'),
            'disc_grade_b'=>0,
            'disc_piece_kecil'=>0,
        ]);

        $paramRoleMarketing = Yii::$app->params['rbac_roles']['marketing'];
        $userId = Yii::$app->user->id;
        $userRole = Yii::$app->authManager->getRolesByUser($userId);
        $userRole = ArrayHelper::toArray($userRole);
        $userRole = array_keys($userRole);
        $isMarketing = in_array($paramRoleMarketing, $userRole);
        if($isMarketing){
            $model->marketing_id = $userId;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create-export', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnSc model.
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
     * Updates an existing TrnSc model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateLocal($id)
    {
        $model = TrnScLocalForm::findOne($id);
        if($model === null){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update-local', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnSc model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateExport($id)
    {
        $model = TrnScExportForm::findOne($id);
        if($model === null){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update-export', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TrnSc model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionPosting($id){
        $model = $this->findModel($id);

        if(!in_array($model->status, [$model::STATUS_DRAFT, $model::STATUS_REJECTED])){
            throw new ForbiddenHttpException('Status SC ini tidak valid, tidak bisa diposting.');
        }

        if($model->getTrnScGreiges()->count() < 1){
            Yii::$app->session->setFlash('error', 'Greige belum diinput, SC ini tidak bisa diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->status = $model::STATUS_POSTED;
        $model->posted_at = time();

        $notifMessage = 'Kontrak Pemesanan ID:'.$model->id.' menunggu persetujuan Anda.';
        $notifUrl = Yii::$app->urlManager->createAbsoluteUrl(['/approval-sc/view', 'id'=>$model->id]);

        $modelNotifDir = new TrnNotif([
            'user_id' => $model->direktur_id,
            'message' => $notifMessage,
            'link' => $notifUrl,
            'type' => TrnNotif::TYPE_TASK,
            'read' => false,
            'created_at' => time(),
        ]);
        $modelNotifDir->save(false);

        $modelNotifMgr = new TrnNotif([
            'user_id' => $model->manager_id,
            'message' => $notifMessage,
            'link' => $notifUrl,
            'type' => TrnNotif::TYPE_TASK,
            'read' => false,
            'created_at' => time(),
        ]);
        $modelNotifMgr->save(false);

        if($model->save(false, ['status', 'posted_at'])){
            Yii::$app->session->setFlash('success', 'Sales Contract berhasil diposting.');
        }else{
            Yii::$app->session->setFlash('error', 'Sales Contract gagal diposting, coba lagi.');
        }

        return $this->redirect(['view', 'id' => $model->id]);
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
            //$pdf->methods['SetHeader'] = 'SALES CONTRACT | ID:'.$model->id.' | DRAFT';
            $pdf->methods['SetHeader'] = 'SALES CONTRACT | DRAFT | '.Yii::$app->params['kode_dokumen']['sc'];
        }else{
            if($model->status == $model::STATUS_APPROVED){
                //$pdf->methods['SetHeader'] = 'SALES CONTRACT - '.$model->jenisOrderName.' | ID:'.$model->id.' | NO:'.$model->no;
                $pdf->methods['SetHeader'] = 'SALES CONTRACT - '.$model->jenisOrderName.' | NO:'.$model->no.' | '.Yii::$app->params['kode_dokumen']['sc'];
            }else {
                //$pdf->methods['SetHeader'] = 'SALES CONTRACT - '.$model->jenisOrderName.' | ID:'.$model->id.' | MENUNGGU PERSETUJUAN';
                $pdf->methods['SetHeader'] = 'SALES CONTRACT - '.$model->jenisOrderName.' | MENUNGGU PERSETUJUAN | '.Yii::$app->params['kode_dokumen']['sc'];
            }
        }

        $pdf->methods['SetFooter'] = 'Page {PAGENO}';

        // return the pdf output as per the destination setting
        return $pdf->render();
    }


    public function actionPrintScApproval($id){
        $model = $this->findModel($id);

        // get your HTML raw content without any layouts or scripts
        switch ($model->tipe_kontrak){
            case TrnSc::TIPE_KONTRAK_EXPORT:
                $content = $this->renderPartial('print/export-approval', ['model' => $model]);
                break;
            case TrnSc::TIPE_KONTRAK_LOKAL:
                $content = $this->renderPartial('print/lokal-approval', ['model' => $model]);
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
            //$pdf->methods['SetHeader'] = 'SALES CONTRACT | ID:'.$model->id.' | DRAFT';
            $pdf->methods['SetHeader'] = 'SALES CONTRACT | DRAFT | '.Yii::$app->params['kode_dokumen']['sc'];
        }else{
            if($model->status == $model::STATUS_APPROVED){
                //$pdf->methods['SetHeader'] = 'SALES CONTRACT - '.$model->jenisOrderName.' | ID:'.$model->id.' | NO:'.$model->no;
                $pdf->methods['SetHeader'] = 'SALES CONTRACT - '.$model->jenisOrderName.' | NO:'.$model->no.' | '.Yii::$app->params['kode_dokumen']['sc'];
            }else {
                //$pdf->methods['SetHeader'] = 'SALES CONTRACT - '.$model->jenisOrderName.' | ID:'.$model->id.' | MENUNGGU PERSETUJUAN';
                $pdf->methods['SetHeader'] = 'SALES CONTRACT - '.$model->jenisOrderName.' | MENUNGGU PERSETUJUAN | '.Yii::$app->params['kode_dokumen']['sc'];
            }
        }

        $pdf->methods['SetFooter'] = 'Page {PAGENO}';

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * Deletes an existing TrnSc model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionCloseManual()
    {
        $tsValidate = strtotime("-6 month", time()); // 6 bulan lalu

        $count = Yii::$app->db->createCommand()
            ->update(
                TrnSc::tableName(),
                [
                    'status'=>TrnSc::STATUS_CLOSED,
                    'closed_at'=>time(),
                    'closed_by'=>Yii::$app->user->id,
                    'closed_note'=>'Close otomatis SC lebih dari 6 bulan'
                ],
                //['<=', 'created_at', $tsValidate],
                'created_at < '.$tsValidate.' AND status <> '.TrnSc::STATUS_CLOSED
            )
            ->execute()
        ;

        Yii::$app->session->setFlash('success', 'Close SC berhasil. Sebanyak '.$count.' telah diclose.');
        return $this->redirect(['index']);
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