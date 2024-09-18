<?php
namespace backend\controllers;

use common\models\ar\MstGreige;
use common\models\ar\TrnKartuProsesPrinting;
use common\models\ar\TrnKartuProsesPrintingSearch;
use common\models\ar\TrnSc;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWo;
use common\models\ar\TrnWoColor;
use Yii;
use yii\helpers\BaseVarDumper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class PenerimaanKartuProsesPrintingController extends Controller
{
    /**
     * Lists all KartuProsesPrinting models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnKartuProsesPrintingSearch(['status'=>TrnKartuProsesPrinting::STATUS_POSTED]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single KartuProsesPrinting model.
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
            foreach ($model->trnKartuProsesPrintingItems as $trnKartuProsesPrintingItem) {
                $stockGreige = $trnKartuProsesPrintingItem->stock->toArray();
                $totalPanjang += $stockGreige['panjang_m'];
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->status = $model::STATUS_DELIVERED;
                $model->delivered_at = time();
                $model->delivered_by = Yii::$app->user->id;
                $model->berat = $nilaiBerat;
                if(!$model->save(false, ['no_proses', 'status', 'delivered_at', 'delivered_by', 'berat'])){
                    $transaction->rollBack();
                    throw new HttpException(500, 'Gagal, coba lagi. (1)');
                }

                $wo = $model->wo;
                $mo = $wo->mo;

                //kembalikan booked greige dan kurangi stock greige
                switch ($mo->jenis_gudang){
                    case TrnStockGreige::JG_WIP:
                        $bookedAttr = 'booked_wip';
                        $stockAttr = 'stock_wip';
                        break;
                    case TrnStockGreige::JG_PFP:
                        $bookedAttr = 'booked_pfp';
                        $stockAttr = 'stock_pfp';
                        break;
                    case TrnStockGreige::JG_EX_FINISH:
                        $bookedAttr = 'booked_ef';
                        $stockAttr = 'stock_ef';
                        break;
                    case TrnStockGreige::JG_FRESH:
                        if($wo->jenis_order === TrnSc::JENIS_ORDER_FRESH_ORDER){
                            Yii::$app->db->createCommand()->update(
                                MstGreige::tableName(),
                                [
                                    'stock' => new \yii\db\Expression('stock - ' . $totalPanjang),
                                    'booked' => new \yii\db\Expression( 'booked + ' . $totalPanjang)
                                ],
                                ['id'=>$wo->greige_id]
                            )->execute();

                            $transaction->commit();
                            return true;
                        }

                        $bookedAttr = 'booked';
                        $stockAttr = 'booked';
                        break;
                    default:
                        $bookedAttr = 'booked';
                        $stockAttr = 'stock';
                }
                Yii::$app->db->createCommand()->update(
                    MstGreige::tableName(),
                    [
                        $bookedAttr => new \yii\db\Expression($bookedAttr . ' - ' . $totalPanjang),
                        $stockAttr => new \yii\db\Expression($stockAttr . ' - ' . $totalPanjang)
                    ],
                    ['id'=>$wo->greige_id]
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
                $totalPanjang = 0;
                foreach ($model->trnKartuProsesPrintingItems as $trnKartuProsesPrintingItems) {
                    $stockItem = $trnKartuProsesPrintingItems->stock;
                    $stockItem->status = $stockItem::STATUS_VALID;
                    if(!$flag = $stockItem->save(false, ['status'])){
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal, coba lagi.');
                    }

                    $totalPanjang += $trnKartuProsesPrintingItems->panjang_m;
                }

                $model->status = $model::STATUS_DRAFT;
                $model->reject_notes = Json::encode($catatanPenolakan);
                if(!$flag = $model->save(false, ['status', 'reject_notes'])){
                    $transaction->rollBack();
                    throw new HttpException(500, 'Gagal, coba lagi.');
                }

                //Kembalikan status kartu_proses_id jika ada
                if($model->kartu_proses_id !== null){
                    $connection = $model::getDb();
                    $res = $connection->createCommand()->update(TrnKartuProsesPrinting::tableName(), ['status' => TrnKartuProsesPrinting::STATUS_GANTI_GREIGE], ['id'=>$model->kartu_proses_id])->execute();
                    if(!$flag = $res > 0){
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal, coba lagi.');
                    }
                }
                //Kembalikan status kartu_proses_id jika ada

                $wo = $model->wo;
                $mo = $wo->mo;

                //kembalikan booked greige
                switch ($mo->jenis_gudang){
                    case TrnStockGreige::JG_WIP:
                        $bookedAttr = 'booked_wip';
                        break;
                    case TrnStockGreige::JG_PFP:
                        $bookedAttr = 'booked_pfp';
                        break;
                    case TrnStockGreige::JG_EX_FINISH:
                        $bookedAttr = 'booked_ef';
                        break;
                    default:
                        $bookedAttr = 'booked';
                }
                $greigeId = $wo->greige_id;
                $cmdSql= "UPDATE mst_greige SET {$bookedAttr} = {$bookedAttr} - {$totalPanjang} WHERE id=:id";
                $command = Yii::$app->db->createCommand($cmdSql)->bindParam(':id', $greigeId);
                if(!$flag = $command->execute() > 0){
                    $transaction->rollBack();
                    throw new HttpException(500, 'Gagal, coba lagi.');
                }
                //kembalikan booked greige

                if($flag){
                    $transaction->commit();
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
     * @param $id
     * @return bool
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionGantiWarna($id)
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

            $model->wo_color_id = $post;
            $model->save(false, ['wo_color_id']);

            return true;
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
    public function actionGantiWo($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if($model->status != $model::STATUS_POSTED){
                throw new ForbiddenHttpException('Kartu proses tidak valid, tidak bisa diproses.');
            }

            $nomorWo = Yii::$app->request->post('data');
            if(empty($nomorWo)){
                throw new ForbiddenHttpException('Nomor WO kosong, tidak bisa diproses.');
            }

            $wo = TrnWo::findOne(['no'=>$nomorWo]);

            if($wo === null){
                throw new NotFoundHttpException('WO dengan nomor yang dimasukan tidak ditemukan.');
            }

            $model->wo_id = $wo->id;
            $model->wo_color_id = TrnWoColor::find()->select('id')->where(['wo_id'=>$wo->id])->asArray()->one()['id'];
            $model->mo_id = $wo->mo_id;
            $model->sc_id = $wo->sc_id;
            $model->save(false, ['wo_id','wo_color_id', 'mo_id', 'sc_id']);

            return true;
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * Finds the KartuProsesPrinting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnKartuProsesPrinting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnKartuProsesPrinting::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}