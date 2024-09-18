<?php

namespace backend\controllers;

use Yii;
use common\models\ar\TrnMemoPerubahanData;
use common\models\ar\TrnMemoPerubahanDataSearch;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnMemoPerubahanDataController implements the CRUD actions for TrnMemoPerubahanData model.
 */
class TrnMemoPerubahanDataController extends Controller
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
     * Lists all TrnMemoPerubahanData models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnMemoPerubahanDataSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $lastData = TrnMemoPerubahanData::find()
            ->where([
                'and',
                ['not', ['no_urut' => null]],
                new Expression('EXTRACT(YEAR FROM "'.TrnMemoPerubahanData::tableName().'"."date") || \'-\' || EXTRACT(MONTH FROM "'.TrnMemoPerubahanData::tableName().'"."date") = \'2020-09\''),
            ])
            ->orderBy(['no_urut' => SORT_DESC])
            ->one()
        ;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnMemoPerubahanData model.
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
     * Creates a new TrnMemoPerubahanData model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnMemoPerubahanData([
            'date' => date('Y-m-d'),
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnMemoPerubahanData model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            throw new ForbiddenHttpException('Status tidak valid, tidak bisa dirubah.');
        }

        if($model->created_by != Yii::$app->user->id){
            throw new ForbiddenHttpException('Perubahan tidak diizinkan, hanya bisa dirubah oleh user terkait.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TrnMemoPerubahanData model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            throw new ForbiddenHttpException('Status tidak valid, tidak bisa dihapus.');
        }

        if($model->created_by != Yii::$app->user->id){
            throw new ForbiddenHttpException('Penghapusan tidak diizinkan, hanya bisa dihapus oleh user terkait.');
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing TrnMemoPerubahanData model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPosting($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            throw new ForbiddenHttpException('Status tidak valid, tidak bisa dipostind.');
        }

        if($model->created_by != Yii::$app->user->id){
            throw new ForbiddenHttpException('Posting tidak diizinkan, hanya bisa diposting oleh user terkait.');
        }

        $model->status = $model::STATUS_POSTED;
        $model->setNomor();
        $model->save(false, ['status', 'no_urut', 'no']);

        Yii::$app->session->setFlash('success', 'Posting berhasil.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Finds the TrnMemoPerubahanData model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnMemoPerubahanData the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnMemoPerubahanData::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
