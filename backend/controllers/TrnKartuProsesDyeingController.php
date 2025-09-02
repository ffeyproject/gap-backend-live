<?php
namespace backend\controllers;

use common\models\ar\MstGreige;
use common\models\ar\MstProcessDyeing;
use common\models\ar\TrnKartuProsesDyeingItem;
use common\models\ar\TrnSc;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWo;
use Yii;
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnKartuProsesDyeingSearch;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnKartuProsesDyeingController implements the CRUD actions for TrnKartuProsesDyeing model.
 */
class TrnKartuProsesDyeingController extends Controller
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
     * Lists all TrnKartuProsesDyeing models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnKartuProsesDyeingSearch();
        $params = Yii::$app->request->queryParams;
        $params['status'] = TrnKartuProsesDyeing::STATUS_DELIVERED;

        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all TrnKartuProsesDyeing models.
     * @return mixed
     */
    public function actionSiapKirim()
    {
        $searchModel = new TrnKartuProsesDyeingSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        $dataProvider->query->andWhere(['trn_kartu_proses_dyeing.status' => TrnKartuProsesDyeing::STATUS_DELIVERED]);

        $dataProvider->sort->defaultOrder = [
            'openDateRange' => SORT_ASC, // Mengatur urutan default berdasarkan openDateRange
        ];

        return $this->render('index-sisa', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGetDataMasukPacking()
    {
        $searchModel = new TrnKartuProsesDyeingSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        $dataProvider->query->andWhere(['or', 
            ['trn_kartu_proses_dyeing.status' => TrnKartuProsesDyeing::STATUS_APPROVED], 
            ['trn_kartu_proses_dyeing.status' => TrnKartuProsesDyeing::STATUS_INSPECTED],
        ]);


        $dataProvider->sort->defaultOrder = [
            'approved_at' => SORT_DESC, // Mengatur urutan default berdasarkan openDateRange
        ];
        
        $dataProvider->sort->attributes['approved_at'] = [
            'asc' => [new Expression("coalesce(trn_kartu_proses_dyeing.approved_at, 0) ASC")],
            'desc' => [new Expression("coalesce(trn_kartu_proses_dyeing.approved_at, 0) DESC")],
        ];

        return $this->render('index_masuk_packing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnKartuProsesDyeing model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        Yii::$app->session->setFlash('info', 'Ketika kartu proses ini diposting, akan membooking greige/bahan baku digudang.');

        $model = $this->findModel($id);

        $processModels = MstProcessDyeing::find()->orderBy('order')->all();
        if(empty($processModels)){
            throw new NotAcceptableHttpException('Tidak ditemukan adanya data master processing untuk Dyeing, silahkan input dulu master processing untuk Dyeing lalu kembali lagi ke halaman ini.');
        }

        $attrsLabels = [];
        if($processModels !== null){
            $attrsLabels = $processModels[0]->attributeLabels();
            unset($attrsLabels['order']); unset($attrsLabels['created_at']); unset($attrsLabels['created_by']); unset($attrsLabels['updated_at']); unset($attrsLabels['updated_by']); unset($attrsLabels['max_pengulangan']);
            //BaseVarDumper::dump($attrsLabels, 10, true);Yii::$app->end();
        }

        //Data pengulangan tiap-tiap proses
        $processesUlang = [];
        foreach ($model->kartuProcessDyeingProcesses as $i=>$kartuProcessDyeingProcess) {
            if($kartuProcessDyeingProcess->value !== null){
                $dataProcess = Json::decode($kartuProcessDyeingProcess->value);
                if(isset($dataProcess['pengulangan']) && !empty($dataProcess['pengulangan'])){
                    $processUlang = [
                        'nama_proses'=>'',
                        'header'=>[],
                        'pengulangan'=>[]
                    ];

                    $headers = [];
                    $attrs = $kartuProcessDyeingProcess->process->attributes;
                    unset($attrs['id']); unset($attrs['order']); unset($attrs['created_at']); unset($attrs['created_by']); unset($attrs['updated_at']); unset($attrs['updated_by']); unset($attrs['max_pengulangan']);
                    foreach ($attrs as $key=>$attr) {
                        if($key === 'nama_proses'){
                            $processUlang['nama_proses'] = $attr;
                            unset($attrs['nama_proses']);
                        }else{
                            if($attr){
                                $headers[$key] = $kartuProcessDyeingProcess->getAttributeLabel($key);
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
     * Creates a new TrnKartuProsesDyeing model.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnKartuProsesDyeing(['date'=>date('Y-m-d')]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($model->wo_id !== null && $model->kartu_proses_id !== null){
                $model->addError('wo_id', 'Isi salah satu saja antara Nomor WO atau Nomor Kartu Proses.');
                $model->addError('kartu_proses_id', 'Isi salah satu saja antara Nomor WO atau Nomor Kartu Proses.');
                return $this->render('create', ['model' => $model]);
            }

            if($model->wo_id == null && $model->kartu_proses_id == null){
                $model->addError('wo_id', 'Salah satu daru Nomor WO atau Nomor Kartu Proses wajib diisi.');
                $model->addError('kartu_proses_id', 'Salah satu daru Nomor WO atau Nomor Kartu Proses wajib diisi.');
                return $this->render('create', ['model' => $model]);
            }

            if($model->wo_id !== null){
                $wo = $model->wo;
            }else{
                $kp = $model->kartuProses;
                $model->wo_id = $kp->wo_id;
                $wo = $model->wo;
            }

            // periksa apakah nomor kartu dan motif sudah pernah dibuat atau belum, jika sudah pernah, maka diblokir
            $qMotifNoKartuExists = (new Query())->from(TrnKartuProsesDyeing::tableName())
                ->leftJoin(TrnWo::tableName(), 'trn_wo.id = trn_kartu_proses_dyeing.wo_id')
                ->select(new Expression(1))
                ->where(['trn_wo.greige_id'=>$wo->greige_id, 'nomor_kartu'=>$model->nomor_kartu])
                ->exists()
            ;
            if($qMotifNoKartuExists){
                Yii::$app->session->setFlash('error', 'Nomor kartu dan motif tidak valid.');
                $model->addError('nomor_kartu', 'Nomor kartu dan motif tidak valid.');
                return $this->render('create', ['model' => $model]);
            }

            /**
             * yang ini pak validasi nomer kartunya per motif yah pak, jadi kalau ada nomer yang sama dalam satu motif ditolak pembuatan kartunya
            */
            if(
                TrnKartuProsesDyeing::find()->joinWith('wo')
                ->where([
                    TrnWo::tableName().'.greige_id'=>$model->wo->greige_id,
                    TrnKartuProsesDyeing::tableName().'.nomor_kartu'=>$model->nomor_kartu
                ])
                ->exists()
            ){
                $model->addError('nomor_kartu', 'Pasangan nomor kartu dan motif tidak valid.');
                return $this->render('create', ['model' => $model]);
            }

            /**
             * memeriksa apakah jumlah kartu proses untuk nomor wo tersebut sudah terpenuhi atau belum
             * jika sudah terpenuhi, coba periksa apakah ada retur buyer yang mereferensi ke wo ini
             * jika tidak ada, gagalkan, jika ada, bolehkan
             * */
            /*$totalWoColor = $wo->getTrnWoColors()->count();
            $totalWoKartuProses = $wo->getTrnKartuProsesDyeingsNonPg()->count();
            if($totalWoKartuProses >= $totalWoColor){
                $model->addError('wo_id', 'Jumlah kartu proses untuk WO sudah terpenuhi, kartu proses untuk WO ini tidak bisa ditambah lagi.');
                return $this->render('create', ['model' => $model]);
            }*/

            $model->mo_id = $wo->mo_id;
            $model->sc_greige_id = $wo->sc_greige_id;
            $model->sc_id = $wo->sc_id;

            /**
             * validasi color pada wo
             * memeriksa apakah color yang dipilih masih bisa dibuatkan kartu proses nya
            */
            /*$clrExist = TrnKartuProsesDyeing::find()
                ->where([
                    'wo_id'=>$model->wo_id,
                    'wo_color_id'=>$model->wo_color_id,
                    //'status'=>[$model::STATUS_POSTED, $model::STATUS_APPROVED, $model::STATUS_INSPECTED, $model::STATUS_DELIVERED]
                ])
                ->count()
            ;

            if($clrExist > 0){
                $colorSelected = TrnWoColor::findOne(['wo_id'=>$model->wo_id, 'mo_color_id'=>$model->woColor->mo_color_id]);
                if($colorSelected !== null){
                    if($colorSelected->qty <= $clrExist){
                        $model->addError('wo_color_id', 'Jumlah color ini pada kartu proses untuk WO sudah terpenuhi, color ini tidak bisa ditambah lagi. ('.$colorSelected->moColor->color.')');
                        return $this->render('create', ['model' => $model]);
                    }
                }
            }*/

            $handling = $wo->handling;
            $model->handling = $handling->name;
            $model->lebar_preset = $handling->lebar_preset;
            $model->lebar_finish = $handling->lebar_finish;
            $model->berat_finish = $handling->berat_finish;
            $model->t_density_lusi = $handling->densiti_lusi;
            $model->t_density_pakan = $handling->densiti_pakan;

            if($model->save(false)){
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnKartuProsesDyeing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Kartu proses bukan draft, tidak bisa dirubah.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }*/

    /**
     * Deletes an existing TrnKartuProsesDyeing model.
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
            Yii::$app->session->setFlash('error', 'Kartu proses bukan draft, tidak bisa dihapus.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            TrnKartuProsesDyeingItem::deleteAll(['kartu_process_id'=>$model->id]);
            if($model->kartu_proses_id !== null){
                Yii::$app->db->createCommand()->update(TrnKartuProsesDyeing::tableName(), ['status' => TrnKartuProsesDyeing::STATUS_GANTI_GREIGE], ['id'=>$model->kartu_proses_id])->execute();
            }
            $model->delete();
            $transaction->commit();

        }catch (\Throwable $e){
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id'=>$model->id]);
        }

        Yii::$app->session->setFlash('success', 'Kartu Proses berhasil dihapus.');
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
     * Deletes an existing KartuProsesDyeing model.
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

        $wo = $model->wo;
        $mo = $wo->mo;
        $greige = $wo->greige;
        $greigeGroup = $greige->group;

        $perBatchHalfToleransiAtas = 0;
        $perBatchHalfToleransiBawah = 0;

        if(!$model->no_limit_item){
            $lenPerBatch = $greigeGroup->qty_per_batch;
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
            foreach ($model->trnKartuProsesDyeingItems as $trnKartuProsesDyeingItem) {
                $stockItem = $trnKartuProsesDyeingItem->stock;
                if($stockItem->status == $stockItem::STATUS_ON_PROCESS_CARD){
                    $panjang = Yii::$app->formatter->asDecimal($stockItem->panjang_m);
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Salah satu roll greige ('.$panjang.'M) sudah digunakan oleh kartu proses lain, coba lagi.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                $stockItem->status = $stockItem::STATUS_ON_PROCESS_CARD;
                if(!$stockItem->save(false, ['status'])){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal, coba lagi. (1)');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                switch ($trnKartuProsesDyeingItem->tube){
                    case $trnKartuProsesDyeingItem::TUBE_KIRI:
                        $totalTubeKiri += (float)$stockItem->panjang_m;
                        break;
                    case $trnKartuProsesDyeingItem::TUBE_KANAN:
                        $totalTubeKanan += (float)$stockItem->panjang_m;
                        break;
                }
            }

            $totalLength = $totalTubeKiri + $totalTubeKanan;

            if(!$model->no_limit_item){
                if(($totalTubeKiri < $perBatchHalfToleransiBawah) || ($totalTubeKanan < $perBatchHalfToleransiBawah)){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Jumlah greige tube kiri atau tube kanan kurang dari setengah BATCH dikurang 2%.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                if(($totalTubeKiri > $perBatchHalfToleransiAtas) || ($totalTubeKanan > $perBatchHalfToleransiAtas)){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Jumlah greige tube kiri atau tube kanan lebih dari setengah BATCH ditambah 2%.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }

            if(!$model->save(false)){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal, coba lagi. (2)');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            if($model->kartu_proses_id !== null){
                $kartuProsesPg = $model->kartuProses;

                if($kartuProsesPg->status != $model::STATUS_GANTI_GREIGE){//mungkin sudah keburu digunkan kartu proses lain
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Status Kartu Proses Penggantian tidak valid, tidak bisa diposting.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                $kartuProsesPg->status = $kartuProsesPg::STATUS_GANTI_GREIGE_LINKED;
                if(!$kartuProsesPg->save(false, ['status'])){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal, coba lagi. (3)');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }

            //Booking greige--------------------------------------------------------------------------------------------
            switch ($mo->jenis_gudang){
                case TrnStockGreige::JG_WIP:
                    $bookedAttr = 'booked_wip';
                    break;
                case TrnStockGreige::JG_PFP:
                    $bookedAttr = 'booked_pfp';
                    break;
                case TrnStockGreige::JG_EX_FINISH:
                    $bookedAttr = 'booked_ef';
                    break;
                case TrnStockGreige::JG_FRESH:
                    if($wo->jenis_order === TrnSc::JENIS_ORDER_FRESH_ORDER){
                        // jika jenis order wo === fresh dan jenis gudang mo == jg_fresh
                        //ambil nilai original qty per batch greige untuk dasar pemotongan stok
                        $qtyPerBatch = $greige->group->qty_per_batch;
                        $selisih = $totalLength - $qtyPerBatch;
                        if($model->is_redyeing){
                            $update = [
                                'booked' => new \yii\db\Expression('booked' . ' + ' . $totalLength),
                            ];
                            if($selisih < 0){
                                $update = [
                                    'booked' => new \yii\db\Expression('booked' . ' + ' . $totalLength),
                                    'available' => new \yii\db\Expression('available' . ' + ' . (abs($selisih) - $qtyPerBatch)),
                                ];
                            }elseif ($selisih > 0){
                                $update = [
                                    'booked' => new \yii\db\Expression('booked' . ' + ' . $totalLength),
                                    'available' => new \yii\db\Expression('available' . ' - ' . ($selisih + $qtyPerBatch)),
                                ];
                            }
                        }else{
                            $update = [
                                'booked_wo' => new \yii\db\Expression('booked_wo' . ' - ' . $totalLength),
                                'booked' => new \yii\db\Expression('booked' . ' + ' . $totalLength),
                            ];
                            if($selisih < 0){
                                $update = [
                                    'booked_wo' => new \yii\db\Expression('booked_wo' . ' - ' . $qtyPerBatch),
                                    'booked' => new \yii\db\Expression('booked' . ' + ' . $totalLength),
                                    'available' => new \yii\db\Expression('available' . ' + ' . abs($selisih)),
                                ];
                            }elseif ($selisih > 0){
                                $update = [
                                    'booked_wo' => new \yii\db\Expression('booked_wo' . ' - ' . $qtyPerBatch),
                                    'booked' => new \yii\db\Expression('booked' . ' + ' . $totalLength),
                                    'available' => new \yii\db\Expression('available' . ' - ' . $selisih),
                                ];
                            }
                        }
                        Yii::$app->db->createCommand()->update(
                            MstGreige::tableName(),
                            $update,
                            ['id'=>$greige->id]
                        )->execute();

                        $transaction->commit();
                        Yii::$app->session->setFlash('success', 'Kartu proses berhasil diposting.');
                        return $this->redirect(['view', 'id' => $model->id]);
                    }

                    $bookedAttr = 'booked';
                    break;
                default:
                    $bookedAttr = 'booked';
            }

            Yii::$app->db->createCommand()->update(
                MstGreige::tableName(),
                [
                    $bookedAttr => new \yii\db\Expression($bookedAttr . ' + ' . $totalLength),
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
     * Finds the TrnKartuProsesDyeing model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnKartuProsesDyeing the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnKartuProsesDyeing::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


}