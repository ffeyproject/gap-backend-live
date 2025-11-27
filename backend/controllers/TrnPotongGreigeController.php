<?php

namespace backend\controllers;

use common\models\ar\TrnPotongGreigeItem;
use common\models\ar\TrnStockGreige;
use common\models\Model;
use Yii;
use common\models\ar\TrnPotongGreige;
use common\models\ar\TrnPotongGreigeSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseVarDumper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnPotongGreigeController implements the CRUD actions for TrnPotongGreige model.
 */
class TrnPotongGreigeController extends Controller
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
     * Lists all TrnPotongGreige models.
     * @return mixed
     */
    public function actionIndex()
    {
        $info = 'Fitur untuk memotong greige menjadi beberapa bagian, biasanya dilakukan untuk kebutuhan khusus. Misalnya menutupi kekurangan greige dengan panjang/berat tertentu pada kartu proses atau butuh sedikit greige untuk sample.';
        Yii::$app->session->setFlash('info', $info);

        $searchModel = new TrnPotongGreigeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['trn_stock_greige.jenis_gudang' => TrnStockGreige::JG_FRESH]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnPotongGreige model.
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
     * Creates a new TrnPotongGreige model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnPotongGreige(['date'=>date('Y-m-d')]);

        /* @var $modelsItem TrnPotongGreigeItem[]*/
        $modelsItem = [new TrnPotongGreigeItem()];

        if ($model->load(Yii::$app->request->post())) {
            $modelsItem = Model::createMultiple(TrnPotongGreigeItem::classname());
            Model::loadMultiple($modelsItem, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;

            if ($valid) {
                //Total item potongan tidak boleh lebih dari qty kain yang dipotong
                $itemsTotalQty = array_sum(ArrayHelper::getColumn(ArrayHelper::toArray($modelsItem), 'panjang_m'));

                /*BaseVarDumper::dump([
                    '$model->stockGreige->panjang_m'=>$model->stockGreige->panjang_m,
                    '$itemsTotalQty'=>$itemsTotalQty
                ], 10, true);Yii::$app->end();*/

                if($itemsTotalQty > $model->stockGreige->panjang_m){
                    foreach ($modelsItem as $item) {
                        $item->addError('panjang_m', 'Jumlah pemotongan melebihi qty kain yang dipotong.');
                    }

                    return $this->render('create', [
                        'model' => $model,
                        'modelsItem' => (empty($modelsItem)) ? [new TrnPotongGreigeItem] : $modelsItem
                    ]);
                }
                //Total item potongan tidak boleh lebih dari qty kain yang dipotong

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        foreach ($modelsItem as $modelItem) {
                            /* @var $modelItem TrnPotongGreigeItem*/
                            $modelItem->potong_greige_id = $model->id;
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
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'modelsItem' => (empty($modelsItem)) ? [new TrnPotongGreigeItem] : $modelsItem
        ]);
    }

    /**
     * Updates an existing TrnPotongGreige model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if($model->status != $model::STATUS_DRAFT){
            throw new ForbiddenHttpException('Status tidak valid. Tidak bisa dirubah.');
        }

        $modelsItem = $model->trnPotongGreigeItems;

        if ($model->load(Yii::$app->request->post())) {
            $oldIDs = ArrayHelper::map($modelsItem, 'id', 'id');
            $modelsItem = Model::createMultiple(TrnPotongGreigeItem::classname(), $modelsItem);
            Model::loadMultiple($modelsItem, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsItem, 'id', 'id')));

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;

            if ($valid) {
                //Total item potongan tidak boleh lebih dari qty kain yang dipotong
                $itemsTotalQty = array_sum(ArrayHelper::getColumn(ArrayHelper::toArray($modelsItem), 'panjang_m'));
                if($itemsTotalQty > $model->stockGreige->panjang_m){

                    foreach ($modelsItem as $item) {
                        $item->addError('panjang_m', 'Jumlah pemotongan melebihi qty kain yang dipotong.');
                    }

                    return $this->render('create', [
                        'model' => $model,
                        'modelsItem' => (empty($modelsItem)) ? [new TrnPotongGreigeItem] : $modelsItem
                    ]);
                }
                //Total item potongan tidak boleh lebih dari qty kain yang dipotong

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            TrnPotongGreigeItem::deleteAll(['id' => $deletedIDs]);
                        }

                        foreach ($modelsItem as $modelItem) {
                            /* @var $modelItem TrnPotongGreigeItem*/
                            $modelItem->potong_greige_id = $model->id;
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
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'modelsItem' => (empty($modelsItem)) ? [new TrnPotongGreigeItem] : $modelsItem
        ]);
    }

    /**
     * Deletes an existing TrnPotongGreige model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model->status != $model::STATUS_DRAFT){
            throw new ForbiddenHttpException('Status tidak valid. Tidak bisa dihapus.');
        }

        TrnPotongGreigeItem::deleteAll(['potong_greige_id'=>$model->id]);

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    // public function actionPosting($id)
    // {
    //     $model = $this->findModel($id);
    //     if($model->status != $model::STATUS_DRAFT){
    //         throw new ForbiddenHttpException('Status tidak valid. Tidak bisa diposting.');
    //     }

    //     //bypass langsung approve
    //     $model->posted_at = time();
    //     $model->status = $model::STATUS_APPROVED;//$model::STATUS_POSTED;
    //     $model->approved_at = time();
    //     $model->approved_by = $model->approved_by === null ? Yii::$app->user->id : $model->approved_by;

    //     $stockGreige = $model->stockGreige;
    //     if($stockGreige->status != $stockGreige::STATUS_VALID){
    //         throw new ForbiddenHttpException('Status tidak valid. Tidak bisa diposting.');
    //     }

    //     $transaction = Yii::$app->db->beginTransaction();
    //     try {
    //         $model->setNomor();
    //         if(!($flag = $model->save(false, ['posted_at', 'status', 'approved_at', 'approved_by', 'no_urut', 'no']))){
    //             Yii::$app->session->setFlash('error', 'gagal menyimpan, coba lagi.');
    //             $transaction->rollBack();
    //             return $this->redirect(['view', 'id' => $model->id]);
    //         }

    //         $stockGreige->status = $stockGreige::STATUS_DIPOTONG;
    //         if(!($flag = $stockGreige->save(false, ['status']))){
    //             Yii::$app->session->setFlash('error', 'gagal merubah status kain yang akan dipotong, coba lagi.');
    //             $transaction->rollBack();
    //             return $this->redirect(['view', 'id' => $model->id]);
    //         }

    //         $itemsTotal = 0;
    //         foreach ($model->trnPotongGreigeItems as $trnPotongGreigeItem) {
    //             $modelNewStock = new TrnStockGreige();
    //             $modelNewStock->load([$modelNewStock->formName()=>$stockGreige->attributes]);
    //             $modelNewStock->panjang_m = $trnPotongGreigeItem->panjang_m;
    //             $modelNewStock->is_pemotongan = true;
    //             //$modelNewStock->note = 'Pemotongan ID: '.$model->id.', ItemID: '.$trnPotongGreigeItem->id; //note tidak perlu di override
    //             $modelNewStock->status = $modelNewStock::STATUS_VALID;

    //             if(!($flag = $modelNewStock->save(false))){
    //                 $transaction->rollBack();
    //                 Yii::$app->session->setFlash('error', 'gagal menyimpan roll baru ke stock gudang, coba lagi.');
    //                 return $this->redirect(['view', 'id' => $model->id]);
    //             }

    //             $trnPotongGreigeItem->stock_greige_id = $modelNewStock->id;
    //             if(!($flag = $trnPotongGreigeItem->save(false, ['stock_greige_id']))){
    //                 $transaction->rollBack();
    //                 Yii::$app->session->setFlash('error', 'gagal menyimpan item pemotongan, coba lagi.');
    //                 return $this->redirect(['view', 'id' => $model->id]);
    //             }

    //             $itemsTotal += $trnPotongGreigeItem->panjang_m;
    //         }

    //         // jika masih ada sisa
    //         // tambahan 1 roll lagi yang merupakan sisa pemotongan, data stock yang dipotong tidak dirubah sama sekali kecuali status nya, sisa pemotongan nya dimasukan ke data baru.
    //         $sisa = $stockGreige->panjang_m - $itemsTotal;
    //         if($sisa > 0){
    //             $modelNewStock = new TrnStockGreige();
    //             $modelNewStock->load([$modelNewStock->formName()=>$stockGreige->attributes]);
    //             $modelNewStock->panjang_m = $sisa;
    //             $modelNewStock->note = 'Roll Tambahan Sisa Pemotongan ID: '.$model->id;
    //             $modelNewStock->status = $modelNewStock::STATUS_VALID;
    //             $modelNewStock->is_pemotongan = true;
    //             if(!($flag = $modelNewStock->save(false))){
    //                 $transaction->rollBack();
    //                 Yii::$app->session->setFlash('error', 'gagal menyimpan roll baru ke stock gudang, coba lagi.');
    //                 return $this->redirect(['view', 'id' => $model->id]);
    //             }
    //         }

    //         if ($flag){
    //             $transaction->commit();
    //             Yii::$app->session->setFlash('success', 'Posting berhasil.');
    //             return $this->redirect(['view', 'id' => $model->id]);
    //         }
    //     }catch (\Throwable $e) {
    //         $transaction->rollBack();
    //         Yii::$app->session->setFlash('error', $e->getMessage());
    //         return $this->redirect(['view', 'id' => $model->id]);
    //     }

    //     return $this->redirect(['view', 'id' => $model->id]);
    // }

    public function actionPosting($id)
    {
        $model = $this->findModel($id);
        if ($model->status != $model::STATUS_DRAFT) {
            throw new ForbiddenHttpException('Status tidak valid. Tidak bisa diposting.');
        }

        // bypass langsung approve
        $model->posted_at = time();
        $model->status = $model::STATUS_APPROVED;
        $model->approved_at = time();
        $model->approved_by = $model->approved_by === null ? Yii::$app->user->id : $model->approved_by;

        $stockGreige = $model->stockGreige;
        if ($stockGreige->status != $stockGreige::STATUS_VALID) {
            throw new ForbiddenHttpException('Status tidak valid. Tidak bisa diposting.');
        }

        // cek apakah ada opname
        $opname = \common\models\ar\TrnStockGreigeOpname::find()
            ->where(['stock_greige_id' => $stockGreige->id])
            ->one();

        $transaction = Yii::$app->db->beginTransaction();
        try {

            /** =============================
             *  SIMPAN HEADER (POTONG GREIGE)
             *  ============================= */
            $model->setNomor();
            if (!($flag = $model->save(false, [
                'posted_at','status','approved_at','approved_by','no_urut','no'
            ]))) {
                Yii::$app->session->setFlash('error','gagal menyimpan header.');
                $transaction->rollBack();
                return $this->redirect(['view','id'=>$model->id]);
            }

            /** =============================
             *  UPDATE STATUS STOCK GREIGE
             *  ============================= */
            $stockGreige->status = $stockGreige::STATUS_DIPOTONG;
            if (!$stockGreige->save(false, ['status'])) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error','Gagal update status stock greige.');
                return $this->redirect(['view','id'=>$model->id]);
            }

            /** =============================
             *  UPDATE STATUS OPNAME JIKA ADA
             *  ============================= */
            if ($opname) {
                $opname->status = \common\models\ar\TrnStockGreigeOpname::STATUS_DIPOTONG;
                if (!$opname->save(false, ['status'])) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error','Gagal update status opname.');
                    return $this->redirect(['view','id'=>$model->id]);
                }
            }

            /** =============================
             *  PROSES ITEM PEMOTONGAN
             *  ============================= */
            $itemsTotal = 0;

            foreach ($model->trnPotongGreigeItems as $item) {

                /** STOCK GREIGE BARU */
            $modelNewStock = new TrnStockGreige();

                // clone manual agar ID tidak terbawa
                $modelNewStock->greige_id        = $stockGreige->greige_id;
                $modelNewStock->greige_group_id  = $stockGreige->greige_group_id;
                $modelNewStock->asal_greige      = $stockGreige->asal_greige;
                $modelNewStock->no_lapak         = $stockGreige->no_lapak;
                $modelNewStock->grade            = $stockGreige->grade;
                $modelNewStock->lot_lusi         = $stockGreige->lot_lusi;
                $modelNewStock->lot_pakan        = $stockGreige->lot_pakan;
                $modelNewStock->no_set_lusi      = $stockGreige->no_set_lusi;

                $modelNewStock->panjang_m        = $item->panjang_m;

                $modelNewStock->status_tsd       = $stockGreige->status_tsd;
                $modelNewStock->no_document      = $stockGreige->no_document;
                $modelNewStock->pengirim         = $stockGreige->pengirim;
                $modelNewStock->mengetahui       = $stockGreige->mengetahui;

                $modelNewStock->note             = $stockGreige->note;
                $modelNewStock->date             = $stockGreige->date;
                $modelNewStock->jenis_gudang     = $stockGreige->jenis_gudang;

                $modelNewStock->status           = TrnStockGreige::STATUS_VALID;
                $modelNewStock->is_pemotongan    = true;

                if (!$modelNewStock->save(false)) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error','Gagal membuat stock greige baru.');
                    return $this->redirect(['view','id'=>$model->id]);
                }

                /** UPDATE ITEM */
                $item->stock_greige_id = $modelNewStock->id;
                if (!$item->save(false,['stock_greige_id'])) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error','Gagal update item pemotongan.');
                    return $this->redirect(['view','id'=>$model->id]);
                }

                /** TAMBAH OPNAME BARU (ITEM POTONG) */
                if ($opname) {
                    $newOpname = new \common\models\ar\TrnStockGreigeOpname();

                    // clone manual (ID tidak ikut)
                    $newOpname->stock_greige_id = $modelNewStock->id;
                    $newOpname->greige_id = $opname->greige_id;
                    $newOpname->greige_group_id = $opname->greige_group_id;
                    $newOpname->asal_greige = $opname->asal_greige; // sesuai permintaan

                    $newOpname->no_lapak = $opname->no_lapak;
                    $newOpname->grade = $opname->grade;
                    $newOpname->lot_lusi = $opname->lot_lusi;
                    $newOpname->lot_pakan = $opname->lot_pakan;
                    $newOpname->no_set_lusi = $opname->no_set_lusi;

                    $newOpname->panjang_m = $item->panjang_m;
                    $newOpname->status_tsd = $opname->status_tsd;
                    $newOpname->no_document = $opname->no_document;
                    $newOpname->pengirim = $opname->pengirim;
                    $newOpname->mengetahui = $opname->mengetahui;

                    $newOpname->note = 'Pemotongan dari Opname ID: '.$opname->id;
                    $newOpname->status = \common\models\ar\TrnStockGreigeOpname::STATUS_VALID;
                    $newOpname->date = date('Y-m-d');
                    $newOpname->jenis_gudang = $opname->jenis_gudang;

                    if (!$newOpname->save(false)) {
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('error','Gagal membuat opname potongan.');
                        return $this->redirect(['view','id'=>$model->id]);
                    }
                }

                $itemsTotal += $item->panjang_m;
            }

            /** =============================
             *  SISA PEMOTONGAN
             *  ============================= */
            $sisa = $stockGreige->panjang_m - $itemsTotal;

           $sisa = $stockGreige->panjang_m - $itemsTotal;

            if ($sisa > 0) {

                $modelNewStock = new TrnStockGreige();

                // clone manual agar ID tidak terbawa
                $modelNewStock->greige_id        = $stockGreige->greige_id;
                $modelNewStock->greige_group_id  = $stockGreige->greige_group_id;
                $modelNewStock->asal_greige      = $stockGreige->asal_greige;
                $modelNewStock->no_lapak         = $stockGreige->no_lapak;
                $modelNewStock->grade            = $stockGreige->grade;
                $modelNewStock->lot_lusi         = $stockGreige->lot_lusi;
                $modelNewStock->lot_pakan        = $stockGreige->lot_pakan;
                $modelNewStock->no_set_lusi      = $stockGreige->no_set_lusi;

                // PERBAIKAN DI SINI
                $modelNewStock->panjang_m        = $sisa;

                $modelNewStock->status_tsd       = $stockGreige->status_tsd;
                $modelNewStock->no_document      = $stockGreige->no_document;
                $modelNewStock->pengirim         = $stockGreige->pengirim;
                $modelNewStock->mengetahui       = $stockGreige->mengetahui;

                $modelNewStock->note             = $stockGreige->note;
                $modelNewStock->date             = $stockGreige->date;
                $modelNewStock->jenis_gudang     = $stockGreige->jenis_gudang;

                $modelNewStock->status           = TrnStockGreige::STATUS_VALID;
                $modelNewStock->is_pemotongan    = true;

                if (!$modelNewStock->save(false)) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error','Gagal membuat roll sisa.');
                    return $this->redirect(['view','id'=>$model->id]);
                }

                /** BUAT OPNAME UNTUK SISA */
                if ($opname) {
                    $newOpnameSisa = new \common\models\ar\TrnStockGreigeOpname();

                    $newOpnameSisa->stock_greige_id = $modelNewStock->id;
                    $newOpnameSisa->greige_id = $opname->greige_id;
                    $newOpnameSisa->greige_group_id = $opname->greige_group_id;
                    $newOpnameSisa->asal_greige = $opname->asal_greige; // sesuai permintaan

                    $newOpnameSisa->no_lapak = $opname->no_lapak;
                    $newOpnameSisa->grade = $opname->grade;
                    $newOpnameSisa->lot_lusi = $opname->lot_lusi;
                    $newOpnameSisa->lot_pakan = $opname->lot_pakan;
                    $newOpnameSisa->no_set_lusi = $opname->no_set_lusi;

                    $newOpnameSisa->panjang_m = $sisa;
                    $newOpnameSisa->status_tsd = $opname->status_tsd;
                    $newOpnameSisa->no_document = $opname->no_document;
                    $newOpnameSisa->pengirim = $opname->pengirim;
                    $newOpnameSisa->mengetahui = $opname->mengetahui;

                    $newOpnameSisa->note = 'Sisa pemotongan dari opname ID: '.$opname->id;
                    $newOpnameSisa->status = \common\models\ar\TrnStockGreigeOpname::STATUS_VALID;
                    $newOpnameSisa->date = date('Y-m-d');
                    $newOpnameSisa->jenis_gudang = $opname->jenis_gudang;

                    if (!$newOpnameSisa->save(false)) {
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('error','Gagal membuat opname sisa.');
                        return $this->redirect(['view','id'=>$model->id]);
                    }
                }
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success','Posting berhasil.');
            return $this->redirect(['view','id'=>$model->id]);

        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error',$e->getMessage());
            return $this->redirect(['view','id'=>$model->id]);
        }
    }

    /**
     * Finds the TrnPotongGreige model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnPotongGreige the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnPotongGreige::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}