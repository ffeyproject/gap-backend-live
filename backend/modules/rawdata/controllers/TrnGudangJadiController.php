<?php

namespace backend\modules\rawdata\controllers;

use Yii;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnGudangJadiSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnGudangJadiController implements the CRUD actions for TrnGudangJadi model.
 */
class TrnGudangJadiController extends Controller
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
     * Lists all TrnGudangJadi models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnGudangJadiSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnGudangJadi model.
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
     * Creates a new TrnGudangJadi model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnGudangJadi();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnGudangJadi model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
        
        //$model = $this->findModel($id);
        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        //     try {
        //         $model->load(Yii::$app->request->post());
        //         $model->save();
        //         //var_dump(Yii::$app->request->post());
        //         //var_dump($model);
        //         //die;
        //         return $this->redirect(['view', 'id' => $model->id]);
        //       } catch (\Throwable $th) {
        //         var_dump($th);
        //         die;
        //       }
        // }

        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
            
        //     // var_dump($model); 
        //     // var_dump(Yii::$app->request->post());
        //     // die; 

        //     // $model->load(Yii::$app->request->post());
        //     // $model->save();

        //     return $this->redirect(['view', 'id' => $model->id]);
        // }

        // return $this->render('update', [
        //     'model' => $model,
        // ]);
    }

    /**
     * Deletes an existing TrnGudangJadi model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TrnGudangJadi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnGudangJadi the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnGudangJadi::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
