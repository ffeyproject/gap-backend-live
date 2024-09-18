<?php

namespace backend\controllers;

use common\models\ar\MstGreige;
use common\models\ar\MstProcessPrinting;
use common\models\ar\TrnKartuProsesPrintingItem;
use common\models\ar\TrnSc;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWo;
use common\models\ar\TrnWoColor;
use Yii;
use common\models\ar\TrnKartuProsesPrinting;
use common\models\ar\TrnKartuProsesPrintingSearch;
use yii\helpers\BaseVarDumper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnKartuProsesPrintingController implements the CRUD actions for TrnKartuProsesPrinting model.
 */
class TrnKartuProsesPrintingController extends Controller
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
     * Lists all TrnKartuProsesPrinting models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnKartuProsesPrintingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnKartuProsesPrinting model.
     * @param integer $id
     * @return mixed
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $processModels = MstProcessPrinting::find()->orderBy('order')->all();
        if(empty($processModels)){
            throw new NotAcceptableHttpException('Tidak ditemukan adanya data master processing untuk Printing, silahkan input dulu master processing untuk Printing lalu kembali lagi ke halaman ini.');
        }

        $attrsLabels = $processModels[0]->attributeLabels();
        unset($attrsLabels['order']); unset($attrsLabels['created_at']); unset($attrsLabels['created_by']); unset($attrsLabels['updated_at']); unset($attrsLabels['updated_by']); unset($attrsLabels['max_pengulangan']);
        //BaseVarDumper::dump($attrsLabels, 10, true);Yii::$app->end();

        //Data pengulangan tiap-tiap proses
        $processesUlang = [];
        foreach ($model->kartuProcessPrintingProcesses as $i=>$kartuProcessPrintingProcess) {
            if($kartuProcessPrintingProcess->value !== null){
                $dataProcess = Json::decode($kartuProcessPrintingProcess->value);
                if(isset($dataProcess['pengulangan']) && !empty($dataProcess['pengulangan'])){
                    $processUlang = [
                        'nama_proses'=>'',
                        //'header'=>[],
                        'pengulangan'=>[]
                    ];

                    $headers = [];
                    $attrs = $kartuProcessPrintingProcess->process->attributes;
                    unset($attrs['id']); unset($attrs['order']); unset($attrs['created_at']); unset($attrs['created_by']); unset($attrs['updated_at']); unset($attrs['updated_by']); unset($attrs['max_pengulangan']);
                    foreach ($attrs as $key=>$attr) {
                        if($key === 'nama_proses'){
                            $processUlang['nama_proses'] = $attr;
                            unset($attrs['nama_proses']);
                        }else{
                            if($attr){
                                $headers[$key] = $kartuProcessPrintingProcess->getAttributeLabel($key);
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
     * Creates a new TrnKartuProsesPrinting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnKartuProsesPrinting(['date'=>date('Y-m-d')]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()){
            if($model->wo_id !== null && $model->kartu_proses_id !== null){
                $model->addError('wo_id', 'Isi salah satu saja antara Nomor WO atau Nomor Kartu Proses.');
                $model->addError('kartu_proses_id', 'Isi salah satu saja antara Nomor WO atau Nomor Kartu Proses.');
                return $this->render('create', ['model' => $model]);
            }

            if($model->wo_id == null && $model->kartu_proses_id == null){
                $model->addError('wo_id', 'Salah satu daru Nomor WO atau Nomor Kartu Proses wajib diisi.');
                $model->addError('kartu_proses_id', 'Salah satu dari Nomor WO atau Nomor Kartu Proses wajib diisi.');
                return $this->render('create', ['model' => $model]);
            }

            if(
                TrnKartuProsesPrinting::find()->joinWith('wo')
                    ->where([
                        TrnWo::tableName().'.greige_id'=>$model->wo->greige_id,
                        TrnKartuProsesPrinting::tableName().'.nomor_kartu'=>$model->nomor_kartu
                    ])
                    ->exists()
            ){
                /**
                 * yang ini pak validasi nomer kartunya per motif yah pak, jadi kalau ada nomer yang sama dalam satu motif ditolak pembuatan kartunya
                 */
                $model->addError('nomor_kartu', 'Pasangan nomor kartu dan motif tidak valid.');
                return $this->render('create', ['model' => $model]);
            }

            if($model->wo_id === null) {
                $kp = $model->kartuProses;
                $model->wo_id = $kp->wo_id;
            }
            $wo = $model->wo;

            /*$totalWoColor = $wo->getTrnWoColors()->count();
            $totalWoKartuProses = $wo->getTrnKartuProsesPrintingsNonPg()->count();
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
            /*$clrExist = TrnKartuProsesPrinting::find()
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
                        //$model->addError('wo_color_id', 'Jumlah color ini pada kartu proses untuk WO sudah terpenuhi, color ini tidak bisa ditambah lagi.'.$colorSelected->moColor->color);
                        //return $this->render('create', ['model' => $model]);
                    }
                }
            }*/

            if($model->save(false)){
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnKartuProsesPrinting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }*/

    /**
     * Deletes an existing TrnKartuProsesPrinting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
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
            TrnKartuProsesPrintingItem::deleteAll(['kartu_process_id'=>$model->id]);
            if($model->kartu_proses_id !== null){
                Yii::$app->db->createCommand()->update(TrnKartuProsesPrinting::tableName(), ['status' => TrnKartuProsesPrinting::STATUS_GANTI_GREIGE], ['id'=>$model->kartu_proses_id])->execute();
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
     * Deletes an existing KartuProsesPrinting model.
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

        $totalWoColors = $wo->getColorQtyBatchToUnit();

        $model->status = $model::STATUS_POSTED;
        $model->posted_at = time();

        if($model->no_urut === null){
            $model->setNomor();
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $totalItem = 0;
            $totalLength = 0;

            foreach ($model->trnKartuProsesPrintingItems as $trnKartuProsesPrintingItem) {
                $stockItem = $trnKartuProsesPrintingItem->stock;
                if($stockItem->status == $stockItem::STATUS_ON_PROCESS_CARD){
                    //BaseVarDumper::dump($trnKartuProsesPrintingItem, 10, true);Yii::$app->end();
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

                $totalItem ++;
                $totalLength += $stockItem->panjang_m;
            }

            if(!$model->no_limit_item){
                if($totalLength > $totalWoColors){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Jumlah greige terlalu banyak, melebihi jumlah pada WO.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }

            if(!$model->save(false)){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal, coba lagi.');
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
                    Yii::$app->session->setFlash('error', 'Gagal, coba lagi.');
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
     * Finds the TrnKartuProsesPrinting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnKartuProsesPrinting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnKartuProsesPrinting::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
