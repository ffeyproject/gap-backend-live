<?php

namespace backend\controllers;

use common\models\ar\MstGreige;
use common\models\ar\TrnBuyGreigeItem;
use common\models\ar\TrnStockGreige;
use common\models\Model;
use Yii;
use common\models\ar\TrnBuyGreige;
use common\models\ar\TrnBuyGreigeSearch;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnBuyGreigeController implements the CRUD actions for TrnBuyGreige model.
 */
class TrnBuyGreigeController extends Controller
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
     * Lists all TrnBuyGreige models.
     * @return mixed
     */
    public function actionRekap()
    {
        $searchModel = new TrnBuyGreigeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('rekap', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all TrnBuyGreige models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnBuyGreigeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnBuyGreige model.
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
     * Creates a new TrnBuyGreige model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnBuyGreige(['date'=>date('Y-m-d'), 'note'=>'-']);

        /* @var $modelsItem TrnBuyGreigeItem[]*/
        $modelsItem = [new TrnBuyGreigeItem()];

        if ($model->load(Yii::$app->request->post())) {
            $modelsItem = Model::createMultiple(TrnBuyGreigeItem::classname());
            Model::loadMultiple($modelsItem, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;

            //BaseVarDumper::dump([$model, $modelsItem], 10, true);Yii::$app->end();

            if ($valid) {
                $model->greige_group_id = $model->greige->group_id;

                $transaction = Yii::$app->db->beginTransaction();
                try{
                    if ($flag = $model->save(false)) {
                        foreach ($modelsItem as $modelItem) {
                            $modelItem->buy_greige_id = $model->id;
                            $modelItem->greige_group_id = $model->greige_group_id;
                            $modelItem->greige_id = $model->greige_id;
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
                }catch (\Throwable $e){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'modelsItem' => (empty($modelsItem)) ? [new TrnBuyGreigeItem] : $modelsItem
        ]);
    }

    /**
     * Updates an existing TrnBuyGreige model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        /* @var $modelsItem TrnBuyGreigeItem[]*/
        $modelsItem = $model->getTrnBuyGreigeItems()->orderBy('id')->all();

        if ($model->load(Yii::$app->request->post())) {
            $oldIDs = ArrayHelper::map($modelsItem, 'id', 'id');
            $modelsItem = Model::createMultiple(TrnBuyGreigeItem::classname(), $modelsItem);
            Model::loadMultiple($modelsItem, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsItem, 'id', 'id')));

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;

            if ($valid) {
                $model->greige_group_id = $model->greige->group_id;

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            TrnBuyGreigeItem::deleteAll(['id' => $deletedIDs]);
                        }

                        foreach ($modelsItem as $modelItem) {
                            $modelItem->buy_greige_id = $model->id;
                            $modelItem->greige_group_id = $model->greige_group_id;
                            $modelItem->greige_id = $model->greige_id;
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
                }catch (\Throwable $e){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'modelsItem' => (empty($modelsItem)) ? [new TrnBuyGreigeItem] : $modelsItem
        ]);
    }

    /**
     * Deletes an existing TrnBuyGreige model.
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
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk dihapus.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $modelsItem = $model->trnBuyGreigeItems;

        $itemIDs = ArrayHelper::map($modelsItem, 'id', 'id');
        TrnBuyGreigeItem::deleteAll(['id' => $itemIDs]);
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing TrnBuyGreige model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionPosting($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        //untuk sementara langsung bypass jadi approved
        $model->status = $model::STATUS_APPROVED;
        $model->approval_id = Yii::$app->user->id;
        $model->approval_time = time();

        $items = $model->trnBuyGreigeItems;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(!$model->save(false, ['status', 'approval_id', 'approval_time'])){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', '');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            $uid = Yii::$app->user->id;
            $ts = time();
            $db = $model::getDb();
            $add = 0;

            switch ($model->jenis_beli){
                case $model::JENIS_BELI_LOKAL:
                    $asalGreige = TrnStockGreige::ASAL_GREIGE_BELI;
                    break;
                case $model::JENIS_BELI_IMPORT:
                    $asalGreige = TrnStockGreige::ASAL_GREIGE_BELI_IMPORT;
                    break;
                default:
                    $asalGreige = TrnStockGreige::ASAL_GREIGE_BELI;
            }
            foreach ($items as $item) {
                $cmd1 = $db->createCommand()->insert(TrnStockGreige::tableName(), [
                    'greige_group_id' => $model->greige_group_id,
                    'greige_id' => $model->greige_id,
                    'asal_greige' => $asalGreige,
                    'no_lapak' => '-',
                    'lot_lusi' => '-',
                    'lot_pakan' => '-',
                    'no_set_lusi' => '-',
                    'status_tsd' => TrnStockGreige::STATUS_TSD_NORMAL,
                    'no_document' => $model->no_document,
                    'pengirim' => $model->vendor,
                    'mengetahui' => $model->approval_id,
                    'note' => $model->note,
                    'date' => date('Y-m-d'),
                    'status' => TrnStockGreige::STATUS_VALID,
                    'jenis_gudang' => TrnStockGreige::JG_FRESH,
                    'grade' => TrnStockGreige::GRADE_NG,
                    'panjang_m' => $item->qty,
                    'created_at' => $ts,
                    'created_by' => $uid,
                    'updated_at' => $ts,
                    'updated_by' => $uid,

                ]);
                if(!$cmd1->execute() > 0){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal, coba lagi.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                $add += $item->qty;
            }

            Yii::$app->db->createCommand()
                ->update(
                    MstGreige::tableName(),
                    [
                        'stock' => new Expression("mst_greige.stock + {$add}"),
                        'available' => new Expression("mst_greige.available + {$add}")
                    ],
                    ['id'=>$model->greige_id]
                )->execute();

            Yii::$app->session->setFlash('success', 'Posting berhasil.');
            $transaction->commit();
            return $this->redirect(['view', 'id' => $model->id]);
        }catch (\Throwable $e){
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Finds the TrnBuyGreige model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnBuyGreige the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnBuyGreige::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
