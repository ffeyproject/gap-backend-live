<?php

namespace backend\controllers;

use Yii;
use common\models\ar\TrnPfpKeluarItem;
use common\models\ar\TrnPfpKeluarItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnPfpKeluarItemController implements the CRUD actions for TrnPfpKeluarItem model.
 */
class TrnPfpKeluarItemController extends Controller
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
     * Lists all TrnPfpKeluarItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnPfpKeluarItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnPfpKeluarItem model.
     * @param integer $pfp_keluar_id
     * @param integer $stock_pfp_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($pfp_keluar_id, $stock_pfp_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($pfp_keluar_id, $stock_pfp_id),
        ]);
    }

    /**
     * Creates a new TrnPfpKeluarItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnPfpKeluarItem();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'pfp_keluar_id' => $model->pfp_keluar_id, 'stock_pfp_id' => $model->stock_pfp_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnPfpKeluarItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $pfp_keluar_id
     * @param integer $stock_pfp_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($pfp_keluar_id, $stock_pfp_id)
    {
        $model = $this->findModel($pfp_keluar_id, $stock_pfp_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'pfp_keluar_id' => $model->pfp_keluar_id, 'stock_pfp_id' => $model->stock_pfp_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TrnPfpKeluarItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $pfp_keluar_id
     * @param integer $stock_pfp_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($pfp_keluar_id, $stock_pfp_id)
    {
        $this->findModel($pfp_keluar_id, $stock_pfp_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TrnPfpKeluarItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $pfp_keluar_id
     * @param integer $stock_pfp_id
     * @return TrnPfpKeluarItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($pfp_keluar_id, $stock_pfp_id)
    {
        if (($model = TrnPfpKeluarItem::findOne(['pfp_keluar_id' => $pfp_keluar_id, 'stock_pfp_id' => $stock_pfp_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
