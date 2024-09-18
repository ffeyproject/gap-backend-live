<?php
namespace backend\controllers;

use common\models\ar\TrnOrderPfp;
use common\models\ar\TrnOrderPfpSearch;
use common\models\ar\MstGreige;
use common\models\ar\TrnStockGreige;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ApprovalOrderPfpController extends Controller
{
    /**
     * Lists all TrnOrderPfp models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnOrderPfpSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['trn_order_pfp.status'=>TrnOrderPfp::STATUS_POSTED]);
        $dataProvider->query->andWhere(['trn_order_pfp.approved_by'=>Yii::$app->user->id]);

        Yii::$app->session->setFlash('info', 'Hanya menampilkan data yang perlu disetujui oleh user yang sedang login. ');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnOrderPfp model.
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
     * @param $id
     * @return bool
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionReject($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if($model->status != $model::STATUS_POSTED){
                throw new ForbiddenHttpException('Status dokumen ini tidak valid, penolakan tidak bisa diproses.');
            }

            if(Yii::$app->user->id != $model->approved_by){
                throw new ForbiddenHttpException('Anda tidak berwenang untuk melakukan persetujuan ini, hanya bisa dilakukan oleh user yang ditunjuk ketika pembuatan dokumen.');
            }

            $rejectNote = [
                'date_time' => date('Y-m-d H:i:s'),
                'note'=> Yii::$app->request->post('data')
            ];

            if($model->approval_note !== null){
                $note = Json::decode($model->approval_note);
                $note[] = $rejectNote;
            }else{
                $note = [$rejectNote];
            }

            $model->approved_at = null;
            $model->status = $model::STATUS_DRAFT;
            $model->approval_note = Json::encode($note);

            if($model->save(false, ['posted_at', 'approved_at', 'status', 'approval_note']) !== false){
                return true;
            }else{
                throw new HttpException(500, 'Gagal menolak Order PFP.');
            }
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * Deletes an existing KartuProsesPfp model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionApprove($id)
    {
        $model = $this->findModel($id);
        if($model->status != $model::STATUS_POSTED){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk disetujui.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if($model->approved_by != Yii::$app->user->id){
            Yii::$app->session->setFlash('error', 'Anda bukan user yang ditunjuk untuk menyetujui dokumen ini.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->status = $model::STATUS_APPROVED;
        $model->approved_at = time();
        $model->setNomor();
        $model->save(false, ['status', 'no_urut', 'no', 'approved_at']);

        $greige = $model->greige;
        $greigeGroup = $greige->group;
        $totalColorsBatch = $model->qty;
        $totalColorsMeter = $totalColorsBatch * ($greigeGroup->qty_per_batch);

        switch ($model->jenis_gudang){

            case TrnStockGreige::JG_PFP:
                if(($greige->stock_pfp - $greige->booked_pfp) < $totalColorsMeter){
                    throw new ForbiddenHttpException('Persediaan digudang greige tidak mencukupi.');
                }
                $availableAttr = 'available_pfp';
                break;
            case TrnStockGreige::JG_FRESH:
                if($greige->available < $totalColorsMeter){
                    throw new ForbiddenHttpException('Persediaan digudang greige tidak mencukupi.');
                }
                $availableAttr = 'available';
                break;
            default:
                $availableAttr = 'available';
                // jenis gudang lainnya
                if(($greige->stock - $greige->booked) < $totalColorsMeter){
                    throw new ForbiddenHttpException('Persediaan digudang greige tidak mencukupi.');
                }
        }

        if($model->status == $model::STATUS_APPROVED){
            // lakukan perubahan data di mst_greige hanya jika Order PFP sudah di approve oleh supervisor
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if(!$model->save(false)){
                    $transaction->rollBack();
                    throw new HttpException(500, 'Gagal menyimpan data, coba lagi.');
                }

                Yii::$app->db->createCommand()->update(
                    MstGreige::tableName(),
                    [    
                        $availableAttr => new \yii\db\Expression($availableAttr . ' - ' . $totalColorsMeter),
                        'booked_opfp' => new \yii\db\Expression('booked_opfp + ' . $totalColorsMeter)
                    ],
                    ['id'=>$greige->id]
                )->execute();

                $transaction->commit();
            }catch (\Throwable $t){
                $transaction->rollBack();
                throw $t;
            }
        }



        

        Yii::$app->session->setFlash('success', 'Order PFP berhasil disetujui.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Finds the TrnOrderPfp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TrnOrderPfp the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnOrderPfp::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}