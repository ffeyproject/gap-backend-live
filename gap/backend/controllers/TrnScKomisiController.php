<?php

namespace backend\controllers;

use Yii;
use common\models\ar\TrnScKomisi;
use common\models\ar\TrnScKomisiSearch;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnScKomisiController implements the CRUD actions for TrnScKomisi model.
 */
class TrnScKomisiController extends Controller
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
     * Lists all TrnScKomisi models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnScKomisiSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnScKomisi model.
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
     * @param $scId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionCreate($scId)
    {
        if(Yii::$app->request->isAjax){
            $model = new TrnScKomisi(['sc_id'=>$scId]);

            if ($model->load(Yii::$app->request->post())) {
                $sc = $model->sc;

                if($sc->status != $sc::STATUS_DRAFT){
                    throw new ForbiddenHttpException('SC bukan draft, komisi tidak bisa ditambahkan.');
                }

                if($model->validate()){
                    $model->save(false);
                    return $this->asJson(['success' => true]);
                    //$model->addError('merek','fsdfssdsf');
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

            $sc = $model->sc;

            if($sc->status != $sc::STATUS_DRAFT){
                throw new ForbiddenHttpException('SC bukan draft, komisi tidak bisa dirubah.');
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

        $sc = $model->sc;
        if($sc->status != $sc::STATUS_DRAFT){
            throw new ForbiddenHttpException('SC bukan draft, agen tidak bisa dihapus.');
        }

        $model->delete();

        return $this->redirect(['/trn-sc/view', 'id'=>$model->sc_id]);
    }

    /**
     * Finds the TrnScKomisi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnScKomisi the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnScKomisi::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
