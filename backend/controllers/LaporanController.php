<?php

namespace backend\controllers;

use common\models\ar\TrnKartuProsesDyeingSearch;
use Yii;
use yii\web\Controller;

class LaporanController extends Controller
{
    /**
     * Lists all TrnKartuProsesDyeing models.
     * @return mixed
     */
    public function actionPersiapanDyeing()
    {
        $searchModel = new TrnKartuProsesDyeingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('persiapan-dyeing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}