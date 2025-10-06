<?php

namespace backend\controllers;

use kartik\mpdf\Pdf;
use Yii;
use common\models\ar\TrnOrderPfp;
use common\models\ar\TrnOrderPfpSearch;
use common\models\ar\MstGreige;
use common\models\ar\TrnStockGreige;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TrnOrderPfpController implements the CRUD actions for TrnOrderPfp model.
 */
class TrnOrderPfpController extends Controller
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
     * Lists all TrnOrderPfp models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnOrderPfpSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnOrderPfp model.
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
     * Creates a new TrnOrderPfp model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnOrderPfp();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->greige_group_id = $model->greige->group_id;
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnOrderPfp model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->greige_group_id = $model->greige->group_id;
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TrnOrderPfp model.
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
     * @param integer $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionPosting($id)
    {
        $model = $this->findModel($id);
        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->status = $model::STATUS_POSTED;

        //validasi stock dan booked greige, sementara di bypass dulu karena order bisa dibuat walaupun stock lebih sedikit dibanding jumlah yang dimasukan.
        /*$greige = $model->greige;
        $greigeGroup = $greige->group;
        $qtyInUnit = $model->qty * ($greigeGroup->qty_per_batch);
        $stockUnit = $greige->stock;
        $bookedUnit = $greige->booked;
        $avUnit = $stockUnit - $bookedUnit;
        $unitName = $greigeGroup->unitName;

        if($avUnit < $qtyInUnit){
            $stockFmt = Yii::$app->formatter->asDecimal($stockUnit).' '.$unitName;
            $bookedFmt = Yii::$app->formatter->asDecimal($bookedUnit).' '.$unitName;
            $avFmt = Yii::$app->formatter->asDecimal($avUnit).' '.$unitName;
            Yii::$app->session->setFlash('error', "Persediaan digudang greige tidak mencukupi, jumlah stock: $stockFmt, Booked: $bookedFmt, Tersedia: $avFmt");
            return $this->redirect(['view', 'id' => $model->id]);
        }*/

        //Validasi Ketersediaan Stock Greige
        if($model->validasi_stock){
            switch ($model->jenis_gudang){
                case TrnStockGreige::JG_PFP:
                    $available = $model->greige->available_pfp;
                    break;
                case TrnStockGreige::JG_FRESH:
                    $available = $model->greige->available;
                    break;
                default:
                $available = $model->greige->available;
            }
            $qtyPerBatch = $model->greige->group->qty_per_batch *  $model->qty;
            if($available < $qtyPerBatch){
                Yii::$app->session->setFlash('error', 'Tidak dapat memposting Order PFP, Persediaan greige tidak mencukupi.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $model->save(false, ['status']);

        Yii::$app->session->setFlash('success', 'Order PFP berhasil diposting.');
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

        // get your HTML raw content without any layouts or scripts
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
                table {font-family: sans-serif; width: 100%; font-size:12px; border-spacing: 0; letter-spacing: 0px;} th, td {padding: 0.2em 0.2em; vertical-align: top;}',
            // set mPDF properties on the fly
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=>['Krajee Report Header'],
                'SetTitle'=>'ORDER PFP - '.$model->id,
                'SetFooter'=>['Page {PAGENO}'],
            ],
            'options' => [
                'title' => 'ORDER PFP - '.$model->id,
                //'setAutoTopMargin' => 'stretch'
            ],
        ]);

        if($model->status == $model::STATUS_DRAFT){
            //$pdf->methods['SetHeader'] = 'ORDER PFP | ID:'.$model->id.' | DRAFT';
            $pdf->methods['SetHeader'] = 'ORDER PFP | DRAFT | '.Yii::$app->params['kode_dokumen']['pfp'];
        }else{
            if($model->status == $model::STATUS_APPROVED){
                $pdf->methods['SetHeader'] = 'ORDER PFP | NO:'.$model->no.' | '.Yii::$app->params['kode_dokumen']['pfp'];
            }else $pdf->methods['SetHeader'] = 'ORDER PFP | MENUNGGU PERSETUJUAN | '.Yii::$app->params['kode_dokumen']['pfp'];
        }

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * Lists all TrnWo models.
     * @return mixed
     */
    public function actionRekapActual()
    {
        $searchModel = new TrnOrderPfpSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('rekap-actual', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the TrnOrderPfp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnOrderPfp the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnOrderPfp::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Melakukan pembatalan terhadap Order PFP.
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

            if($model->getTrnKartuProsesPfps()->count('id') > 0){
                throw new ForbiddenHttpException('Masih ada Kartu Proses PFP yang sedang aktif, tidak bisa dibatalkan.');
            }

            $post = Yii::$app->request->post('data');
            if(empty($post)){
                throw new ForbiddenHttpException('Keterangan pembatalan harus diisi.');
            }

            $model->status = $model::STATUS_BATAL;
            $model->batal_at = time();
            $model->batal_by = Yii::$app->user->id;
            $model->batal_note = $post;

            $greige = $model->greige;
            $greigeGroup = $greige->group;
            $totalColorsBatch = $model->qty;
            $totalColorsMeter = $totalColorsBatch * ($greigeGroup->qty_per_batch);

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if(!$model->save(false)){
                    $transaction->rollBack();
                    throw new HttpException(500, 'Gagal menyimpan data, coba lagi.');
                }

                switch ($model->jenis_gudang){

                    case TrnStockGreige::JG_PFP:
                        $availableAttr = 'available_pfp';
                        break;
                    case TrnStockGreige::JG_FRESH:
                        $availableAttr = 'available';
                        break;
                    default:
                        $availableAttr = 'available';
                }

                Yii::$app->db->createCommand()->update(
                    MstGreige::tableName(),
                    [
                        $availableAttr => new \yii\db\Expression($availableAttr . ' + ' . $totalColorsMeter),
                        'booked_opfp' => new \yii\db\Expression('booked_opfp - ' . $totalColorsMeter)
                    ],
                    ['id'=>$greige->id]
                )->execute();

                $transaction->commit();
                return true;
            }catch (\Throwable $t){
                $transaction->rollBack();
                throw $t;
            }

            $model->save(false, ['status', 'batal_at', 'batal_by', 'batal_note']);

            return true;
        }

        throw new MethodNotAllowedHttpException('Metode tidak diijinkan.');
    }


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

    public function actionSelectHandling($id)
    {
        $order = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $handlingId = Yii::$app->request->post('TrnOrderPfp')['handling_id'] ?? null;
            if ($handlingId) {
                $order->handling_id = $handlingId;
                $order->save(false, ['handling_id']); // simpan hanya field handling_id
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => true];
            }
        }

        return $this->renderAjax('select-handling', [
            'model' => $order,
        ]);
    }


    
}