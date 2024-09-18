<?php
namespace backend\controllers;

use common\models\ar\TrnInspecting;
use common\models\ar\TrnInspectingSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class GudangJadiController extends Controller
{
    /**
     * Lists all Inspecting models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnInspectingSearch(['status'=>TrnInspecting::STATUS_DELIVERED]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Inspecting model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (($model = TrnInspecting::findOne($id)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }
}
