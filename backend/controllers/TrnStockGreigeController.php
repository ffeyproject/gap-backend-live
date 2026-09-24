<?php

namespace backend\controllers;

use backend\models\ar\StockGreige;
use backend\models\form\StockGreigeForm;
use backend\models\TrnStockGreigePrSearch;
use common\models\ar\MstGreige;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnMixedGreige;
use common\models\ar\TrnMixedGreigeItem;
use common\models\Model;
use common\models\rekap\LaporanStockSearch;
use Yii;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnStockGreigeDaily;
use common\models\ar\TrnStockGreigeSearch;
use common\models\ar\TrnGudangInspect;
use common\models\ar\TrnGudangInspectItem;
use common\models\ar\TrnGudangInspectSearch;
use common\models\ar\TrnStockGreigeOpname;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\BaseVarDumper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UnprocessableEntityHttpException;

/**
 * TrnStockGreigeController implements the CRUD actions for TrnStockGreige model.
 */
class TrnStockGreigeController extends Controller
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
                    'delete-doc' => ['POST'],
                    'posting-doc' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all TrnStockGreige models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnStockGreigeSearch([
            'jenis_gudang' => TrnStockGreige::JG_FRESH,
        ]);

        $queryParams = Yii::$app->request->queryParams;

        if (!isset($queryParams['TrnStockGreigeSearch'])) {
            $searchModel->status = TrnStockGreige::STATUS_VALID;
        }

        $dataProvider = $searchModel->search($queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Menyimpan snapshot stock harian per motif dari Packing List Greige.
     * @return mixed
     */
    public function actionSaveDailyStock()
    {
        $date = Yii::$app->request->post('date', Yii::$app->request->get('date', date('Y-m-d')));

        try {
            $result = TrnStockGreigeDaily::snapshotStock($date);
            $msg = sprintf(
                'Berhasil menyimpan stock harian tanggal %s. Total %d motif (%s m, %d roll). Baru: %d, Terupdate: %d.',
                $result['date'],
                $result['total_motifs'],
                Yii::$app->formatter->asDecimal($result['grand_total_m']),
                $result['grand_total_roll'],
                $result['new_saved'],
                $result['updated']
            );

            if (Yii::$app->request->isAjax) {
                return $this->asJson(['success' => true, 'message' => $msg, 'data' => $result]);
            }
            Yii::$app->session->setFlash('success', $msg);
        } catch (\Throwable $e) {
            $msg = 'Gagal menyimpan snapshot stock harian: ' . $e->getMessage();
            if (Yii::$app->request->isAjax) {
                return $this->asJson(['success' => false, 'message' => $msg]);
            }
            Yii::$app->session->setFlash('error', $msg);
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    /**
     * Lists all TrnStockGreige models.
     * @return mixed
     */
    public function actionRekap()
    {
        $searchModel = new TrnStockGreigeSearch(['jenis_gudang'=>TrnStockGreige::JG_FRESH]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('rekap', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnStockGreige model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if(Yii::$app->request->isAjax){
            return $this->asJson($model->toArray());
        }else{
            return $this->render('view', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new TrnStockGreige model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnStockGreige();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->date = date('Y-m-d');
            $model->greige_group_id = $model->greige->group_id;
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new TrnStockGreige bulk model (Draft / Pending).
     * If creation is successful, the browser will be redirected to the 'view-doc' page.
     * @return mixed
     */
    public function actionCreateDua()
    {
        $model = new StockGreigeForm();

        /* @var $modelsStock StockGreige[]*/
        $modelsStock = [new StockGreige()];

        if ($model->load(Yii::$app->request->post())) {
            $modelsStock = Model::createMultiple(StockGreige::classname());
            Model::loadMultiple($modelsStock, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsStock) && $valid;

            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try{
                    $date = date('Y-m-d');
                    foreach ($modelsStock as $modelStock) {
                        $modelStock->greige_id = $model->greige_id;
                        $modelStock->greige_group_id = MstGreige::findOne($model->greige_id)->group_id;
                        $modelStock->asal_greige = $model->asal_greige;
                        $modelStock->no_lapak = $model->no_lapak;
                        $modelStock->lot_lusi = $model->lot_lusi;
                        $modelStock->lot_pakan = $model->lot_pakan;
                        $modelStock->status_tsd = $model->status_tsd;
                        $modelStock->no_document = $model->no_document;
                        $modelStock->pengirim = $model->pengirim;
                        $modelStock->mengetahui = $model->mengetahui;
                        $modelStock->note = $model->note;
                        $modelStock->is_hasil_setting = $model->is_hasil_setting ? true : false;
                        $modelStock->date = $date;
                        $modelStock->status = $modelStock::STATUS_PENDING;
                        $modelStock->jenis_gudang = $modelStock::JG_FRESH;
                        if(!$modelStock->save(false)){
                            $transaction->rollBack();
                            Yii::$app->session->setFlash('error', 'Gagal memproses data roll, periksa kembali inputan Anda.');
                            return $this->render('create-dua', ['model' => $model, 'modelsStock' => (empty($modelsStock)) ? [new StockGreige] : $modelsStock]);
                        }
                    }

                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Packing list berhasil disimpan sebagai DRAFT (Pending). Silakan periksa kembali dan klik "Posting ke Stock" untuk memasukkannya ke stock.');
                    return $this->redirect(['view-doc', 'no_doc' => $model->no_document]);
                }catch (\Throwable $e){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());
                    return $this->render('create-dua', ['model' => $model, 'modelsStock' => (empty($modelsStock)) ? [new StockGreige] : $modelsStock]);
                }
            }
        }

        return $this->render('create-dua', [
            'model' => $model,
            'modelsStock' => (empty($modelsStock)) ? [new StockGreige] : $modelsStock
        ]);
    }

    /**
     * Menampilkan dokumen packing list greige per no_document.
     * @param string $no_doc
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionViewDoc($no_doc)
    {
        // Prioritaskan mengambil item yang masih PENDING untuk nomor dokumen ini
        $models = TrnStockGreige::find()->where(['no_document' => $no_doc, 'status' => TrnStockGreige::STATUS_PENDING])->orderBy(['id' => SORT_ASC])->all();
        
        // Jika tidak ada data pending (misalnya dokumen sudah diposting), ambil seluruh data untuk no_doc tersebut
        if (empty($models)) {
            $models = TrnStockGreige::find()->where(['no_document' => $no_doc])->orderBy(['id' => SORT_ASC])->all();
        }

        if (empty($models)) {
            throw new NotFoundHttpException('Dokumen packing list greige tidak ditemukan.');
        }

        $header = $models[0];
        $totalRoll = count($models);
        $totalMeter = 0;
        $gradeBreakdown = [];
        $gradeOptions = StockGreige::gradeOptions();

        foreach ($models as $item) {
            $totalMeter += (float)$item->panjang_m;
            $gLabel = isset($gradeOptions[$item->grade]) ? $gradeOptions[$item->grade] : ($item->grade ?: 'Lainnya');
            if (!isset($gradeBreakdown[$gLabel])) {
                $gradeBreakdown[$gLabel] = ['qty' => 0, 'roll' => 0];
            }
            $gradeBreakdown[$gLabel]['qty'] += (float)$item->panjang_m;
            $gradeBreakdown[$gLabel]['roll'] += 1;
        }

        return $this->render('view-doc', [
            'noDoc' => $no_doc,
            'header' => $header,
            'models' => $models,
            'totalRoll' => $totalRoll,
            'totalMeter' => $totalMeter,
            'gradeBreakdown' => $gradeBreakdown,
        ]);
    }

    /**
     * Mengubah packing list greige yang statusnya masih PENDING.
     * @param string $no_doc
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdateDoc($no_doc)
    {
        $items = TrnStockGreige::find()->where(['no_document' => $no_doc, 'status' => TrnStockGreige::STATUS_PENDING])->orderBy(['id' => SORT_ASC])->all();
        if (empty($items)) {
            throw new NotFoundHttpException('Dokumen tidak ditemukan atau sudah diposting.');
        }

        $firstItem = $items[0];
        $model = new StockGreigeForm();
        $model->greige_id = $firstItem->greige_id;
        $model->asal_greige = $firstItem->asal_greige;
        $model->no_lapak = $firstItem->no_lapak;
        $model->lot_lusi = $firstItem->lot_lusi;
        $model->lot_pakan = $firstItem->lot_pakan;
        $model->status_tsd = $firstItem->status_tsd;
        $model->no_document = $firstItem->no_document;
        $model->pengirim = $firstItem->pengirim;
        $model->mengetahui = $firstItem->mengetahui;
        $model->note = $firstItem->note;
        $model->is_hasil_setting = $firstItem->is_hasil_setting ? 1 : 0;

        $modelsStock = [];
        foreach ($items as $item) {
            $s = new StockGreige();
            $s->grade = $item->grade;
            $s->no_set_lusi = $item->no_set_lusi;
            $s->panjang_m = (float)$item->panjang_m;
            $modelsStock[] = $s;
        }

        if ($model->load(Yii::$app->request->post())) {
            $modelsStock = Model::createMultiple(StockGreige::classname());
            Model::loadMultiple($modelsStock, Yii::$app->request->post());

            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsStock) && $valid;

            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    // Delete previous pending records of this document
                    TrnStockGreige::deleteAll([
                        'no_document' => $no_doc,
                        'status' => TrnStockGreige::STATUS_PENDING
                    ]);

                    $date = date('Y-m-d');
                    foreach ($modelsStock as $modelStock) {
                        $modelStock->greige_id = $model->greige_id;
                        $modelStock->greige_group_id = MstGreige::findOne($model->greige_id)->group_id;
                        $modelStock->asal_greige = $model->asal_greige;
                        $modelStock->no_lapak = $model->no_lapak;
                        $modelStock->lot_lusi = $model->lot_lusi;
                        $modelStock->lot_pakan = $model->lot_pakan;
                        $modelStock->status_tsd = $model->status_tsd;
                        $modelStock->no_document = $model->no_document;
                        $modelStock->pengirim = $model->pengirim;
                        $modelStock->mengetahui = $model->mengetahui;
                        $modelStock->note = $model->note;
                        $modelStock->is_hasil_setting = $model->is_hasil_setting ? true : false;
                        $modelStock->date = $date;
                        $modelStock->status = $modelStock::STATUS_PENDING;
                        $modelStock->jenis_gudang = $modelStock::JG_FRESH;
                        if (!$modelStock->save(false)) {
                            $transaction->rollBack();
                            Yii::$app->session->setFlash('error', 'Gagal menyimpan perubahan roll.');
                            return $this->render('update-dua', ['model' => $model, 'modelsStock' => (empty($modelsStock)) ? [new StockGreige] : $modelsStock]);
                        }
                    }

                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Dokumen berhasil diperbarui.');
                    return $this->redirect(['view-doc', 'no_doc' => $model->no_document]);
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());
                    return $this->render('update-dua', ['model' => $model, 'modelsStock' => (empty($modelsStock)) ? [new StockGreige] : $modelsStock]);
                }
            }
        }

        return $this->render('update-dua', [
            'model' => $model,
            'modelsStock' => (empty($modelsStock)) ? [new StockGreige] : $modelsStock,
        ]);
    }

    /**
     * Menghapus dokumen packing list greige yang berstatus PENDING.
     * @param string $no_doc
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDeleteDoc($no_doc)
    {
        $condition = ['no_document' => $no_doc, 'status' => TrnStockGreige::STATUS_PENDING];
        $items = TrnStockGreige::findAll($condition);
        if (empty($items)) {
            throw new NotFoundHttpException('Dokumen draft tidak ditemukan atau sudah diposting.');
        }

        TrnStockGreige::deleteAll($condition);
        Yii::$app->session->setFlash('success', "Dokumen {$no_doc} berhasil dihapus.");
        return $this->redirect(['index']);
    }

    /**
     * Memposting dokumen packing list greige (STATUS_PENDING -> STATUS_VALID) dan menambahkan stock ke mst_greige.
     * @param string $no_doc
     * @return mixed
     */
    public function actionPostingDoc($no_doc)
    {
        $condition = ['no_document' => $no_doc, 'status' => TrnStockGreige::STATUS_PENDING];
        $items = TrnStockGreige::findAll($condition);
        if (empty($items)) {
            Yii::$app->session->setFlash('error', 'Tidak ada data pending yang dapat diposting untuk dokumen ini.');
            return $this->redirect(['view-doc', 'no_doc' => $no_doc]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $greyQty = [];
            foreach ($items as $item) {
                $item->status = TrnStockGreige::STATUS_VALID;
                if (!$item->save(false, ['status'])) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal memposting data item.');
                    return $this->redirect(['view-doc', 'no_doc' => $no_doc]);
                }

                if (!isset($greyQty[$item->greige_id])) {
                    $greyQty[$item->greige_id] = 0;
                }
                $greyQty[$item->greige_id] += (float)$item->panjang_m;
            }

            foreach ($greyQty as $greigeId => $qty) {
                Yii::$app->db->createCommand()
                    ->update(
                        MstGreige::tableName(),
                        [
                            'stock' => new Expression("mst_greige.stock + {$qty}"),
                            'available' => new Expression("mst_greige.available + {$qty}")
                        ],
                        ['id' => $greigeId]
                    )->execute();
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', "Dokumen {$no_doc} berhasil diposting ke database stock.");
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Gagal memposting: ' . $e->getMessage());
        }

        return $this->redirect(['view-doc', 'no_doc' => $no_doc]);
    }

    /**
     * Creates a new TrnStockGreige model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionProcess()
    {
        $searchModel = new TrnStockGreigePrSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('process', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @param $noDoc
     * @return \yii\web\Response
     */
    public function actionExecuteProcess($noDoc)
    {
        $models = TrnStockGreige::findAll(['no_document'=>$noDoc, 'status'=>TrnStockGreige::STATUS_PENDING]);

        if($models !== null){
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;

            try{
                $greyQty = [];
                foreach ($models as $model) {
                    $model->status = TrnStockGreige::STATUS_VALID;
                    if(!$flag = $model->save(false, ['status'])){
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('error', 'Gagal memproses, coba lagi 1.');
                        return $this->redirect(['process', 'TrnStockGreigePrSearch[no_document]'=>$noDoc]);
                    }

                    if(array_key_exists($model->greige_id, $greyQty)){
                        $greyQty[$model->greige_id] += $model->panjang_m;
                    }else{
                        $greyQty[$model->greige_id]=$model->panjang_m;
                    }
                }

                //BaseVarDumper::dump($greyQty, 10, true);Yii::$app->end();

                foreach ($greyQty as $key=>$item) {
                    $command = Yii::$app->db->createCommand('UPDATE mst_greige SET stock = stock + '.$item.' WHERE id=:id')->bindParam(':id', $key);
                    if(!$flag = $command->execute() > 0){
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('error', 'Gagal memproses, coba lagi 2.');
                        return $this->redirect(['process', 'TrnStockGreigePrSearch[no_document]'=>$noDoc]);
                    }
                }

                if($flag){
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Proses berhasil.');
                    return $this->redirect(['index', 'TrnStockGreigeSearch[no_document]'=>$noDoc]);
                }
            }catch (\Throwable $e){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect(['process', 'TrnStockGreigePrSearch[no_document]'=>$noDoc]);
            }
        }else{
            Yii::$app->session->setFlash('error', 'Tidak ada data untuk diproses.');
        }

        return $this->redirect(['process']);
    }

    /**
     * Updates an existing TrnStockGreige model.
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
     * Deletes an existing TrnStockGreige model.
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
        $model->delete();

        // kurangi stock di mst_greige
        //Yii::$app->db->createCommand()->update('mst_greige', ['stock'=>new Expression('mst_greige.stock - '.$model->panjang_m)], ['id'=>$model->greige_id])->execute();
        Yii::$app->db->createCommand()
            ->update(
                MstGreige::tableName(),
                [
                    'stock' => new Expression("mst_greige.stock - {$model->panjang_m}"),
                    'available' => new Expression("mst_greige.available - {$model->panjang_m}")
                ],
                ['id'=>$model->greige_id]
            )->execute();

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing TrnStockGreige model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws NotFoundHttpException if the model cannot be found
     * @throws UnprocessableEntityHttpException
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function actionMixQuality(){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $data = Yii::$app->request->post('formData');

            $greige = MstGreige::findOne($data['greigeId']);
            if($greige === null){
                throw new NotFoundHttpException('Greige target mix tidak valid.');
            }

            $greigeGroup = $greige->group;

            $newModelMix = new TrnMixedGreige([
                'greige_id' => $greige->id,
                'status' => TrnMixedGreige::STATUS_POSTED,
            ]);

            //model stock greige baru, otomatis masuk ke jenis gudang fresh.
            $modelNewStock = new TrnStockGreige([
                'greige_group_id' => $greige->group_id,
                'greige_id' => $greige->id,
                'asal_greige' => TrnStockGreige::ASAL_GREIGE_WJL,
                'no_lapak' => '-',
                'grade' => $data['greigeGrade'],
                'lot_lusi' => '-',
                'lot_pakan' => '-',
                'no_set_lusi' => '-',
                //'panjang_m' => 0,
                'status_tsd' => TrnStockGreige::STATUS_TSD_NORMAL,
                'no_document' => '-',
                'pengirim' => '-',
                'mengetahui' => '-',
                'note' => 'Mix Quality',
                'status' => TrnStockGreige::STATUS_VALID,
                'date' => date('Y-m-d'),
                'jenis_gudang' => TrnStockGreige::JG_FRESH,
                //'nomor_wo' => null,
                //'keputusan_qc' => null,
                //'color' => null,
                //'pfp_jenis_gudang' => null,
                //'is_pemotongan' => false,
                'is_hasil_mix' => true,
            ]);

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if(!$newModelMix->save(false)){
                    $transaction->rollBack();
                    throw new HttpException('500', 'Gagal, coba lagi. (2)');
                }

                $qty = 0;
                foreach ($data['keys'] as $stockId) {
                    $stockGreige = TrnStockGreige::findOne($stockId);
                    if($stockGreige === null){
                        $transaction->rollBack();
                        throw new NotFoundHttpException('Stock greige yang akan dipotong tidak ditemukan.');
                    }

                    $stckGreigeGroup = $stockGreige->greigeGroup;

                    //periksa jenis unit greige group setiap item harus dipastikan sama denga unit greige target mix
                    if($greigeGroup->unit != $stckGreigeGroup->unit){
                        throw new UnprocessableEntityHttpException('Unit salah satu greige yang akan di mix tidak sesuai dengan unit greige groop target mixing.');
                    }

                    $stockGreige->status = $stockGreige::STATUS_MIXED;
                    if(!$stockGreige->save(false, ['status'])){
                        $transaction->rollBack();
                        throw new HttpException('500', 'Gagal, coba lagi. (3)');
                    }

                    //kurangi stock greige terkait
                    Yii::$app->db->createCommand()
                        ->update(
                            MstGreige::tableName(),
                            [
                                'stock' => new Expression("mst_greige.stock - {$stockGreige->panjang_m}"),
                                'available' => new Expression("mst_greige.available - {$stockGreige->panjang_m}")
                            ],
                            ['id'=>$stockGreige->greige_id]
                        )->execute();

                    $newModelMixItem = new TrnMixedGreigeItem([
                        'mix_id' => $newModelMix->id,
                        'stock_greige_id' => $stockGreige->id,
                    ]);
                    if(!$newModelMixItem->save(false)){
                        $transaction->rollBack();
                        throw new HttpException('500', 'Gagal, coba lagi. (4)');
                    }

                    $qty += $stockGreige->panjang_m;
                }

                $modelNewStock->panjang_m = $qty;
                if(!$modelNewStock->save()){
                    $transaction->rollBack();
                    throw new HttpException('500', 'Gagal, coba lagi. (1)');
                }

                //tambah stock greige terkait
                Yii::$app->db->createCommand()
                    ->update(
                        MstGreige::tableName(),
                        [
                            'stock' => new Expression("mst_greige.stock + {$modelNewStock->panjang_m}"),
                            'available' => new Expression("mst_greige.available + {$modelNewStock->panjang_m}")
                        ],
                        ['id'=>$modelNewStock->greige_id]
                    )->execute();

                $transaction->commit();
                return true;
            }catch (\Throwable $t){
                $transaction->rollBack();
                throw $t;
            }
        }

        throw new ForbiddenHttpException('Method tidak diizinkan');
    }

    /**
     * Lists all TrnStockGreige models.
     * @return mixed
     */
    public function actionLaporanStock()
    {
        $searchModel = new LaporanStockSearch(['jenis_gudang'=>TrnStockGreige::JG_FRESH]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['status'=>TrnStockGreige::STATUS_VALID]);
        $dataProvider->pagination = false;

        $datas = [];
        foreach ($dataProvider->models as $model) {
            /* @var $model TrnStockGreige*/
            $key = $model->lot_lusi.$model->lot_pakan;
            $gw = strtolower($model->gradeName);

            if(isset($datas[$model->greige_id])){
                if(isset($datas[$model->greige_id][$key])){
                    $datas[$model->greige_id][$key][$gw] += $model->panjang_m;
                    $datas[$model->greige_id][$key]['total'] += $model->panjang_m;

                    $datas[$model->greige_id][$key]['keterangan'] .= ', '.$model->note;
                    /*if($datas[$model->greige_id][$key]['keterangan'] == ''){
                        $datas[$model->greige_id][$key]['keterangan'] = $model->note;
                    }*/
                }else{
                    $datas[$model->greige_id][$key] = [
                        'lebar_kain' => $model->greigeGroup->lebarKainName,
                        'lot_lusi' => '{'.$model->lot_lusi.'}',
                        'lot_pakan' => '{'.$model->lot_pakan.'}',
                        'kondisi_greige' => $model->kondisiGreige,
                        'asal_greige' => $model->asalGreige,
                        'a' => $gw === 'a' ? $model->panjang_m : 0,
                        'b' => $gw === 'b' ? $model->panjang_m : 0,
                        'c' => $gw === 'c' ? $model->panjang_m : 0,
                        'd' => $gw === 'd' ? $model->panjang_m : 0,
                        'ng' => $gw === 'x' ? $model->panjang_m : 0,
                        'total' => $model->panjang_m,
                        'keterangan' => $model->note
                    ];
                }
                $datas[$model->greige_id]['jumlah_stock'] += $model->panjang_m;
            }else{
                $datas[$model->greige_id] = [
                    $key => [
                        'lebar_kain' => $model->greigeGroup->lebarKainName,
                        'lot_lusi' => '{'.$model->lot_lusi.'}',
                        'lot_pakan' => '{'.$model->lot_pakan.'}',
                        'kondisi_greige' => $model->kondisiGreige,
                        'asal_greige' => $model->asalGreige,
                        'a' => $gw === 'a' ? $model->panjang_m : 0,
                        'b' => $gw === 'b' ? $model->panjang_m : 0,
                        'c' => $gw === 'c' ? $model->panjang_m : 0,
                        'd' => $gw === 'd' ? $model->panjang_m : 0,
                        'ng' => $gw === 'x' ? $model->panjang_m : 0,
                        'total' => $model->panjang_m,
                        'keterangan' => $model->note
                    ],
                    'date' => '',
                    'motif' => $model->greigeNamaKain,
                    'jumlah_stock' => $model->panjang_m
                ];
            }
            //$datas[(string)$model->greigeNamaKain][$model->lot_lusi][$model->grade] += $model->panjang_m;
        }

        //BaseVarDumper::dump($datas, 10, true);Yii::$app->end();

        return $this->render('laporan-stock', [
            'searchModel' => $searchModel,
            'datas' => $datas,
        ]);
    }

    /**
     * @throws ForbiddenHttpException
     */
    public function actionChangeNotes(){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $ids = Yii::$app->request->post('ids');
            $note = Yii::$app->request->post('note');

            Yii::$app->db->createCommand()->update(
                TrnStockGreige::tableName(),
                ['note'=>$note],
                ['in', 'id', $ids]
            )->execute();

            return ['ids'=>$ids, 'note'=>$note];
        }

        throw new ForbiddenHttpException('Hanya ajajx call yang diizinkan.');
    }

    /**
     * @throws ForbiddenHttpException
     */
    public function actionChangeKetWeaving(){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $ids = Yii::$app->request->post('ids');
            $ketWeaving = Yii::$app->request->post('ket_weaving');

            Yii::$app->db->createCommand()->update(
                TrnStockGreige::tableName(),
                ['status_tsd'=>$ketWeaving],
                ['in', 'id', $ids]
            )->execute();

            // Juga update status_tsd pada TrnStockGreigeOpname yang terhubung
            Yii::$app->db->createCommand()->update(
                TrnStockGreigeOpname::tableName(),
                ['status_tsd'=>$ketWeaving],
                ['in', 'stock_greige_id', $ids]
            )->execute();

            return ['ids'=>$ids, 'status_tsd'=>$ketWeaving];
        }

        throw new ForbiddenHttpException('Hanya ajajx call yang diizinkan.');
    }

    public function actionSyncStatusOpname()
    {
        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
            throw new ForbiddenHttpException('Method tidak diizinkan.');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $ids = Yii::$app->request->post('ids', []);

        if (!empty($ids) && is_array($ids)) {
            $idsClean = implode(',', array_map('intval', $ids));
            $updated = Yii::$app->db->createCommand("
                UPDATE trn_stock_greige_opname o
                SET status_tsd = s.status_tsd
                FROM trn_stock_greige s
                WHERE o.stock_greige_id = s.id
                AND o.stock_greige_id IN ($idsClean)
                AND o.status_tsd <> s.status_tsd
            ")->execute();
        } else {
            $updated = Yii::$app->db->createCommand("
                UPDATE trn_stock_greige_opname o
                SET status_tsd = s.status_tsd
                FROM trn_stock_greige s
                WHERE o.stock_greige_id = s.id
                AND o.status_tsd <> s.status_tsd
            ")->execute();
        }

        return [
            'status' => true,
            'message' => "Berhasil menyelaraskan $updated data status Keterangan Weaving di Stock Opname."
        ];
    }

    /**
     * @throws ForbiddenHttpException
     */
    // public function actionSeluruhStock(){
    //     if(Yii::$app->request->isAjax){
    //         Yii::$app->response->format = Response::FORMAT_JSON;

    //         $wjl = TrnStockGreige::find()->where(['asal_greige'=>TrnStockGreige::ASAL_GREIGE_WJL, 'status'=>TrnStockGreige::STATUS_VALID])->sum('panjang_m');
    //         $rap = TrnStockGreige::find()->where(['asal_greige'=>TrnStockGreige::ASAL_GREIGE_RAPIER, 'status'=>TrnStockGreige::STATUS_VALID])->sum('panjang_m');
    //         $lokal = TrnStockGreige::find()->where(['asal_greige'=>TrnStockGreige::ASAL_GREIGE_BELI, 'status'=>TrnStockGreige::STATUS_VALID])->sum('panjang_m');
    //         $import = TrnStockGreige::find()->where(['asal_greige'=>TrnStockGreige::ASAL_GREIGE_BELI_IMPORT, 'status'=>TrnStockGreige::STATUS_VALID])->sum('panjang_m');

    //         $data = [
    //             'water_jet_loom' => $wjl > 0 ? $wjl : 0,
    //             'rapier_loom' => $rap > 0 ? $rap : 0,
    //             'beli_lokal' => $lokal > 0 ? $lokal : 0,
    //             'beli_import' => $import > 0 ? $import : 0,
    //         ];

    //         return $this->renderAjax('seluruh-stock', ['data'=>$data]);
    //     }

    //     throw new ForbiddenHttpException('Hanya ajajx call yang diizinkan.');
    // }

    public function actionSeluruhStock(){
    if(Yii::$app->request->isAjax){
    Yii::$app->response->format = Response::FORMAT_JSON;

    $asalGreigeList = [
        'water_jet_loom' => TrnStockGreige::ASAL_GREIGE_WJL,
        'rapier_loom' => TrnStockGreige::ASAL_GREIGE_RAPIER,
        'beli_lokal' => TrnStockGreige::ASAL_GREIGE_BELI,
        'beli_import' => TrnStockGreige::ASAL_GREIGE_BELI_IMPORT,
    ];

    $data = [];

    foreach ($asalGreigeList as $key => $asalGreige) {
        $data[$key] = [];

        foreach (TrnStockGreige::tsdOptions() as $statusKey => $statusName) {
            $jumlah = TrnStockGreige::find()
                ->where([
                    'asal_greige' => $asalGreige,
                    'status' => TrnStockGreige::STATUS_VALID,
                    'status_tsd' => $statusKey, // Tambahkan status_tsd
                ])
                ->sum('panjang_m');

            if ($jumlah > 0) {
                $data[$key][$statusName] = $jumlah;
            }
        }
    }

    return $this->renderAjax('seluruh-stock', ['data' => $data]);
}
}

    public function actionIndexGudangInspect(){
        $searchModel = new TrnGudangInspectSearch(['jenis_gudang'=>TrnGudangInspect::JG_FRESH,'status' => TrnGudangInspect::STATUS_POSTED]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['or',
            ['status' => TrnGudangInspect::STATUS_POSTED],
            ['status' => TrnGudangInspect::STATUS_OUT]
        ]);

        return $this->render('index-gudang-inspect', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewGudangInspect($id){
        $model = TrnGudangInspect::findOne($id);
        if(Yii::$app->request->isAjax){
            return $this->asJson($model->toArray());
        }else{
            return $this->render('view-gudang-inspect', [
                'model' => $model,
            ]);
        }
    }


    public function actionTransferToGreige($id)
    {   
        $model = TrnGudangInspect::findOne($id);
        $selectedItemIds = Yii::$app->request->post('selected_items', []);
    
        if ($model->status != $model::STATUS_POSTED) {
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk transfer ke Gudang Greige.');
            return $this->redirect(['view-gudang-inspect', 'id' => $model->id]);
        }
    
        if (empty($selectedItemIds)) {
            Yii::$app->session->setFlash('warning', 'Tidak ada item yang dipilih.');
            return $this->redirect(['view-gudang-inspect', 'id' => $id]);
        }
    
        $modelSelectedItems = TrnGudangInspectItem::findAll(['id' => $selectedItemIds]);
    
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $greyQty = 0;
            $date = date('Y-m-d');
            $greige = MstGreige::findOne($model->greige_id);
    
            if (!$greige) {
                throw new \Exception('Greige tidak ditemukan.');
            }
    
            foreach ($modelSelectedItems as $modelSelectedItem) {
                // Tandai item sebagai keluar
                $modelSelectedItem->is_out = true;
                if (!$modelSelectedItem->save(false)) {
                    throw new \Exception('Gagal menyimpan status item.');
                }
    
                // Buat stock baru untuk tiap item
                $modelStock = new TrnStockGreige();
                $modelStock->greige_id = $model->greige_id;
                $modelStock->greige_group_id = $greige->group_id;
                $modelStock->asal_greige = $model->asal_greige;
                $modelStock->no_lapak = $model->no_lapak;
                $modelStock->lot_lusi = $model->lot_lusi;
                $modelStock->lot_pakan = $model->lot_pakan;
                $modelStock->status_tsd = $model->status_tsd;
                $modelStock->no_document = $model->no_document;
                $modelStock->pengirim = $model->pengirim;
                $modelStock->mengetahui = Yii::$app->user->identity->id;
                $modelStock->note = $model->note;
                $modelStock->date = $date;
                $modelStock->status = TrnStockGreige::STATUS_VALID;
                $modelStock->jenis_gudang = TrnStockGreige::JG_FRESH;
                $modelStock->panjang_m = $modelSelectedItem->panjang_m;
                $modelStock->grade = $modelSelectedItem->grade;
                $modelStock->no_set_lusi = $modelSelectedItem->no_set_lusi;
    
                if (!$modelStock->save(false)) {
                    throw new \Exception('Gagal menyimpan stock item.');
                }
    
                $greyQty += $modelSelectedItem->panjang_m;
            }

            //cek apakah masih ada item yang is_outnya = false
            if(TrnGudangInspectItem::find()->where(['trn_gudang_inspect_id'=>$model->id, 'is_out'=>false])->count() == 0){
                $model->status = TrnGudangInspect::STATUS_OUT;
                $model->save(false);
            }

            Yii::$app->db->createCommand()
            ->update(
                MstGreige::tableName(),
                [
                    'stock' => new Expression("mst_greige.stock + {$greyQty}"),
                    'available' => new Expression("mst_greige.available + {$greyQty}")
                ],
                ['id'=>$model->greige_id]
            )->execute();
    
            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Berhasil kirim ke Gudang Greige.');
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Gagal memproses: ' . $e->getMessage());
        }
    
        return $this->redirect(['view-gudang-inspect', 'id' => $id]);
    }


    public function actionSeluruhStockGudangInspect(){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $asalGreigeList = [
                'water_jet_loom' => TrnGudangInspect::ASAL_GREIGE_WJL,
                'rapier_loom' => TrnGudangInspect::ASAL_GREIGE_RAPIER,
                'beli_lokal' => TrnGudangInspect::ASAL_GREIGE_BELI,
                'beli_import' => TrnGudangInspect::ASAL_GREIGE_BELI_IMPORT,
            ];

            $data = [];

            foreach ($asalGreigeList as $key => $asalGreige) {
                $data[$key] = [];
                foreach (TrnGudangInspect::tsdOptions() as $statusKey => $statusName) {
                    $jumlah = TrnGudangInspect::find()
                        ->joinWith('trnGudangInspectItems')
                        ->where([
                            'trn_gudang_inspect.asal_greige' => $asalGreige,
                            'trn_gudang_inspect.status' => TrnGudangInspect::STATUS_POSTED, 
                            'trn_gudang_inspect.status_tsd' => $statusKey,
                        ])
                        ->sum('trn_gudang_inspect_item.panjang_m');

                    if ($jumlah > 0) {
                        $data[$key][$statusName] = $jumlah;
                    }
                }
            }

            return $this->renderAjax('seluruh-stock', ['data' => $data]);
        }

    }

    public function actionEditNoDocument($id)
    {
        $model = TrnGudangInspect::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Data Gudang Inspect tidak ditemukan.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
            Yii::$app->session->setFlash('success', 'No Document berhasil diperbarui.');
        }

        return $this->redirect(['view-gudang-inspect', 'id' => $model->id]);
    }

    /**
     * Finds the TrnStockGreige model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnStockGreige the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnStockGreige::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * @throws ForbiddenHttpException
     */
    public function actionDuplicateBulk()
    {
        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
            throw new \yii\web\ForbiddenHttpException('Method tidak diizinkan.');
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $ids = Yii::$app->request->post('ids', []);

        if (empty($ids)) {
            throw new \yii\web\BadRequestHttpException('Tidak ada item yang dipilih.');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($ids as $id) {
                $stock = TrnStockGreige::findOne($id);
                if (!$stock) {
                    throw new \yii\web\NotFoundHttpException("Stock dengan ID $id tidak ditemukan.");
                }

                // ✅ VALIDASI STATUS – misalnya cuma boleh status VALID
                if ($stock->status != TrnStockGreige::STATUS_VALID) {
                
                    throw new \yii\web\BadRequestHttpException("Stock ID $id statusnya tidak VALID.");
                }

                // ✅ CEK SUDAH PERNAH DUPLIKAT BELUM
                $sudahAda = TrnStockGreigeOpname::find()
                    ->where(['stock_greige_id' => $stock->id])
                    ->exists();

                if ($sudahAda) {
                    throw new \yii\web\BadRequestHttpException("Stock ID $id sudah pernah di-duplikat sebelumnya.");
                }

                $opname = new \common\models\ar\TrnStockGreigeOpname([
                    'stock_greige_id' => $stock->id,
                    'greige_id' => $stock->greige_id,
                    'greige_group_id' => $stock->greige_group_id,
                    'asal_greige' => $stock->asal_greige,
                    'no_lapak' => $stock->no_lapak,
                    'grade' => $stock->grade,
                    'lot_lusi' => $stock->lot_lusi,
                    'lot_pakan' => $stock->lot_pakan,
                    'no_set_lusi' => $stock->no_set_lusi,
                    'panjang_m' => $stock->panjang_m,
                    'status_tsd' => $stock->status_tsd,
                    'no_document' => $stock->no_document,
                    'pengirim' => $stock->pengirim,
                    'mengetahui' => $stock->mengetahui,
                    'note' => 'Duplikasi stock greige',
                    'status' => 2,
                    'date' => date('Y-m-d'),
                    'jenis_gudang' => 1,
                ]);

                if (!$opname->save(false)) {
                    $transaction->rollBack();
                    throw new \yii\web\HttpException(500, "Gagal menyimpan stock opname untuk stock ID $id.");
                }

                // ✅ UPDATE FIELD STOCK_OPNAME DI MST_GREIGE
                $mstGreige = \common\models\ar\MstGreige::findOne($stock->greige_id);
                if ($mstGreige) {
                    // tambahkan panjang_m ke stock_opname yang sudah ada
                    $mstGreige->stock_opname = (float)$mstGreige->stock_opname + (float)$stock->panjang_m;
                    if (!$mstGreige->save(false)) {
                        $transaction->rollBack();
                        throw new \yii\web\HttpException(500, "Gagal update stock_opname di mst_greige ID {$stock->greige_id}");
                    }
                }
            }

            $transaction->commit();
            return ['status'=>true];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function actionDuplicateRetur()
    {
        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
            throw new \yii\web\ForbiddenHttpException('Method tidak diizinkan.');
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $ids = Yii::$app->request->post('ids', []);

        if (empty($ids)) {
            throw new \yii\web\BadRequestHttpException('Tidak ada item yang dipilih.');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $updatedCount = 0;
            foreach ($ids as $id) {
                $stock = TrnStockGreige::findOne($id);
                if (!$stock) {
                    continue;
                }

                // Cek status stock fresh harus VALID (value = 2)
                if ($stock->status != TrnStockGreige::STATUS_VALID) {
                    continue;
                }

                // Cari stock opname terkait
                $opname = \common\models\ar\TrnStockGreigeOpname::find()
                    ->where(['stock_greige_id' => $stock->id])
                    ->one();

                // Jika stock opname ditemukan dan statusnya OUT / KELUAR_GUDANG (value = 5) atau status != VALID (value = 2)
                if ($opname) {
                    if ($opname->status == \common\models\ar\TrnStockGreigeOpname::STATUS_KELUAR_GUDANG || $opname->status != \common\models\ar\TrnStockGreigeOpname::STATUS_VALID) {
                        $oldStatus = $opname->status;
                        $opname->status = \common\models\ar\TrnStockGreigeOpname::STATUS_VALID;
                        $opname->note = 'hasil retur kartu proses';
                        if ($opname->save(false)) {
                            // Update field stock_opname di MstGreige jika statusnya berubah dari non-valid ke valid
                            if ($oldStatus != \common\models\ar\TrnStockGreigeOpname::STATUS_VALID) {
                                $mstGreige = \common\models\ar\MstGreige::findOne($stock->greige_id);
                                if ($mstGreige) {
                                    $mstGreige->stock_opname = (float)$mstGreige->stock_opname + (float)$stock->panjang_m;
                                    $mstGreige->save(false, ['stock_opname']);
                                }
                            }
                            $updatedCount++;
                        }
                    }
                }
            }

            $transaction->commit();
            return [
                'success' => true,
                'message' => "Berhasil memproses $updatedCount data stock opname menjadi VALID."
            ];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }


  public function actionEditQty($ids)
{
    $ids = explode(',', $ids);
    $models = \common\models\ar\TrnStockGreige::findAll($ids);

    if (Yii::$app->request->isPost) {
        $post = Yii::$app->request->post('TrnStockGreige', []);

        foreach ($models as $model) {
            if (isset($post[$model->id]['panjang_m_baru'])) {
                $qtyBaru = (float) $post[$model->id]['panjang_m_baru'];
                $qtyLama = (float) $model->panjang_m;
                $selisih = $qtyBaru - $qtyLama;

                // Update field utama di TrnStockGreige
                $model->panjang_m = $qtyBaru;
                $model->save(false, ['panjang_m']);

                // Update relasi greige jika ada (field stock, available, stock_opname)
                if ($model->greige) {
                    $greige = $model->greige;
                    $greige->stock += $selisih;
                    $greige->available += $selisih;
                    $greige->stock_opname += $selisih;
                    $greige->save(false, ['stock', 'available', 'stock_opname']);
                }

                // Update opname terkait jika ada
                $opname = \common\models\ar\TrnStockGreigeOpname::find()
                    ->where(['stock_greige_id' => $model->id])
                    ->one();
                if ($opname) {
                    $opname->panjang_m += $selisih;
                    $opname->save(false, ['panjang_m']);
                }
            }
        }

        // Jika request AJAX, kirim JSON response
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['success' => true, 'message' => 'Qty Stock & Opname berhasil diperbarui.'];
        }

        Yii::$app->session->setFlash('success', 'Qty Stock & Opname berhasil diperbarui.');
        return $this->redirect(['index']);
    }

    return $this->renderAjax('edit-qty', [
        'models' => $models,
    ]);
}


public function actionChangeNoDocument()
{
    if (!Yii::$app->request->isAjax) {
        throw new ForbiddenHttpException('Hanya AJAX yang diperbolehkan.');
    }

    Yii::$app->response->format = Response::FORMAT_JSON;

    $ids = Yii::$app->request->post('ids', []);
    $noDocument = Yii::$app->request->post('no_document');

    if (empty($ids) || empty($noDocument)) {
        return ['success' => false, 'message' => 'Data tidak lengkap'];
    }

    // Transaksi agar aman
    $transaction = Yii::$app->db->beginTransaction();
    try {

        // 1️⃣ Update no_document pada TrnStockGreige
        Yii::$app->db->createCommand()->update(
            TrnStockGreige::tableName(),
            ['no_document' => $noDocument],
            ['id' => $ids]
        )->execute();

        // 2️⃣ Update juga pada TrnStockOpname
        Yii::$app->db->createCommand()->update(
            \common\models\ar\TrnStockGreigeOpname::tableName(),
            ['no_document' => $noDocument],
            ['stock_greige_id' => $ids]
        )->execute();

        $transaction->commit();
        return ['success' => true];

    } catch (\Throwable $e) {
        $transaction->rollBack();
        return [
            'success' => false,
            'message' => $e->getMessage(),
        ];
    }
}





}