<?php

namespace backend\controllers;

use Yii;
use common\models\ar\MstGreige;
use common\models\ar\MstGreigeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MstGreigeController implements the CRUD actions for MstGreige model.
 */
class MstGreigeController extends Controller
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
     * Lists all MstGreige models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MstGreigeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MstGreige model.
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
     * Creates a new MstGreige model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MstGreige();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing MstGreige model.
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
    }

    /**
     * Deletes an existing MstGreige model.
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
     * Finds the MstGreige model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MstGreige the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MstGreige::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
 * Update status_weaving via modal.
 * @param integer $id
 * @return mixed
 * @throws NotFoundHttpException
 */
 // Action AJAX modal untuk update status_weaving
public function actionUpdateWeaving($id)
{
    $model = $this->findModel($id);

    // Tangani POST AJAX
    if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
        if ($model->save()) {
            if (Yii::$app->request->isAjax) {
                // Mengembalikan plain text 'success' untuk JS
                Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
                return 'success';
            }
            // Jika bukan AJAX, redirect ke view
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            // Jika gagal validasi, kirim error AJAX untuk ActiveForm
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($model);
            }
        }
    }

    // Render modal via AJAX
    if (Yii::$app->request->isAjax) {
        return $this->renderAjax('_updateWeaving', [
            'model' => $model,
        ]);
    }

    // Render normal jika bukan AJAX
    return $this->render('_updateWeaving', [
        'model' => $model,
    ]);
}




}