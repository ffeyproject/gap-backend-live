<?php

namespace backend\controllers;

use backend\models\ar\StockGreige;
use backend\models\ar\GudangInspect;
use backend\models\ar\GudangInspectItem;
use backend\models\form\StockGreigeForm;
use backend\models\form\GudangInspectForm;
use backend\models\TrnStockGreigePrSearch;
use common\models\ar\MstGreige;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnMixedGreige;
use common\models\ar\TrnMixedGreigeItem;
use common\models\Model;
use common\models\rekap\LaporanStockSearch;
use Yii;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnGudangInspect;
use common\models\ar\TrnGudangInspectItem;
use common\models\ar\TrnGudangInspectSearch;
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
use yii\helpers\ArrayHelper; 
use yii\helpers\VarDumper;


/**
 * TrnStockGreigeController implements the CRUD actions for TrnStockGreige model.
 */
class TrnGudangInspectController extends Controller
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
        $searchModel = new TrnGudangInspectSearch(['jenis_gudang'=>TrnGudangInspect::JG_FRESH]);
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


        $form = new GudangInspectForm();
        $modelsStock = [new GudangInspectItem()];
    
        if ($form->load(Yii::$app->request->post())) {
            // echo '<pre>';
            // print_r(Yii::$app->request->post());
            // echo '</pre>';
            // exit;
            $modelsStock = Model::createMultiple(GudangInspectItem::classname());
            Model::loadMultiple($modelsStock, Yii::$app->request->post());
    
            $valid = $form->validate();
            $valid = Model::validateMultiple($modelsStock) && $valid;
    
            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    // Pindahkan data dari form ke ActiveRecord
                    $model = new GudangInspect();
                    $model->attributes = $form->attributes;
                    $model->date = date('Y-m-d');
                    $model->greige_group_id = $model->greige->group_id;
                    
                    if (!$model->save(false)) {
                        throw new \Exception("Gagal menyimpan data utama.");
                    }
    
                    $greyQty = 0;
                    foreach ($modelsStock as $modelStock) {
                        $modelStock->trn_gudang_inspect_id = $model->id;
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
            'modelsStock' => (empty($modelsStock)) ? [new GudangInspectItem()] : $modelsStock
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
        $models = TrnStockGreige::findAll(['no_document'=>$noDoc, 'status'=>TrnStockGreige::STATUS_DRAFT]);

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
        $model = GudangInspect::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Data tidak ditemukan.");
        }
    
        $form = new TrnGudangInspect();
        $form->attributes = $model->attributes;
    
        $modelsStock = $model->trnGudangInspectItems; // relasi dengan GudangInspectItem
        if (empty($modelsStock)) {
            $modelsStock = [new GudangInspectItem()];
        }
    
        if ($form->load(Yii::$app->request->post())) {
            $oldIDs = ArrayHelper::map($modelsStock, 'id', 'id');

            // $modelsStock = Model::createMultiple(GudangInspectItem::className(), $modelsStock);

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
                        GudangInspectItem::deleteAll(['id' => $deletedIDs, 'is_out' => false]);
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
        $modelsStock = $model->getTrnGudangInspectItems()->orderBy('id')->all();

        if ($model->load(Yii::$app->request->post())) {
            $oldIDs = ArrayHelper::map($modelsStock, 'id', 'id');
            $modelsItem = Model::createMultiple(TrnGudangInspectItem::classname(), $modelsStock);
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
                            TrnGudangInspectItem::deleteAll(['id' => $deletedIDs, 'is_out' => false]);
                        }

                        foreach ($modelsItem as $modelItem) {
                            $modelItem->trn_gudang_inspect_id = $model->id;
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
        $model  = TrnGudangInspect::findOne($id);
        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        $model->status = TrnGudangInspect::STATUS_POSTED;
        $model->save();

        Yii::$app->session->setFlash('success', 'Berhasil diposting.');
        return $this->redirect(['view', 'id' => $model->id]);
    }


    public function actionBatalPosting ($id){
        $model  = TrnGudangInspect::findOne($id);
        if($model->status != $model::STATUS_POSTED){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        $model->status = TrnGudangInspect::STATUS_DRAFT;
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

    /**
     * Finds the TrnStockGreige model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnStockGreige the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnGudangInspect::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}