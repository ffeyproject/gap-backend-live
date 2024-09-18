<?php

namespace backend\controllers;

use backend\models\search\LaporanGreigeKeluar;
use common\models\ar\TrnGreigeKeluarItem;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnStockGreigeSearch;
use Yii;
use common\models\ar\TrnGreigeKeluar;
use common\models\ar\TrnGreigeKeluarSearch;
use common\models\ar\TrnWo;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TrnGreigeKeluarMakloonController implements the CRUD actions for TrnGreigeKeluar model.
 */
class TrnGreigeKeluarMakloonController extends Controller
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
     * Lists all TrnGreigeKeluarItem models.
     * @return mixed
     */
    public function actionRekap()
    {
        $searchModel = new LaporanGreigeKeluar();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('rekap', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all TrnGreigeKeluar models.
     * @return mixed
     */
    public function actionIndex()
    {
        $info = 'Fitur untuk mengeluarkan greige khusus untuk <b>Order Makloon</b> dari gudang diluar keperluan processing, misalnya untuk sample.';
        Yii::$app->session->setFlash('info', $info);

        $searchModel = new TrnGreigeKeluarSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (!empty($dataProvider)) {
            $dataProvider->query->andFilterWhere(['jenis' => TrnGreigeKeluar::JENIS_MAKLOON]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnGreigeKeluar model.
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
     * Creates a new TrnGreigeKeluar model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws HttpException
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {   
        $searchModel = new TrnStockGreigeSearch([
            'jenis_gudang'=>TrnStockGreige::JG_FRESH,
            'status'=> 0,
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $model = new TrnGreigeKeluar(['date'=>date('Y-m-d')]);

        $model->jenis = TrnGreigeKeluar::JENIS_MAKLOON;

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $transaction = Yii::$app->db->beginTransaction();

                try {
                    $wo = TrnWo::findOne($model->wo_id);
                    if($wo->totalPanjangGreigeKeluar > 0){
                        // Jika ada WO yang sama, cek total Greige yang sudah keluar pada WO yang dipilih
                        $totalGreigeKeluarForThisWos = $wo->totalPanjangGreigeKeluar ? $wo->totalPanjangGreigeKeluar : 0;

                        // Ambil total Greige pada WO
                        $trnWoColorQty = $wo->colorQtyBatchToMeter;

                        //cek total Greige yang akan keluar pada request
                        $currentGreigeKeluarTotalPanjang = 0;
                        foreach (Json::decode(Yii::$app->request->post('items')) as $item) {
                            $a  = TrnStockGreige::findOne($item['id']);
                            $currentGreigeKeluarTotalPanjang += $a->panjang_m;
                        }

                        if(($totalGreigeKeluarForThisWos + $currentGreigeKeluarTotalPanjang) > $trnWoColorQty){
                            //Jika jumlah antara total Greige yang akan keluar dan total Greige yang sudah keluar melebihi total Greige pada WO maka sistem menolak
                            $transaction->rollBack();
                            throw new ForbiddenHttpException('Tidak dapat menambahkan Greige Keluar dengan Nomor WO ini, Greige Keluar Sudah melebihi order pada Working Order. <br> <br> Total Order Greige WO = <b>'.$trnWoColorQty.'</b> <br> Total Greige Yang Sudah Keluar = <b>'.$totalGreigeKeluarForThisWos.' (+'.$currentGreigeKeluarTotalPanjang.')</b>');
                        }
                    }

                    if ($model->save(false)) {
                        foreach (Json::decode(Yii::$app->request->post('items')) as $item) {
                            $modelItem = new TrnGreigeKeluarItem([
                                'greige_keluar_id' => $model->id,
                                'stock_greige_id' => $item['id'],
                            ]);
                            if (!$modelItem->save(false)) {
                                $transaction->rollBack();
                                throw new HttpException(500, 'Gagal, coba lagi. (2)');
                            }
                        }
                    }else{
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal, coba lagi. (1)');
                    }

                    $transaction->commit();
                    return ['success'=>true, 'redirect'=>Url::to(['view', 'id'=>$model->id])];
                } catch (\Throwable $t) {
                    $transaction->rollBack();
                    throw $t;
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing TrnPotongGreige model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if($model->status != $model::STATUS_DRAFT){
            throw new ForbiddenHttpException('Status tidak valid. Tidak bisa dirubah.');
        }

        $searchModel = new TrnStockGreigeSearch([
            'jenis_gudang'=>TrnStockGreige::JG_FRESH,
            'status'=>TrnStockGreige::STATUS_VALID
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $wo_id = $model->wo_id;

        $greige = TrnWo::findOne($wo_id)->greige->id;

        $dataProvider->query->andFilterWhere(['greige_id' => $greige]);

        $modelsItem = $model->trnGreigeKeluarItems;

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $wo = TrnWo::findOne($model->wo_id);
                    if($wo->totalPanjangGreigeKeluar > 0){
                        // Jika ada WO yang sama, cek total Greige yang sudah keluar pada WO yang dipilih
                        $totalGreigeKeluarForThisWos = $wo->totalPanjangGreigeKeluar ? $wo->totalPanjangGreigeKeluar : 0;

                        // Ambil total Greige pada WO
                        $trnWoColorQty = $wo->colorQtyBatchToMeter;

                        //cek total Greige yang akan keluar pada request
                        $currentGreigeKeluarTotalPanjang = 0;
                        foreach (Json::decode(Yii::$app->request->post('items')) as $item) {
                            $a  = TrnStockGreige::findOne($item['id']);
                            $currentGreigeKeluarTotalPanjang += $a->panjang_m;
                        }

                        if(($totalGreigeKeluarForThisWos + $currentGreigeKeluarTotalPanjang) > $trnWoColorQty){
                            //Jika jumlah antara total Greige yang akan keluar dan total Greige yang sudah keluar melebihi total Greige pada WO maka sistem menolak
                            $transaction->rollBack();
                            throw new ForbiddenHttpException('Tidak dapat menambahkan Greige Keluar dengan Nomor WO ini, Greige Keluar Sudah melebihi order pada Working Order. <br> <br> Total Order Greige WO = <b>'.$trnWoColorQty.'</b> <br> Total Greige Yang Sudah Keluar = <b>'.$totalGreigeKeluarForThisWos.' (+'.$currentGreigeKeluarTotalPanjang.')</b>');
                        }
                    }
                    
                    if (!($flag = $model->save(false))) {
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal, coba lagi. (1)');
                    }

                    TrnGreigeKeluarItem::deleteAll(['greige_keluar_id'=>$model->id]);

                    foreach (Json::decode(Yii::$app->request->post('items')) as $item) {
                        $modelItem = new TrnGreigeKeluarItem([
                            'greige_keluar_id' => $model->id,
                            'stock_greige_id' => $item['id'],
                        ]);
                        $modelItem->greige_keluar_id = $model->id;
                        if (! ($flag = $modelItem->save(false))) {
                            $transaction->rollBack();
                            throw new HttpException(500, 'Gagal, coba lagi. (2)');
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        return ['success'=>true, 'redirect'=>Url::to(['view', 'id'=>$model->id])];
                    }
                }catch (\Throwable $t) {
                    $transaction->rollBack();
                    throw $t;
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'modelsItem' => $modelsItem
        ]);
    }

    /**
     * Deletes an existing TrnGreigeKeluar model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model->status != $model::STATUS_DRAFT){
            throw new ForbiddenHttpException('Status tidak valid. Tidak bisa dihapus.');
        }

        TrnGreigeKeluarItem::deleteAll(['greige_keluar_id'=>$model->id]);

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing TrnPotongGreige model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPosting($id)
    {
        $model = $this->findModel($id);
        if($model->status != $model::STATUS_DRAFT){
            throw new ForbiddenHttpException('Status tidak valid. Tidak bisa diposting.');
        }

        //bypass langsung approve
        $model->posted_at = time();
        $model->status = $model::STATUS_APPROVED;//$model::STATUS_POSTED;
        $model->approved_at = time();
        $model->approved_by = $model->approved_by === null ? Yii::$app->user->id : $model->approved_by;
        $model->setNomor();

        $wo = $model->wo;
        $greige = $wo->greige;
        $greigeGroup = $wo->scGreige->greigeGroup;
        $totalColorsBatch = $wo->colorQty;
        $totalColorsMeter = $totalColorsBatch * ($greigeGroup->qty_per_batch);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(!$model->save(false, ['posted_at', 'status', 'approved_at', 'approved_by', 'no_urut', 'no'])){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal menyimpan, coba lagi.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            $greigesKeluar = [];
            foreach ($model->trnGreigeKeluarItems as $trnGreigeKeluarItem) {
                $model::getDb()->createCommand()->update(
                    TrnStockGreige::tableName(),
                    ['status'=>TrnStockGreige::STATUS_KELUAR_GUDANG],
                    ['id'=>$trnGreigeKeluarItem->stock_greige_id]
                )->execute();

                $stockGreige = $trnGreigeKeluarItem->stockGreige;
                $greige = $stockGreige->greige;
                $fieldToUpdate = '';
                switch ($stockGreige->jenis_gudang){
                    case TrnStockGreige::JG_FRESH:
                        $fieldToUpdate = 'stock';
                        break;
                    case TrnStockGreige::JG_PFP:
                        $fieldToUpdate = 'stock_pfp';
                        break;
                    case TrnStockGreige::JG_WIP:
                        $fieldToUpdate = 'stock_wip';
                        break;
                    case TrnStockGreige::JG_EX_FINISH:
                        $fieldToUpdate = 'stock_ex_finish';
                        break;
                }

                if(isset($greigesKeluar[$greige->id][$fieldToUpdate])){
                    $greigesKeluar[$greige->id][$fieldToUpdate] += $stockGreige->panjang_m;
                }else{
                    $greigesKeluar[$greige->id][$fieldToUpdate] = $stockGreige->panjang_m;
                }
            }

            foreach ($greigesKeluar as $greigeId=>$greigeKeluar) {
                $fields = [];
                foreach ($greigeKeluar as $key=>$value) {
                    $fields[] = $key.' = '.$key.' - '.$value; //"stock = stock - 2500"

                    //deklarasi variable selisih
                    $difference = 0;
                    //hitung selisih antara greige keluar dengan greige yang di order pada
                    if ($value > $totalColorsMeter) {
                        $difference = abs($totalColorsMeter - $value);
                    }

                    if($key === 'stock'){
                        $fields[] = "available = available - {$difference}";
                        $fields[] = "booked_wo = booked_wo - {$value}+{$difference}";
                    }
                }
                $fieldStr = implode(', ', $fields);
                Yii::$app->db->createCommand('UPDATE mst_greige SET '.$fieldStr.' WHERE id='.$greigeId)->execute();
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Posting berhasil.');
            return $this->redirect(['view', 'id' => $model->id]);
        }catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    /**
     * Finds the TrnGreigeKeluar model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnGreigeKeluar the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnGreigeKeluar::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Action for looking up stock greige.
     *
     * This action searches for stock greige based on the given parameters and renders the results.
     * If the request is an AJAX request, it also filters the results based on the `wo_id` parameter.
     *
     * @return string|Response The rendered view or AJAX response.
     */
    public function actionLookupStockGreige()
    {   
        $searchModel = new TrnStockGreigeSearch([
            'jenis_gudang'=>TrnStockGreige::JG_FRESH,
            'status'=>TrnStockGreige::STATUS_VALID
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $model = new TrnGreigeKeluar(['date'=>date('Y-m-d')]);

        $model->jenis = TrnGreigeKeluar::JENIS_MAKLOON;

        if(Yii::$app->request->isAjax){   
            $wo_id = Yii::$app->request->get('wo_id');

            $greige = TrnWo::findOne($wo_id)->greige->id;

            $dataProvider->query->andFilterWhere(['greige_id' => $greige]);
    
            return $this->renderAjax('_stock-greige-grid', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }

        return $this->render('create', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
