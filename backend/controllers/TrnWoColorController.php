<?php

namespace backend\controllers;

use common\models\ar\TrnMo;
use common\models\ar\TrnWo;
use Yii;
use common\models\ar\TrnWoColor;
use common\models\ar\TrnWoColorSearch;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnWoColorController implements the CRUD actions for TrnWoColor model.
 */
class TrnWoColorController extends Controller
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
     * Lists all TrnWoColor models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnWoColorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnWoColor model.
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
     * Creates a new TrnWoColor model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $woId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionCreate($woId)
    {
        if(Yii::$app->request->isAjax){
            $model = new TrnWoColor(['wo_id' => $woId, 'note'=>'-']);

            $wo = $model->wo;
            if($wo->status != $wo::STATUS_DRAFT){
                throw new ForbiddenHttpException('Status WO tidak valid, color tidak bisa ditambahkan.');
            }

            $wo = $model->wo;
            $model->greige_id = $wo->greige_id;
            $model->mo_id = $wo->mo_id;
            $model->sc_greige_id = $wo->sc_greige_id;
            $model->sc_id = $wo->sc_id;

            //$woColorsMoColIds = $wo->getTrnWoColors()->select(['mo_color_id'])->asArray()->all();
            //$woColorsMoColIdsCol = ArrayHelper::getColumn($woColorsMoColIds, 'mo_color_id');

            $moColors = $model->mo->getTrnMoColors()->asArray()->all();
            $colors = ArrayHelper::map($moColors, 'id', 'color');
            if(empty($colors)){
                throw new ForbiddenHttpException('Semua color pada MO sudah diinput dalam WO ini, color tidak bisa ditambahkan lagi.');
            }

            if ($model->load(Yii::$app->request->post())) {
                if($model->validate()){
                    // wo_color tidak boleh lebih banyak daripada qty mo_color
                    if($model->qty > $model->moColor->qty){
                        $model->addError('qty', 'Qty tidak valid, terlalu besar.');
                    }else{
                        // hitung berapa banyak color yang sudah dipakai di wo ini dan wo lain, jika sudah terpakai semua, gagalkan. wo batal tidak dihitung.
                        $moColorSudahDigunakan = (new Query())->from(TrnWoColor::tableName())
                            ->innerJoin(TrnWo::tableName(), 'trn_wo.id = trn_wo_color.wo_id')
                            ->where(['mo_color_id'=>$model->mo_color_id])
                            ->andWhere(['<>', 'trn_wo.status', TrnWo::STATUS_BATAL])
                            ->sum('qty')
                        ;
                        $moColorTotal = $moColorSudahDigunakan === null ? $model->qty : $moColorSudahDigunakan + $model->qty;
                        if($moColorTotal > $model->moColor->qty){
                            $model->addError('qty', 'Qty tidak valid, sudah melebihi. Mo Color: '.$model->moColor->qty. ', Sudah digunakan: '.$moColorSudahDigunakan);
                        }else{
                            $model->save(false);
                            return $this->asJson(['success' => true]);
                        }
                    }
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
                'colors' => $colors,
            ]);
        }

        throw new ForbiddenHttpException('Not allowed');
    }

    /**
     * Updates an existing TrnWoColor model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if(Yii::$app->request->isAjax){
            $model = $this->findModel($id);

            if ($model->load(Yii::$app->request->post())) {
                if($model->validate()){
                    // wo_color tidak boleh lebih banyak daripada qty mo_color
                    if($model->qty > $model->moColor->qty){
                        $model->addError('qty', 'Qty tidak valid, terlalu besar.');
                    }else{
                        // hitung berapa banyak color yang sudah dipakai di wo ini dan wo lain, jika sudah terpakai semua, gagalkan. wo batal tidak dihitung.
                        $moColorSudahDigunakan = (new Query())->from(TrnWoColor::tableName())
                            ->innerJoin(TrnWo::tableName(), 'trn_wo.id = trn_wo_color.wo_id')
                            ->where(['mo_color_id'=>$model->mo_color_id])
                            ->andWhere(['<>', 'trn_wo.status', TrnWo::STATUS_BATAL])
                            ->sum('qty')
                        ;
                        $moColorTotal = $moColorSudahDigunakan === null ? $model->qty : $moColorSudahDigunakan + $model->qty;
                        if($moColorTotal > $model->moColor->qty){
                            $model->addError('qty', 'Qty tidak valid, sudah melebihi. Mo Color: '.$model->moColor->qty. ', Sudah digunakan: '.$moColorSudahDigunakan);
                        }else{
                            $model->save(false);
                            return $this->asJson(['success' => true]);
                        }
                    }
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
     * Deletes an existing TrnWoColor model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $wo = $model->wo;
        if($wo->status != $wo::STATUS_DRAFT){
            throw new ForbiddenHttpException('Status WO tidak valid, color tidak bisa dihapus.');
        }

        $model->delete();

        return $this->redirect(['/trn-wo/view', 'id'=>$model->wo_id]);
    }

    /**
     * Finds the TrnWoColor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnWoColor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnWoColor::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
