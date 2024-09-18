<?php

namespace backend\controllers;

use common\models\ar\MstGreige;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnKartuProsesMaklonItem;
use common\models\ar\TrnSc;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWoColor;
use common\models\Model;
use kartik\mpdf\Pdf;
use Yii;
use common\models\ar\TrnKartuProsesMaklon;
use common\models\ar\TrnKartuProsesMaklonSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseVarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnKartuProsesMaklonController implements the CRUD actions for TrnKartuProsesMaklon model.
 */
class TrnKartuProsesMaklonController extends Controller
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
     * Lists all TrnKartuProsesMaklon models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnKartuProsesMaklonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnKartuProsesMaklon model.
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
     * Creates a new TrnKartuProsesMaklon model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate(){
        $model = new TrnKartuProsesMaklon(['date'=>date('Y-m-d')]);

        $modelsItem = [new TrnKartuProsesMaklonItem()];

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $modelsItem = Model::createMultiple(TrnKartuProsesMaklonItem::classname());
            Model::loadMultiple($modelsItem, Yii::$app->request->post());

            //BaseVarDumper::dump([$model], 10, true);Yii::$app->end();

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;

            if ($valid) {
                $model->mo_id = $model->wo->mo_id;
                $model->sc_greige_id = $model->mo->sc_greige_id;
                $model->sc_id = $model->scGreige->sc_id;

                $transaction = Yii::$app->db->beginTransaction();
                try{
                    if(!$model->save(false)){
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('error', 'Gagal memproses, coba lagi. (1)');
                        return $this->render('create', [
                            'model' => $model,
                            'modelsItem' => (empty($modelsItem)) ? [new TrnKartuProsesMaklonItem] : $modelsItem
                        ]);
                    }

                    foreach ($modelsItem as $modelItem) {
                        $modelItem->kartu_process_id = $model->id;
                        $modelItem->wo_id = $model->wo_id;
                        $modelItem->mo_id = $model->mo_id;
                        $modelItem->sc_greige_id = $model->sc_greige_id;
                        $modelItem->sc_id = $model->sc_id;

                        if(!$modelItem->save(false)){
                            $transaction->rollBack();
                            Yii::$app->session->setFlash('error', 'Gagal memproses, coba lagi. (2)');
                            return $this->render('create', [
                                'model' => $model,
                                'modelsItem' => (empty($modelsItem)) ? [new TrnKartuProsesMaklonItem] : $modelsItem
                            ]);
                        }
                    }

                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                }catch (\Throwable $t){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $t->getMessage().'---');
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'modelsItem' => (empty($modelsItem)) ? [new TrnKartuProsesMaklonItem] : $modelsItem
        ]);
    }

    /**
     * Updates an existing Customer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Kartu proses bukan draft, tidak bisa dirubah.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $modelsItem = $model->trnKartuProsesMaklonItems;

        if ($model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsItem, 'id', 'id');
            $modelsItem = Model::createMultiple(TrnKartuProsesMaklonItem::classname(), $modelsItem);
            Model::loadMultiple($modelsItem, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsItem, 'id', 'id')));

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            TrnKartuProsesMaklonItem::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsItem as $modelItem) {
                            $modelItem->kartu_process_id = $model->id;
                            $modelItem->wo_id = $model->wo_id;
                            $modelItem->mo_id = $model->mo_id;
                            $modelItem->sc_greige_id = $model->sc_greige_id;
                            $modelItem->sc_id = $model->sc_id;
                            if (! ($flag = $modelItem->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                } catch (\Throwable $t) {
                    $transaction->rollBack();
                    throw $t;
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'modelsItem' => (empty($modelsItem)) ? [new TrnKartuProsesMaklonItem] : $modelsItem
        ]);
    }

    /**
     * Deletes an existing TrnKartuProsesMaklon model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Kartu proses bukan draft, tidak bisa dihapus.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            TrnKartuProsesMaklonItem::deleteAll(['kartu_process_id'=>$model->id]);
            $model->delete();
            $transaction->commit();

        }catch (\Throwable $e){
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id'=>$model->id]);
        }

        Yii::$app->session->setFlash('success', 'Kartu Proses berhasil dihapus.');
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing KartuProsesPrinting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPosting($id)
    {
        $model = $this->findModel($id);

        if ($model->status != $model::STATUS_DRAFT) {
            Yii::$app->session->setFlash('error', 'Kartu proses bukan draft, tidak bisa diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if(!$model->getTrnKartuProsesMaklonItems()->count('id') > 0){
            Yii::$app->session->setFlash('error', 'Item belum diinput, tidak bisa diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $wo = $model->wo;
        $mo = $wo->mo;
        $greige = $wo->greige;

        $totalItemQty = $model->getTrnKartuProsesMaklonItems()->sum('qty');
        $totalItemQty = $totalItemQty > 0 ? $totalItemQty : 0;

        switch ($model->unit){
            case MstGreigeGroup::UNIT_YARD:
                $woColorTotal = $wo->getColorQtyBatchToYard();
                break;
            case MstGreigeGroup::UNIT_METER:
                $woColorTotal = $wo->getColorQtyBatchToMeter();
                break;
            default:
                $woColorTotal = $wo->getColorQtyBatchToUnit();
        }

        if($totalItemQty > $woColorTotal){
            Yii::$app->session->setFlash('error', 'Kuantiti melebihi color pada WO.');
            return $this->redirect(['view', 'id'=>$model->id]);
        }

        $model->setNomor();

        //status bukan posted, tapi Bypass langsung disetujui karena tidak ada fitur persetujuan
        $model->status = $model::STATUS_APPROVED;
        $model->approved_at = time();
        $model->approved_by = Yii::$app->user->id;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(!$model->save(false, ['status', 'no_urut', 'no', 'approved_at', 'approved_by'])){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal, coba lagi.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            //Kurangi stock greige--------------------------------------------------------------------------------------------
            switch ($mo->jenis_gudang){
                case TrnStockGreige::JG_WIP:
                    $stockAttr = 'stock_wip';
                    break;
                case TrnStockGreige::JG_PFP:
                    $stockAttr = 'stock_pfp';
                    break;
                case TrnStockGreige::JG_EX_FINISH:
                    $stockAttr = 'stock_ef';
                    break;
                case TrnStockGreige::JG_FRESH:
                    if($wo->jenis_order === TrnSc::JENIS_ORDER_FRESH_ORDER){
                        // jika jenis order wo === fresh dan jenis gudang mo == jg_fresh
                        //ambil nilai original qty per batch greige untuk dasar pemotongan stok
                        $qtyPerBatch = $greige->group->qty_per_batch;
                        $selisih = $totalItemQty - $qtyPerBatch;
                        $update = [
                            'booked_wo' => new \yii\db\Expression('booked_wo' . ' - ' . $totalItemQty),
                            'booked' => new \yii\db\Expression('booked' . ' + ' . $totalItemQty),
                        ];
                        if($selisih < 0){
                            $update = [
                                'booked_wo' => new \yii\db\Expression('booked_wo' . ' - ' . $qtyPerBatch),
                                'booked' => new \yii\db\Expression('booked' . ' + ' . $totalItemQty),
                                'available' => new \yii\db\Expression('available' . ' + ' . abs($selisih)),
                            ];
                        }elseif ($selisih > 0){
                            $update = [
                                'booked_wo' => new \yii\db\Expression('booked_wo' . ' - ' . $qtyPerBatch),
                                'booked' => new \yii\db\Expression('booked' . ' + ' . $totalItemQty),
                                'available' => new \yii\db\Expression('available' . ' - ' . $selisih),
                            ];
                        }
                        Yii::$app->db->createCommand()->update(
                            MstGreige::tableName(),
                            $update,
                            ['id'=>$greige->id]
                        )->execute();

                        $transaction->commit();
                        Yii::$app->session->setFlash('success', 'Kartu proses berhasil diposting.');
                        return $this->redirect(['view', 'id' => $model->id]);
                    }

                    $stockAttr = 'stock';
                    break;
                default:
                    $stockAttr = 'stock';
            }

            Yii::$app->db->createCommand()->update(
                MstGreige::tableName(),
                [
                    $stockAttr => new \yii\db\Expression($stockAttr . ' - ' . $totalItemQty),
                ],
                ['id'=>$greige->id]
            )->execute();
            //Kurangi stock greige--------------------------------------------------------------------------------------------

            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Kartu proses berhasil diposting.');
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
    public function actionDisplayPengantar($id){
        return $this->renderPartial('display-pengantar', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionPrintPengantar($id){
        $model = $this->findModel($id);
        $sc = $model->sc;

        $content = $this->renderPartial('print-pengantar', ['model' => $model]);

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_BLANK,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
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
                'SetTitle'=>'SURAT PENGANTAR - '.$model->id,
            ]
        ]);

        if($sc->status == $sc::STATUS_DRAFT){
            $pdf->methods['SetHeader'] = 'SURAT PENGANTAR | '.$model->id.' | DRAFT';
        }else{
            if($sc->status == $sc::STATUS_APPROVED){
                $pdf->methods['SetHeader'] = 'SURAT PENGANTAR | '.$model->id.' | '.$model->no;
            }else $pdf->methods['SetHeader'] = 'SURAT PENGANTAR | '.$model->id.' | MENUNGGU PERSETUJUAN';
        }

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * Finds the TrnKartuProsesMaklon model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnKartuProsesMaklon the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnKartuProsesMaklon::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
