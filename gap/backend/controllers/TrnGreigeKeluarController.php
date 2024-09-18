<?php

namespace backend\controllers;

use backend\models\search\LaporanGreigeKeluar;
use common\models\ar\TrnGreigeKeluarItem;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnStockGreigeSearch;
use Yii;
use common\models\ar\TrnGreigeKeluar;
use common\models\ar\TrnGreigeKeluarSearch;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TrnGreigeKeluarController implements the CRUD actions for TrnGreigeKeluar model.
 */
class TrnGreigeKeluarController extends Controller
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
        $info = 'Fitur untuk mengeluarkan greige dari gudang diluar keperluan processing, misalnya untuk sample.';
        Yii::$app->session->setFlash('info', $info);

        $searchModel = new TrnGreigeKeluarSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
            'status'=>TrnStockGreige::STATUS_VALID
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $model = new TrnGreigeKeluar(['date'=>date('Y-m-d')]);

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $transaction = Yii::$app->db->beginTransaction();

                try {
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

        $modelsItem = $model->trnGreigeKeluarItems;

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
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

                    if($key === 'stock'){
                        $fields[] = "available = available - {$value}";
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
}
