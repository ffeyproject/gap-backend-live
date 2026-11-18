<?php
namespace backend\controllers;

use common\models\ar\MstGreige;
use common\models\ar\TrnBuyPfp;
use common\models\ar\TrnBuyPfpItem;
use common\models\ar\TrnBuyPfpSearch;
use common\models\ar\TrnStockGreige;
use common\models\Model;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * TrnInputPfpController implements the CRUD actions for Input PFP (Gudang 2) model.
 */
class TrnInputPfpController extends Controller
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
                    'posting' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all TrnBuyPfp models for Gudang 2.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnBuyPfpSearch(['pfp_jenis_gudang' => TrnStockGreige::PFP_JG_TWO]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['trn_buy_pfp.pfp_jenis_gudang' => TrnStockGreige::PFP_JG_TWO]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnBuyPfp model.
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
     * Creates a new TrnBuyPfp model for Gudang 2.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnBuyPfp([
            'date' => date('Y-m-d'),
            'note' => '-',
            'pfp_jenis_gudang' => TrnStockGreige::PFP_JG_TWO,
        ]);

        /* @var $modelsItem TrnBuyPfpItem[] */
        $modelsItem = [new TrnBuyPfpItem()];

        if ($model->load(Yii::$app->request->post())) {
            $model->pfp_jenis_gudang = TrnStockGreige::PFP_JG_TWO;
            $modelsItem = Model::createMultiple(TrnBuyPfpItem::classname());
            Model::loadMultiple($modelsItem, Yii::$app->request->post());

            // validate jenis_printing is required for Gudang 2
            if (empty($model->jenis_printing)) {
                $model->addError('jenis_printing', 'Jenis Printing harus dipilih.');
            }

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;

            if ($valid) {
                $model->greige_group_id = $model->greige->group_id;

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        foreach ($modelsItem as $modelItem) {
                            $modelItem->buy_pfp_id = $model->id;
                            $modelItem->greige_group_id = $model->greige_group_id;
                            $modelItem->greige_id = $model->greige_id;
                            if (!($flag = $modelItem->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'modelsItem' => (empty($modelsItem)) ? [new TrnBuyPfpItem()] : $modelsItem,
        ]);
    }

    /**
     * Updates an existing TrnBuyPfp model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        /* @var $modelsItem TrnBuyPfpItem[] */
        $modelsItem = $model->trnBuyPfpItems;

        if ($model->load(Yii::$app->request->post())) {
            $model->pfp_jenis_gudang = TrnStockGreige::PFP_JG_TWO;
            $oldIDs = ArrayHelper::map($modelsItem, 'id', 'id');
            $modelsItem = Model::createMultiple(TrnBuyPfpItem::classname(), $modelsItem);
            Model::loadMultiple($modelsItem, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsItem, 'id', 'id')));

            // validate jenis_printing is required for Gudang 2
            if (empty($model->jenis_printing)) {
                $model->addError('jenis_printing', 'Jenis Printing harus dipilih.');
            }

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;

            if ($valid) {
                $model->greige_group_id = $model->greige->group_id;

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            TrnBuyPfpItem::deleteAll(['id' => $deletedIDs]);
                        }

                        foreach ($modelsItem as $modelItem) {
                            $modelItem->buy_pfp_id = $model->id;
                            $modelItem->greige_group_id = $model->greige_group_id;
                            $modelItem->greige_id = $model->greige_id;
                            if (!($flag = $modelItem->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'modelsItem' => (empty($modelsItem)) ? [new TrnBuyPfpItem()] : $modelsItem,
        ]);
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
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->status != $model::STATUS_DRAFT) {
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk dihapus.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $modelsItem = $model->trnBuyPfpItems;

        $itemIDs = ArrayHelper::map($modelsItem, 'id', 'id');
        TrnBuyPfpItem::deleteAll(['id' => $itemIDs]);
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Postings an existing TrnBuyPfp model to Stock Greige (Gudang 2).
     * If posting is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPosting($id)
    {
        $model = $this->findModel($id);

        if ($model->status != $model::STATUS_DRAFT) {
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if (empty($model->jenis_printing)) {
            Yii::$app->session->setFlash('error', 'Jenis Printing belum dipilih. Harap perbarui data.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        // bypass to approved
        $model->status = $model::STATUS_APPROVED;
        $model->approval_id = Yii::$app->user->id;
        $model->approval_time = time();

        $items = $model->trnBuyPfpItems;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$flag = $model->save(false, ['status', 'approval_id', 'approval_time'])) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal memperbarui status.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            $uid = Yii::$app->user->id;
            $ts = time();
            $db = $model::getDb();
            $add = 0;
            foreach ($items as $item) {
                switch ($model->jenis) {
                    case $model::JENIS_BELI:
                        $asalGreige = TrnStockGreige::ASAL_GREIGE_BELI;
                        break;
                    case $model::JENIS_MAKLOON:
                        $asalGreige = TrnStockGreige::ASAL_GREIGE_MAKLOON;
                        break;
                    default:
                        $asalGreige = TrnStockGreige::ASAL_GREIGE_LAIN_LAIN;
                }

                $cmd1 = $db->createCommand()->insert(TrnStockGreige::tableName(), [
                    'greige_group_id' => $model->greige_group_id,
                    'greige_id' => $model->greige_id,
                    'asal_greige' => $asalGreige,
                    'no_lapak' => '-',
                    'lot_lusi' => '-',
                    'lot_pakan' => '-',
                    'no_set_lusi' => '-',
                    'status_tsd' => TrnStockGreige::STATUS_TSD_NORMAL,
                    'no_document' => $model->no_document,
                    'pengirim' => $model->vendor,
                    'mengetahui' => (string)($model->approvedBy ? $model->approvedBy->full_name : $model->approval_id),
                    'note' => $model->note,
                    'date' => date('Y-m-d'),
                    'status' => TrnStockGreige::STATUS_VALID,
                    'jenis_gudang' => TrnStockGreige::JG_PFP,
                    'pfp_jenis_gudang' => TrnStockGreige::PFP_JG_TWO,
                    'jenis_printing' => $model->jenis_printing,
                    'grade' => TrnStockGreige::GRADE_NG,
                    'panjang_m' => $item->panjang_m,
                    'created_at' => $ts,
                    'created_by' => $uid,
                    'updated_at' => $ts,
                    'updated_by' => $uid,
                    'color' => $model->color,
                ]);

                if (!$flag = $cmd1->execute() > 0) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal menyimpan stok, coba lagi.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                $add += $item->panjang_m;
            }

            $command = $db->createCommand("UPDATE mst_greige SET stock_pfp = stock_pfp + {$add}, available_pfp = available_pfp + {$add} WHERE id= {$model->greige_id}");
            if (!$flag = $command->execute() > 0) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal mengupdate stok master greige, coba lagi.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            if ($flag) {
                Yii::$app->session->setFlash('success', 'Posting berhasil. Stok telah masuk ke Gudang 2.');
                $transaction->commit();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Finds the TrnBuyPfp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnBuyPfp the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnBuyPfp::findOne(['id' => $id, 'pfp_jenis_gudang' => TrnStockGreige::PFP_JG_TWO])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
