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

            $nullAttributes = array_keys(array_filter($model->inspectingItems, function($value) {
                return $value->qr_code === null && $value->is_head == 1;
            }));

            if(count($nullAttributes) > 0){
                Yii::$app->session->setFlash('error', 'Qr code belum di cetak, silahkan generate terlebih dahulu.');
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

                        // ===============================
                        // UPDATE STATUS KARTU PROSES DYEING
                        // ===============================
                        if (!empty($model->kartu_process_dyeing_id)) {
                            $kp = $model->kartuProcessDyeing;
                            if ($kp !== null) {
                                $kp->status = \common\models\ar\TrnKartuProsesDyeing::STATUS_TERIMA_GUDANG_JADI;
                                $kp->updated_at = time();
                                $kp->updated_by = Yii::$app->user->id;
                                $kp->save(false, ['status', 'updated_at', 'updated_by']);
                            }
                        }
                        $rollsQty = [];
                        foreach ($model->getInspectingItems()->orderBy('id ASC')->all() as $index=>$item) {
                            if(!empty($item->join_piece)) {

                                if ($item->grade_up <> NULL) {
                                    if(isset($rollsQty[$item->grade_up.'_'.$item->join_piece])) {
                                        $rollsQty[$item->grade_up.'_'.$item->join_piece]['qty'] += $item->qty_sum;
                                    } else {
                                        $rollsQty[$item->grade_up.'_'.$item->join_piece] = [
                                            'qty'=>$item->qty_sum,
                                            'grade'=>$item->grade_up,
                                            'trans_from' => 'INS',
                                            'id_from' => $item->id,
                                            'qr_code' => $item->qr_code,
                                            'qr_code_desc' => $item->qr_code_desc
                                        ];
                                    }
                                } else {
                                    if(isset($rollsQty[$item->grade.'_'.$item->join_piece])) {
                                        $rollsQty[$item->grade.'_'.$item->join_piece]['qty'] += $item->qty_sum;
                                    } else {
                                        $rollsQty[$item->grade.'_'.$item->join_piece] = [
                                            'qty'=>$item->qty_sum,
                                            'grade'=>$item->grade,
                                            'trans_from' => 'INS',
                                            'id_from' => $item->id,
                                            'qr_code' => $item->qr_code,
                                            'qr_code_desc' => $item->qr_code_desc
                                        ];
                                    }
                                }

                            } else {
                                $rollsQty[] = [
                                    'qty'=>$item->qty_sum,
                                    'grade'=> $item->grade_up <> NULL ? $item->grade_up : $item->grade,
                                    'trans_from' => 'INS',
                                    'id_from' => $item->id,
                                    'qr_code' => $item->qr_code,
                                    'qr_code_desc' => $item->qr_code_desc
                                ];
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
                                    'grade' => $rollQty['grade'],
                                    'trans_from' => $rollQty['trans_from'],
                                    'id_from' => $rollQty['id_from'],
                                    'qr_code' => $rollQty['qr_code'],
                                    'qr_code_desc' => $rollQty['qr_code_desc']
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