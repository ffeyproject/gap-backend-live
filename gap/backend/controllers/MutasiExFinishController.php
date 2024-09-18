<?php

namespace backend\controllers;

use common\models\ar\MutasiExFinishItem;
use common\models\ar\TrnStockGreige;
use common\models\Model;
use Yii;
use common\models\ar\MutasiExFinish;
use common\models\ar\MutasiExFinishSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MutasiExFinishController implements the CRUD actions for MutasiExFinish model.
 */
class MutasiExFinishController extends Controller
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
     * Lists all MutasiExFinish models.
     * @return mixed
     */
    public function actionIndex()
    {
        $info = 'Fitur untuk memasukan kembali greige finish yang dikembalikan oleh buyer ke gudang ex finish, dan bisa digunakan lagi untuk produksi jika sesuai dengan kebutuhan.';
        Yii::$app->session->setFlash('info', $info);

        $searchModel = new MutasiExFinishSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MutasiExFinish model.
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
     * Creates a new MutasiExFinish model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MutasiExFinish(['date'=>date('Y-m-d'), 'note'=>'-']);

        /* @var $modelsItem MutasiExFinishItem[]*/
        $modelsItem = [new MutasiExFinishItem()];

        if ($model->load(Yii::$app->request->post())) {
            $modelsItem = Model::createMultiple(MutasiExFinishItem::classname());
            Model::loadMultiple($modelsItem, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;

            //BaseVarDumper::dump([$model, $modelsItem], 10, true);Yii::$app->end();

            if ($valid) {
                $model->greige_group_id = $model->greige->group_id;

                $transaction = Yii::$app->db->beginTransaction();
                try{
                    if ($flag = $model->save(false)) {
                        foreach ($modelsItem as $modelItem) {
                            $modelItem->mutasi_id = $model->id;
                            $modelItem->greige_group_id = $model->greige_group_id;
                            $modelItem->greige_id = $model->greige_id;
                            if (! ($flag = $modelItem->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                }catch (\Throwable $e){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }
        }

        return $this->render('create', [
            'model' => $model, 'modelsItem' => (empty($modelsItem)) ? [new MutasiExFinishItem] : $modelsItem
        ]);
    }

    /**
     * Updates an existing MutasiExFinish model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid, tidak dapat dirubah.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        /* @var $modelsItem MutasiExFinishItem[]*/
        $modelsItem = $model->mutasiExFinishItems;

        if ($model->load(Yii::$app->request->post())) {
            $oldIDs = ArrayHelper::map($modelsItem, 'id', 'id');
            $modelsItem = Model::createMultiple(MutasiExFinishItem::classname(), $modelsItem);
            Model::loadMultiple($modelsItem, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsItem, 'id', 'id')));

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;

            if ($valid) {
                $model->greige_group_id = $model->greige->group_id;

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            MutasiExFinishItem::deleteAll(['id' => $deletedIDs]);
                        }

                        foreach ($modelsItem as $modelItem) {
                            $modelItem->mutasi_id = $model->id;
                            $modelItem->greige_group_id = $model->greige_group_id;
                            $modelItem->greige_id = $model->greige_id;

                            if (! ($flag = $modelItem->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                }catch (\Throwable $e){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }
        }

        return $this->render('update', [
            'model' => $model, 'modelsItem' => (empty($modelsItem)) ? [new MutasiExFinishItem] : $modelsItem
        ]);
    }

    /**
     * Deletes an existing MutasiExFinish model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk dihapus.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $modelsItem = $model->mutasiExFinishItems;

        $itemIDs = ArrayHelper::map($modelsItem, 'id', 'id');
        MutasiExFinishItem::deleteAll(['id' => $itemIDs]);
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing TrnBuyPfp model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionPosting($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        //untuk sementara langsung bypass jadi approved
        $model->status = $model::STATUS_APPROVED;
        $model->approval_id = Yii::$app->user->id;
        $model->approval_time = time();
        $model->setNomor();

        $items = $model->mutasiExFinishItems;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(!$flag = $model->save(false, ['status', 'approval_id', 'approval_time', 'no_urut', 'no'])){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', '');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            $uid = Yii::$app->user->id;
            $ts = time();
            $db = $model::getDb();
            $add = 0;
            foreach ($items as $item) {
                $cmd1 = $db->createCommand()->insert(TrnStockGreige::tableName(), [
                    'nomor_wo' => $model->no_wo,
                    'greige_group_id' => $model->greige_group_id,
                    'greige_id' => $model->greige_id,
                    'asal_greige' => TrnStockGreige::ASAL_GREIGE_MUTASI,
                    'no_lapak' => '-',
                    'lot_lusi' => '-',
                    'lot_pakan' => '-',
                    'no_set_lusi' => '-',
                    'status_tsd' => TrnStockGreige::STATUS_TSD_NORMAL,
                    'no_document' => $model->no_wo,
                    'pengirim' => 'Ex Finish',
                    'mengetahui' => $model->approval_id,
                    'note' => 'Mutasi Ex Finish',
                    'date' => date('Y-m-d'),
                    'status' => TrnStockGreige::STATUS_VALID,
                    'jenis_gudang' => TrnStockGreige::JG_EX_FINISH,
                    'grade' => $item->grade,
                    'panjang_m' => $item->panjang_m,
                    'created_at' => $ts,
                    'created_by' => $uid,
                    'updated_at' => $ts,
                    'updated_by' => $uid,

                ]);
                if(!$flag = $cmd1->execute() > 0){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal, coba lagi.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                $add += $item->panjang_m;
            }

            //menambah jumlah stock ex finish
            $command = $db->createCommand("UPDATE mst_greige SET stock_ef = stock_ef + {$add} WHERE id= {$model->greige_id}");
            if(!$flag = $command->execute() > 0){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal, coba lagi.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            if ($flag) {
                Yii::$app->session->setFlash('success', 'Posting berhasil.');
                $transaction->commit();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }catch (\Throwable $e){
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Finds the MutasiExFinish model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MutasiExFinish the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MutasiExFinish::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
