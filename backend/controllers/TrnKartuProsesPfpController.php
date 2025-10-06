<?php

namespace backend\controllers;

use common\models\ar\MstGreige;
use common\models\ar\MstProcessPfp;
use common\models\ar\TrnKartuProsesPfpItem;
use Yii;
use common\models\ar\TrnKartuProsesPfp;
use common\models\ar\TrnKartuProsesPfpSearch;
use common\models\ar\TrnStockGreige;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\BaseVarDumper;

/**
 * TrnKartuProsesPfpController implements the CRUD actions for TrnKartuProsesPfp model.
 */
class TrnKartuProsesPfpController extends Controller
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
     * Lists all TrnKartuProsesPfp models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnKartuProsesPfpSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnKartuProsesPfp model.
     * @param integer $id
     * @return mixed
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {   
        $model = $this->findModel($id);

        $processModels = MstProcessPfp::find()->orderBy('order')->all();
        if(empty($processModels)){
            throw new NotAcceptableHttpException('Tidak ditemukan adanya data master processing untuk PFP, silahkan input dulu master processing untuk PFP lalu kembali lagi ke halaman ini.');
        }

        $attrsLabels = [];
        if($processModels !== null){
            $attrsLabels = $processModels[0]->attributeLabels();
            unset($attrsLabels['order']); unset($attrsLabels['created_at']); unset($attrsLabels['created_by']); unset($attrsLabels['updated_at']); unset($attrsLabels['updated_by']); unset($attrsLabels['max_pengulangan']);
            //BaseVarDumper::dump($attrsLabels, 10, true);Yii::$app->end();
        }

        //Data pengulangan tiap-tiap proses
        $processesUlang = [];
        foreach ($model->kartuProcessPfpProcesses as $i=>$kartuProcessPfpProcess) {
            if($kartuProcessPfpProcess->value !== null){
                $dataProcess = Json::decode($kartuProcessPfpProcess->value);
                if(isset($dataProcess['pengulangan']) && !empty($dataProcess['pengulangan'])){
                    $processUlang = [
                        'nama_proses'=>'',
                        'pengulangan'=>[]
                    ];

                    $headers = [];
                    $attrs = $kartuProcessPfpProcess->process->attributes;
                    unset($attrs['id']); unset($attrs['order']); unset($attrs['created_at']); unset($attrs['created_by']); unset($attrs['updated_at']); unset($attrs['updated_by']); unset($attrs['max_pengulangan']);
                    foreach ($attrs as $key=>$attr) {
                        if($key === 'nama_proses'){
                            $processUlang['nama_proses'] = $attr;
                            unset($attrs['nama_proses']);
                        }else{
                            if($attr){
                                $headers[$key] = $kartuProcessPfpProcess->getAttributeLabel($key);
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
     * Creates a new TrnKartuProsesPfp model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnKartuProsesPfp([
            'date' => date('Y-m-d'),
            'note' => '-'
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $orderPfp = $model->orderPfp;

            if($orderPfp->status != $orderPfp::STATUS_APPROVED){
                Yii::$app->session->setFlash('error', 'Status order pfp tidak valid, kartu proses tidak bisa dibuat.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            /**
             * Periksa berapa banyak Order PFP yang sudah dibuat kartu prosesnya
             * kalau sudah mencukupi, jangan ijinkan pembuatan ini
            */
            $jumlahKartuProses = $orderPfp->getTrnKartuProsesPfps()
                ->andWhere(['!=', 'status', TrnKartuProsesPfp::STATUS_GAGAL_PROSES])
                ->count('id');

            if ($jumlahKartuProses >= $orderPfp->qty) {
                $model->addError('order_pfp_id', 'Order PFP ini sudah tercukupi kartu prosesnya, sudah dibuat sebanyak '.$jumlahKartuProses.' kartu proses.');
                return $this->render('create', ['model' => $model]);
            }

            // periksa apakah nomor kartu dan motif sudah pernah dibuat atau belum, jika sudah pernah, maka diblokir
            $qMotifNoKartuExists = (new Query())->from(TrnKartuProsesPfp::tableName())
                ->select(new Expression(1))
                ->where(['greige_id'=>$orderPfp->greige_id, 'nomor_kartu'=>$model->nomor_kartu])
                ->exists()
            ;
            if($qMotifNoKartuExists){
                Yii::$app->session->setFlash('error', 'Nomor kartu dan motif tidak valid.');
                $model->addError('nomor_kartu', 'Nomor kartu dan motif tidak valid.');
                return $this->render('create', ['model' => $model]);
            }

            $handling = $orderPfp->handling;
            $model->handling = $handling->name;
            $model->lebar_preset = $handling->lebar_preset;
            $model->lebar_finish = $handling->lebar_finish;
            $model->berat_finish = $handling->berat_finish;
            $model->t_density_lusi = $handling->densiti_lusi;
            $model->t_density_pakan = $handling->densiti_pakan;

            $model->greige_group_id = $orderPfp->greige_group_id;
            $model->greige_id = $orderPfp->greige_id;
            $model->save(false);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing TrnKartuProsesPfp model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk dirubah.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $orderPfp = $model->orderPfp;
            $model->greige_group_id = $orderPfp->greige_group_id;
            $model->greige_id = $orderPfp->greige_id;
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }*/

    /**
     * Deletes an existing TrnKartuProsesPfp model.
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

        TrnKartuProsesPfpItem::deleteAll(['kartu_process_id'=>$model->id]);
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

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk diseting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->no_limit_item = true;
        $model->save(false, ['no_limit_item']);
        Yii::$app->session->setFlash('success', 'Berhasil, sekarang kartu proses ini sudah tidak dibatasi jumlah item nya.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Membuat kartu proses tidak lagi dibatasi jumlah itemnya berdasarkan qty per batch greige terkait
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSetLimitedItem($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk diseting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->no_limit_item = false;
        $model->save(false, ['no_limit_item']);
        Yii::$app->session->setFlash('success', 'Berhasil, sekarang kartu proses ini sudah dibatasi jumlah item nya.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Deletes an existing KartuProsesPfp model.
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

        $orderPfp = $model->orderPfp;
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
            foreach ($model->trnKartuProsesPfpItems as $trnKartuProsesPfpItem) {
                $stockItem = $trnKartuProsesPfpItem->stock;
                if($stockItem->status == $stockItem::STATUS_ON_PROCESS_CARD){
                    //BaseVarDumper::dump($trnKartuProsesPfpItem, 10, true);Yii::$app->end();
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

                switch ($trnKartuProsesPfpItem->tube){
                    case $trnKartuProsesPfpItem::TUBE_KIRI:
                        $totalTubeKiri += (float)$stockItem->panjang_m;
                        break;
                    case $trnKartuProsesPfpItem::TUBE_KANAN:
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

            /**
             * Periksa berapa banyak Order PFP yang sudah dibuat kartu prosesnya
             * kalau sudah mencukupi, ubah status nya jadi processed
             */
            $jumlahKartuProses = $orderPfp->getTrnKartuProsesPfps()->count('id');
            if($jumlahKartuProses >= $orderPfp->qty){
                if($orderPfp->status == $orderPfp::STATUS_APPROVED){
                    $orderPfp->status = $orderPfp::STATUS_PROCESSED;
                    if(!$orderPfp->save(false, ['status'])){
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('error', 'Gagal, coba lagi.');
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                }
            }

            $qtyBatch = $model->greigeGroup->qty_per_batch;
            //menghitung selisih antaara total panjang dan qty per batch
            $difference = 0;

            if ($totalLength > $qtyBatch) {
                $difference = abs($totalLength - $qtyBatch);
            }
            // BaseVarDumper::dump($difference, 10, true);Yii::$app->end();
            //Booking greige--------------------------------------------------------------------------------------------

            switch ($model->orderPfp->jenis_gudang){

                case TrnStockGreige::JG_PFP:
                    $availableAttr = 'available_pfp';
                    break;
                case TrnStockGreige::JG_FRESH:
                    $availableAttr = 'available';
                    break;
                default:
                    $availableAttr = 'available';
            }
            Yii::$app->db->createCommand()->update(
                MstGreige::tableName(),
                [   
                    'booked_opfp' => new \yii\db\Expression('booked_opfp - ' . ($totalLength - $difference)),
                    'booked' => new \yii\db\Expression('booked + ' . $totalLength),
                    $availableAttr => new \yii\db\Expression($availableAttr . ' - ' . $difference),
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
     * Finds the TrnKartuProsesPfp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnKartuProsesPfp the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnKartuProsesPfp::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionEditNomorKartu($id)
    {
        $model = $this->findModel($id);

        // cek status dulu
        if (!in_array($model->status, [
            $model::STATUS_DRAFT,
            $model::STATUS_POSTED,
            $model::STATUS_DELIVERED,
        ])) {
            Yii::$app->session->setFlash('error', 'Status tidak valid.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        // Jika submit POST
        if ($model->load(Yii::$app->request->post())) {

            // validasi nomor_kartu + greige_id tidak duplikat
            $qExists = (new \yii\db\Query())
                ->from(\common\models\ar\TrnKartuProsesPfp::tableName())
                ->select(new \yii\db\Expression('1'))
                ->where([
                    'greige_id'   => $model->greige_id,
                    'nomor_kartu' => $model->nomor_kartu,
                ])
                ->andWhere(['<>', 'id', $model->id])
                ->exists();

            if ($qExists) {
                $model->addError('nomor_kartu', 'Nomor kartu dan motif sudah dipakai.');
            }

            if ($model->hasErrors()) {
                // kirim balik form dengan error (HTML)
                return $this->asJson([
                    'success' => false,
                    'html' => $this->renderAjax('_form_nomor_kartu', ['model' => $model]),
                ]);
            }

            // ==== update field no ====
        $namaMotif = $model->greige->nama_kain ?? ''; // atau relasi lain sesuai model kamu
        $model->no = $namaMotif . '/' . $model->nomor_kartu;
        // ==== end update field no ====

        // simpan field nomor_kartu dan no sekaligus
        $model->save(false, ['nomor_kartu','no']);

            // simpan
            $model->save(false, ['nomor_kartu']);
            Yii::$app->session->setFlash('success', 'Nomor kartu berhasil diupdate.');
            return $this->asJson(['success' => true]);
        }

        // pertama kali buka modal → kembalikan partial
        return $this->renderAjax('_form_nomor_kartu', ['model' => $model]);
    }

}