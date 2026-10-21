<?php

namespace backend\controllers;

use backend\components\Converter;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadi;
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
        $modelsItem = [new TrnReturBuyerItem(['scenario'=>TrnReturBuyerItem::SCENARIO_MULTI_WO])];

        if ($model->load(Yii::$app->request->post())) {
            // dibaca berurutan apa adanya, index baris dari dynamic form bisa tidak urut setelah baris ditambah/dihapus
            $modelsItem = [];
            foreach (array_values((array)Yii::$app->request->post((new TrnReturBuyerItem())->formName(), [])) as $itemData) {
                $modelItem = new TrnReturBuyerItem(['scenario' => TrnReturBuyerItem::SCENARIO_MULTI_WO]);
                $modelItem->load($itemData, '');
                $modelsItem[] = $modelItem;
            }

            // unit kosong -> mengikuti unit pengiriman masing-masing WO
            $unitKosong = empty($model->unit);

            // WO diisi per item, data header dipakai bersama oleh setiap dokumen retur yang terbentuk
            $headerAttributes = array_diff($model->activeAttributes(), ['wo_id']);

            // validate all models
            $valid = $model->validate($headerAttributes);
            $valid = Model::validateMultiple($modelsItem) && $valid;

            /* @var $returs array[] [TrnReturBuyer, TrnReturBuyerItem[]] per WO */
            $returs = [];
            if ($valid) {
                $itemsPerWo = [];
                foreach ($modelsItem as $modelItem) {
                    $itemsPerWo[$modelItem->wo_id][] = $modelItem;
                }

                $customerIds = [];
                foreach ($itemsPerWo as $woId => $items) {
                    $retur = new TrnReturBuyer($model->getAttributes($headerAttributes));
                    $retur->wo_id = $woId;
                    if ($unitKosong) {
                        $kirimUnits = array_keys(TrnReturBuyer::kirimQtyPerUnit($woId));
                        if (!empty($kirimUnits)) {
                            $retur->unit = $kirimUnits[0];
                        }
                    }
                    $retur->mo_id = $retur->wo->mo_id;
                    $retur->sc_greige_id = $retur->mo->sc_greige_id;
                    $retur->sc_id = $retur->scGreige->sc_id;
                    $retur->customer_id = $retur->sc->cust_id;
                    $customerIds[$retur->customer_id] = $retur->customer_id;

                    $valid = $retur->validateItems($items) && $valid;
                    $returs[] = [$retur, $items];
                }

                if (count($customerIds) > 1) {
                    $model->addError('wo_id', 'Semua WO dalam satu input retur harus dari customer yang sama.');
                    $valid = false;
                }
            }

            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try{
                    foreach ($returs as list($retur, $items)) {
                        if(!$retur->save(false)){
                            throw new HttpException(500, 'Gagal memproses, coba lagi. (1)');
                        }

                        foreach ($items as $modelItem) {
                            $modelItem->retur_buyer_id = $retur->id;
                            if(!$modelItem->save(false)){
                                throw new HttpException(500, 'Gagal memproses, coba lagi. (2)');
                            }
                        }
                    }

                    $transaction->commit();

                    if (count($returs) === 1) {
                        return $this->redirect(['view', 'id' => $returs[0][0]->id]);
                    }
                    Yii::$app->session->setFlash('success', count($returs).' dokumen retur berhasil dibuat (1 dokumen per WO) dengan nomor dokumen '.$model->no_document.'.');
                    return $this->redirect(['index', 'TrnReturBuyerSearch' => ['no_document' => $model->no_document]]);
                }catch (\Throwable $t){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $t->getMessage());
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'modelsItem' => (empty($modelsItem)) ? [new TrnReturBuyerItem(['scenario'=>TrnReturBuyerItem::SCENARIO_MULTI_WO])] : $modelsItem
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

            // agar validasi "roll sudah diretur" tidak bentrok dengan item milik dokumen ini sendiri
            foreach ($modelsItem as $modelItem) {
                $modelItem->retur_buyer_id = $model->id;
            }

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;
            $valid = $valid && $model->validateItems($modelsItem);

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

            // validateItems() juga memastikan total retur tidak melebihi total kirim WO
            $modelsItem = $model->trnReturBuyerItems;
            if(!Model::validateMultiple($modelsItem) || !$model->validateItems($modelsItem)){
                $errors = [];
                foreach ($modelsItem as $trnReturBuyerItem) {
                    $errors = array_merge($errors, $trnReturBuyerItem->getErrorSummary(false));
                }
                throw new ForbiddenHttpException('Item tidak valid: '.implode(' ', array_unique($errors)));
            }

            $model->setNomor();
            $model->status = $model::STATUS_POSTED;

            $transaction = Yii::$app->db->beginTransaction();
            try{
                if(! ($flag = $model->save(false, ['status', 'no_urut', 'no']))){
                    $transaction->rollBack();
                    throw new HttpException(500, 'Posting gagal, coba lagi. (1).');
                }

                /**
                 * Semua barang retur masuk lagi ke gudang jadi sebagai stok baru (tanpa keputusan QC).
                 * Roll asal tetap berstatus OUT agar riwayat surat jalan tidak berubah,
                 * keterkaitannya disimpan di retur_from_id (boleh kosong jika roll asal tidak diketahui).
                 * Proses lanjutan (Re Dyeing / Repair) dilakukan dari halaman view setelah posting.
                 */
                foreach ($modelsItem as $trnReturBuyerItem) {
                    $stockAsal = $trnReturBuyerItem->stock;
                    $noSj = $trnReturBuyerItem->kirimBuyer !== null ? $trnReturBuyerItem->kirimBuyer->header->no : 'tidak diketahui';
                    $modelGudangJadi = new TrnGudangJadi([
                        'jenis_gudang' => $model->jenis_gudang,
                        'wo_id' => $model->wo_id,
                        'source' => TrnGudangJadi::SOURCE_RETUR,
                        'source_ref' => $model->no,
                        'unit' => $model->unit,
                        'qty' => $trnReturBuyerItem->qty,
                        'date' => date('Y-m-d'),
                        'status' => TrnGudangJadi::STATUS_STOCK,
                        'grade' => $trnReturBuyerItem->grade,
                        'color' => $stockAsal !== null ? $stockAsal->color : null,
                        'retur_from_id' => $trnReturBuyerItem->stock_id,
                        'note' => 'Dari retur buyer: '.$model->no.($stockAsal === null ? ' (roll asal tidak diketahui, SJ: '.$noSj.')' : ''),
                    ]);
                    if(! ($flag = $modelGudangJadi->save(false))){
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal menyimpan stock gudang jadi, coba lagi.');
                    }

                    $trnReturBuyerItem->gudang_jadi_id = $modelGudangJadi->id;
                    if(! ($flag = $trnReturBuyerItem->save(false, ['gudang_jadi_id']))){
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal menyimpan item retur, coba lagi.');
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

            if ($model->status != $model::STATUS_POSTED) {
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
                    // barang dikembalikan ke greige untuk diredyeing, stok hasil retur di gudang jadi dikeluarkan
                    $stockRetur = $trnReturBuyerItem->gudangJadi;
                    if($stockRetur !== null){
                        if($stockRetur->status != TrnGudangJadi::STATUS_STOCK){
                            $transaction->rollBack();
                            throw new ForbiddenHttpException('Stok hasil retur (ID '.$stockRetur->id.') sudah tidak berstatus stock, tidak bisa diredyeing.');
                        }
                        $stockRetur->status = TrnGudangJadi::STATUS_OUT;
                        if(! ($flag = $stockRetur->save(false, ['status']))){
                            $transaction->rollBack();
                            throw new HttpException(500, 'Gagal mengeluarkan stok hasil retur, coba lagi.');
                        }
                    }

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

            if ($model->status != $model::STATUS_POSTED) {
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
