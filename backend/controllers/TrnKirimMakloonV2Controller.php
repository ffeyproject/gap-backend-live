<?php

namespace backend\controllers;

use common\models\ar\TrnKirimMakloonV2Item;
use common\models\Model;
use Yii;
use common\models\ar\TrnKirimMakloonV2;
use common\models\ar\TrnKirimMakloonV2Search;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnKirimMakloonV2Controller implements the CRUD actions for TrnKirimMakloonV2 model.
 */
class TrnKirimMakloonV2Controller extends Controller
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
     * Lists all TrnKirimMakloonV2 models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnKirimMakloonV2Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnKirimMakloonV2 model.
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
     * Creates a new TrnKirimMakloonV2 model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnKirimMakloonV2();
        $modelsItem = [new TrnKirimMakloonV2Item];

        if ($model->load(Yii::$app->request->post())) {
            $modelsItem = Model::createMultiple(TrnKirimMakloonV2Item::class);
            Model::loadMultiple($modelsItem, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();

                try {
                    if ($flag = $model->save(false)) {
                        foreach ($modelsItem as $modelItem) {
                            /* @var $modelItem TrnKirimMakloonV2Item*/
                            $modelItem->kirim_makloon_id = $model->id;
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
                }catch (\Throwable $t){
                    Yii::$app->session->setFlash('error', $t->getMessage());
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'modelsItem' => (empty($modelsItem)) ? [new TrnKirimMakloonV2Item] : $modelsItem
        ]);
    }

    /**
     * Updates an existing TrnKirimMakloonV2 model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelsItem = $model->trnKirimMakloonItems;

        if ($model->load(Yii::$app->request->post())) {
            $oldIDs = ArrayHelper::map($modelsItem, 'id', 'id');
            $modelsItem = Model::createMultiple(TrnKirimMakloonV2Item::class, $modelsItem);
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
                            TrnKirimMakloonV2Item::deleteAll(['id' => $deletedIDs]);
                        }

                        foreach ($modelsItem as $modelItem) {
                            /* @var $modelItem TrnKirimMakloonV2Item*/
                            $modelItem->kirim_makloon_id = $model->id;
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
                }catch (\Throwable $t){
                    Yii::$app->session->setFlash('error', $t->getMessage());
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'modelsItem' => (empty($modelsItem)) ? [new TrnKirimMakloonV2Item] : $modelsItem
        ]);
    }

    /**
     * Deletes an existing TrnKirimMakloonV2 model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

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
            Yii::$app->session->setFlash('error', 'Gagal, Belum ada item yang dimasukan.');
            return $this->redirect(['view', 'id'=>$id]);
        }

        $model->status = $model::STATUS_POSTED;
        $model->setNomor();
        $model->save(false, ['status', 'no_urut', 'no']);

        Yii::$app->session->setFlash('success', 'Posting berhasil.');
        return $this->redirect(['view', 'id'=>$id]);
    }

    /**
     * Finds the TrnKirimMakloonV2 model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnKirimMakloonV2 the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnKirimMakloonV2::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
