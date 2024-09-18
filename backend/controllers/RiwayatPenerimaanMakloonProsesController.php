<?php

namespace backend\controllers;

use common\models\ar\TrnTerimaMakloonProcess;
use common\models\ar\TrnTerimaMakloonProcessSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 *
*/
class RiwayatPenerimaanMakloonProsesController extends Controller
{
    /**
     * Lists all TrnTerimaMakloonProcess models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnTerimaMakloonProcessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['trn_terima_makloon_process.status'=>TrnTerimaMakloonProcess::STATUS_INSPECTED]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnTerimaMakloonProcess model.
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
     * Finds the TrnTerimaMakloonProcess model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnTerimaMakloonProcess the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnTerimaMakloonProcess::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}