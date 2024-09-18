<?php

namespace backend\controllers;

use common\models\ar\InspectingItem;
use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class InspectingItemController extends Controller
{
    /**
     * Updates an existing TrnKirimBuyerHeader model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionEditNote($id){
        if(Yii::$app->request->isAjax){
            $model = $this->findModel($id);

            Yii::$app->response->format = Response::FORMAT_JSON;

            $model->note = Yii::$app->request->post('formData');
            $model->save(false, ['note']);

            return true;
        }

        throw new MethodNotAllowedHttpException('Metode tidak diizinkan.');
    }

    /**
     * Finds the InspectingItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InspectingItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InspectingItem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}