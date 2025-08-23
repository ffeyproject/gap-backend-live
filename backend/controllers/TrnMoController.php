<?php

namespace backend\controllers;

use backend\models\form\TrnMoDyeingForm;
use backend\models\form\TrnMoPrintingForm;
use common\models\ar\TrnMoColor;
use common\models\ar\TrnNotif;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnMoSearchArray;
use kartik\mpdf\Pdf;
use Yii;
use common\models\ar\TrnMo;
use common\models\ar\TrnMoSearch;
use yii\db\Query;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\data\Pagination;
use yii\helpers\BaseVarDumper;



/**
 * TrnMoController implements the CRUD actions for TrnMo model.
 */
class TrnMoController extends Controller
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
     * Lists all TrnMo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnMoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

        /**
     * Lists all TrnMo models.
     * @return mixed
     */
    public function actionIndexSisa()
    {
        $searchModel2 = new TrnMoSearch(['status'=>TrnMo::STATUS_APPROVED]);
        $dataProvider2 = $searchModel2->search(Yii::$app->request->queryParams);

        $searchModel = new TrnMoSearchArray(['status'=>TrnMo::STATUS_APPROVED]);;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // $dataProvider->pagination->pageSize = 500;  

        // $filtered_models = [];
        // $filter_models = false; // if you only want to filter if there is some value

    
        // foreach ($dataProvider->models as $model) {
        //     // if ($model->status == 1) // example
        //     if ($model->woSisaBatch > 0 ) { // better approach, using virtual attribute $status
        //         $filter_models = true;
        //         $filtered_models[] = $model;
        //     }
        // }
    
        // if ($filter_models){
        //     $dataProvider->setModels($filtered_models);
        // }

        
        return $this->render('index-sisa', [    
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'searchModel2' => $searchModel2,
            'dataProvider2' => $dataProvider2,
        ]);
    }

    /**
     * Displays a single TrnMo model.
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
     * Creates a new TrnMo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $scGreigeId
     * @return mixed
     * @throws HttpException
     */
    public function actionCreate($scGreigeId)
    {
        $scGreige = $this->validateScGreige($scGreigeId);

        $date = date('Y-m-d');

        switch ($scGreige->process){
            case $scGreige::PROCESS_PRINTING:
                $view = 'create-printing';
                $model = new TrnMoPrintingForm([
                    'sc_greige_id'=>$scGreige->id,
                    'date'=>$date,
                    'no_po' => $scGreige->sc->no_po,
                    'jet_black' => $scGreige->sc->jet_black,
                    /*'est_produksi'=>$date,
                    'est_packing'=>$date,
                    'target_shipment'=>$date,
                    'approval_id'=>9,
                    'piece_length'=>'10 M',
                    'jet_black'=>true,
                    'heat_cut'=>true,
                    'article'=>'article',
                    'hanger'=>'hanger',
                    'label'=>'label',
                    'joint'=>true,
                    'joint_qty'=>5,
                    'selvedge_stamping'=>'Selvedge Stamping',
                    'selvedge_continues'=>'Selvedge Continues',
                    'side_band'=>'Side Band',
                    'tag'=>'tag',
                    'folder'=>'folder',
                    'arsip'=>'arsip',
                    'album'=>'album',
                    'packing_method'=>1,
                    'shipping_method'=>1,
                    'shipping_sorting'=>1,
                    'plastic'=>1,
                    'face_stamping'=>'face_stamping',
                    'note'=>'note',
                    'design'=>'design',
                    'strike_off'=>'strike_off',
                    'border_size'=>10,
                    'block_size'=>10,
                    'foil'=>true,*/
                ]);
                break;
            case $scGreige::PROCESS_DYEING:
                $view = 'create-dyeing';
                $model = new TrnMoDyeingForm([
                    'sc_greige_id'=>$scGreige->id,
                    'date'=>$date,
                    'no_po' => $scGreige->sc->no_po,
                    'jet_black' => $scGreige->sc->jet_black,
                    /*'est_produksi'=>$date,
                    'est_packing'=>$date,
                    'target_shipment'=>$date,
                    'approval_id'=>9,
                    'piece_length'=>'10 M',
                    'jet_black'=>true,
                    'heat_cut'=>true,
                    'article'=>'article',
                    'hanger'=>'hanger',
                    'label'=>'label',
                    'joint'=>true,
                    'joint_qty'=>0,
                    'selvedge_stamping'=>'Selvedge Stamping',
                    'selvedge_continues'=>'Selvedge Continues',
                    'side_band'=>'Side Band',
                    'tag'=>'tag',
                    'folder'=>'folder',
                    'arsip'=>'arsip',
                    'album'=>'album',
                    'packing_method'=>1,
                    'shipping_method'=>1,
                    'shipping_sorting'=>1,
                    'plastic'=>1,
                    'face_stamping'=>'face_stamping',
                    'note'=>'note',
                    'sulam_pinggir'=>'sulam_pinggir',*/
                ]);
                break;
            default:
                throw new ForbiddenHttpException($scGreige::processOptions()[$scGreige->process].' belum didukung untuk pembuatan MO.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->process = $scGreige->process;
            $model->sc_id = $scGreige->sc_id;
            if($model->save(false)){
                return $this->redirect(['view', 'id' => $model->id]);
            }

            throw new HttpException(500, 'Gagal menyimpan, coba lagi.');
        }

        return $this->render($view, [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnMo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $row = (new \yii\db\Query())
            ->select(['id', 'sc_greige_id'])
            ->from(TrnMo::tableName())
            ->where(['id' => $id])
            ->one();
        if(!$row){
            throw new NotFoundHttpException('NOT FOUND.');
        }

        if(($scGreige = TrnScGreige::findOne($row['sc_greige_id'])) === null) throw new NotFoundHttpException('NOT FOUND.');

        switch ($scGreige->process){
            case $scGreige::PROCESS_PRINTING:
                $view = 'update-printing';
                $model = TrnMoPrintingForm::findOne($id);
                break;
            case $scGreige::PROCESS_DYEING:
                $view = 'update-dyeing';
                $model = TrnMoDyeingForm::findOne($id);
                break;
            default:
                throw new ForbiddenHttpException($scGreige::processOptions()[$scGreige->process].' belum didukung untuk perubahan MO.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render($view, [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TrnMo model.
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

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            TrnMoColor::deleteAll(['mo_id'=>$model->id]);
            $model->delete();
            $transaction->commit();
            return $this->redirect(['index']);
        }catch (\Throwable $e){
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    public function actionPosting($id){
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status MO ini tidak valid, MO ini tidak bisa diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if(!($model->getTrnMoColors()->count('id') >= 1)){
            Yii::$app->session->setFlash('error', 'Color belum diinput, MO ini tidak bisa diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $scGreige = $model->scGreige;

        //validasi jumlah batch mo_color dari semua MO dalam satu sc_greige yang sudah diinput-----------------------------------
        $scGreigeQty = $scGreige->qty;

        $totalColorCreated = $scGreige->getTrnMoColorsAktif()->sum('qty'); //total mo color dalam sc greige dan mo yang bukan batal
        $totalColorCreated = $totalColorCreated === null ? 0 : $totalColorCreated;

        $qtColorInput = $model->getTrnMoColors()->sum('qty'); //total mo color yang akan diposting
        $qtColorInput = $qtColorInput === null ? 0 : $qtColorInput;

        if($totalColorCreated  > $scGreigeQty){
            Yii::$app->session->setFlash('error', 'Color melebihi jumlah kontrak yang tersisa ('.Yii::$app->formatter->asDecimal(($scGreigeQty - $totalColorCreated + $qtColorInput)).' BATCH), MO ini tidak bisa diposting. Periksa MO lainnya yang belum diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        //validasi jumlah batch mo_color dari semua MO dalam satu sc_greige yang sudah diinput-----------------------------------

        $model->status = $model::STATUS_POSTED;
        $model->posted_at = time();
        $model->save(false, ['status', 'posted_at']);

        $notifMessage = 'Marketing Order ID:'.$model->id.' menunggu persetujuan Anda.';
        $notifUrl = Yii::$app->urlManager->createAbsoluteUrl(['/approval-mo/view', 'id'=>$model->id]);

        $modelNotif = new TrnNotif([
            'user_id' => $model->approval_id,
            'message' => $notifMessage,
            'link' => $notifUrl,
            'type' => TrnNotif::TYPE_TASK,
            'read' => false,
            'created_at' => time(),
        ]);
        $modelNotif->save(false);

        Yii::$app->session->setFlash('success', 'MO berhasil diposting');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Melakukan close terhadap MO.
     * Syarat: MO berstatus disetujui.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionClose($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if($model->status != $model::STATUS_APPROVED){
                throw new ForbiddenHttpException('Status tidak valid, tidak bisa diclose.');
            }

            $post = Yii::$app->request->post('data');
            if(empty($post)){
                throw new ForbiddenHttpException('Keterangan closing harus diisi.');
            }

            $model->status = $model::STATUS_CLOSED;
            $model->closed_at = time();
            $model->closed_by = Yii::$app->user->id;
            $model->closed_note = $post;
            $model->save(false, ['status', 'closed_at', 'closed_by', 'closed_note']);

            //Jika pada scGreige induk semua mo anaknya sudah close, maka close otomatis scGreige nya
            $rowsCount = (new \yii\db\Query())
                ->select(['id'])
                ->from(TrnMo::tableName())
                ->where(['sc_greige_id' => $model->sc_greige_id])
                ->andWhere(['not in', 'status', [TrnMo::STATUS_CLOSED, TrnMo::STATUS_BATAL]])
                ->count('id');
            if(!($rowsCount > 0)){
                Yii::$app->db->createCommand()->update(
                    TrnScGreige::tableName(),
                    ['closed'=>true, 'closing_note'=>'Close otomatis dari MO yang diclose manual.'],
                    'id = '.$model->sc_greige_id
                )->execute();
            }

            return true;
        }

        throw new MethodNotAllowedHttpException('Metode tidak diijinkan.');
    }

    /**
     * Melakukan pembatalan terhadap MO.
     * Syarat pembatalan: MO berstatus disetujui, semua wo-nya harus berstatus batal.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionBatal($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if($model->status != $model::STATUS_APPROVED){
                throw new ForbiddenHttpException('Status tidak valid, tidak bisa dibatalkan.');
            }

            if($model->getTrnWosAktif()->count('id') > 0){
                throw new ForbiddenHttpException('Masih ada WO yang sedang aktif, tidak bisa dibatalkan.');
            }

            $post = Yii::$app->request->post('data');
            if(empty($post)){
                throw new ForbiddenHttpException('Keterangan pembatalan harus diisi.');
            }

            $model->status = $model::STATUS_BATAL;
            $model->batal_at = time();
            $model->batal_by = Yii::$app->user->id;
            $model->batal_note = $post;
            $model->save(false, ['status', 'batal_at', 'batal_by', 'batal_note']);

            return true;
        }

        throw new MethodNotAllowedHttpException('Metode tidak diijinkan.');
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionPrintMo($id){
        $model = $this->findModel($id);
        $scGreige = $model->scGreige;
        $sc = $model->sc;

        // get your HTML raw content without any layouts or scripts
        switch ($scGreige->process){
            case $scGreige::PROCESS_DYEING://DYEING
                $content = $this->renderPartial('print/dyeing', ['model' => $model, 'scGreige' => $scGreige, 'sc' => $sc]);
                break;
            case $scGreige::PROCESS_PRINTING://PRINTING
                $content = $this->renderPartial('print/printing', ['model' => $model, 'scGreige' => $scGreige, 'sc' => $sc]);
                break;
            default:
                $processName = $scGreige::processOptions()[$scGreige->process];
                throw new NotAcceptableHttpException("Mohon maaf, untuk sementara proses \"{$processName}\" belum didukung.");
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
            //'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            //'cssFile' => Yii::$app->vendorPath.'/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
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
            'options' => ['title' => 'Marketing Order - '.$model->id],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=>['Krajee Report Header'],
                'SetTitle'=>'MARKETING ORDER - '.$model->id,
                'SetFooter'=>['Page {PAGENO}'],
            ]
        ]);

        if($model->status == $model::STATUS_DRAFT){
            $pdf->methods['SetHeader'] = 'MARKETING ORDER | DRAFT | '.Yii::$app->params['kode_dokumen']['mo'];
        }else{
            if($model->status == $model::STATUS_APPROVED){
                $pdf->methods['SetHeader'] = 'MARKETING ORDER | NO:'.$model->no.' | '.Yii::$app->params['kode_dokumen']['mo'];
            }else $pdf->methods['SetHeader'] = 'MARKETING ORDER | MENUNGGU PERSETUJUAN | '.Yii::$app->params['kode_dokumen']['mo'];
        }

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * Finds the TrnMo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnMo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnMo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param $scGreigeId
     * @return TrnScGreige|null
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    protected function validateScGreige($scGreigeId){
        if (($model = TrnScGreige::findOne($scGreigeId)) !== null) {
            /*
            * Memeriksa apakah SC Greige masih tersedia quantiti untuk dibuat MO nya
            * Jika qty MO (Aktif) sudah mencukupi, throw exception
            * */
            $scGreigeMoColorQty = $model->getTrnMoColorsAktif()->sum('trn_mo_color.qty');
            $scGreigeMoColorQty = $scGreigeMoColorQty > 0 ? $scGreigeMoColorQty : 0;
            if ($model->qty <= $scGreigeMoColorQty) {
                throw new ForbiddenHttpException('Quantiti MO untuk greige ini sudah mencukupi, MO tidak bisa dibuat.');
            }

            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}