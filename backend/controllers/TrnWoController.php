<?php

namespace backend\controllers;

use common\models\ar\MstGreige;
use common\models\ar\TrnMo;
use common\models\ar\TrnNotif;
use common\models\ar\TrnSc;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWoColor;
use common\models\rekap\RekapWoTotalSearch;
use kartik\mpdf\Pdf;
use Yii;
use common\models\ar\TrnWo;
use common\models\ar\TrnWoSearch;
use common\models\ar\User;
use yii\db\Query;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\Expression;

/**
 * TrnWoController implements the CRUD actions for TrnWo model.
 */
class TrnWoController extends Controller
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
                    'validasi-stock-off' => ['POST'],
                    'validasi-stock-on' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all TrnWo models.
     * @return mixed
     */
    public function actionRekapOrderActualDyeing()
    {
        $searchModel = new TrnWoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('rekap-order-actual-dyeing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all TrnWo models.
     * @return mixed
     */
    public function actionRekapTotalWo()
    {
        $searchModel = new RekapWoTotalSearch(['status'=>TrnWo::STATUS_APPROVED]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('rekap-total-wo', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all TrnWo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnWoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnWo model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $users = User::find()
            ->where(['status_notif_email' => true])
            ->andWhere(['status' => 10])
            ->orderBy(['full_name' => SORT_ASC])
            ->all();

            $userWa = User::find()
            ->where(['IS NOT', 'phone_number', null])
            ->andWhere(['status' => 10])
            ->orderBy(['full_name' => SORT_ASC])
            ->all();    

        $mo = $model->mo;
        $greige = $model->greige;

        switch ($mo->jenis_gudang){
            case TrnStockGreige::JG_WIP:
                $stockM = $greige->stock_wip;
                $bookedM = $greige->booked_wip;
                $stockLabel = 'stock WIP';
                $bookkLabel = 'booked WIP';
                $avM = $stockM - $bookedM;
                break;
            case TrnStockGreige::JG_PFP:
                $stockM = $greige->stock_pfp;
                $bookedM = $greige->booked_pfp;
                $stockLabel = 'stock PFP';
                $bookkLabel = 'booked PFP';
                $avM = $stockM - $bookedM;
                break;
            case TrnStockGreige::JG_EX_FINISH:
                $stockM = $greige->stock_ef;
                $bookedM = $greige->booked_ef;
                $stockLabel = 'stock Ex Finish';
                $bookkLabel = 'booked Ex Finish';
                $avM = $stockM - $bookedM;
                break;
            default:
                $stockM = $greige->stock;
                $bookedM = $greige->booked;
                $stockLabel = 'stock';
                $bookkLabel = 'booked';
                $avM = $greige->available;
        }


        return $this->render('view', [
            'model' => $model,
            'stockM' => $stockM,
            'bookedM' => $bookedM,
            'stockLabel' => $stockLabel,
            'bookkLabel' => $bookkLabel,
            'users' => $users,
            'userWa' => $userWa,
            'avM' => $avM
        ]);
    }

    /**
     * Creates a new TrnWo model.
     * status MO harus approved.
     * total jumlah color pada wo turunan (aktif/tidak batal) harus lebih kecil dibanding jumlah color pada mo
     * @param $mo_id
     * @return mixed
     */
    public function actionCreate($mo_id)
    {
        $model = new TrnWo(['mo_id'=>$mo_id, 'date'=>date('Y-m-d')]);

        $mo = $model->mo;
        if($mo->status != $mo::STATUS_APPROVED){
            Yii::$app->session->setFlash('error', 'Status MO tidak valid, WO tidak bisa dibuat.');
            return $this->redirect(['/trn-mo/view', 'id' => $mo_id]);
        }

        if($mo->trnWoColorsAktifQty >= $mo->colorQty){
            Yii::$app->session->setFlash('error', 'Color pada MO sudah tercukupi, WO tidak bisa dibuat. Periksa WO turunan lain nya dari MO ini untuk mengetahui sisa WO yang bisa dibuat.');
            return $this->redirect(['/trn-mo/view', 'id' => $mo_id]);
        }

        $model->sc_greige_id = $mo->sc_greige_id;
        $model->sc_id = $mo->sc_id;
        $model->marketing_id = $model->sc->marketing_id;

        $model->jenis_order = $model->sc->jenis_order;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnWo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TrnWo model.
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

        TrnWoColor::deleteAll(['wo_id'=>$id]);

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionPosting($id){
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status WO ini tidak valid, WO ini tidak bisa diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        // periksa apakah wo ini sudah diisi color, jika belum maka gagalkan.
        $colExist= (new Query())->select(new Expression(1))->from(TrnWoColor::tableName())
            ->where(['wo_id'=>$model->id])
            ->exists()
        ;
        if(!$colExist){
            Yii::$app->session->setFlash('error', 'Color belum diinput, WO ini tidak bisa diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->status = $model::STATUS_POSTED;
        $model->posted_at = time();
        $model->save(false, ['status', 'posted_at']);

        $notifMessage = 'Working Order ID:'.$model->id.' menunggu persetujuan Anda.';
        $notifUrl = Yii::$app->urlManager->createAbsoluteUrl(['/approval-wo/view', 'id'=>$model->id]);

        $modelNotifMeng = new TrnNotif([
            'user_id' => $model->mengetahui_id,
            'message' => $notifMessage,
            'link' => $notifUrl,
            'type' => TrnNotif::TYPE_TASK,
            'read' => false,
            'created_at' => time(),
        ]);
        $modelNotifMeng->save(false);

        $modelNotifMkt = new TrnNotif([
            'user_id' => $model->marketing_id,
            'message' => $notifMessage,
            'link' => $notifUrl,
            'type' => TrnNotif::TYPE_TASK,
            'read' => false,
            'created_at' => time(),
        ]);
        $modelNotifMkt->save(false);

        Yii::$app->session->setFlash('success', 'Posting berhasil.');
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

            //Jika pada MO induk semua wo anaknya sudah close, maka close otomatis MO nya
            $rowsCount = (new \yii\db\Query())
                ->select(['id'])
                ->from(TrnWo::tableName())
                ->where(['mo_id' => $model->mo_id])
                ->andWhere(['not in', 'status', [TrnWo::STATUS_BATAL, TrnWo::STATUS_CLOSED]])
                ->count('id');

            if(!($rowsCount > 0)){
                Yii::$app->db->createCommand()->update(
                    TrnMo::tableName(),
                    [
                        'status'=>TrnMo::STATUS_CLOSED,
                        'closed_at'=>time(),
                        'closed_by'=>Yii::$app->user->id,
                        'closed_note'=>'Close otomatis dari WO yang diclose manual.'
                    ],
                    'id = '.$model->mo_id
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

            if($model->getTrnKartuProsesDyeingsNonPg()->count('id') > 0){
                throw new ForbiddenHttpException('Masih ada Kartu Proses Dyeing yang sedang aktif, tidak bisa dibatalkan.');
            }

            if($model->getTrnKartuProsesPrintingsNonPg()->count('id') > 0){
                throw new ForbiddenHttpException('Masih ada Kartu Proses Printing yang sedang aktif, tidak bisa dibatalkan.');
            }

            if($model->getTrnKartuProsesMaklons()->count('id') > 0){
                throw new ForbiddenHttpException('Masih ada Kartu Proses Maklon yang sedang aktif, tidak bisa dibatalkan.');
            }

            if($model->getTrnGreigeKeluarMakloon()->count('id') > 0){
                throw new ForbiddenHttpException('Sudah Ada Greige Makloon yang keluar, tidak bisa dibatalkan.');
            }

            $post = Yii::$app->request->post('data');
            if(empty($post)){
                throw new ForbiddenHttpException('Keterangan pembatalan harus diisi.');
            }

            $model->status = $model::STATUS_BATAL;
            $model->batal_at = time();
            $model->batal_by = Yii::$app->user->id;
            $model->batal_note = $post;

            $mo = $model->mo;

            if($model->jenis_order === TrnSc::JENIS_ORDER_FRESH_ORDER && $mo->jenis_gudang === TrnStockGreige::JG_FRESH){
                // jika jenis order wo === fresh dan jenis gudang mo == jg_fresh, maka update stock greige
                // kurangi booked_wo dan kembalikan available

                $greige = $model->greige;
                $greigeGroup = $greige->group;
                $totalColorsBatch = $model->colorQty;
                $totalColorsMeter = $totalColorsBatch * ($greigeGroup->qty_per_batch);

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if(!$model->save(false)){
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal menyimpan data, coba lagi.');
                    }

                    Yii::$app->db->createCommand()->update(
                        MstGreige::tableName(),
                        [
                            'available' => new \yii\db\Expression('available + ' . $totalColorsMeter),
                            'booked_wo' => new \yii\db\Expression('booked_wo - ' . $totalColorsMeter)
                        ],
                        ['id'=>$greige->id]
                    )->execute();

                    $transaction->commit();
                    return true;
                }catch (\Throwable $t){
                    $transaction->rollBack();
                    throw $t;
                }
            }

            if($model->jenis_order === TrnSc::JENIS_ORDER_MAKLOON && $mo->jenis_gudang === TrnStockGreige::JG_FRESH){
                // jika jenis order wo === Makloon dan jenis gudang mo == jg_fresh, maka update stock greige
                // kurangi booked_wo dan kembalikan available
                
                $greige = $model->greige;
                $greigeGroup = $model->scGreige->greigeGroup;
                $totalColorsBatch = $model->colorQty;
                $totalColorsMeter = $totalColorsBatch * ($greigeGroup->qty_per_batch);

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if(!$model->save(false)){
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal menyimpan data, coba lagi.');
                    }

                    Yii::$app->db->createCommand()->update(
                        MstGreige::tableName(),
                        [
                            'available' => new \yii\db\Expression('available + ' . $totalColorsMeter),
                            'booked_wo' => new \yii\db\Expression('booked_wo - ' . $totalColorsMeter)
                        ],
                        ['id'=>$greige->id]
                    )->execute();

                    $transaction->commit();
                    return true;
                }catch (\Throwable $t){
                    $transaction->rollBack();
                    throw $t;
                }
            }
            $model->save(false, ['status', 'batal_at', 'batal_by', 'batal_note']);

            return true;
        }

        throw new MethodNotAllowedHttpException('Metode tidak diijinkan.');
    }

    /**
     * @param $id
     * @return yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionValidasiStockOff($id){
        $model = $this->findModel($id);

        if($model->validasi_stock === false){
            Yii::$app->session->setFlash('error', 'Validasi stock sudah dalam posisi nonaktif.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->validasi_stock = false;
        $model->save(false, ['validasi_stock']);

        Yii::$app->session->setFlash('success', 'Validasi stock berhasil dinonaktifkan.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * @param $id
     * @return yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionValidasiStockOn($id){
        $model = $this->findModel($id);

        if($model->validasi_stock){
            Yii::$app->session->setFlash('error', 'Validasi stock sudah dalam posisi aktif.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->validasi_stock = true;
        $model->save(false, ['validasi_stock']);

        Yii::$app->session->setFlash('success', 'Validasi stock berhasil diaktifkan.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionPrint($id){
        $model = $this->findModel($id);
        $mo = $model->mo;
        $scGreige = $model->scGreige;

        // get your HTML raw content without any layouts or scripts
        switch ($scGreige->process){
            case $scGreige::PROCESS_DYEING:
                $content = $this->renderPartial('print/print', ['model' => $model, 'mo'=>$mo, 'scGreige' => $scGreige]);
                $proccessName = 'Dyeing';
                break;
            case $scGreige::PROCESS_PRINTING:
                $content = $this->renderPartial('print/print', ['model' => $model, 'mo'=>$mo, 'scGreige' => $scGreige]);
                $proccessName = 'Printing';
                break;
            default:
                $procName = $scGreige::processOptions()[$scGreige->process];
                throw new NotAcceptableHttpException("Mohon maaf, untuk sementara proses \"{$procName}\" belum didukung.");
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
            'options' => ['title' => 'Working Order - '.$model->id],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=>['Krajee Report Header'],
                'SetTitle'=>'WORKING ORDER - '.$model->id,
                'SetFooter'=>['Page {PAGENO}'],
            ]
        ]);

        if($model->status == $model::STATUS_BATAL){
            //$pdf->methods['SetHeader'] = 'WORKING ORDER '.$proccessName.'| ID:'.$model->id.' (BATAL) | NO:'.$model->no;
            $pdf->methods['SetHeader'] = 'WORKING ORDER '.$proccessName.'| NO:'.$model->no.' (BATAL) | '.Yii::$app->params['kode_dokumen']['wo'];
        }else if($model->status == $model::STATUS_APPROVED){
            //$pdf->methods['SetHeader'] = 'WORKING ORDER '.$proccessName.'| ID:'.$model->id.' | NO:'.$model->no;
            $pdf->methods['SetHeader'] = 'WORKING ORDER '.$proccessName.'| NO:'.$model->no.' | '.Yii::$app->params['kode_dokumen']['wo'];
        }else if($model->status == $model::STATUS_DRAFT){
            //$pdf->methods['SetHeader'] = 'WORKING ORDER '.$proccessName.'| ID:'.$model->id.' | DRAFT';
            $pdf->methods['SetHeader'] = 'WORKING ORDER '.$proccessName.'| DRAFT | '.Yii::$app->params['kode_dokumen']['wo'];
        }else{
            //$pdf->methods['SetHeader'] = 'WORKING ORDER '.$proccessName.'| ID:'.$model->id.' | MENUNGGU PERSETUJUAN';
            $pdf->methods['SetHeader'] = 'WORKING ORDER '.$proccessName.'| MENUNGGU PERSETUJUAN | '.Yii::$app->params['kode_dokumen']['wo'];
        }

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * Finds the TrnWo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnWo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnWo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}