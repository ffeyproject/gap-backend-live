<?php

namespace backend\controllers;

use common\models\ar\MstGreige;
use common\models\ar\MstProcessDyeing;
use common\models\ar\MstProcessCelup;
use common\models\ar\TrnKartuProsesCelupItem;
use Yii;
use common\models\ar\TrnKartuProsesCelup;
use common\models\ar\TrnKartuProsesCelupSearch;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnKartuProsesCelupController implements the CRUD actions for TrnKartuProsesCelup model.
 */
class TrnKartuProsesCelupController extends Controller
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
     * Lists all TrnKartuProsesCelup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnKartuProsesCelupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnKartuProsesCelup model.
     * @param integer $id
     * @return mixed
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $processModels = MstProcessDyeing::find()->orderBy('order')->all();
        if(empty($processModels)){
            throw new NotAcceptableHttpException('Tidak ditemukan adanya data master processing untuk Celup, silahkan input dulu master processing untuk Celup lalu kembali lagi ke halaman ini.');
        }

        $attrsLabels = [];
        if($processModels !== null){
            $attrsLabels = $processModels[0]->attributeLabels();
            unset($attrsLabels['order']); unset($attrsLabels['created_at']); unset($attrsLabels['created_by']); unset($attrsLabels['updated_at']); unset($attrsLabels['updated_by']); unset($attrsLabels['max_pengulangan']);
            //BaseVarDumper::dump($attrsLabels, 10, true);Yii::$app->end();
        }

        //Data pengulangan tiap-tiap proses
        $processesUlang = [];
        foreach ($model->kartuProcessCelupProcesses as $i=>$kartuProcessCelupProcess) {
            if($kartuProcessCelupProcess->value !== null){
                $dataProcess = Json::decode($kartuProcessCelupProcess->value);
                if(isset($dataProcess['pengulangan']) && !empty($dataProcess['pengulangan'])){
                    $processUlang = [
                        'nama_proses'=>'',
                        'header'=>[],
                        'pengulangan'=>[]
                    ];

                    $headers = [];
                    $attrs = $kartuProcessCelupProcess->process->attributes;
                    unset($attrs['id']); unset($attrs['order']); unset($attrs['created_at']); unset($attrs['created_by']); unset($attrs['updated_at']); unset($attrs['updated_by']); unset($attrs['max_pengulangan']);
                    foreach ($attrs as $key=>$attr) {
                        if($key === 'nama_proses'){
                            $processUlang['nama_proses'] = $attr;
                            unset($attrs['nama_proses']);
                        }else{
                            if($attr){
                                $headers[$key] = $kartuProcessCelupProcess->getAttributeLabel($key);
                            }
                        }
                    }
                    $processUlang['header'] = $headers;

                    foreach ($dataProcess['pengulangan'] as $j=>$pengulangan) {
                        $data = [
                            'head'=>['time'=>$pengulangan['time'], 'memo'=>$pengulangan['memo'], 'by'=>$pengulangan['by'], 'data'=>[]]
                        ];
                        $pengulanganData = $pengulangan['data'];
                        foreach ($headers as $key=>$header) {
                            if(isset($pengulanganData[$key])){
                                $data['data'][$key] = $pengulanganData[$key];
                            }else{
                                $data['data'][$key] = null;
                            }
                        }
                        $processUlang['pengulangan'][] = $data;
                    }

                    $processesUlang[] = $processUlang;
                }
            }
        }
        //BaseVarDumper::dump($processesUlang, 10, true);Yii::$app->end();
        //Data pengulangan tiap-tiap proses

        return $this->render('view', [
            'model' => $model,
            'attrsLabels' => $attrsLabels,
            'processModels' => $processModels,
            'processesUlang' => $processesUlang
        ]);
    }

    /**
     * Creates a new TrnKartuProsesCelup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnKartuProsesCelup([
            'date' => date('Y-m-d'),
            'note' => '-'
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $orderCelup = $model->orderCelup;

            /**
             * Periksa berapa banyak Order PFP yang sudah dibuat kartu prosesnya
             * kalau sudah mencukupi, jangan ijinkan pembuatan ini
             */
            $jumlahKartuProses = $orderCelup->getTrnKartuProsesCelups()->count('id');
            if($jumlahKartuProses >= $orderCelup->qty){
                $model->addError('order_pfp_id', 'Order Celup ini sudah tercukupi kartu proses nya.');
                return $this->render('create', ['model' => $model]);
            }

            $handling = $orderCelup->handling;
            $model->handling = $handling->name;
            $model->lebar_preset = $handling->lebar_preset;
            $model->lebar_finish = $handling->lebar_finish;
            $model->berat_finish = $handling->berat_finish;
            $model->t_density_lusi = $handling->densiti_lusi;
            $model->t_density_pakan = $handling->densiti_pakan;

            $model->greige_group_id = $orderCelup->greige_group_id;
            $model->greige_id = $orderCelup->greige_id;
            $model->save(false);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnKartuProsesCelup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TrnKartuProsesCelup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk dihapus.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        TrnKartuProsesCelupItem::deleteAll(['kartu_process_id'=>$model->id]);
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Membuat kartu proses tidak lagi dibatasi jumlah itemnya berdasarkan qty per batch greige terkait
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSetUnlimitItem($id)
    {
        $model = $this->findModel($id);
        $model->no_limit_item = true;
        $model->save(false, ['no_limit_item']);
        Yii::$app->session->setFlash('success', 'Berhasil, sekarang kartu proses ini sudah tidak dibatasi jumlah item nya.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Deletes an existing KartuProsesCelup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPosting($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Kartu proses bukan draft, tidak bisa diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $orderCelup = $model->orderCelup;
        $greigeGroup = $model->greigeGroup;
        $greige = $model->greige;

        $perBatchHalfToleransiAtas = 0;
        $perBatchHalfToleransiBawah = 0;

        if(!$model->no_limit_item){
            $lenPerBatch = (float)$greigeGroup->qty_per_batch;
            $perBatchHalf = $lenPerBatch / 2; // setengah batch
            $perBatchHalfInPercent = 0.02 * $perBatchHalf; //dua persen dari setengah batch
            $perBatchHalfToleransiAtas = $perBatchHalf + $perBatchHalfInPercent;
            $perBatchHalfToleransiBawah = $perBatchHalf - $perBatchHalfInPercent;
        }

        $model->status = $model::STATUS_POSTED;
        $model->posted_at = time();

        if($model->no_urut === null){
            $model->setNomor();
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $totalTubeKiri = 0;
            $totalTubeKanan = 0;
            foreach ($model->trnKartuProsesCelupItems as $trnKartuProsesCelupItem) {
                $stockItem = $trnKartuProsesCelupItem->stock;
                if($stockItem->status == $stockItem::STATUS_ON_PROCESS_CARD){
                    //BaseVarDumper::dump($trnKartuProsesCelupItem, 10, true);Yii::$app->end();
                    $panjang = Yii::$app->formatter->asDecimal($stockItem->panjang_m);
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Salah satu roll greige (ID:'.$stockItem->id.', Pannjang: '.$panjang.'M) sudah digunakan oleh kartu proses lain, coba lagi.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                $stockItem->status = $stockItem::STATUS_ON_PROCESS_CARD;
                if(!$stockItem->save(false, ['status'])){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal, coba lagi.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                switch ($trnKartuProsesCelupItem->tube){
                    case $trnKartuProsesCelupItem::TUBE_KIRI:
                        $totalTubeKiri += (float)$stockItem->panjang_m;
                        break;
                    case $trnKartuProsesCelupItem::TUBE_KANAN:
                        $totalTubeKanan += (float)$stockItem->panjang_m;
                        break;
                }
            }

            $totalLength = $totalTubeKiri + $totalTubeKanan;

            if(!$model->no_limit_item){
                if(($totalTubeKiri < $perBatchHalfToleransiBawah) || ($totalTubeKanan < $perBatchHalfToleransiBawah)){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Jumlah greige tube kiri atau tube kanan kurang dari 1 BATCH dikurang 2%.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                if(($totalTubeKiri > $perBatchHalfToleransiAtas) || ($totalTubeKanan > $perBatchHalfToleransiAtas)){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Jumlah greige tube kiri atau tube kanan lebih dari 1 BATCH ditambah 2%.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }

            if(!$model->save(false)){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal, coba lagi.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            if($orderCelup->status == $orderCelup::STATUS_POSTED){
                $orderCelup->status = $orderCelup::STATUS_PROCESSED;
                if(!$orderCelup->save(false, ['status'])){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal, coba lagi.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }

            //Booking greige--------------------------------------------------------------------------------------------
            Yii::$app->db->createCommand()->update(
                MstGreige::tableName(),
                [
                    'booked' => new \yii\db\Expression('booked + ' . $totalLength),
                ],
                ['id'=>$greige->id]
            )->execute();
            //Booking greige--------------------------------------------------------------------------------------------

            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Kartu proses berhasil diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    /**
     * Finds the TrnKartuProsesCelup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnKartuProsesCelup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnKartuProsesCelup::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
