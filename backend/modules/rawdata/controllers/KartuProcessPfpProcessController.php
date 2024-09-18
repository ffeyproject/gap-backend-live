<?php

namespace backend\modules\rawdata\controllers;

use Yii;
use backend\modules\rawdata\models\KartuProcessPfpProcess;
use backend\modules\rawdata\models\KartuProcessPfpProcessSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * KartuProcessPfpProcessController implements the CRUD actions for KartuProcessPfpProcess model.
 */
class KartuProcessPfpProcessController extends Controller
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
     * Lists all KartuProcessPfpProcess models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new KartuProcessPfpProcessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single KartuProcessPfpProcess model.
     * @param integer $kartu_process_id
     * @param integer $process_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($kartu_process_id, $process_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($kartu_process_id, $process_id),
        ]);
    }

    /**
     * Creates a new KartuProcessPfpProcess model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new KartuProcessPfpProcess();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'kartu_process_id' => $model->kartu_process_id, 'process_id' => $model->process_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing KartuProcessPfpProcess model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $kartu_process_id
     * @param integer $process_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($kartu_process_id, $process_id)
    {
        $model = $this->findModel($kartu_process_id, $process_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'kartu_process_id' => $model->kartu_process_id, 'process_id' => $model->process_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing KartuProcessPfpProcess model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $kartu_process_id
     * @param integer $process_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($kartu_process_id, $process_id)
    {
        $this->findModel($kartu_process_id, $process_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the KartuProcessPfpProcess model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $kartu_process_id
     * @param integer $process_id
     * @return KartuProcessPfpProcess the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($kartu_process_id, $process_id)
    {
        if (($model = KartuProcessPfpProcess::findOne(['kartu_process_id' => $kartu_process_id, 'process_id' => $process_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
