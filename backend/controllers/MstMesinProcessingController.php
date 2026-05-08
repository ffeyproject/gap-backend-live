<?php

namespace backend\controllers;

use Yii;
use common\models\ar\MstMesinProcessing;
use common\models\ar\MstMesinProcessingSearch;
use common\models\ar\MstMesinProcessingForbiddenGreige;
use common\models\ar\MstGreige;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MstMesinProcessingController implements the CRUD actions for MstMesinProcessing model.
 */
class MstMesinProcessingController extends Controller
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
                    'delete-forbidden-greige' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all MstMesinProcessing models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MstMesinProcessingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MstMesinProcessing model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $greigeIds = Yii::$app->request->post('forbidden_greige_ids');
            if (is_array($greigeIds)) {
                foreach ($greigeIds as $greigeId) {
                    $exists = MstMesinProcessingForbiddenGreige::find()
                        ->where(['mesin_id' => $id, 'greige_id' => $greigeId])
                        ->exists();
                    if (!$exists) {
                        $forbidden = new MstMesinProcessingForbiddenGreige();
                        $forbidden->mesin_id = $id;
                        $forbidden->greige_id = $greigeId;
                        $forbidden->save();
                    }
                }
                Yii::$app->session->setFlash('success', 'Greige yang dilarang berhasil ditambahkan.');
                return $this->redirect(['view', 'id' => $id]);
            }
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes a forbidden greige.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteForbiddenGreige($id)
    {
        $forbidden = MstMesinProcessingForbiddenGreige::findOne($id);
        if ($forbidden !== null) {
            $mesinId = $forbidden->mesin_id;
            $forbidden->delete();
            Yii::$app->session->setFlash('success', 'Greige dilarang berhasil dihapus.');
            return $this->redirect(['view', 'id' => $mesinId]);
        }
        return $this->redirect(['index']);
    }

    /**
     * Creates a new MstMesinProcessing model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MstMesinProcessing();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing MstMesinProcessing model.
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
     * Deletes an existing MstMesinProcessing model.
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
     * Finds the MstMesinProcessing model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MstMesinProcessing the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MstMesinProcessing::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
