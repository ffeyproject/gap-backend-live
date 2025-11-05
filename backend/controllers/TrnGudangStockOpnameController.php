<?php

namespace backend\controllers;

use backend\models\ar\StockGreige;
use backend\models\ar\GudangStockOpname;
use backend\models\ar\GudangStockOpnameItem;
use backend\models\form\StockGreigeForm;
use backend\models\form\GudangStockOpnameForm;
use backend\models\TrnStockGreigePrSearch;
use common\models\ar\MstGreige;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnMixedGreige;
use common\models\ar\TrnMixedGreigeItem;
use common\models\Model;
use common\models\rekap\LaporanStockSearch;
use Yii;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnGudangStockOpname;
use common\models\ar\TrnGudangStockOpnameItem;
use common\models\ar\TrnGudangStockOpnameSearch;
use common\models\ar\TrnGudangStockOpnameItemSearch;
use common\models\ar\TrnStockGreigeOpname;
use common\models\ar\TrnStockGreigeSearch;
use yii\data\ActiveDataProvider;
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
use yii\helpers\ArrayHelper; 
use yii\helpers\VarDumper;


/**
 * TrnStockGreigeController implements the CRUD actions for TrnStockGreige model.
 */
class TrnGudangStockOpnameController extends Controller
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
        $searchModel = new TrnGudangStockOpnameSearch(['jenis_gudang'=>TrnGudangStockOpname::JG_FRESH]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionStockKeseluruhan()
    {
        $searchModel = new TrnGudangStockOpnameItemSearch(['is_out' => false]);
        $searchModel->statusGudangStockOpname = TrnGudangStockOpname::STATUS_POSTED;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('stock-keseluruhan', [
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


        $form = new GudangStockOpnameForm();
        $modelsStock = [new GudangStockOpnameItem()];
    
        if ($form->load(Yii::$app->request->post())) {
            // echo '<pre>';
            // print_r(Yii::$app->request->post());
            // echo '</pre>';
            // exit;
            $modelsStock = Model::createMultiple(GudangStockOpnameItem::classname());
            Model::loadMultiple($modelsStock, Yii::$app->request->post());
    
            $valid = $form->validate();
            $valid = Model::validateMultiple($modelsStock) && $valid;
    
            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    // Pindahkan data dari form ke ActiveRecord
                    $model = new GudangStockOpname();
                    $model->attributes = $form->attributes;
                    $model->date = date('Y-m-d');
                    $model->greige_group_id = $model->greige->group_id;
                    
                    if (!$model->save(false)) {
                        throw new \Exception("Gagal menyimpan data utama.");
                    }
    
                    $greyQty = 0;
                    foreach ($modelsStock as $modelStock) {
                        $modelStock->trn_gudang_stock_opname_id = $model->id;
                        if (!$modelStock->save(false)) {
                            throw new \Exception("Gagal menyimpan item.");
                        }
                        $greyQty += $modelStock->panjang_m;
                    }
    
                    // Jika perlu update stock di mst_greige
                    // Yii::$app->db->createCommand()->update(...);
    
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Proses berhasil.');
                    return $this->redirect(['view', 'id' => $model->id]);
    
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal: ' . $e->getMessage());
                }
            }
        }
    
        return $this->render('create-dua', [
            'model' => $form,
            'modelsStock' => (empty($modelsStock)) ? [new GudangStockOpnameItem()] : $modelsStock
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
        $models = TrnStockGreige::findAll(['no_document'=>$noDoc, 'status'=>TrnStockGreige::STATUS_VALID]);

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
    public function actionUpdateDua($id)
    {
        $model = GudangStockOpname::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Data tidak ditemukan.");
        }
    
        $form = new TrnGudangStockOpname();
        $form->attributes = $model->attributes;
    
        $modelsStock = $model->trnGudangStockOpnameItems; // relasi dengan GudangStockOpnameItem
        if (empty($modelsStock)) {
            $modelsStock = [new GudangStockOpnameItem()];
        }
    
        if ($form->load(Yii::$app->request->post())) {
            $oldIDs = ArrayHelper::map($modelsStock, 'id', 'id');

            // $modelsStock = Model::createMultiple(GudangStockOpnameItem::className(), $modelsStock);

            Model::loadMultiple($modelsStock, Yii::$app->request->post());
            

    
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsStock, 'id', 'id')));
            echo '<pre>';
            print_r($deletedIDs);
            echo '</pre>';
            exit;
    
            $valid = $form->validate();
            $valid = Model::validateMultiple($modelsStock) && $valid;
    
            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();

                try {
                    // Update data utama
                    $model->attributes = $form->attributes;
                    $model->greige_group_id = $model->greige->group_id;
                    if (!$model->save(false)) {
                        throw new \Exception("Gagal menyimpan data utama.");
                    }
                    
                    if (!empty($deletedIDs)) {
                        GudangStockOpnameItem::deleteAll(['id' => $deletedIDs, 'is_out' => false]);
                    }
    
                    // Simpan item baru dan update yang lama
                    foreach ($modelsStock as $modelStock) {
                        $modelStock->trn_gudang_inspect_id = $model->id;
                        if (!$modelStock->save(false)) {
                            throw new \Exception("Gagal menyimpan item.");
                        }
                    }
    
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Data berhasil diperbarui.');
                    return $this->redirect(['index']);
    
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal: ' . $e->getMessage());
                }
            }
        }
    
        return $this->render('update', [
            'model' => $form,
            'modelsStock' => $modelsStock,
        ]);
    }

    public function actionUpdate($id)
    {   
        
        $model = $this->findModel($id);
        $modelsStock = $model->getTrnGudangStockOpnameItems()->orderBy('id')->all();

        if ($model->load(Yii::$app->request->post())) {
            $oldIDs = ArrayHelper::map($modelsStock, 'id', 'id');
            $modelsItem = Model::createMultiple(TrnGudangStockOpnameItem::classname(), $modelsStock);
            Model::loadMultiple($modelsItem, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsItem, 'id', 'id')));

            
            // echo '<pre>';
            // print_r($deletedIDs);
            // echo '</pre>';
            // exit;

            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsStock) && $valid;
            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            TrnGudangStockOpnameItem::deleteAll(['id' => $deletedIDs, 'is_out' => false]);
                        }

                        foreach ($modelsItem as $modelItem) {
                            $modelItem->trn_gudang_stock_opname_id = $model->id;
                            if (!$modelItem->save(false)) {
                                throw new \Exception("Gagal menyimpan item.");
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
            'model' => $model,
            'modelsStock' => $modelsStock
        ]);
    }
    

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        return $this->redirect(['index']);
    }

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
                TrnGudangInspect::tableName(),
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
                TrnGudangInspect::tableName(),
                ['status_tsd'=>$ketWeaving],
                ['in', 'id', $ids]
            )->execute();

            return ['ids'=>$ids, 'status_tsd'=>$ketWeaving];
        }

        throw new ForbiddenHttpException('Hanya ajajx call yang diizinkan.');
    }

    public function actionPosting ($id){
        $model  = TrnGudangStockOpname::findOne($id);
        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        $model->status = TrnGudangStockOpname::STATUS_POSTED;
        $model->save();

        Yii::$app->session->setFlash('success', 'Berhasil diposting.');
        return $this->redirect(['view', 'id' => $model->id]);
    }


    public function actionBatalPosting ($id){
        $model  = TrnGudangStockOpname::findOne($id);
        if($model->status != $model::STATUS_POSTED){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        $model->status = TrnGudangStockOpname::STATUS_DRAFT;
        $model->save();

        Yii::$app->session->setFlash('success', 'Berhasil dibatalkan.');
        return $this->redirect(['view', 'id' => $model->id]);
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
                'water_jet_loom' => TrnGudangStockOpname::ASAL_GREIGE_WJL,
                'rapier_loom' => TrnGudangStockOpname::ASAL_GREIGE_RAPIER,
                'beli_lokal' => TrnGudangStockOpname::ASAL_GREIGE_BELI,
                'beli_import' => TrnGudangStockOpname::ASAL_GREIGE_BELI_IMPORT,
            ];

            $data = [];

            foreach ($asalGreigeList as $key => $asalGreige) {
                $data[$key] = [];
                foreach (TrnGudangStockOpname::tsdOptions() as $statusKey => $statusName) {
                    $jumlah = TrnGudangStockOpname::find()
                        ->joinWith('trnGudangStockOpnameItems')
                        ->where([
                            'trn_gudang_stock_opname.asal_greige' => $asalGreige,
                            'trn_gudang_stock_opname.status' => TrnGudangStockOpname::STATUS_POSTED, 
                            'trn_gudang_stock_opname.status_tsd' => $statusKey,
                        ])
                        ->sum('trn_gudang_stock_opname_item.panjang_m');

                    if ($jumlah > 0) {
                        $data[$key][$statusName] = $jumlah;
                    }
                }
            }

            return $this->renderAjax('seluruh-stock', ['data' => $data]);
        }

    }

    public function actionSetOut()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        } else {
            throw new ForbiddenHttpException('Hanya ajajx call yang diizinkan.');
        }

        $keys = Yii::$app->request->post('formData')['keys'] ?? [];

        if (empty($keys)) {
            return $this->asJson(['success' => false, 'message' => 'Tidak ada item yang dipilih.']);
        }

        try {
            // Update semua data dengan id yang dikirim
            $count = \common\models\ar\TrnGudangStockOpnameItem::updateAll(
                ['is_out' => true],   // field yang mau diubah ke kebalikan dari sebelumnya
                ['id' => $keys]       // kondisi id
            );

            if ($count > 0) {
                return $this->asJson(['success' => true, 'message' => $count . ' item berhasil di-set out.']);
            } else {
                return $this->asJson(['success' => false, 'message' => 'Tidak ada data yang berubah.']);
            }
        } catch (\Exception $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
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
        if (($model = TrnStockGreigeOpname::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionIndexDuplicate()
    {
        $searchModel = new \common\models\ar\TrnStockGreigeOpnameSearch();

        if (Yii::$app->request->get('TrnStockGreigeOpnameSearch') === null) {
            $searchModel->status = TrnStockGreigeOpname::STATUS_VALID; 
        }
    
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        $dataProvider->query->orderBy(['id' => SORT_DESC]);

        return $this->render('index-duplicate', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionDuplicateBulk()
    {
        $idsParam = Yii::$app->request->post('ids', []);

        // handle ids bisa string "9,8,33" atau array [9,8,33]
        if (!is_array($idsParam)) {
            $ids = array_filter(array_map('intval', explode(',', $idsParam)));
        } else {
            $ids = array_filter(array_map('intval', $idsParam));
        }

        if (empty($ids)) {
            Yii::$app->session->setFlash('error', 'Tidak ada data yang dipilih.');
            return $this->redirect(['trn-gudang-stock-opname/index-duplicate']);
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $count = 0;

            // kolom tujuan TrnStockGreige
            $targetColumns = array_keys(TrnStockGreige::getTableSchema()->columns);
            $exclude = ['id', 'created_at', 'updated_at', 'updated_by'];

            foreach ($ids as $id) {
                $opname = TrnStockGreigeOpname::findOne((int)$id);
                if (!$opname) {
                    continue;
                }

                // ambil atribut yang valid
                $data = array_intersect_key($opname->attributes, array_flip($targetColumns));
                foreach ($exclude as $ex) {
                    unset($data[$ex]);
                }

                $panjang = (float)($opname->panjang_m ?? 0);

                $data['created_at'] = time();
                $data['created_by'] = Yii::$app->user->id ?? null;

                $new = new TrnStockGreige();
                $new->setAttributes($data, false);
                $new->date = date('Y-m-d');
                $new->panjang_m = $panjang;

                if (!$new->save(false)) {
                    throw new \Exception("Gagal menyimpan TrnStockGreige untuk opname id: {$id}");
                }

                // update stock & available di MstGreige
                if (!empty($opname->greige_id) && $panjang > 0) {
                    $mst = MstGreige::findOne((int)$opname->greige_id);
                    if ($mst) {
                        $mst->stock += $panjang;
                        $mst->available += $panjang;
                        $mst->stock_opname -= $panjang;
                        if ($mst->stock_opname < 0) {
                            $mst->stock_opname = 0;
                        }
                        if (!$mst->save(false, ['stock', 'available', 'stock_opname'])) {
                            throw new \Exception("Gagal update stok MstGreige id: {$mst->id}");
                        }
                    }
                }

                // update note dengan teks + tanggal
                $opname->note = 'Telah Migrasi Ke Stock - ' . date('d-m-Y');
                $opname->status = TrnStockGreigeOpname::STATUS_KELUAR_GUDANG;
                if (!$opname->save(false, ['note','status'])) {
                    throw new \Exception("Gagal update note untuk opname id: {$opname->id}");
                }


                $count++;
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', "$count data berhasil diduplikasi ke TrnStockGreige dan stok MstGreige diperbarui.");
            return $this->redirect(['trn-gudang-stock-opname/index-duplicate']);

        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error("Error duplicate-bulk: " . $e->getMessage(), __METHOD__);
            Yii::$app->session->setFlash('error', 'Terjadi error saat proses duplikasi: ' . $e->getMessage());
            return $this->redirect(['trn-gudang-stock-opname/index-duplicate']);
        }
    }


    public function actionKeluarBulk()
    {
        $ids = Yii::$app->request->post('ids');
        if (empty($ids)) {
            Yii::$app->session->setFlash('error', 'Tidak ada data yang dipilih.');
            return $this->redirect(['index-duplicate']);
        }

        $idArray = explode(',', $ids);

        // Ambil semua record opname yang dipilih
        $records = TrnStockGreigeOpname::find()
            ->where(['id' => $idArray])
            ->all();

        // Grouping total panjang_m per greige_id
        $totals = [];
        foreach ($records as $record) {
            $greigeId = $record->greige_id;
            $totals[$greigeId] = ($totals[$greigeId] ?? 0) + (float)$record->panjang_m;
        }

        // Update status & note di tabel opname
        TrnStockGreigeOpname::updateAll(
            [
                'status' => TrnStockGreigeOpname::STATUS_KELUAR_GUDANG,
                'note'   => 'Telah dikeluarkan gudang - ' . date('d-m-Y'),
            ],
            ['id' => $idArray]
        );

        // Update stock_opname di tabel mst_greige
        foreach ($totals as $greigeId => $jumlah) {
            $greige = MstGreige::findOne($greigeId);
            if ($greige !== null) {
                $greige->stock_opname = (float)$greige->stock_opname - $jumlah;
                if ($greige->stock_opname < 0) {
                    $greige->stock_opname = 0; // supaya tidak minus
                }
                $greige->save(false, ['stock_opname']);
            }
        }

        Yii::$app->session->setFlash('success', count($idArray) . ' data berhasil dikeluarkan dari stock opname.');
        return $this->redirect(['index-duplicate']);
    }

    public function actionLaporanGreigeOpname()
    {
        $query = (new \yii\db\Query())
            ->select([
                'trn_stock_greige_opname.date',
                'mst_greige.nama_kain AS nama_kain',
                'SUM(trn_stock_greige_opname.panjang_m) AS total_panjang'
            ])
            ->from('trn_stock_greige_opname')
            ->leftJoin('mst_greige', 'mst_greige.id = trn_stock_greige_opname.greige_id')
            ->groupBy(['trn_stock_greige_opname.date', 'mst_greige.nama_kain'])
            ->orderBy(['trn_stock_greige_opname.date' => SORT_ASC]);

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $query->all(),
            'pagination' => ['pageSize' => 50],
        ]);

        return $this->render('laporan-greige-opname', [
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionLaporanGreigeOpnameMotif()
    {
        // ambil parameter dari GET
        $request = Yii::$app->request;
        $tanggalRange = $request->get('tanggalRange');
        $namaMotif = $request->get('namaMotif');

        // default tanggal awal & akhir
        if (empty($tanggalRange)) {
            $tanggalRange = '2025-11-03 to ' . date('Y-m-d');
        }

        // query utama
        $query = (new \yii\db\Query())
            ->select([
                'mst_greige.nama_kain AS nama_kain',
                'SUM(trn_stock_greige_opname.panjang_m) AS total_panjang',
                'SUM(CASE WHEN trn_stock_greige_opname.status = 2 THEN trn_stock_greige_opname.panjang_m ELSE 0 END) AS total_valid'
            ])
            ->from('trn_stock_greige_opname')
            ->leftJoin('mst_greige', 'mst_greige.id = trn_stock_greige_opname.greige_id');

        // filter tanggal
        if (!empty($tanggalRange)) {
            $range = explode(' to ', $tanggalRange);
            if (count($range) === 2) {
                $query->andWhere(['between', 'trn_stock_greige_opname.date', trim($range[0]), trim($range[1])]);
            }
        }

        // filter motif
        if (!empty($namaMotif)) {
            $query->andWhere(['like', 'mst_greige.nama_kain', $namaMotif]);
        }

        // group & urutan
        $query->groupBy(['mst_greige.nama_kain'])
            ->orderBy(['mst_greige.nama_kain' => SORT_ASC]);

        // hasilkan data provider
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $query->all(),
            'pagination' => [
                'pageSize' => 50, // 50 data per halaman
            ],
        ]);

        // kirim ke view
        return $this->render('laporan-greige-opname-motif', [
            'dataProvider' => $dataProvider,
            'tanggalRange' => $tanggalRange,
            'namaMotif' => $namaMotif,
        ]);
    }

    public function actionHistoryMotif($nama)
    {
        $bulanSekarang = date('m');
        $tahunSekarang = date('Y');

        // --- Hitung bulan sebelumnya ---
        $bulanSebelumnya = $bulanSekarang - 1;
        $tahunSebelumnya = $tahunSekarang;
        if ($bulanSebelumnya == 0) {
            $bulanSebelumnya = 12;
            $tahunSebelumnya--;
        }

        // --- Data per hari untuk bulan sekarang ---
        $data = (new \yii\db\Query())
            ->select([
                'trn_stock_greige_opname.date',
                'SUM(trn_stock_greige_opname.panjang_m) AS total_panjang',
                'SUM(CASE WHEN trn_stock_greige_opname.status = 2 THEN trn_stock_greige_opname.panjang_m ELSE 0 END) AS total_valid'
            ])
            ->from('trn_stock_greige_opname')
            ->leftJoin('mst_greige', 'mst_greige.id = trn_stock_greige_opname.greige_id')
            ->where(['mst_greige.nama_kain' => $nama])
            ->andWhere(new \yii\db\Expression('EXTRACT(MONTH FROM trn_stock_greige_opname.date) = :bulan', [':bulan' => $bulanSekarang]))
            ->andWhere(new \yii\db\Expression('EXTRACT(YEAR FROM trn_stock_greige_opname.date) = :tahun', [':tahun' => $tahunSekarang]))
            ->groupBy(['trn_stock_greige_opname.date'])
            ->orderBy(['trn_stock_greige_opname.date' => SORT_ASC])
            ->all();

        // --- Total bulan sekarang ---
        $totalSekarang = (new \yii\db\Query())
            ->select([
                'SUM(trn_stock_greige_opname.panjang_m) AS total_panjang',
                'SUM(CASE WHEN trn_stock_greige_opname.status = 2 THEN trn_stock_greige_opname.panjang_m ELSE 0 END) AS total_valid'
            ])
            ->from('trn_stock_greige_opname')
            ->leftJoin('mst_greige', 'mst_greige.id = trn_stock_greige_opname.greige_id')
            ->where(['mst_greige.nama_kain' => $nama])
            ->andWhere(new \yii\db\Expression('EXTRACT(MONTH FROM trn_stock_greige_opname.date) = :bulan', [':bulan' => $bulanSekarang]))
            ->andWhere(new \yii\db\Expression('EXTRACT(YEAR FROM trn_stock_greige_opname.date) = :tahun', [':tahun' => $tahunSekarang]))
            ->one();

        // --- Total bulan sebelumnya ---
        $totalSebelumnya = (new \yii\db\Query())
            ->select([
                'SUM(trn_stock_greige_opname.panjang_m) AS total_panjang',
                'SUM(CASE WHEN trn_stock_greige_opname.status = 2 THEN trn_stock_greige_opname.panjang_m ELSE 0 END) AS total_valid'
            ])
            ->from('trn_stock_greige_opname')
            ->leftJoin('mst_greige', 'mst_greige.id = trn_stock_greige_opname.greige_id')
            ->where(['mst_greige.nama_kain' => $nama])
            ->andWhere(new \yii\db\Expression('EXTRACT(MONTH FROM trn_stock_greige_opname.date) = :bulan', [':bulan' => $bulanSebelumnya]))
            ->andWhere(new \yii\db\Expression('EXTRACT(YEAR FROM trn_stock_greige_opname.date) = :tahun', [':tahun' => $tahunSebelumnya]))
            ->one();

        return $this->renderPartial('_history-motif', [
            'namaMotif' => $nama,
            'data' => $data,
            'totalSekarang' => $totalSekarang,
            'totalSebelumnya' => $totalSebelumnya,
            'bulanSekarang' => $bulanSekarang,
            'bulanSebelumnya' => $bulanSebelumnya,
        ]);
    }


    
}