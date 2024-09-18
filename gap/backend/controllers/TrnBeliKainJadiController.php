<?php

namespace backend\controllers;

use common\models\ar\TrnBeliKainJadiItem;
use common\models\ar\TrnGudangJadi;
use common\models\Model;
use Yii;
use common\models\ar\TrnBeliKainJadi;
use common\models\ar\TrnBeliKainJadiSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnBeliKainJadiController implements the CRUD actions for TrnBeliKainJadi model.
 */
class TrnBeliKainJadiController extends Controller
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
     * Lists all TrnBeliKainJadi models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnBeliKainJadiSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnBeliKainJadi model.
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
     * Creates a new TrnBeliKainJadi model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnBeliKainJadi(['date'=>date('Y-m-d')]);

        /* @var $modelsItem TrnBeliKainJadiItem[]*/
        $modelsItem = [new TrnBeliKainJadiItem()];

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $modelsItem = Model::createMultiple(TrnBeliKainJadiItem::classname());
            Model::loadMultiple($modelsItem, Yii::$app->request->post());

            //BaseVarDumper::dump([$model], 10, true);Yii::$app->end();

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;

            if ($valid) {
                $model->mo_id = $model->wo->mo_id;
                $model->sc_greige_id = $model->mo->sc_greige_id;
                $model->sc_id = $model->scGreige->sc_id;

                $transaction = Yii::$app->db->beginTransaction();
                try{
                    if(!$flag = $model->save(false)){
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('error', 'Gagal memproses, coba lagi. (1)');
                        return $this->render('create', [
                            'model' => $model,
                            'modelsItem' => (empty($modelsItem)) ? [new TrnBeliKainJadiItem] : $modelsItem
                        ]);
                    }

                    foreach ($modelsItem as $modelItem) {
                        $modelItem->beli_kain_jadi_id = $model->id;

                        if(!$flag = $modelItem->save(false)){
                            $transaction->rollBack();
                            Yii::$app->session->setFlash('error', 'Gagal memproses, coba lagi. (2)');
                            return $this->render('create', [
                                'model' => $model,
                                'modelsItem' => (empty($modelsItem)) ? [new TrnBeliKainJadiItem] : $modelsItem
                            ]);
                        }
                    }

                    if($flag){
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                }catch (\Throwable $t){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $t->getMessage().'---');
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'modelsItem' => (empty($modelsItem)) ? [new TrnBeliKainJadiItem] : $modelsItem
        ]);
    }

    /**
     * Updates an existing TrnBeliKainJadi model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status bukan draft, tidak bisa dirubah.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $modelsItem = $model->trnBeliKainJadiItems;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $oldIDs = ArrayHelper::map($modelsItem, 'id', 'id');
            $modelsItem = Model::createMultiple(TrnBeliKainJadiItem::classname(), $modelsItem);
            Model::loadMultiple($modelsItem, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsItem, 'id', 'id')));

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItem) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            TrnBeliKainJadiItem::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsItem as $modelItem) {
                            $modelItem->beli_kain_jadi_id = $model->id;
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
                } catch (\Throwable $t) {
                    $transaction->rollBack();
                    throw $t;
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'modelsItem' => (empty($modelsItem)) ? [new TrnBeliKainJadiItem] : $modelsItem
        ]);
    }

    /**
     * Deletes an existing TrnKartuProsesMaklon model.
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
            Yii::$app->session->setFlash('error', 'Status bukan draft, tidak bisa dihapus.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            TrnBeliKainJadiItem::deleteAll(['beli_kain_jadi_id'=>$model->id]);
            $model->delete();
            $transaction->commit();

        }catch (\Throwable $e){
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id'=>$model->id]);
        }

        Yii::$app->session->setFlash('success', 'Berhasil dihapus.');
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing KartuProsesPrinting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPosting($id)
    {
        $model = $this->findModel($id);

        if ($model->status != $model::STATUS_DRAFT) {
            Yii::$app->session->setFlash('error', 'Status bukan draft, tidak bisa diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if(!$model->getTrnBeliKainJadiItems()->count('id') > 0){
            Yii::$app->session->setFlash('error', 'Item belum diinput, tidak bisa diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->setNomor();
        $model->status = $model::STATUS_POSTED;

        $transaction = Yii::$app->db->beginTransaction();
        try{
            if(! ($flag = $model->save(false, ['status', 'no_urut', 'no']))){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Posting gagal, coba lagi. (1)');
            }

            foreach ($model->trnBeliKainJadiItems as $trnBeliKainJadiItem) {
                $modelStock = new TrnGudangJadi([
                    'jenis_gudang' => $model->jenis_gudang,
                    'wo_id' => $model->wo_id,
                    'source' => TrnGudangJadi::SOURCE_BELI_JADI,
                    'source_ref' => $model->no,
                    'unit' => $model->unit,
                    'qty' => $trnBeliKainJadiItem->qty,
                    'date' => $model->date,
                    'status' => TrnGudangJadi::STATUS_STOCK,
                    'note' => $trnBeliKainJadiItem->note,
                    'color' => $model->woColor->moColor->color
                ]);
                if(! ($flag = $modelStock->save(false))){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Posting gagal, coba lagi. (2)');
                    break;
                }
            }

            if($flag){
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Posting berhasil.');
            }
        }catch (\Throwable $t){
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $t->getMessage());
        }

        return $this->redirect(['view', 'id'=>$model->id]);
    }

    /**
     * Finds the TrnBeliKainJadi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnBeliKainJadi the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnBeliKainJadi::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
