<?php

namespace backend\controllers;

use Yii;
use common\models\ar\MutasiPfp;
use common\models\ar\MutasiPfpSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PenerimaanMutasiPfpController implements the CRUD actions for MutasiPfp model.
 */
class PenerimaanMutasiPfpController extends Controller
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
     * Lists all MutasiPfp models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MutasiPfpSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['>=', 'status', MutasiPfp::STATUS_POSTED]);
        
        $dataProvider->sort->attributes['status'] = [
            'asc' => ['mutasi_pfp.status' => SORT_ASC],
            'desc' => ['mutasi_pfp.status' => SORT_DESC],
        ];
        $dataProvider->sort->defaultOrder = ['status' => SORT_ASC, 'id' => SORT_DESC];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionTerima($id)
    {
        $model = $this->findModel($id);
        if($model->status != MutasiPfp::STATUS_POSTED){
            throw new \yii\web\ForbiddenHttpException('Status tidak valid. Tidak bisa diterima.');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->status = MutasiPfp::STATUS_DITERIMA;
            if(!($flag = $model->save(false, ['status']))){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal menyimpan status, coba lagi.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            $greigesDiterima = [];
            foreach ($model->mutasiPfpItems as $mutasiItem) {
                $oldStock = $mutasiItem->stockPfp;
                
                $greige = $oldStock->greige;
                if(!isset($greigesDiterima[$greige->id])){
                    $greigesDiterima[$greige->id] = 0;
                }
                $greigesDiterima[$greige->id] += $oldStock->panjang_m;

                $newStock = new \common\models\ar\TrnStockGreige();
                $newStock->attributes = $oldStock->attributes;
                $newStock->setIsNewRecord(true);
                unset($newStock->id);
                $newStock->jenis_gudang = \common\models\ar\TrnStockGreige::JG_PFP_PERSIAPAN;
                $newStock->status = \common\models\ar\TrnStockGreige::STATUS_VALID;
                $newStock->asal_greige = \common\models\ar\TrnStockGreige::ASAL_GREIGE_MUTASI;
                $newStock->no_document = $model->no;
                $newStock->date = date('Y-m-d');

                if(!($flag = $newStock->save(false))){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal membuat stock baru, coba lagi.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }

            // Kurangi stock_pfp and available_pfp in MstGreige
            foreach ($greigesDiterima as $greigeId => $totalQty) {
                $updateGreigeCmd = Yii::$app->db->createCommand(
                    "UPDATE mst_greige SET stock_pfp = stock_pfp - {$totalQty}, available_pfp = available_pfp - {$totalQty} WHERE id = {$greigeId}"
                );

                if(!($flag = $updateGreigeCmd->execute() === 1)){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal memotong stok, coba lagi.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }

            if ($flag){
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Mutasi PFP berhasil Diterima.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    public function actionTolak($id)
    {
        $model = $this->findModel($id);
        if($model->status != MutasiPfp::STATUS_POSTED){
            throw new \yii\web\ForbiddenHttpException('Status tidak valid. Tidak bisa ditolak.');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->status = MutasiPfp::STATUS_DRAFT; // Kembalikan ke draft
            if(!($flag = $model->save(false, ['status']))){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal menyimpan status, coba lagi.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            $greigesKembali = [];
            foreach ($model->mutasiPfpItems as $mutasiItem) {
                // Update status roll kembali menjadi valid
                $updateStockStatus = MutasiPfp::getDb()->createCommand()->update(
                    \common\models\ar\TrnStockGreige::tableName(),
                    ['status'=>\common\models\ar\TrnStockGreige::STATUS_VALID],
                    ['id'=>$mutasiItem->stock_pfp_id]
                )->execute();

                if(!($flag = $updateStockStatus === 1)){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal update status stock roll.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                $stockGreige = $mutasiItem->stockPfp;
                $greige = $stockGreige->greige;

                if(!isset($greigesKembali[$greige->id])){
                    $greigesKembali[$greige->id] = 0;
                }
                $greigesKembali[$greige->id] += $stockGreige->panjang_m;
            }

            if ($flag){
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Mutasi PFP berhasil Ditolak dan dikembalikan menjadi Draft.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    protected function findModel($id)
    {
        if (($model = MutasiPfp::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
