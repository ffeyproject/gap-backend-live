<?php

namespace backend\controllers;

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnKirimBuyerItem;
use common\models\ar\TrnWo;
use Yii;
use common\models\ar\TrnKirimBuyer;
use common\models\ar\TrnKirimBuyerSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseVarDumper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use backend\components\Converter;

/**
 * TrnKirimBuyerController implements the CRUD actions for TrnKirimBuyer model.
 */
class TrnKirimBuyerController extends Controller
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
     * Lists all TrnKirimBuyer models.
     * @return mixed
     */
    public function actionRekap()
    {
        $searchModel = new \backend\models\TrnKirimBuyerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dateRange = Yii::$app->request->queryParams['TrnKirimBuyerSearch']['dateRange'] ?? null;
    
        $totalQty = 0;
    
        if ($dateRange) {
            $query1 = TrnKirimBuyer::find();
            $query1->joinWith(['header', 'trnKirimBuyerItems as tkbi']);
            $query1->select(['SUM(tkbi.qty) as qty_all']);
            $query1->andFilterWhere(['trn_kirim_buyer.unit' => MstGreigeGroup::UNIT_YARD]);

            $query2 = TrnKirimBuyer::find();
            $query2->joinWith(['header', 'trnKirimBuyerItems as tkbi']);
            $query2->select(['SUM(tkbi.qty) as qty_all']);
            $query2->andFilterWhere(['trn_kirim_buyer.unit' => MstGreigeGroup::UNIT_METER]);
    
            $from_date = substr($dateRange, 0, 10);
            $to_date = substr($dateRange, 14);
    
            if ($from_date == $to_date) {
                $query1->andFilterWhere(['trn_kirim_buyer_header.date' => $from_date]);
                $query2->andFilterWhere(['trn_kirim_buyer_header.date' => $from_date]);

            } else {
                $query1->andFilterWhere(['between', 'trn_kirim_buyer_header.date', $from_date, $to_date]);
                $query2->andFilterWhere(['between', 'trn_kirim_buyer_header.date', $from_date, $to_date]);
            }
    
            $query1 = $query1->asArray()->one();
            $query2 = $query2->asArray()->one();

            $totalQty = $query1['qty_all'] && $query2['qty_all'] ? $query1['qty_all'] + Converter::meterToYard($query2['qty_all']) : 0;
        }
    
        return $this->render('rekap', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totalQty' => Yii::$app->formatter->asDecimal($totalQty),
        ]);
    }

    /**
     * Lists all TrnKirimBuyer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnKirimBuyerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnKirimBuyer model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \yii\base\InvalidConfigException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $modelsItem = $model->trnKirimBuyerItems;
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
     * Creates a new TrnKirimBuyer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnKirimBuyer(['date'=>date('Y-m-d')]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnKirimBuyer model.
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
     * Deletes an existing TrnKirimBuyer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        TrnKirimBuyerItem::deleteAll(['kirim_buyer_id'=>$model->id]);

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Updates an existing TrnKirimBuyerHeader model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionEditAlias($id){
        if(Yii::$app->request->isAjax){
            $model = $this->findModel($id);

            $header = $model->header;
            if($header->status != $header::STATUS_DRAFT){
                throw new ForbiddenHttpException('Status tidak valid, tidak bisa diproses.');
            }

            Yii::$app->response->format = Response::FORMAT_JSON;

            $model->nama_kain_alias = Yii::$app->request->post('formData');
            $model->save(false, ['nama_kain_alias']);

            return true;
        }

        throw new MethodNotAllowedHttpException('Metode tidak diizinkan.');
    }

    /**
     * Updates an existing TrnKirimBuyerHeader model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionEditNote($id){
        if(Yii::$app->request->isAjax){
            $model = $this->findModel($id);

            $header = $model->header;
            if($header->status != $header::STATUS_DRAFT){
                throw new ForbiddenHttpException('Status tidak valid, tidak bisa diproses.');
            }

            Yii::$app->response->format = Response::FORMAT_JSON;

            $model->note = Yii::$app->request->post('formData');
            $model->save(false, ['note']);

            return true;
        }

        throw new MethodNotAllowedHttpException('Metode tidak diizinkan.');
    }

    /**
     * @param $id
     * @return TrnKirimBuyerItem[]
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

            $stockIds = Yii::$app->request->post('formData');
            if(empty($stockIds)){
                throw new ForbiddenHttpException('data kosong, tidak bisa diproses.');
            }

            $transaction = Yii::$app->db->beginTransaction();
            try{
                $flag = false;
                foreach ($stockIds as $stockId) {
                    $modelStok = TrnGudangJadi::findOne($stockId);
                    $modelItem = new TrnKirimBuyerItem([
                        'kirim_buyer_id' => $model->id,
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
                    foreach ($model->trnKirimBuyerItems as $item) {
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

            $itemIds = Yii::$app->request->post('formData');
            if(empty($itemIds)){
                throw new ForbiddenHttpException('data kosong, tidak bisa diproses.');
            }

            /* @var $itemsToDelete TrnKirimBuyerItem[]*/
            $itemsToDelete = $model->getTrnKirimBuyerItems()->where(['in', 'id', $itemIds])->all();
            foreach ($itemsToDelete as $itemToDelete) {
                $itemToDelete->delete();
            }

            $modelItemStockIds = ArrayHelper::getColumn($model->getTrnKirimBuyerItems()->asArray()->all(), 'stock_id');
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
     * Finds the TrnKirimBuyer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnKirimBuyer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnKirimBuyer::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
