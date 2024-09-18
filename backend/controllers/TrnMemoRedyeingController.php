<?php

namespace backend\controllers;

use Yii;
use common\models\ar\TrnMemoRedyeing;
use common\models\ar\TrnMemoRedyeingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnMemoRedyeingController implements the CRUD actions for TrnMemoRedyeing model.
 */
class TrnMemoRedyeingController extends Controller
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
     * Lists all TrnMemoRedyeing models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnMemoRedyeingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnMemoRedyeing model.
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
     * Creates a new TrnMemoRedyeing model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnMemoRedyeing();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            //periksa apakah retur buyer yang direferensikan belum pernah dibuat memmo redyeing sebelumya
            $count = TrnMemoRedyeing::find()->where(['retur_buyer_id'=>$model->retur_buyer_id])->count('id');
            if($count > 0){
                Yii::$app->session->setFlash('error', 'Dokumen retur buyer tidak valid, sudah pernah diproses.');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            $returBuyer = $model->returBuyer;
            $model->wo_id = $returBuyer->wo_id;
            $model->mo_id = $model->wo->mo_id;
            $model->sc_greige_id = $model->mo->sc_greige_id;
            $model->sc_id = $model->scGreige->sc_id;

            $model->save(false);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnMemoRedyeing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $returBuyer = $model->returBuyer;
            $model->wo_id = $returBuyer->wo_id;
            $model->mo_id = $model->wo->mo_id;
            $model->sc_greige_id = $model->mo->sc_greige_id;
            $model->sc_id = $model->scGreige->sc_id;

            $model->save(false);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TrnMemoRedyeing model.
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
     * Deletes an existing TrnMemoRepair model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPosting($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid, tidak bisa diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->status = $model::STATUS_POSTED;
        $model->setNomor();
        $model->save(false, ['status', 'no', 'no_urut']);

        Yii::$app->session->setFlash('success', 'Posting berhasil.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Finds the TrnMemoRedyeing model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnMemoRedyeing the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnMemoRedyeing::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
