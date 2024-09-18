<?php

namespace backend\controllers;

use backend\components\Converter;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnKirimBuyerItem;
use common\models\ar\TrnReturBuyerItem;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnStockGreige;
use common\models\Model;
use Yii;
use common\models\ar\TrnReturBuyer;
use common\models\ar\TrnReturBuyerSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseVarDumper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TrnReturBuyerController implements the CRUD actions for TrnReturBuyer model.
 */
class TrnReturBuyerController extends Controller
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
     * Lists all TrnReturBuyer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnReturBuyerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnReturBuyer model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $hints = [];
        foreach (Yii::$app->params['hints']['trn-retur-buyer/view'] as $hint) {
            $hints[] = '<li>'.$hint.'</li>';
        }
        Yii::$app->session->setFlash('info', '<ul>'.implode('', $hints).'</ul>');

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TrnReturBuyer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnReturBuyer(['date'=>date('Y-m-d')]);

        /* @var $modelsItem TrnReturBuyerItem[]*/
        $modelsItem = [new TrnReturBuyerItem()];

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $modelsItem = Model::createMultiple(TrnReturBuyerItem::classname());
            Model::loadMultiple($modelsItem, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;

            if ($valid) {
                $model->mo_id = $model->wo->mo_id;
                $model->sc_greige_id = $model->mo->sc_greige_id;
                $model->sc_id = $model->scGreige->sc_id;
                $model->customer_id = $model->sc->cust_id;

                $transaction = Yii::$app->db->beginTransaction();
                try{
                    if(!$flag = $model->save(false)){
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('error', 'Gagal memproses, coba lagi. (1)');
                        return $this->render('create', [
                            'model' => $model,
                            'modelsItem' => (empty($modelsItem)) ? [new TrnReturBuyerItem] : $modelsItem
                        ]);
                    }

                    foreach ($modelsItem as $modelItem) {
                        $modelItem->retur_buyer_id = $model->id;

                        if(!$flag = $modelItem->save(false)){
                            $transaction->rollBack();
                            Yii::$app->session->setFlash('error', 'Gagal memproses, coba lagi. (2)');
                            return $this->render('create', [
                                'model' => $model,
                                'modelsItem' => (empty($modelsItem)) ? [new TrnReturBuyerItem] : $modelsItem
                            ]);
                        }
                    }

                    if($flag){
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                }catch (\Throwable $t){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $t->getMessage().'---');
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'modelsItem' => (empty($modelsItem)) ? [new TrnReturBuyerItem] : $modelsItem
        ]);
    }

    /**
     * Updates an existing TrnReturBuyer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status bukan draft, tidak bisa dirubah.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        /* @var $modelsItem TrnReturBuyerItem[]*/
        $modelsItem = $model->trnReturBuyerItems;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $oldIDs = ArrayHelper::map($modelsItem, 'id', 'id');
            $modelsItem = Model::createMultiple(TrnReturBuyerItem::classname(), $modelsItem);
            Model::loadMultiple($modelsItem, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsItem, 'id', 'id')));

            /*BaseVarDumper::dump([
                '$oldIDs'=>$oldIDs,
                '$deletedIDs'=>$deletedIDs,
                '$modelsItem'=>ArrayHelper::toArray($modelsItem),
                'post'=>Yii::$app->request->post()
            ], 10, true);Yii::$app->end();*/

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;

            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            TrnReturBuyerItem::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsItem as $modelItem) {
                            $modelItem->retur_buyer_id = $model->id;
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
            'modelsItem' => (empty($modelsItem)) ? [new TrnReturBuyerItem] : $modelsItem
        ]);
    }

    /**
     * Deletes an existing TrnKartuProsesMaklon model.
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
            Yii::$app->session->setFlash('error', 'Status bukan draft, tidak bisa dihapus.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            TrnReturBuyerItem::deleteAll(['retur_buyer_id'=>$model->id]);
            $model->delete();
            $transaction->commit();

        }catch (\Throwable $e){
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id'=>$model->id]);
        }

        Yii::$app->session->setFlash('success', 'Berhasil dihapus.');
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
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if ($model->status != $model::STATUS_DRAFT) {
                throw new ForbiddenHttpException('Status bukan draft, tidak bisa diposting.');
            }

            if(!$model->getTrnReturBuyerItems()->count('id') > 0){
                throw new ForbiddenHttpException('Item belum diinput, tidak bisa diposting.');
            }

            $qc = Yii::$app->request->post('formData');
            if(!in_array($qc, array_keys($model::keputusanQcOptions()))){
                throw new ForbiddenHttpException('Pilihan keputusan QC tidak valid, tidak bisa diposting.');
            }

            $woSudahKirim = TrnKirimBuyerItem::find()->joinWith('kirimBuyer')->where(['trn_kirim_buyer.wo_id'=>$model->wo_id])->sum('qty');
            $woSudahKirim = $woSudahKirim > 0 ? $woSudahKirim : 0;
            $woDiretur = $model->getTrnReturBuyerItems()->sum('qty');
            $woDiretur = $woDiretur > 0 ? $woDiretur : 0;
            if($woSudahKirim < $woDiretur){
                throw new HttpException(500, 'Item melebihi jumlah pengiriman, tidak bisa diposting.');
            }

            $model->keputusan_qc = $qc;
            $model->setNomor();
            $model->status = $model::STATUS_POSTED;

            $transaction = Yii::$app->db->beginTransaction();
            try{
                if(! ($flag = $model->save(false, ['status', 'no_urut', 'no', 'keputusan_qc']))){
                    $transaction->rollBack();
                    throw new HttpException(500, 'Posting gagal, coba lagi. (1).');
                }

                foreach ($model->trnReturBuyerItems as $trnReturBuyerItem) {
                    $greigeUnit = $model->scGreige->greigeGroup->unit;
                    if($greigeUnit == $model->unit){
                        $qty = $trnReturBuyerItem->qty;
                    }else{
                        switch ($greigeUnit){
                            case MstGreigeGroup::UNIT_YARD:
                                if($model->unit == MstGreigeGroup::UNIT_METER){
                                    $qty = Converter::yardToMeter($trnReturBuyerItem->qty);
                                }else{
                                    $transaction->rollBack();
                                    throw new NotAcceptableHttpException('Posting gagal, unit tidak bisa dikonversi. Periksa kembali kecocokan unit dengan unit pada greige group. (1)');
                                }
                                break;
                            case MstGreigeGroup::UNIT_METER:
                                if($model->unit == MstGreigeGroup::UNIT_YARD){
                                    $qty = Converter::meterToYard($trnReturBuyerItem->qty);
                                }else{
                                    $transaction->rollBack();
                                    throw new NotAcceptableHttpException('Posting gagal, unit tidak bisa dikonversi. Periksa kembali kecocokan unit dengan unit pada greige group. (2)');
                                }
                                break;
                            default:
                                $transaction->rollBack();
                                throw new NotAcceptableHttpException('Posting gagal, unit tidak bisa dikonversi. Periksa kembali kecocokan unit dengan unit pada greige group. (3)');
                        }
                    }

                    /**
                     * Jika keputusan qc tidak bisa diperbaiki, maka langsung masuk ke stock greige
                     * jika masih bisa diperbaiki, ada dua kemungkinan proses lanjutan, di repair atau redyeing
                    */
                    if($model->keputusan_qc == TrnReturBuyer::QC_REJECT){
                        $modelStock = new TrnStockGreige([
                            'greige_group_id' => $model->wo->greige->group_id,
                            'greige_id' => $model->wo->greige_id,
                            'asal_greige' => TrnStockGreige::ASAL_GREIGE_RETUR,
                            'no_lapak' => '-',
                            'grade' => $trnReturBuyerItem->grade,
                            'lot_lusi' => '-',
                            'lot_pakan' => '-',
                            'no_set_lusi' => '-',
                            'panjang_m' => $qty,
                            'status_tsd' => TrnStockGreige::STATUS_TSD_LAIN_LAIN,
                            'no_document' => $model->no,
                            'pengirim' => '-',
                            'mengetahui' => '-',
                            'note' => 'Dari retur buyer: '.$model->no,
                            'status' => TrnStockGreige::STATUS_VALID,
                            'date' => date('Y-m-d'),
                            'jenis_gudang' => TrnStockGreige::JG_EX_FINISH,
                            'nomor_wo' => $model->wo->no,
                            'keputusan_qc' => $model->keputusan_qc
                        ]);
                        if(! ($flag = $modelStock->save(false))){
                            $transaction->rollBack();
                            throw new HttpException(500, 'Gagal menyimpan stock, coba lagi.');
                        }

                        //menambah jumlah stock ex finish
                        $command = Yii::$app->db->createCommand("UPDATE mst_greige SET stock_ef = stock_ef + {$qty} WHERE id= {$modelStock->greige_id}");
                        if(!$flag = $command->execute() > 0){
                            $transaction->rollBack();
                            Yii::$app->session->setFlash('error', 'Gagal, coba lagi.');
                            return $this->redirect(['view', 'id' => $model->id]);
                        }
                    }
                }

                if($flag){
                    $transaction->commit();
                    return true;
                }
            }catch (\Throwable $t){
                $transaction->rollBack();
                throw $t;
            }
        }

        throw new MethodNotAllowedHttpException('Metode tidak diijinkan.');
    }

    /**
     * Deletes an existing KartuProsesPrinting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionReDyeing($id)
    {
        if(Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if ($model->status != $model::STATUS_POSTED && $model->keputusan_qc != $model::QC_REPAIR) {
                throw new ForbiddenHttpException('Status tidak valid, tidak bisa diproses.');
            }

            if ($model->scGreige->process != TrnScGreige::PROCESS_DYEING) {
                throw new ForbiddenHttpException('Jenis proses tidak valid, hanya untuk poses dyeing.');
            }

            $model->status = $model::STATUS_RE_DYEING;

            $transaction = Yii::$app->db->beginTransaction();
            try{
                if(! ($flag = $model->save(false, ['status']))){
                    $transaction->rollBack();
                    throw new HttpException(500, 'Posting gagal, coba lagi. (1).');
                }

                foreach ($model->trnReturBuyerItems as $trnReturBuyerItem) {
                    $greigeUnit = $model->scGreige->greigeGroup->unit;
                    if($greigeUnit == $model->unit){
                        $qty = $trnReturBuyerItem->qty;
                    }else{
                        switch ($greigeUnit){
                            case MstGreigeGroup::UNIT_YARD:
                                if($model->unit == MstGreigeGroup::UNIT_METER){
                                    $qty = Converter::yardToMeter($trnReturBuyerItem->qty);
                                }else{
                                    $transaction->rollBack();
                                    throw new NotAcceptableHttpException('Posting gagal, unit tidak bisa dikonversi. Periksa kembali kecocokan unit dengan unit pada greige group. (1)');
                                }
                                break;
                            case MstGreigeGroup::UNIT_METER:
                                if($model->unit == MstGreigeGroup::UNIT_YARD){
                                    $qty = Converter::meterToYard($trnReturBuyerItem->qty);
                                }else{
                                    $transaction->rollBack();
                                    throw new NotAcceptableHttpException('Posting gagal, unit tidak bisa dikonversi. Periksa kembali kecocokan unit dengan unit pada greige group. (2)');
                                }
                                break;
                            default:
                                $transaction->rollBack();
                                throw new NotAcceptableHttpException('Posting gagal, unit tidak bisa dikonversi. Periksa kembali kecocokan unit dengan unit pada greige group. (3)');
                        }
                    }

                    $modelStock = new TrnStockGreige([
                        'greige_group_id' => $model->wo->greige->group_id,
                        'greige_id' => $model->wo->greige_id,
                        'asal_greige' => TrnStockGreige::ASAL_GREIGE_LAIN_LAIN,
                        'no_lapak' => '-',
                        'grade' => $trnReturBuyerItem->grade,
                        'lot_lusi' => '-',
                        'lot_pakan' => '-',
                        'no_set_lusi' => '-',
                        'panjang_m' => $qty,
                        'status_tsd' => TrnStockGreige::STATUS_TSD_LAIN_LAIN,
                        'no_document' => $model->no,
                        'pengirim' => '-',
                        'mengetahui' => '-',
                        'note' => 'Dari retur buyer No.: '.$model->no.', untuk diredyeing',
                        'status' => TrnStockGreige::STATUS_VALID,
                        'date' => date('Y-m-d'),
                        'jenis_gudang' => $model->mo->jenis_gudang,
                        'nomor_wo' => $model->wo->no,
                        'keputusan_qc' => $model->keputusan_qc
                    ]);
                    if(! ($flag = $modelStock->save(false))){
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal menyimpan stock, coba lagi.');
                    }
                }

                if($flag){
                    $transaction->commit();
                    return true;
                }
            }catch (\Throwable $t){
                throw $t;
            }
            return true;
        }

        throw new MethodNotAllowedHttpException('Metode tidak diijinkan.');
    }

    /**
     * Deletes an existing KartuProsesPrinting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionRepair($id)
    {
        if(Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if ($model->status != $model::STATUS_POSTED && $model->keputusan_qc != $model::QC_REPAIR) {
                throw new ForbiddenHttpException('Status tidak valid, tidak bisa diproses.');
            }

            $model->status = $model::STATUS_REPAIR;
            $model->save(false, ['status']);
            return true;
        }

        throw new MethodNotAllowedHttpException('Metode tidak diijinkan.');
    }

    /**
     * Finds the TrnReturBuyer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnReturBuyer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnReturBuyer::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
