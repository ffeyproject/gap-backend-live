<?php

namespace backend\controllers;

use common\models\ar\TrnPfpKeluarItem;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnStockGreigeSearch;
use Yii;
use common\models\ar\TrnPfpKeluar;
use common\models\ar\TrnPfpKeluarSearch;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TrnPfpKeluarController implements the CRUD actions for TrnPfpKeluar model.
 */
class TrnPfpKeluarController extends Controller
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
     * Lists all TrnPfpKeluar models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnPfpKeluarSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnPfpKeluar model.
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
            'jenis_gudang'=>TrnStockGreige::JG_PFP,
            'status'=>TrnStockGreige::STATUS_VALID
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $model = new TrnPfpKeluar(['date'=>date('Y-m-d')]);

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $transaction = Yii::$app->db->beginTransaction();

                try {
                    if ($flag = $model->save(false)) {
                        foreach (Json::decode(Yii::$app->request->post('items')) as $item) {
                            $modelItem = new TrnPfpKeluarItem([
                                'pfp_keluar_id' => $model->id,
                                'stock_pfp_id' => $item['id'],
                            ]);
                            $modelItem->pfp_keluar_id = $model->id;
                            if (! ($flag = $modelItem->save(false))) {
                                $transaction->rollBack();
                                throw new HttpException(500, 'Gagal, coba lagi. (2)');
                            }
                        }
                    }else{
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal, coba lagi. (1)');
                    }

                    if ($flag) {
                        $transaction->commit();
                        return ['success'=>true, 'redirect'=>Url::to(['view', 'id'=>$model->id])];
                    }
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
            'jenis_gudang'=>TrnStockGreige::JG_PFP,
            'status'=>TrnStockGreige::STATUS_VALID
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $modelsItem = $model->trnPfpKeluarItems;

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if (!($flag = $model->save(false))) {
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal, coba lagi. (1)');
                    }

                    TrnPfpKeluarItem::deleteAll(['pfp_keluar_id'=>$model->id]);

                    foreach (Json::decode(Yii::$app->request->post('items')) as $item) {
                        $modelItem = new TrnPfpKeluarItem([
                            'pfp_keluar_id' => $model->id,
                            'stock_pfp_id' => $item['id'],
                        ]);
                        $modelItem->pfp_keluar_id = $model->id;
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
     * Deletes an existing TrnPfpKeluar model.
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

        TrnPfpKeluarItem::deleteAll(['pfp_keluar_id'=>$model->id]);

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing TrnPotongGreige model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
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
        $model->setNomor();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(!($flag = $model->save(false, ['posted_at', 'status', 'approved_at', 'approved_by', 'no_urut', 'no']))){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal menyimpan, coba lagi.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            $greigesKeluar = [];
            foreach ($model->trnPfpKeluarItems as $trnPfpKeluarItem) {
                $updateStockStatus = $model::getDb()->createCommand()->update(
                    TrnStockGreige::tableName(),
                    ['status'=>TrnStockGreige::STATUS_KELUAR_GUDANG],
                    ['id'=>$trnPfpKeluarItem->stock_pfp_id]
                )->execute();
                if(!($flag = $updateStockStatus === 1)){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal menyimpan, coba lagi.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                $stockGreige = $trnPfpKeluarItem->stockPfp;
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

                if(!isset($greigesKeluar[$greige->id][$fieldToUpdate])){
                    $greigesKeluar[$greige->id][$fieldToUpdate] = 0;
                }

                $greigesKeluar[$greige->id][$fieldToUpdate] += $stockGreige->panjang_m;
            }

            foreach ($greigesKeluar as $greigeId=>$greigeKeluar) {
                $fields = [];
                foreach ($greigeKeluar as $key=>$value) {
                    $fields[] = $key.' = '.$key.' - '.$value; //"stock = stock - 2500"
                }
                $fieldStr = implode(', ', $fields);
                $updateGreigeCmd = Yii::$app->db->createCommand('UPDATE mst_greige SET '.$fieldStr.' WHERE id='.$greigeId);

                /*BaseVarDumper::dump($updateGreigeCmd->query(), 10, true);
                $transaction->rollBack();
                Yii::$app->end();*/

                if(!($flag = $updateGreigeCmd->execute() === 1)){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal menyimpan, coba lagi.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }

            if ($flag){
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Posting berhasil.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

        }catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    /**
     * Finds the TrnPfpKeluar model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnPfpKeluar the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnPfpKeluar::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
