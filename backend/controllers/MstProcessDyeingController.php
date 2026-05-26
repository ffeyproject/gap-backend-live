<?php

namespace backend\controllers;

use Yii;
use common\models\ar\MstProcessDyeing;
use common\models\ar\MstProcessDyeingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MstProcessDyeingController implements the CRUD actions for MstProcessDyeing model.
 */
class MstProcessDyeingController extends Controller
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
     * Lists all MstProcessDyeing models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MstProcessDyeingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MstProcessDyeing model.
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
     * Creates a new MstProcessDyeing model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MstProcessDyeing();

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
                            Yii::$app->db->createCommand()->insert('mst_process_dyeing_mesin', [
                                'mst_process_dyeing_id' => $model->id,
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
     * Updates an existing MstProcessDyeing model.
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
                    Yii::$app->db->createCommand()->delete('mst_process_dyeing_mesin', [
                        'mst_process_dyeing_id' => $model->id
                    ])->execute();
                    
                    $machineIds = $model->mesin_proses_ids ?? [];
                    if (!is_array($machineIds)) {
                        $machineIds = [$machineIds];
                    }
                    foreach ($machineIds as $machineId) {
                        if ($machineId) {
                            Yii::$app->db->createCommand()->insert('mst_process_dyeing_mesin', [
                                'mst_process_dyeing_id' => $model->id,
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
     * Deletes an existing MstProcessDyeing model.
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
     * Finds the MstProcessDyeing model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MstProcessDyeing the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MstProcessDyeing::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
