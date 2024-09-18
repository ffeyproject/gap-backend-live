<?php

namespace backend\controllers;

use common\models\ar\MutasiExFinishAltItem;
use common\models\ar\PfpKeluarVerpackingItem;
use common\models\ar\TrnGudangJadi;
use common\models\Model;
use Yii;
use common\models\ar\PfpKeluarVerpacking;
use common\models\ar\PfpKeluarVerpackingSearch;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PfpKeluarVerpackingController implements the CRUD actions for PfpKeluarVerpacking model.
 */
class PfpKeluarVerpackingController extends Controller
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
     * Lists all PfpKeluarVerpacking models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PfpKeluarVerpackingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PfpKeluarVerpacking model.
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
     * Creates a new PfpKeluarVerpacking model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PfpKeluarVerpacking();

        $modelsItem = [new PfpKeluarVerpackingItem()];

        if ($model->load(Yii::$app->request->post())) {
            $modelsItem = Model::createMultiple(PfpKeluarVerpackingItem::classname());
            Model::loadMultiple($modelsItem, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;

            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try{
                    if ($flag = $model->save(false)) {
                        foreach ($modelsItem as $modelItem) {
                            /* @var $modelItem PfpKeluarVerpackingItem*/
                            $modelItem->pfp_keluar_verpacking_id = $model->id;
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
            'modelsItem' => (empty($modelsItem)) ? [new PfpKeluarVerpackingItem] : $modelsItem
        ]);
    }

    /**
     * Updates an existing PfpKeluarVerpacking model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelsItem = $model->pfpKeluarVerpackingItems;

        if ($model->load(Yii::$app->request->post())) {
            $oldIDs = ArrayHelper::map($modelsItem, 'id', 'id');
            $modelsItem = Model::createMultiple(PfpKeluarVerpackingItem::classname(), $modelsItem);
            Model::loadMultiple($modelsItem, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsItem, 'id', 'id')));

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;

            if($valid){
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            PfpKeluarVerpackingItem::deleteAll(['id' => $deletedIDs]);
                        }

                        foreach ($modelsItem as $modelItem) {
                            /* @var $modelItem PfpKeluarVerpackingItem*/
                            $modelItem->pfp_keluar_verpacking_id = $model->id;
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
            'modelsItem' => (empty($modelsItem)) ? [new PfpKeluarVerpackingItem] : $modelsItem
        ]);
    }

    /**
     * Deletes an existing PfpKeluarVerpacking model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk dihapus.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $modelsItem = $model->pfpKeluarVerpackingItems;

        $itemIDs = ArrayHelper::map($modelsItem, 'id', 'id');
        PfpKeluarVerpackingItem::deleteAll(['id' => $itemIDs]);
        $model->delete();

        return $this->redirect(['index']);
    }

    public function actionPosting($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->setNomor();
        $model->status = $model::STATUS_APPROVED; //bypass dari posted langsung approved

        $model->save(false);
        Yii::$app->session->setFlash('success', 'Posting berhasil.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Finds the PfpKeluarVerpacking model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PfpKeluarVerpacking the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PfpKeluarVerpacking::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
