<?php

namespace backend\controllers;

use common\models\ar\JualExFinishItem;
use common\models\ar\MutasiExFinishAltItem;
use common\models\ar\PfpKeluarVerpackingItem;
use Yii;
use common\models\ar\JualExFinish;
use common\models\ar\JualExFinishSearch;
use yii\base\BaseObject;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * JualExFinishController implements the CRUD actions for JualExFinish model.
 */
class JualExFinishController extends Controller
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
     * Lists all JualExFinish models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new JualExFinishSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single JualExFinish model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $model->jualExFinishItems,
            'pagination' => false,
            'sort' => false,
        ]);

        return $this->render('view', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Creates a new JualExFinish model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws \Throwable
     * @throws yii\base\InvalidConfigException
     * @throws yii\db\Exception
     */
    public function actionCreate()
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = new JualExFinish(['jenis_gudang'=>JualExFinish::JENIS_GUDANG_EX_GD_JADI]);

            $data = Yii::$app->request->post();

            $model->load([$model->formName()=>$data['header']]);

            if($model->validate()){
                $model->setNomor();

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $model->save(false);

                    foreach ($data['items'] as $item) {
                        $ids[] = $item['id'];

                        //insert data ke tabel jual_ex_finish_item
                        // INSERT (table name, column values)
                        Yii::$app->db->createCommand()->insert(JualExFinishItem::tableName(), [
                            'jual_id' => $model->id,
                            'no_wo' => $item['no_wo'],
                            'greige_id' => $item['greige_id'],
                            'grade' => $item['grade'],
                            'qty' => $item['qty'],
                            'unit' => $item['unit'],
                        ])->execute();

                        // UPDATE status table mutasi_ex_finish_alt_item menjadi dijual
                        Yii::$app->db->createCommand()->update(MutasiExFinishAltItem::tableName(), ['status' => MutasiExFinishAltItem::STATUS_DIJUAL], ['id'=>$item['id']])->execute();
                    }

                    $transaction->commit();
                    return true;
                }catch (\Throwable $t){
                    $transaction->rollBack();
                    throw $t;
                }
            }

            throw new HttpException(422, Json::encode($model->errors));
        }

        throw new ForbiddenHttpException('Only ajax allowed');
    }

    /**
     * Creates a new JualExFinish model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws \Throwable
     * @throws yii\base\InvalidConfigException
     * @throws yii\db\Exception
     */
    public function actionCreatePfpKeluarVerpacking()
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = new JualExFinish(['jenis_gudang'=>JualExFinish::JENIS_GUDANG_EX_GD_JADI]);

            $data = Yii::$app->request->post();

            $model->load([$model->formName()=>$data['header']]);

            if($model->validate()){
                $model->setNomor();

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $model->save(false);

                    $ids = [];
                    foreach ($data['items'] as $item) {
                        $ids[] = $item['id'];

                        //insert data ke tabel jual_ex_finish_item
                        // INSERT (table name, column values)
                        Yii::$app->db->createCommand()->insert(JualExFinishItem::tableName(), [
                            'jual_id' => $model->id,
                            'no_wo' => $item['no_wo'],
                            'greige_id' => $item['greige_id'],
                            'grade' => $item['grade'],
                            'qty' => $item['qty'],
                            'unit' => $item['unit'],
                        ])->execute();
                    }

                    if(count($ids) > 0){
                        // UPDATE status table mutasi_ex_finish_alt_item menjadi dijual
                        Yii::$app->db->createCommand()->update(PfpKeluarVerpackingItem::tableName(), ['status' => PfpKeluarVerpackingItem::STATUS_DIJUAL], ['id'=>$ids])->execute();
                    }

                    $transaction->commit();
                    return true;
                }catch (\Throwable $t){
                    $transaction->rollBack();
                    throw $t;
                }
            }

            throw new HttpException(422, Json::encode($model->errors));
        }

        throw new ForbiddenHttpException('Only ajax allowed');
    }

    /**
     * Updates an existing JualExFinish model.
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
     * Deletes an existing JualExFinish model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            JualExFinishItem::deleteAll(['jual_id'=>$model->id]);
            $model->delete();

            $transaction->commit();
        }catch (\Throwable $t){
            $transaction->rollBack();
            throw $t;
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the JualExFinish model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return JualExFinish the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = JualExFinish::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
