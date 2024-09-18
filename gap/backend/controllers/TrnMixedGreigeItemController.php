<?php

namespace backend\controllers;

use Yii;
use common\models\ar\TrnMixedGreigeItem;
use common\models\ar\TrnMixedGreigeItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnMixedGreigeItemController implements the CRUD actions for TrnMixedGreigeItem model.
 */
class TrnMixedGreigeItemController extends Controller
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
     * Lists all TrnMixedGreigeItem models.
     * @return mixed
     */
    public function actionRiwayat()
    {
        $searchModel = new TrnMixedGreigeItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('riwayat', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all TrnMixedGreigeItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnMixedGreigeItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnMixedGreigeItem model.
     * @param integer $mix_id
     * @param integer $stock_greige_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($mix_id, $stock_greige_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($mix_id, $stock_greige_id),
        ]);
    }

    /**
     * Creates a new TrnMixedGreigeItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnMixedGreigeItem();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'mix_id' => $model->mix_id, 'stock_greige_id' => $model->stock_greige_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnMixedGreigeItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $mix_id
     * @param integer $stock_greige_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($mix_id, $stock_greige_id)
    {
        $model = $this->findModel($mix_id, $stock_greige_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'mix_id' => $model->mix_id, 'stock_greige_id' => $model->stock_greige_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TrnMixedGreigeItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $mix_id
     * @param integer $stock_greige_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($mix_id, $stock_greige_id)
    {
        $this->findModel($mix_id, $stock_greige_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TrnMixedGreigeItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $mix_id
     * @param integer $stock_greige_id
     * @return TrnMixedGreigeItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($mix_id, $stock_greige_id)
    {
        if (($model = TrnMixedGreigeItem::findOne(['mix_id' => $mix_id, 'stock_greige_id' => $stock_greige_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
