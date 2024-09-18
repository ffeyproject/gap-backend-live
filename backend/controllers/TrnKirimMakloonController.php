<?php

namespace backend\controllers;

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnKirimMakloonItem;
use common\models\ar\TrnWo;
use Yii;
use common\models\ar\TrnKirimMakloon;
use common\models\ar\TrnKirimMakloonSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TrnKirimMakloonController implements the CRUD actions for TrnKirimMakloon model.
 */
class TrnKirimMakloonController extends Controller
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
     * Lists all TrnKirimMakloon models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnKirimMakloonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnKirimMakloon model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $modelsItem = $model->trnKirimMakloonItems;
        $modelItemStockIds = []; //ArrayHelper::getColumn($modelsItem, 'stock_id');
        $dataItems = [];
        foreach ($modelsItem as $item) {
            $itemData = [
                'checkbox'=>'',
                'qty_fmt'=>Yii::$app->formatter->asDecimal($item->qty)
            ];
            $dataItems[] = ArrayHelper::merge($item->attributes, $itemData);
            $modelItemStockIds[] = $item->stock_id;
        }

        $qDataStocks = TrnGudangJadi::find()
            ->where(['wo_id'=>$model->wo_id, 'status'=>TrnGudangJadi::STATUS_STOCK])
            ->andFilterWhere(['not in', 'id', $modelItemStockIds])
            ->asArray()
            ->all()
        ;
        $dataStocks = [];
        foreach ($qDataStocks as $qDataStock) {
            $formattedData = [
                'checkbox'=>'',
                'jenis_gudang_name'=>TrnGudangJadi::jenisGudangOptions()[$qDataStock['jenis_gudang']],
                'source_name'=>TrnGudangJadi::sourceOptions()[$qDataStock['source']],
                'unit_name'=>MstGreigeGroup::unitOptions()[$qDataStock['unit']],
                'nomor_wo'=>TrnWo::findOne($qDataStock['wo_id'])->no,
                'date_fmt'=>Yii::$app->formatter->asDate($qDataStock['date']),
                'qty_fmt'=>Yii::$app->formatter->asDecimal($qDataStock['qty'])
            ];
            $dataStocks[] = ArrayHelper::merge($qDataStock, $formattedData);
        }

        //BaseVarDumper::dump($dataStocks, 10, true);Yii::$app->end();

        return $this->render('view', [
            'model' => $model,
            'dataStocks' => $dataStocks,
            'dataItems' => $dataItems
        ]);
    }

    /**
     * Creates a new TrnKirimMakloon model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnKirimMakloon(['date'=>date('Y-m-d')]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnKirimMakloon model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            throw new ForbiddenHttpException('Status tidak valid, tidak bisa diproses.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TrnKirimMakloon model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            throw new ForbiddenHttpException('Status tidak valid, tidak bisa diproses.');
        }

        TrnKirimMakloonItem::deleteAll(['kirim_makloon_id'=>$model->id]);

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing TrnKirimMakloon model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPosting($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            throw new ForbiddenHttpException('Status tidak valid, tidak bisa diproses.');
        }

        if(!$model->getTrnKirimMakloonItems()->exists()){
            Yii::$app->session->setFlash('error', 'Gagal, Belum ada item yang dipilih.');
            return $this->redirect(['view', 'id'=>$id]);
        }

        $stockIds = [];
        foreach ($model->trnKirimMakloonItems as $trnKirimMakloonItems) {
            if($trnKirimMakloonItems->stock->status != TrnGudangJadi::STATUS_STOCK){
                Yii::$app->session->setFlash('error', 'Gagal, Stok tidak valid, mungkin sudah digunakan oleh dokumen pengiriman lain. (0)');
                return $this->redirect(['view', 'id'=>$id]);
            }

            $stockIds[] = $trnKirimMakloonItems->stock_id;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try{
            $model->status = $model::STATUS_POSTED;
            $model->setNomor();
            if(!$flag = $model->save(false, ['status', 'no_urut', 'no'])){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal, coba lagi. (1)');
                return $this->redirect(['view', 'id'=>$id]);
            }

            if(!$flag = TrnGudangJadi::updateAll(['status'=>TrnGudangJadi::STATUS_OUT], ['id'=>$stockIds])){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal, coba lagi. (2)');
                return $this->redirect(['view', 'id'=>$id]);
            }

            if($flag){
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Posting berhasil.');
            }
        }catch (\Throwable $t){
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $t->getMessage());
        }

        return $this->redirect(['view', 'id'=>$id]);
    }

    /**
     * @param $id
     * @return TrnKirimMakloonItem[]
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionAmbilStock($id)
    {
        if(Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if($model->status != $model::STATUS_DRAFT){
                throw new ForbiddenHttpException('Status tidak valid, tidak bisa diproses.');
            }

            $stockIds = Yii::$app->request->post('formData');
            if(empty($stockIds)){
                throw new ForbiddenHttpException('data kosong, tidak bisa diproses.');
            }

            $transaction = Yii::$app->db->beginTransaction();
            try{
                $flag = false;
                foreach ($stockIds as $stockId) {
                    $modelStok = TrnGudangJadi::findOne($stockId);
                    $modelItem = new TrnKirimMakloonItem([
                        'kirim_makloon_id' => $model->id,
                        'stock_id' => $modelStok->id,
                        'qty' => $modelStok->qty,
                    ]);

                    if(!$flag = $modelItem->save(false)){
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal, coba lagi.');
                    }
                }

                if($flag){
                    $transaction->commit();

                    $dataItems = [];
                    foreach ($model->trnKirimMakloonItems as $item) {
                        $itemData = [
                            'checkbox'=>'',
                            'qty_fmt'=>Yii::$app->formatter->asDecimal($item->qty)
                        ];
                        $dataItems[] = ArrayHelper::merge($item->attributes, $itemData);
                    }

                    return $dataItems;
                }
            }catch (\Throwable $t){
                $transaction->rollBack();
                throw $t;
            }
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * @param $id
     * @return array
     * @throws ForbiddenHttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionKembalikanStock($id)
    {
        if(Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if($model->status != $model::STATUS_DRAFT){
                throw new ForbiddenHttpException('Status tidak valid, tidak bisa diproses.');
            }

            $itemIds = Yii::$app->request->post('formData');
            if(empty($itemIds)){
                throw new ForbiddenHttpException('data kosong, tidak bisa diproses.');
            }

            /* @var $itemsToDelete TrnKirimMakloonItem[]*/
            $itemsToDelete = $model->getTrnKirimMakloonItems()->where(['in', 'id', $itemIds])->all();
            foreach ($itemsToDelete as $itemToDelete) {
                $itemToDelete->delete();
            }

            $modelItemStockIds = ArrayHelper::getColumn($model->getTrnKirimMakloonItems()->asArray()->all(), 'stock_id');
            $qDataStocks = TrnGudangJadi::find()
                ->where(['wo_id'=>$model->wo_id, 'status'=>TrnGudangJadi::STATUS_STOCK])
                ->andFilterWhere(['not in', 'id', $modelItemStockIds])
                ->asArray()
                ->all()
            ;
            $dataStocks = [];
            foreach ($qDataStocks as $qDataStock) {
                $formattedData = [
                    'checkbox'=>'',
                    'jenis_gudang_name'=>TrnGudangJadi::jenisGudangOptions()[$qDataStock['jenis_gudang']],
                    'source_name'=>TrnGudangJadi::sourceOptions()[$qDataStock['source']],
                    'unit_name'=>MstGreigeGroup::unitOptions()[$qDataStock['unit']],
                    'nomor_wo'=>TrnWo::findOne($qDataStock['wo_id'])->no,
                    'date_fmt'=>Yii::$app->formatter->asDate($qDataStock['date']),
                    'qty_fmt'=>Yii::$app->formatter->asDecimal($qDataStock['qty'])
                ];
                $dataStocks[] = ArrayHelper::merge($qDataStock, $formattedData);
            }

            return $dataStocks;
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * Finds the TrnKirimMakloon model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnKirimMakloon the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnKirimMakloon::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
