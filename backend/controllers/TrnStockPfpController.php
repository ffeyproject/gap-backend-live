<?php
namespace backend\controllers;

use common\models\ar\TrnStockGreige;
use common\models\ar\TrnStockGreigeSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnStockPfpController implements the CRUD actions for TrnStockPfp model.
 */
class TrnStockPfpController extends Controller
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
     * Lists all TrnStockPfp models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnStockGreigeSearch(['jenis_gudang'=>TrnStockGreige::JG_PFP]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all TrnStockPfp models.
     * @return mixed
     */
    /*public function actionIndexInspecting()
    {
        $searchModel = new TrnInspectingSearch(['status'=>TrnInspecting::STATUS_APPROVED]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->where(['status'=>TrnInspecting::STATUS_APPROVED])
            ->andWhere(['not', ['kartu_process_pfp_id' => null]])
        ;

        return $this->render('index-inspecting', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }*/

    /**
     * Displays a single TrnStockPfp model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }*/

    /**
     * Creates a new TrnStockPfp model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    /*public function actionCreate()
    {
        $model = new TrnStockPfp();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }*/

    /**
     * Updates an existing TrnStockPfp model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }*/

    /**
     * Deletes an existing TrnStockPfp model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    /*public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/

    /**
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    /*public function actionTerimaHasilInspecting($id){
        if (($modelInspecting = TrnInspecting::findOne($id)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try{
            $flag = false;

            foreach ($modelInspecting->trnInspectingItems as $trnInspectingItem) {
                $model = new TrnStockPfp([
                    'greige_group_id' => $modelInspecting->kartuProcessPfp->greige_group_id,
                    'greige_id' => $modelInspecting->kartuProcessPfp->greige_id,
                    'kartu_proses_id' => $modelInspecting->kartu_process_pfp_id,
                    'grade' => $trnInspectingItem->gr,
                    'panjang_m' => 'Panjang M',
                    'no_document' => 'No Document',
                    'pengirim' => 'Pengirim',
                    'mengetahui' => 'Mengetahui',
                    'note' => 'Note',
                    'status' => 'Status',
                    'date' => 'Date',
                ]);
            }

            if($flag){
                $transaction->commit();
                return true;
            }
        }catch(\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        //BaseVarDumper::dump($modelInspecting, 10, true);Yii::$app->end();
    }*/

    /**
     * Finds the TrnStockPfp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnStockGreige the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnStockGreige::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
