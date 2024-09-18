<?php
namespace backend\controllers;

use common\models\ar\MstGreige;
use common\models\ar\TrnKartuProsesPfp;
use common\models\ar\TrnKartuProsesPfpSearch;
use common\models\ar\TrnStockGreige;
use Yii;
use yii\helpers\BaseVarDumper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class PenerimaanKartuProsesPfpController extends Controller
{
    /**
     * Lists all KartuProsesPfp models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnKartuProsesPfpSearch(['status'=>TrnKartuProsesPfp::STATUS_POSTED]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single KartuProsesPfp model.
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
     * ......
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function actionTerima($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $nilaiBerat = Yii::$app->request->post('data');
            if(empty($nilaiBerat)){
                throw new ForbiddenHttpException('Berat tidak boleh kosong, tidak bisa diproses.');
            }

            $model = $this->findModel($id);

            if($model->status != $model::STATUS_POSTED){
                throw new HttpException(500, 'Kartu proses tidak valid, tidak bisa diproses.');
            }

            $totalPanjang = 0;
            foreach ($model->trnKartuProsesPfpItems as $trnKartuProsesPfpItem) {
                $stockGreige = $trnKartuProsesPfpItem->stock->toArray();
                $totalPanjang += $stockGreige['panjang_m'];
            }

            //BaseVarDumper::dump($totalPanjang, 10, true);Yii::$app->end();

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->status = $model::STATUS_DELIVERED;
                $model->delivered_at = time();
                $model->delivered_by = Yii::$app->user->id;
                $model->berat = $nilaiBerat;
                $model->setNomorProses();
                if(!$model->save(false, ['no_proses', 'status', 'delivered_at', 'delivered_by', 'berat'])){
                    $transaction->rollBack();
                    throw new HttpException(500, 'Gagal, coba lagi. (1)');
                }

                switch ($model->orderPfp->jenis_gudang){

                    case TrnStockGreige::JG_PFP:
                        $stockAttr = 'stock_pfp';
                        break;
                    case TrnStockGreige::JG_FRESH:
                        $stockAttr = 'stock';
                        break;
                    default:
                        $stockAttr = 'stock';
                }
                //kembalikan booked greige dan kurangi stock greige
                Yii::$app->db->createCommand()->update(
                    MstGreige::tableName(),
                    [
                        'booked' => new \yii\db\Expression('booked - ' . $totalPanjang),
                        $stockAttr => new \yii\db\Expression($stockAttr . ' - ' . $totalPanjang)

                    ],
                    ['id'=>$model->greige_id]
                )->execute();
                //kembalikan booked greige dan kurangi stock greige

                $transaction->commit();
                return true;
            }catch (\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * @param $id
     * @return bool
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionTolak($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if($model->status != $model::STATUS_POSTED){
                throw new ForbiddenHttpException('Kartu proses tidak valid, tidak bisa diproses.');
            }

            $post = Yii::$app->request->post('data');
            if(empty($post)){
                throw new ForbiddenHttpException('Keterangan kosong, tidak bisa diproses.');
            }

            $catatanPenolakan = Json::decode($model->reject_notes);
            $catatanPenolakan[] = [
                'time' => time(),
                'note'=> $post,
                'by'=>Yii::$app->user->id
            ];

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $totalItem = 0;
                $totalLenngth = 0;

                foreach ($model->trnKartuProsesPfpItems as $trnKartuProsesPfpItem) {
                    $stockItem = $trnKartuProsesPfpItem->stock;
                    $stockItem->status = $stockItem::STATUS_VALID;
                    if(!$flag = $stockItem->save(false, ['status'])){
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal, coba lagi.');
                    }

                    $totalItem++;
                    $totalLenngth += $trnKartuProsesPfpItem->panjang_m;
                }

                $model->status = $model::STATUS_DRAFT;
                $model->reject_notes = Json::encode($catatanPenolakan);
                if(!$flag = $model->save(false, ['status', 'reject_notes'])){
                    $transaction->rollBack();
                    throw new HttpException(500, 'Gagal, coba lagi.');
                }

                $qtyBatch = $model->greigeGroup->qty_per_batch;
                //menghitung selisih antaara total panjang dan qty per batch
                $difference = 0;
    
                if ($totalLenngth > $qtyBatch) {
                    $difference = abs($totalLenngth - $qtyBatch);
                }
                switch ($model->orderPfp->jenis_gudang){

                    case TrnStockGreige::JG_PFP:
                        $availableAttr = 'available_pfp';
                        break;
                    case TrnStockGreige::JG_FRESH:
                        $availableAttr = 'available';
                        break;
                    default:
                        $availableAttr = 'available';
                }
                //kembalikan booked greige
                $greigeId = $model->greige_id;
                $cmdSql= "UPDATE mst_greige SET 
                booked = booked - {$totalLenngth},
                booked_opfp = booked_opfp + ({$totalLenngth} - {$difference}),
                $availableAttr = $availableAttr + {$difference}
                 WHERE id=:id";
                $command = Yii::$app->db->createCommand($cmdSql)->bindParam(':id', $greigeId);
                if(!$flag = $command->execute() > 0){
                    $transaction->rollBack();
                    throw new HttpException(500, 'Gagal, coba lagi.');
                }
                //kembalikan booked greige

                if($flag){
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Kartu Proses PFP telah ditolak.');
                    return true;
                }
            }catch (\Throwable $e) {
                $transaction->rollBack();
                throw new HttpException(500, $e->getMessage());
            }
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * Finds the KartuProsesPfp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnKartuProsesPfp the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnKartuProsesPfp::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}