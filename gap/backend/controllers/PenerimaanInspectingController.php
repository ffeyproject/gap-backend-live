<?php
namespace backend\controllers;

use backend\models\form\PenerimaanPackingForm;
use common\models\ar\MstGreige;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnInspecting;
use common\models\ar\TrnInspectingSearch;
use common\models\ar\TrnSc;
use common\models\ar\TrnStockGreige;
use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class PenerimaanInspectingController extends Controller
{
    /**
     * Lists all Inspecting models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnInspectingSearch(['status'=>TrnInspecting::STATUS_APPROVED]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Inspecting model.
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
     * Deletes an existing Inspecting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionTerima($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if($model->status != $model::STATUS_APPROVED){
                Yii::$app->session->setFlash('error', 'Status tidak valid.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            $modelPenerimaan = new PenerimaanPackingForm();
            if($modelPenerimaan->load(Yii::$app->request->post())){
                if($modelPenerimaan->validate()){
                    $model->status = $model::STATUS_DELIVERED;
                    $model->delivered_by = Yii::$app->user->id;
                    $model->delivered_at = time();

                    $transaction = Yii::$app->db->beginTransaction();
                    try{
                        if(!$model->save(false, ['status', 'delivered_by', 'delivered_at'])){
                            $transaction->rollBack();
                            throw new HttpException(500, 'Gagal, coba lagi. (1)');
                        }

                        $rollsQty = [];
                        foreach ($model->inspectingItems as $index=>$item){
                            if(!empty($item->join_piece)){
                                if(isset($rollsQty[$item->grade.'_'.$item->join_piece])){
                                    $rollsQty[$item->grade.'_'.$item->join_piece]['qty'] += $item->qty;
                                }else{
                                    $rollsQty[$item->grade.'_'.$item->join_piece] = ['qty'=>$item->qty, 'grade'=>$item->grade];
                                }
                            }else{
                                $rollsQty[] = ['qty'=>$item->qty, 'grade'=>$item->grade];
                            }
                        }

                        foreach ($rollsQty as $rollQty) {
                            if($rollQty['qty'] > 0){
                                $modelStock = new TrnGudangJadi([
                                    'jenis_gudang' => $modelPenerimaan->jenis_gudang,
                                    'wo_id' => $model->wo_id,
                                    'source' => TrnGudangJadi::SOURCE_PACKING,
                                    'source_ref' => $model->no,
                                    'unit' => $model->unit,
                                    'qty' => $rollQty['qty'],
                                    'date' => date('Y-m-d'),
                                    'status' => TrnGudangJadi::STATUS_STOCK,
                                    'note' => 'Dari inspecting dengan nomor '.$model->no,
                                    'color' => $model->kombinasi,
                                    'grade' => $rollQty['grade']
                                ]);

                                if($model->memo_repair_id !== null){
                                    $modelMemoRepair = $model->memoRepair;
                                    $modelStock->no_memo_repair = $modelMemoRepair->no;
                                    $modelStock->note = 'Dari inspecting dengan nomor '.$model->no.' (Repair Retur Buyer '.$modelMemoRepair->returBuyer->customer->name.')';
                                }

                                if(!$modelStock->save(false)){
                                    $transaction->rollBack();
                                    throw new HttpException(500, 'Gagal, coba lagi. (2)');
                                }
                            }
                        }

                        $transaction->commit();
                        return ['success' => true];
                    }catch (\Throwable $t){
                        $transaction->rollBack();
                        throw $t;
                    }
                }

                $result = [];
                // The code below comes from ActiveForm::validate(). We do not need to validate the model
                // again, as it was already validated by save(). Just collect the messages.
                foreach ($modelPenerimaan->getErrors() as $attribute => $errors) {
                    $result[Html::getInputId($modelPenerimaan, $attribute)] = $errors;
                }

                return ['validation' => $result];
            }

            return $this->renderAjax('terima', [
                'model'=>$model,
                'modelPenerimaan'=>$modelPenerimaan
            ]);
        }

        throw new MethodNotAllowedHttpException('Metode tidak diijinkan.');
    }

    /**
     * Sementara tidak perlu ditolak.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionTolak($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $this->findModel($id);

            if($model->status != $model::STATUS_APPROVED){
                Yii::$app->session->setFlash('error', 'Status tidak valid.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            $rejectNote = [
                'date_time' => date('Y-m-d H:i:s'),
                'note'=> Yii::$app->request->post('data')
            ];

            if($model->delivery_reject_note !== null){
                $note = Json::decode($model->delivery_reject_note);
                $note[] = $rejectNote;
            }else{
                $note = [$rejectNote];
            }

            $model->delivered_at = null;
            $model->delivery_reject_note = Json::encode($note);
            $model->status = $model::STATUS_DRAFT;
            $model->approved_by = null;
            $model->approved_at = null;

            if($model->save(false, ['delivered_at', 'delivery_reject_note', 'status', 'approved_by', 'approved_at']) !== false){
                return true;
            }else{
                throw new HttpException(500, 'Gagal menolak, coba lagi.');
            }
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * Finds the Inspecting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnInspecting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnInspecting::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}