<?php

namespace backend\controllers;

use Yii;
use common\models\ar\TrnOrderCelup;
use common\models\ar\TrnOrderCelupSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnOrderCelupController implements the CRUD actions for TrnOrderCelup model.
 */
class TrnOrderCelupController extends Controller
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
     * Lists all TrnOrderCelup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnOrderCelupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnOrderCelup model.
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
     * Creates a new TrnOrderCelup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnOrderCelup();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->greige_group_id = $model->greige->group_id;
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnOrderCelup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->greige_group_id = $model->greige->group_id;
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TrnOrderCelup model.
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
     * @param integer $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionPosting($id)
    {
        $model = $this->findModel($id);
        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->status = $model::STATUS_POSTED;
        $model->setNomor();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            //validasi stock dan booked greige
            $greige = $model->greige;
            $greigeGroup = $greige->group;
            $qtyInMeter = $model->qty * ($greigeGroup->qty_per_batch);
            $stockM = $greige->stock;
            $bookedM = $greige->booked;
            $avM = $stockM - $bookedM;

            if($avM < $qtyInMeter){
                $transaction->rollBack();
                $stockFmt = Yii::$app->formatter->asDecimal($stockM).'M';
                $bookedFmt = Yii::$app->formatter->asDecimal($bookedM).'M';
                $avFmt = Yii::$app->formatter->asDecimal($avM).'M';
                Yii::$app->session->setFlash('error', "Persediaan digudang greige tidak mencukupi, jumlah stock: $stockFmt, Booked: $bookedFmt, Tersedia: $avFmt");
                return $this->redirect(['view', 'id' => $model->id]);
            }
            //validasi stock dan booked greige

            if(!$flag = $model->save(false, ['status', 'no_urut', 'no'])){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal memposting, coba lagi.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            if($flag){
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Order Celup berhasil diposting.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    /**
     * Finds the TrnOrderCelup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnOrderCelup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnOrderCelup::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
