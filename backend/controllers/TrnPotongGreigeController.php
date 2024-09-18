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
    public function actionPosting($id)
    {
        $model = $this->findModel($id);
        if($model->status != $model::STATUS_DRAFT){
            throw new ForbiddenHttpException('Status tidak valid. Tidak bisa diposting.');
        }

        //bypass langsung approve
        $model->posted_at = time();
        $model->status = $model::STATUS_APPROVED;//$model::STATUS_POSTED;
        $model->approved_at = time();
        $model->approved_by = $model->approved_by === null ? Yii::$app->user->id : $model->approved_by;

        $stockGreige = $model->stockGreige;
        if($stockGreige->status != $stockGreige::STATUS_VALID){
            throw new ForbiddenHttpException('Status tidak valid. Tidak bisa diposting.');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->setNomor();
            if(!($flag = $model->save(false, ['posted_at', 'status', 'approved_at', 'approved_by', 'no_urut', 'no']))){
                Yii::$app->session->setFlash('error', 'gagal menyimpan, coba lagi.');
                $transaction->rollBack();
                return $this->redirect(['view', 'id' => $model->id]);
            }

            $stockGreige->status = $stockGreige::STATUS_DIPOTONG;
            if(!($flag = $stockGreige->save(false, ['status']))){
                Yii::$app->session->setFlash('error', 'gagal merubah status kain yang akan dipotong, coba lagi.');
                $transaction->rollBack();
                return $this->redirect(['view', 'id' => $model->id]);
            }

            $itemsTotal = 0;
            foreach ($model->trnPotongGreigeItems as $trnPotongGreigeItem) {
                $modelNewStock = new TrnStockGreige();
                $modelNewStock->load([$modelNewStock->formName()=>$stockGreige->attributes]);
                $modelNewStock->panjang_m = $trnPotongGreigeItem->panjang_m;
                $modelNewStock->is_pemotongan = true;
                //$modelNewStock->note = 'Pemotongan ID: '.$model->id.', ItemID: '.$trnPotongGreigeItem->id; //note tidak perlu di override
                $modelNewStock->status = $modelNewStock::STATUS_VALID;

                if(!($flag = $modelNewStock->save(false))){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'gagal menyimpan roll baru ke stock gudang, coba lagi.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                $trnPotongGreigeItem->stock_greige_id = $modelNewStock->id;
                if(!($flag = $trnPotongGreigeItem->save(false, ['stock_greige_id']))){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'gagal menyimpan item pemotongan, coba lagi.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                $itemsTotal += $trnPotongGreigeItem->panjang_m;
            }

            // jika masih ada sisa
            // tambahan 1 roll lagi yang merupakan sisa pemotongan, data stock yang dipotong tidak dirubah sama sekali kecuali status nya, sisa pemotongan nya dimasukan ke data baru.
            $sisa = $stockGreige->panjang_m - $itemsTotal;
            if($sisa > 0){
                $modelNewStock = new TrnStockGreige();
                $modelNewStock->load([$modelNewStock->formName()=>$stockGreige->attributes]);
                $modelNewStock->panjang_m = $sisa;
                $modelNewStock->note = 'Roll Tambahan Sisa Pemotongan ID: '.$model->id;
                $modelNewStock->status = $modelNewStock::STATUS_VALID;
                $modelNewStock->is_pemotongan = true;
                if(!($flag = $modelNewStock->save(false))){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'gagal menyimpan roll baru ke stock gudang, coba lagi.');
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
