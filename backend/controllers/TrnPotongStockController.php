<?php

namespace backend\controllers;

use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnPotongStockItem;
use common\models\Model;
use Yii;
use common\models\ar\TrnPotongStock;
use common\models\ar\TrnPotongStockSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnPotongStockController implements the CRUD actions for TrnPotongStock model.
 */
class TrnPotongStockController extends Controller
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
     * Lists all TrnPotongStock models.
     * @return mixed
     */
    public function actionIndex()
    {
        $info = 'Fitur untuk memotong stock gudang jadi menjadi beberapa bagian.';
        Yii::$app->session->setFlash('info', $info);

        $searchModel = new TrnPotongStockSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnPotongStock model.
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
     * Creates a new TrnPotongStock model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnPotongStock(['date'=>date('Y-m-d')]);

        /* @var $modelsItem TrnPotongStockItem[]*/
        $modelsItem = [new TrnPotongStockItem()];

        if ($model->load(Yii::$app->request->post())) {
            $modelsItem = Model::createMultiple(TrnPotongStockItem::classname());
            Model::loadMultiple($modelsItem, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;

            if ($valid) {
                $stockGudangJadi = $model->stock;

                // periksa apakah status stock gudang jadi valid untuk dipotong (status = stock)
                if($stockGudangJadi->status != $stockGudangJadi::STATUS_STOCK){
                    $model->addError('stock_id', 'Status stock tidak valid.');
                    return $this->render('create', [
                        'model' => $model,
                        'modelsItem' => (empty($modelsItem)) ? [new TrnPotongStockItem] : $modelsItem
                    ]);
                }

                // periksa apakah stock sudah pernah dipotong atau belum, jika sudah maka gagalkan.
                if($stockGudangJadi->dipotong){
                    $model->addError('stock_id', 'Stock ini sudah pernah dipotong.');
                    return $this->render('create', [
                        'model' => $model,
                        'modelsItem' => (empty($modelsItem)) ? [new TrnPotongStockItem] : $modelsItem
                    ]);
                }

                //Total item potongan tidak boleh lebih dari qty kain yang dipotong
                $itemsTotalQty = array_sum(ArrayHelper::getColumn(ArrayHelper::toArray($modelsItem), 'panjang_m'));

                if($itemsTotalQty > $model->stock->qty){
                    foreach ($modelsItem as $item) {
                        $item->addError('qty', 'Jumlah pemotongan melebihi qty stock yang dipotong.');
                    }

                    return $this->render('create', [
                        'model' => $model,
                        'modelsItem' => (empty($modelsItem)) ? [new TrnPotongStockItem] : $modelsItem
                    ]);
                }
                //Total item potongan tidak boleh lebih dari qty kain yang dipotong

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        foreach ($modelsItem as $modelItem) {
                            /* @var $modelItem TrnPotongStockItem*/
                            $modelItem->potong_stock_id = $model->id;
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
                    Yii::$app->session->setFlash('error', $e->getMessage());
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'modelsItem' => (empty($modelsItem)) ? [new TrnPotongStockItem] : $modelsItem
        ]);
    }

    /**
     * Updates an existing TrnPotongStock model.
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

        $modelsItem = $model->trnPotongStockItems;

        if ($model->load(Yii::$app->request->post())) {
            $oldIDs = ArrayHelper::map($modelsItem, 'id', 'id');
            $modelsItem = Model::createMultiple(TrnPotongStockItem::classname(), $modelsItem);
            Model::loadMultiple($modelsItem, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsItem, 'id', 'id')));

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;

            if ($valid) {
                $stockGudangJadi = $model->stock;

                // periksa apakah status stock gudang jadi valid untuk dipotong (status = stock)
                if($stockGudangJadi->status != $stockGudangJadi::STATUS_STOCK){
                    $model->addError('stock_id', 'Status stock tidak valid.');
                    return $this->render('create', [
                        'model' => $model,
                        'modelsItem' => (empty($modelsItem)) ? [new TrnPotongStockItem] : $modelsItem
                    ]);
                }

                // periksa apakah stock sudah pernah dipotong atau belum, jika sudah maka gagalkan.
                if($stockGudangJadi->dipotong){
                    $model->addError('stock_id', 'Stock ini sudah pernah dipotong.');
                    return $this->render('create', [
                        'model' => $model,
                        'modelsItem' => (empty($modelsItem)) ? [new TrnPotongStockItem] : $modelsItem
                    ]);
                }

                //Total item potongan tidak boleh lebih dari qty kain yang dipotong
                $itemsTotalQty = array_sum(ArrayHelper::getColumn(ArrayHelper::toArray($modelsItem), 'panjang_m'));
                if($itemsTotalQty > $model->stock->qty){

                    foreach ($modelsItem as $item) {
                        $item->addError('qty', 'Jumlah pemotongan melebihi qty stock yang dipotong.');
                    }

                    return $this->render('create', [
                        'model' => $model,
                        'modelsItem' => (empty($modelsItem)) ? [new TrnPotongStockItem] : $modelsItem
                    ]);
                }
                //Total item potongan tidak boleh lebih dari qty kain yang dipotong

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            TrnPotongStockItem::deleteAll(['id' => $deletedIDs]);
                        }

                        foreach ($modelsItem as $modelItem) {
                            /* @var $modelItem TrnPotongStockItem*/
                            $modelItem->potong_stock_id = $model->id;
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
            'modelsItem' => (empty($modelsItem)) ? [new TrnPotongStockItem] : $modelsItem
        ]);
    }

    /**
     * Deletes an existing TrnPotongStock model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model->status != $model::STATUS_DRAFT){
            throw new ForbiddenHttpException('Status tidak valid. Tidak bisa dihapus.');
        }

        TrnPotongStockItem::deleteAll(['potong_stock_id'=>$model->id]);

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPosting($id)
    {   
           $model = $this->findModel($id);
        if($model->status != $model::STATUS_DRAFT){
            throw new ForbiddenHttpException('Status tidak valid. Tidak bisa diposting.');
        }

        $model->status = $model::STATUS_POSTED;

        $stockGudangJadi = $model->stock;
        if($stockGudangJadi->status != $stockGudangJadi::STATUS_STOCK){
            Yii::$app->session->setFlash('error', 'Status tidak valid. Tidak bisa diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if($stockGudangJadi->dipotong){
            Yii::$app->session->setFlash('error', 'Tidak bisa diposting, item ini sudah dipotong.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->setNomor();
            if(!($flag = $model->save(false, ['status', 'no_urut', 'no']))){
                Yii::$app->session->setFlash('error', 'gagal menyimpan, coba lagi.');
                $transaction->rollBack();
                return $this->redirect(['view', 'id' => $model->id]);
            }

            $stockGudangJadi->dipotong = true;
            $stockGudangJadi->status = $stockGudangJadi::STATUS_OUT;
            if(!($flag = $stockGudangJadi->save(false, ['dipotong', 'status']))){
                Yii::$app->session->setFlash('error', 'gagal merubah status stock yang akan dipotong, coba lagi.');
                $transaction->rollBack();
                return $this->redirect(['view', 'id' => $model->id]);
            }

            $itemsTotal = 0;
            foreach ($model->trnPotongStockItems as $trnPotongStockItem) {
                $modelNewStock = new TrnGudangJadi();
                $modelNewStock->load([$modelNewStock->formName()=>$stockGudangJadi->attributes]);
                $modelNewStock->qty = $trnPotongStockItem->qty;
                $modelNewStock->dipotong = false;
                $modelNewStock->hasil_pemotongan = true;
                $modelNewStock->note = 'Pemotongan ID: '.$model->id.', ItemID: '.$trnPotongStockItem->id;
                $modelNewStock->status = $modelNewStock::STATUS_STOCK;

                if(!($flag = $modelNewStock->save(false))){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'gagal menyimpan roll baru ke stock gudang, coba lagi.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                $itemsTotal += $trnPotongStockItem->qty;
            }

            // jika masih ada sisa
            // tambahan 1 roll lagi yang merupakan sisa pemotongan, data stock yang dipotong tidak dirubah sama sekali kecuali status nya, sisa pemotongan nya dimasukan ke data baru.
            $sisa = $stockGudangJadi->qty - $itemsTotal;
            if($sisa > 0){
                $modelNewStock = new TrnGudangJadi();
                $modelNewStock->load([$modelNewStock->formName()=>$stockGudangJadi->attributes]);
                $modelNewStock->qty = $sisa;
                $modelNewStock->note = 'Roll Tambahan Sisa Pemotongan ID: '.$model->id;
                $modelNewStock->status = $modelNewStock::STATUS_STOCK;
                $modelNewStock->dipotong = false;
                $modelNewStock->hasil_pemotongan = true;
                if(!($flag = $modelNewStock->save(false))){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'gagal menyimpan roll baru ke stock gudang jadi, coba lagi.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }

            if ($flag){
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Posting berhasil.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Finds the TrnPotongStock model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnPotongStock the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnPotongStock::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
