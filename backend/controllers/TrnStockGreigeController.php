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
use common\models\ar\TrnStockGreigeSearch;
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
        $searchModel = new TrnStockGreigeSearch(['jenis_gudang'=>TrnStockGreige::JG_FRESH]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
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
     * Creates a new TrnStockGreige model.
     * If creation is successful, the browser will be redirected to the 'view' page.
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

            //BaseVarDumper::dump([$model], 10, true);Yii::$app->end();

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsStock) && $valid;

            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try{
                    $greyQty = 0;
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
                        $modelStock->date = $date;
                        $modelStock->status = $modelStock::STATUS_VALID;
                        $modelStock->jenis_gudang = $modelStock::JG_FRESH;
                        if(!$modelStock->save(false)){
                            $transaction->rollBack();
                            Yii::$app->session->setFlash('error', 'Gagal memproses, coba lagi 1.');
                            return $this->render('create-dua', ['model' => $model, 'modelsStock' => (empty($modelsStock)) ? [new StockGreige] : $modelsStock]);
                        }

                        $greyQty += $modelStock->panjang_m;
                    }

                    //Yii::$app->db->createCommand('UPDATE mst_greige SET stock = stock + '.$greyQty.' WHERE id=:id')->bindParam(':id', $model->greige_id)->execute();
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
                    Yii::$app->session->setFlash('success', 'Proses berhasil.');
                    return $this->redirect(['index']);
                }catch (\Throwable $e){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage().'---');
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

            return ['ids'=>$ids, 'status_tsd'=>$ketWeaving];
        }

        throw new ForbiddenHttpException('Hanya ajajx call yang diizinkan.');
    }

    /**
     * @throws ForbiddenHttpException
     */
    public function actionSeluruhStock(){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $wjl = TrnStockGreige::find()->where(['asal_greige'=>TrnStockGreige::ASAL_GREIGE_WJL, 'status'=>TrnStockGreige::STATUS_VALID])->sum('panjang_m');
            $rap = TrnStockGreige::find()->where(['asal_greige'=>TrnStockGreige::ASAL_GREIGE_RAPIER, 'status'=>TrnStockGreige::STATUS_VALID])->sum('panjang_m');
            $lokal = TrnStockGreige::find()->where(['asal_greige'=>TrnStockGreige::ASAL_GREIGE_BELI, 'status'=>TrnStockGreige::STATUS_VALID])->sum('panjang_m');
            $import = TrnStockGreige::find()->where(['asal_greige'=>TrnStockGreige::ASAL_GREIGE_BELI_IMPORT, 'status'=>TrnStockGreige::STATUS_VALID])->sum('panjang_m');

            $data = [
                'water_jet_loom' => $wjl > 0 ? $wjl : 0,
                'rapier_loom' => $rap > 0 ? $rap : 0,
                'beli_lokal' => $lokal > 0 ? $lokal : 0,
                'beli_import' => $import > 0 ? $import : 0,
            ];

            return $this->renderAjax('seluruh-stock', ['data'=>$data]);
        }

        throw new ForbiddenHttpException('Hanya ajajx call yang diizinkan.');
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
}
