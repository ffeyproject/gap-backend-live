<?php

namespace backend\controllers;

use Yii;
use common\models\ar\TrnMoColor;
use common\models\ar\TrnMoColorSearch;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnMoColorController implements the CRUD actions for TrnMoColor model.
 */
class TrnMoColorController extends Controller
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
     * Lists all TrnMoColor models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnMoColorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnMoColor model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TrnScAgen model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $moId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionCreate($moId)
    {
        if(Yii::$app->request->isAjax){
            $model = new TrnMoColor(['mo_id'=>$moId]);

            $mo = $model->mo;
            if($mo->status != $mo::STATUS_DRAFT){
                throw new ForbiddenHttpException('Status MO tidak valid, color tidak bisa ditambahkan.');
            }

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $model->sc_greige_id = $mo->sc_greige_id;
                $model->sc_id = $mo->sc_id;
                if($model->save(false)){
                    return $this->asJson(['success' => true]);
                }

                $result = [];
                // The code below comes from ActiveForm::validate(). We do not need to validate the model
                // again, as it was already validated by save(). Just collect the messages.
                foreach ($model->getErrors() as $attribute => $errors) {
                    $result[Html::getInputId($model, $attribute)] = $errors;
                }

                return $this->asJson(['validation' => $result]);
            }

            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }

        throw new ForbiddenHttpException('Not allowed');
    }

    /**
     * Updates an existing TrnScGreige model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if(Yii::$app->request->isAjax){
            $model = $this->findModel($id);

            $mo = $model->mo;
            if($mo->status != $mo::STATUS_DRAFT){
                throw new ForbiddenHttpException('Status MO tidak valid, color tidak bisa dirubah.');
            }

            if ($model->load(Yii::$app->request->post())) {
                if($model->save()){
                    return $this->asJson(['success' => true]);
                    //$model->addError('merek', 'sdfsfsf');
                }

                $result = [];
                // The code below comes from ActiveForm::validate(). We do not need to validate the model
                // again, as it was already validated by save(). Just collect the messages.
                foreach ($model->getErrors() as $attribute => $errors) {
                    $result[Html::getInputId($model, $attribute)] = $errors;
                }

                return $this->asJson(['validation' => $result]);
            }

            return $this->renderAjax('update', [
                'model' => $model,
            ]);
        }

        throw new ForbiddenHttpException('Not allowed');
    }

    /**
     * Deletes an existing TrnScAgen model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $mo = $model->mo;
        if($mo->status != $mo::STATUS_DRAFT){
            throw new ForbiddenHttpException('Status MO tidak valid, color tidak bisa dihapus.');
        }

        $model->delete();

        return $this->redirect(['/trn-mo/view', 'id'=>$model->mo_id]);
    }

    /**
     * Finds the TrnMoColor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnMoColor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnMoColor::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
