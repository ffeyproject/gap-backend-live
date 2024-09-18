<?php

namespace backend\controllers;

use Yii;
use common\models\ar\TrnGreigeKeluarItem;
use common\models\ar\TrnGreigeKeluarItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnGreigeKeluarItemController implements the CRUD actions for TrnGreigeKeluarItem model.
 */
class TrnGreigeKeluarItemController extends Controller
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
     * Lists all TrnGreigeKeluarItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnGreigeKeluarItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnGreigeKeluarItem model.
     * @param integer $greige_keluar_id
     * @param integer $stock_greige_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($greige_keluar_id, $stock_greige_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($greige_keluar_id, $stock_greige_id),
        ]);
    }

    /**
     * Creates a new TrnGreigeKeluarItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnGreigeKeluarItem();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'greige_keluar_id' => $model->greige_keluar_id, 'stock_greige_id' => $model->stock_greige_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnGreigeKeluarItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $greige_keluar_id
     * @param integer $stock_greige_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($greige_keluar_id, $stock_greige_id)
    {
        $model = $this->findModel($greige_keluar_id, $stock_greige_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'greige_keluar_id' => $model->greige_keluar_id, 'stock_greige_id' => $model->stock_greige_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TrnGreigeKeluarItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $greige_keluar_id
     * @param integer $stock_greige_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($greige_keluar_id, $stock_greige_id)
    {
        $this->findModel($greige_keluar_id, $stock_greige_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TrnGreigeKeluarItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $greige_keluar_id
     * @param integer $stock_greige_id
     * @return TrnGreigeKeluarItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($greige_keluar_id, $stock_greige_id)
    {
        if (($model = TrnGreigeKeluarItem::findOne(['greige_keluar_id' => $greige_keluar_id, 'stock_greige_id' => $stock_greige_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
