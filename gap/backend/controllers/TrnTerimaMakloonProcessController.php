<?php

namespace backend\controllers;

use backend\modules\rawdata\models\TrnKartuProsesMaklon;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnKartuProsesMaklonItem;
use common\models\ar\TrnTerimaMakloonProcessItem;
use common\models\Model;
use Yii;
use common\models\ar\TrnTerimaMakloonProcess;
use common\models\ar\TrnTerimaMakloonProcessSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnTerimaMakloonProcessController implements the CRUD actions for TrnTerimaMakloonProcess model.
 */
class TrnTerimaMakloonProcessController extends Controller
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
     * Lists all TrnTerimaMakloonProcess models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnTerimaMakloonProcessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnTerimaMakloonProcess model.
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
     * Creates a new TrnTerimaMakloonProcess model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnTerimaMakloonProcess(['date'=>date('Y-m-d')]);

        /* @var $modelsItem TrnTerimaMakloonProcessItem[]*/
        $modelsItem = [new TrnTerimaMakloonProcessItem()];

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $modelsItem = Model::createMultiple(TrnTerimaMakloonProcessItem::classname());
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
                            'modelsItem' => (empty($modelsItem)) ? [new TrnTerimaMakloonProcessItem] : $modelsItem
                        ]);
                    }

                    foreach ($modelsItem as $modelItem) {
                        $modelItem->terima_makloon_id = $model->id;

                        if(!$flag = $modelItem->save(false)){
                            $transaction->rollBack();
                            Yii::$app->session->setFlash('error', 'Gagal memproses, coba lagi. (2)');
                            return $this->render('create', [
                                'model' => $model,
                                'modelsItem' => (empty($modelsItem)) ? [new TrnTerimaMakloonProcessItem] : $modelsItem
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
            'modelsItem' => (empty($modelsItem)) ? [new TrnTerimaMakloonProcessItem] : $modelsItem
        ]);
    }

    /**
     * Updates an existing TrnTerimaMakloonProcess model.
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

        $modelsItem = $model->trnTerimaMakloonProcessItems;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $oldIDs = ArrayHelper::map($modelsItem, 'id', 'id');
            $modelsItem = Model::createMultiple(TrnTerimaMakloonProcessItem::classname(), $modelsItem);
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
                            TrnTerimaMakloonProcessItem::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsItem as $modelItem) {
                            $modelItem->terima_makloon_id = $model->id;
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
            'modelsItem' => (empty($modelsItem)) ? [new TrnTerimaMakloonProcessItem] : $modelsItem
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
            TrnTerimaMakloonProcessItem::deleteAll(['terima_makloon_id'=>$model->id]);
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

        if(!$model->getTrnTerimaMakloonProcessItems()->count('id') > 0){
            Yii::$app->session->setFlash('error', 'Item belum diinput, tidak bisa diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $woSudahKirim = TrnKartuProsesMaklonItem::find()->joinWith('kartuProcess')->where(['trn_kartu_proses_maklon.wo_id'=>$model->wo_id])->sum('qty');
        $woSudahKirim = $woSudahKirim > 0 ? $woSudahKirim : 0;
        $woDiterima = $model->getTrnTerimaMakloonProcessItems()->sum('qty');
        $woDiterima = $woDiterima > 0 ? $woDiterima : 0;

        if($woSudahKirim < $woDiterima){
            Yii::$app->session->setFlash('error', 'Item melebihi jumlah pengiriman, tidak bisa diposting.');
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

            foreach ($model->trnTerimaMakloonProcessItems as $trnTerimaMakloonProcessItem) {
                $modelStock = new TrnGudangJadi([
                    'jenis_gudang' => $model->jenis_gudang,
                    'wo_id' => $model->wo_id,
                    'source' => TrnGudangJadi::SOURCE_MAKLOON_PROSES,
                    'source_ref' => $model->no,
                    'unit' => $model->unit,
                    'qty' => $trnTerimaMakloonProcessItem->qty,
                    'date' => $model->date,
                    'status' => TrnGudangJadi::STATUS_STOCK,
                    'note' => $trnTerimaMakloonProcessItem->note,
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
     *
     */
    public function actionInspect($id){
        $model = $this->findModel($id);

        if ($model->status != $model::STATUS_DRAFT) {
            Yii::$app->session->setFlash('error', 'Status bukan draft, tidak bisa diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if(!$model->getTrnTerimaMakloonProcessItems()->count('id') > 0){
            Yii::$app->session->setFlash('error', 'Item belum diinput, tidak bisa diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $woSudahKirim = TrnKartuProsesMaklonItem::find()->joinWith('kartuProcess')->where(['trn_kartu_proses_maklon.wo_id'=>$model->wo_id])->sum('qty');
        $woSudahKirim = $woSudahKirim > 0 ? $woSudahKirim : 0;
        $woDiterima = $model->getTrnTerimaMakloonProcessItems()->sum('qty');
        $woDiterima = $woDiterima > 0 ? $woDiterima : 0;

        if($woSudahKirim < $woDiterima){
            // Sementara di bypass dulu
            //Yii::$app->session->setFlash('error', 'Item melebihi jumlah pengiriman, tidak bisa diteruskan ke inspecting.');
            //return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->setNomor();
        $model->status = $model::STATUS_INSPECTED;
        $model->save(false);

        Yii::$app->session->setFlash('success', 'Berhasil, telah diteruskan ke inspecting.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Finds the TrnTerimaMakloonProcess model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnTerimaMakloonProcess the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnTerimaMakloonProcess::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
