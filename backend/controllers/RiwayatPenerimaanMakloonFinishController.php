<?php

namespace backend\controllers;

use common\models\ar\TrnTerimaMakloonFinish;
use common\models\ar\TrnTerimaMakloonFinishSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
*/
class RiwayatPenerimaanMakloonFinishController extends Controller
{
    /**
     * Lists all TrnTerimaMakloonFinish models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnTerimaMakloonFinishSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['trn_terima_makloon_finish.status'=>TrnTerimaMakloonFinish::STATUS_INSPECTED]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnTerimaMakloonFinish model.
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
     * Finds the TrnTerimaMakloonFinish model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnTerimaMakloonFinish the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnTerimaMakloonFinish::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}