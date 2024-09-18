<?php

namespace backend\controllers;

use Yii;
use common\models\ar\KartuProcessPrintingProcess;
use common\models\ar\KartuProcessPrintingProcessSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * KartuProcessPrintingProcessController implements the CRUD actions for KartuProcessPrintingProcess model.
 */
class KartuProcessPrintingProcessController extends Controller
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
     * Lists all KartuProcessPrintingProcess models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new KartuProcessPrintingProcessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single KartuProcessPrintingProcess model.
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
     * Creates a new KartuProcessPrintingProcess model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new KartuProcessPrintingProcess();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'kartu_process_id' => $model->kartu_process_id, 'process_id' => $model->process_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing KartuProcessPrintingProcess model.
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
     * Deletes an existing KartuProcessPrintingProcess model.
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
     * Finds the KartuProcessPrintingProcess model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $kartu_process_id
     * @param integer $process_id
     * @return KartuProcessPrintingProcess the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($kartu_process_id, $process_id)
    {
        if (($model = KartuProcessPrintingProcess::findOne(['kartu_process_id' => $kartu_process_id, 'process_id' => $process_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
