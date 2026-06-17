<?php

namespace backend\controllers;

use Yii;
use common\models\ar\MstProcessPrinting;
use common\models\ar\MstProcessPrintingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MstProcessPrintingController implements the CRUD actions for MstProcessPrinting model.
 */
class MstProcessPrintingController extends Controller
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
     * Lists all MstProcessPrinting models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MstProcessPrintingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MstProcessPrinting model.
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
     * Creates a new MstProcessPrinting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MstProcessPrinting([
            'tanggal' => true,
            'start' => true,
            'stop' => true,
            'no_mesin' => true,
            'operator' => true,
            'temp' => true,
            'speed_depan' => true,
            'speed_belakang' => true,
            'speed' => true,
            'resep' => true,
            'density' => true,
            'jumlah_pcs' => true,
            'lebar_jadi' => true,
            'panjang_jadi' => true,
            'info_kualitas' => true,
            'gangguan_produksi' => true,
        ]);

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {
                    $machineIds = $model->mesin_proses_ids ?? [];
                    if (!is_array($machineIds)) {
                        $machineIds = [$machineIds];
                    }
                    foreach ($machineIds as $machineId) {
                        if ($machineId) {
                            Yii::$app->db->createCommand()->insert('mst_process_printing_mesin', [
                                'mst_process_printing_id' => $model->id,
                                'mst_mesin_proses_id' => $machineId
                            ])->execute();
                        }
                    }
                    
                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing MstProcessPrinting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {
                    // Delete old links
                    Yii::$app->db->createCommand()->delete('mst_process_printing_mesin', [
                        'mst_process_printing_id' => $model->id
                    ])->execute();
                    
                    $machineIds = $model->mesin_proses_ids ?? [];
                    if (!is_array($machineIds)) {
                        $machineIds = [$machineIds];
                    }
                    foreach ($machineIds as $machineId) {
                        if ($machineId) {
                            Yii::$app->db->createCommand()->insert('mst_process_printing_mesin', [
                                'mst_process_printing_id' => $model->id,
                                'mst_mesin_proses_id' => $machineId
                            ])->execute();
                        }
                    }
                    
                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing MstProcessPrinting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the MstProcessPrinting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MstProcessPrinting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MstProcessPrinting::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
