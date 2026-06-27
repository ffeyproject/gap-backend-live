<?php

namespace backend\controllers;

use Yii;
use common\models\ar\TrnGreigeStockHistory;
use common\models\ar\TrnGreigeStockHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnGreigeStockHistoryController implements the actions for TrnGreigeStockHistory model.
 */
class TrnGreigeStockHistoryController extends Controller
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
     * Lists all TrnGreigeStockHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnGreigeStockHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnGreigeStockHistory model.
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
     * Finds the TrnGreigeStockHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnGreigeStockHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnGreigeStockHistory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang Anda cari tidak ditemukan.');
    }
}
