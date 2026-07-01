<?php
namespace backend\controllers;

use backend\models\form\CatatanProsesForm;
use common\models\ar\KartuProcessPrintingProcess;
use common\models\ar\MstProcessPrinting;
use common\models\ar\TrnKartuProsesPrintingItem;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWo;
use common\models\ar\TrnWoColor;
use Yii;
use common\models\ar\TrnKartuProsesPrinting;
use common\models\ar\TrnKartuProsesPrintingSearch;
use yii\helpers\BaseVarDumper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TrnKartuProsesPrintingController implements the CRUD actions for TrnKartuProsesPrinting model.
 */
class ProcessingPrintingController extends Controller
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
    public function actionRekap()
    {
        $searchModel = new TrnKartuProsesPrintingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['>', 'trn_kartu_proses_printing.status', TrnKartuProsesPrinting::STATUS_POSTED]);

        return $this->render('rekap', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all TrnKartuProsesPrinting models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnKartuProsesPrintingSearch(['status'=>TrnKartuProsesPrinting::STATUS_DELIVERED]);
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
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $processModels = MstProcessPrinting::find()->orderBy('order')->all();

        $attrsLabels = [];
        if($processModels !== null){
            $attrsLabels = $processModels[0]->attributeLabels();
            unset($attrsLabels['order']); unset($attrsLabels['created_at']); unset($attrsLabels['created_by']); unset($attrsLabels['updated_at']); unset($attrsLabels['updated_by']); unset($attrsLabels['max_pengulangan']);
            //BaseVarDumper::dump($attrsLabels, 10, true);Yii::$app->end();
        }

        //Data pengulangan tiap-tiap proses
        $processesUlang = [];
        foreach ($model->kartuProcessPrintingProcesses as $i=>$kartuProcessPrintingProcess) {
            if($kartuProcessPrintingProcess->value !== null){
                $dataProcess = Json::decode($kartuProcessPrintingProcess->value);
                if(isset($dataProcess['pengulangan']) && !empty($dataProcess['pengulangan'])){
                    $processUlang = [
                        'nama_proses'=>'',
                        'header'=>[],
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
     * Updates an existing TrnKartuProsesDyeing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     */
    public function actionGantiGreige($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if($model->status != $model::STATUS_DELIVERED){
                throw new NotFoundHttpException('Status Kartu Proses tidak valid, proses tidak bisa dilanjutkan.');
            }

            $memoPg = Yii::$app->request->post('data');
            if(empty($memoPg)){
                throw new ForbiddenHttpException('Memo penggantian greige wajib diisi.');
            }

            $model->status = $model::STATUS_GANTI_GREIGE;
            $model->memo_pg = $memoPg;
            $model->memo_pg_at = time();
            $model->memo_pg_by = Yii::$app->user->id;

            $greigeId = $model->wo->greige_id;
            $totalPanjang  = 0;

            /* @var $newStocks TrnStockGreige[]*/
            $newStocks = [];

            //siapkan stok semua roll untuk ditambahkan ke gudang wip
            foreach ($model->trnKartuProsesPrintingItems as $trnKartuProsesPrintingItem) {
                $stock = $trnKartuProsesPrintingItem->stock;
                $newStock = new TrnStockGreige();
                $newStock->load([$newStock->formName()=>$stock->attributes]);
                $newStock->setAttributes([
                    'created_at' => null,
                    'created_by' => null,
                    'updated_at' => null,
                    'updated_by' => null,
                    'jenis_gudang'=>TrnStockGreige::JG_WIP,
                    'status'=>TrnStockGreige::STATUS_VALID,
                    'date'=>date('Y-m-d'),
                    'note'=>'Gagal proses pada kartu proses printing No:'.$model->no
                ]);
                $newStocks[] = $newStock;
                $totalPanjang += $newStock->panjang_m;
            }//siapkan stok semua roll untuk ditambahkan ke gudang wip

            $transaction = Yii::$app->db->beginTransaction();
            try {
                //tambahkan stok semua roll ke gudang wip
                foreach ($newStocks as $newStock) {
                    if(!$flag = $newStock->save()){
                        $transaction->rollBack();
                        throw new HttpException(500, Json::encode($newStock->attributes));
                        //throw new HttpException(500, 'Gagal membuat memo penggantian greige, coba lagi.');
                    }
                }
                //tambahkan stok semua roll ke gudang wip

                //sesuaikan jumlah stok pada maste greige
                $cmd = "UPDATE mst_greige SET stock_wip=stock_wip+{$totalPanjang} WHERE id=:id";
                $command = Yii::$app->db->createCommand($cmd)->bindParam(':id', $greigeId);
                if(!$flag = $command->execute() > 0){
                    $transaction->rollBack();
                    throw new HttpException(500, 'Gagal membuat memo penggantian greige, coba lagi.');
                }
                //sesuaikan jumlah stok pada maste greige

                if(!$flag = $model->save(false, ['status', 'memo_pg', 'memo_pg_at', 'memo_pg_by'])){
                    $transaction->rollBack();
                    throw new HttpException(500, 'Gagal membuat memo penggantian greige, coba lagi.');
                }

                if($flag){
                    $transaction->commit();
                    return true;
                }
            }catch (\Throwable $e){
                $transaction->rollBack();
                throw $e;
            }
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * @param $id
     * @param $proses_id
     * @param $attr
     * @return bool
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionJalankanProses($id, $proses_id, $attr)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if($model->status !== $model::STATUS_DELIVERED){
                throw new ForbiddenHttpException('Kartu proses tidak valid.');
            }

            $pcModel = KartuProcessPrintingProcess::findOne(['kartu_process_id'=>$model->id, 'process_id'=>$proses_id]);
            if($pcModel === null){
                $datas = [];
                $pcModel = new KartuProcessPrintingProcess(['kartu_process_id'=>$model->id, 'process_id'=>$proses_id]);
            }else{
                $datas = Json::decode($pcModel->value);
            }

            $mstProcessModel = $pcModel->process;

            $datas[$attr] = Yii::$app->request->post('data');

            $pcModel->value = Json::encode($datas);

            $label = $pcModel->getAttributeLabel($attr);
            $lblBtn = $datas[$attr].' <span class="glyphicon glyphicon-pencil text-warning" aria-hidden="true"></span>';
            switch ($attr){
                case 'tanggal':
                    $btn = Html::a($lblBtn, ['jalankan-proses', 'id'=>$model->id, 'proses_id'=>$mstProcessModel->id, 'attr'=>$attr], [
                        'onclick' => 'setDateInput(event, "'.$label.' '.$mstProcessModel->nama_proses.'");',
                        'title' => 'Set '.$label.' '.$mstProcessModel->nama_proses
                    ]);
                    break;
                case 'start':
                case 'stop':
                    $btn = Html::a($lblBtn, ['jalankan-proses', 'id'=>$model->id, 'proses_id'=>$mstProcessModel->id, 'attr'=>$attr], [
                        'onclick' => 'setTimeInput(event, "Waktu '.$label.' '.$mstProcessModel->nama_proses.'");',
                        'title' => 'Set '.$label.' '.$mstProcessModel->nama_proses
                    ]);
                    break;
                default:
                    $btn = Html::a($lblBtn, ['jalankan-proses', 'id'=>$model->id, 'proses_id'=>$mstProcessModel->id, 'attr'=>$attr], [
                        'onclick' => 'setTextInput(event, "'.$label.' '.$mstProcessModel->nama_proses.'");',
                        'title' => 'Set '.$label.' '.$mstProcessModel->nama_proses
                    ]);
            }

            if($pcModel->save(false)){
                return $btn;
            }else{
                throw new HttpException(500, 'Gagal, coba lagi.');
            }
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * @param $id
     * @param $proses_id
     * @return bool
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionReProses($id, $proses_id){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if($model->status !== $model::STATUS_DELIVERED){
                throw new ForbiddenHttpException('Kartu proses tidak valid.');
            }

            $pcModel = KartuProcessPrintingProcess::findOne(['kartu_process_id'=>$model->id, 'process_id'=>$proses_id]);
            if($pcModel === null){
                throw new ForbiddenHttpException('Belum pernah diproses, tidak bisa diulang.');
            }

            if($pcModel->process->max_pengulangan < 1){
                throw new ForbiddenHttpException('Proses ini tidak bisa diulang.');
            }

            $pengulangans = [];
            $oldData = Json::decode($pcModel->value);
            if(isset($oldData['pengulangan']) && !empty($oldData['pengulangan'])){
                $kuotaPengulangan = $pcModel->process->max_pengulangan;
                if($kuotaPengulangan <= count($oldData['pengulangan'])){
                    throw new ForbiddenHttpException('Pengulangan sudah mencapai kuota <strong>'.$kuotaPengulangan.' kali</strong> pengulangan.');
                }

                $pengulangans = $oldData['pengulangan'];
                unset($oldData['pengulangan']);
            }

            $pengulangans[] = [
                'memo'=>Yii::$app->request->post('data'),
                'time'=>time(),
                'by'=>Yii::$app->user->id,
                'data'=>$oldData
            ];

            $pcModel->value = Json::encode(['pengulangan'=>$pengulangans]);
            if($pcModel->save(false)){
                return true;
            }else{
                throw new HttpException(500, 'Gagal, coba lagi.');
            }
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * Deletes an existing KartuProsesDyeing model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionApprove($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DELIVERED){
            Yii::$app->session->setFlash('error', 'Status Kartu proses tidak valid.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        //di bypass saja, tidak perlu divalidasi
        /*if(!$model->isAllProcessDone){
            Yii::$app->session->setFlash('error', 'Proses belum selesai.');
            return $this->redirect(['view', 'id' => $model->id]);
        }*/

        $model->status = $model::STATUS_APPROVED;
        $model->approved_at = time();
        $model->approved_by = Yii::$app->user->id;
        $model->save(false, ['status', 'approved_at', 'approved_by']);

        Yii::$app->session->setFlash('success', 'Berhasil disetujui, proses bisa dilanjutkan ke tahap inspecting.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * @param $id
     * @return array|bool|string|Response
     * @throws ForbiddenHttpException
     */
    public function actionAddCatatanProses($id){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = new CatatanProsesForm(['kartu_proses_id'=>$id]);

            if($model->load(Yii::$app->request->post())){
                if($model->validate()){
                    $modelKp = TrnKartuProsesPrinting::findOne($model->kartu_proses_id);
                    if($modelKp === null){
                        $model->addError('kartu_proses_id', 'Kartu Proses Id tidak valid');
                    }else{
                        $modelKp->note = $model->note;
                        $modelKp->save(false, ['note']);
                        return ['success'=>true, 'data'=>$model->note];
                    }
                }

                $result = [];
                // The code below comes from ActiveForm::validate(). We do not need to validate the model
                // again, as it was already validated by save(). Just collect the messages.
                foreach ($model->getErrors() as $attribute => $errors) {
                    $result[Html::getInputId($model, $attribute)] = $errors;
                }
                return ['validation' => $result];
            }

            return $this->renderAjax('add-catatan-proses', [
                'model'=>$model,
            ]);
        }

        throw new ForbiddenHttpException('Not allowed');
    }

    /**
     * @param $id
     * @return bool
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionGantiWo($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if($model->status != $model::STATUS_DELIVERED){
                throw new ForbiddenHttpException('Kartu proses tidak valid, tidak bisa diproses.');
            }

            $nomorWo = Yii::$app->request->post('data');
            if(empty($nomorWo)){
                throw new ForbiddenHttpException('Nomor WO kosong, tidak bisa diproses.');
            }

            $wo = TrnWo::findOne(['no'=>$nomorWo]);

            if($wo === null){
                throw new NotFoundHttpException('WO dengan nomor yang dimasukan tidak ditemukan.');
            }

            $model->wo_id = $wo->id;
            $model->wo_color_id = TrnWoColor::find()->select('id')->where(['wo_id'=>$wo->id])->asArray()->one()['id'];
            $model->mo_id = $wo->mo_id;
            $model->sc_id = $wo->sc_id;
            $model->save(false, ['wo_id','wo_color_id', 'mo_id', 'sc_id']);

            return true;
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * @param $id
     * @return bool
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionGantiWarna($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if($model->status != $model::STATUS_DELIVERED){
                throw new ForbiddenHttpException('Kartu proses tidak valid, tidak bisa diproses.');
            }

            $post = Yii::$app->request->post('data');
            if(empty($post)){
                throw new ForbiddenHttpException('Keterangan kosong, tidak bisa diproses.');
            }

            $model->wo_color_id = $post;
            $model->save(false, ['wo_color_id']);

            return true;
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * Finds the TrnKartuProsesPrinting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnKartuProsesPrinting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionInputProduksi()
    {
        $request = Yii::$app->request;
        $jenis_mesin = $request->get('jenis_mesin');
        $mesin_id = $request->get('mesin_id');
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $shift = $request->get('shift');
        $pembagian_hari = $request->get('pembagian_hari');

        $jenisMesins = ['Rotary' => 'Rotary', 'Flat' => 'Flat', 'Digital' => 'Digital'];

        $machines = [];
        if ($jenis_mesin) {
            $machines = \common\models\ar\MstMesinProses::find()
                ->where(['like', 'model_mesin', $jenis_mesin])
                ->all();
        }

        $mesin = null;
        if ($mesin_id) {
            $mesin = \common\models\ar\MstMesinProses::findOne($mesin_id);
        }

        $existingData = [];
        if ($mesin && $tanggal && $shift && $pembagian_hari) {
            // 1. Query from TrnProduksiMesinPrinting
            $rekapRecords = \common\models\ar\TrnProduksiMesinPrinting::find()
                ->where([
                    'mst_mesin_proses_id' => $mesin->id,
                    'tanggal' => $tanggal,
                    'shift' => $shift,
                    'pembagian_hari' => $pembagian_hari
                ])
                ->all();

            foreach ($rekapRecords as $record) {
                $existingData[] = [
                    'id' => 'rekap_' . $record->id,
                    'jenis_input' => $record->jenis_input,
                    'start' => $record->start,
                    'stop' => $record->stop,
                    'no_mc' => $record->mstMesinProses->nama_mesin,
                    'no_wo' => $record->wo_no,
                    'nk' => $record->nk_no,
                    'kartu_proses_id' => $record->kartu_proses_id,
                    'design' => $record->design,
                    'motif' => $record->motif,
                    'warna' => $record->warna,
                    'jumlah_pesanan' => number_format((float)$record->jumlah_pesanan, 2, '.', ''),
                    'realisasi' => number_format((float)$record->realisasi, 2, '.', ''),
                    'kurang' => number_format((float)$record->kurang, 2, '.', ''),
                    'panjang_greige' => number_format((float)$record->panjang_greige, 2, '.', ''),
                    'panjang_jadi' => number_format((float)$record->panjang_jadi, 2, '.', ''),
                    'keterangan' => $record->keterangan,
                    'jenis_hambatan' => $record->mstJenisHambatan ? $record->mstJenisHambatan->nama : '-',
                    'raw_id' => $record->id,
                    'tipe_record' => 'rekap',
                ];
            }

            // 2. Query from KartuProcessPrintingProcess (card records)
            $existingCardIds = \common\models\ar\TrnProduksiMesinPrinting::find()
                ->select('kartu_proses_id')
                ->where(['mst_mesin_proses_id' => $mesin->id, 'tanggal' => $tanggal, 'shift' => $shift])
                ->andWhere(['not', ['kartu_proses_id' => null]])
                ->column();

            $queryPrinting = \common\models\ar\KartuProcessPrintingProcess::find()
                ->alias('kp')
                ->innerJoin('trn_kartu_proses_printing kpp', 'kp.kartu_process_id = kpp.id')
                ->where(['>=', 'kpp.status', \common\models\ar\TrnKartuProsesPrinting::STATUS_DELIVERED])
                ->andWhere(['not', ['kpp.status' => \common\models\ar\TrnKartuProsesPrinting::STATUS_BATAL]])
                ->andWhere(['like', 'kp.value', '"no_mesin":"' . str_replace(['%', '_'], ['\%', '\_'], $mesin->nama_mesin) . '"'])
                ->andWhere(['like', 'kp.value', '"tanggal":"' . $tanggal . '"']);

            if (!empty($existingCardIds)) {
                $queryPrinting->andWhere(['not', ['kpp.id' => $existingCardIds]]);
            }

            $queryPrinting->with(['kartuProcess.trnKartuProsesPrintingItems', 'kartuProcess.wo', 'kartuProcess.woColor.moColor', 'process']);

            $printingRecords = $queryPrinting->all();
            foreach ($printingRecords as $record) {
                $values = Json::decode($record->value);
                $shiftGroup = isset($values['shift_group']) ? $values['shift_group'] : (isset($values['shift_operator']) ? $values['shift_operator'] : '-');
                
                if ($shiftGroup !== $shift) {
                    continue;
                }

                $kpp = $record->kartuProcess;
                $pcs = count($kpp->trnKartuProsesPrintingItems);
                $panjangGreige = $kpp->getTrnKartuProsesPrintingItems()->sum('panjang_m') ?: 0;
                $woNo = $kpp->wo ? $kpp->wo->no : '';
                $warna = ($kpp->woColor && $kpp->woColor->moColor) ? $kpp->woColor->moColor->color : '';
                $qtyOrder = $kpp->woColor ? $kpp->woColor->qtyFinishToMeter : 0;

                $realisasi = 0;
                if ($kpp->wo_color_id) {
                    $cards = \common\models\ar\TrnKartuProsesPrinting::find()
                        ->where(['wo_color_id' => $kpp->wo_color_id])
                        ->andWhere(['status' => \common\models\ar\TrnKartuProsesPrinting::STATUS_DELIVERED])
                        ->all();
                    foreach ($cards as $card) {
                        $realisasi += (float)$card->getTrnKartuProsesPrintingItems()->sum('panjang_m');
                    }
                }

                $existingData[] = [
                    'id' => 'card_' . $kpp->id . '_' . $record->process_id,
                    'jenis_input' => 'Produksi',
                    'start' => isset($values['start']) ? $values['start'] : '',
                    'stop' => isset($values['stop']) ? $values['stop'] : '',
                    'no_mc' => $mesin->nama_mesin,
                    'no_wo' => $woNo,
                    'nk' => $kpp->nomor_kartu,
                    'kartu_proses_id' => $kpp->id,
                    'design' => $kpp->wo ? ($kpp->wo->mo ? $kpp->wo->mo->design : '') : '',
                    'motif' => $kpp->wo ? ($kpp->wo->greige ? $kpp->wo->greige->nama_kain : '') : '',
                    'warna' => $warna,
                    'jumlah_pesanan' => number_format((float)$qtyOrder, 2, '.', ''),
                    'realisasi' => number_format((float)$realisasi, 2, '.', ''),
                    'kurang' => number_format((float)($qtyOrder - $realisasi), 2, '.', ''),
                    'panjang_greige' => number_format((float)(isset($values['panjang_greige']) ? $values['panjang_greige'] : $panjangGreige), 2, '.', ''),
                    'panjang_jadi' => isset($values['panjang_jadi']) ? number_format((float)$values['panjang_jadi'], 2, '.', '') : '',
                    'keterangan' => isset($values['keterangan']) ? $values['keterangan'] : '',
                    'jenis_hambatan' => '-',
                    'raw_id' => $kpp->id,
                    'process_id' => $record->process_id,
                    'tipe_record' => 'card',
                ];
            }

            usort($existingData, function($a, $b) {
                return strcmp($a['start'], $b['start']);
            });
        }

        $hambatanList = \common\models\ar\MstJenisHambatan::find()->asArray()->all();

        return $this->render('input-produksi', [
            'jenisMesins' => $jenisMesins,
            'machines' => $machines,
            'mesin' => $mesin,
            'jenis_mesin' => $jenis_mesin,
            'mesin_id' => $mesin_id,
            'tanggal' => $tanggal,
            'shift' => $shift,
            'pembagian_hari' => $pembagian_hari,
            'existingData' => $existingData,
            'hambatanList' => $hambatanList,
        ]);
    }

    public function actionTambahInputProduksi()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $mesinId = $request->post('mesin_id');
            $tanggal = $request->post('tanggal');
            $shift = $request->post('shift');
            $pembagian_hari = $request->post('pembagian_hari');

            $jenis_input = $request->post('jenis_input');
            $start = $request->post('start');
            $stop = $request->post('stop');
            $nk_no = trim($request->post('nk_no'));
            $wo_no = trim($request->post('wo_no'));
            $design = $request->post('design');
            $motif = $request->post('motif');
            $warna = $request->post('warna');
            $jumlah_pesanan = $request->post('jumlah_pesanan');
            $realisasi = $request->post('realisasi');
            $kurang = $request->post('kurang');
            $panjang_greige = $request->post('panjang_greige');
            $panjang_jadi = $request->post('panjang_jadi');
            $keterangan = $request->post('keterangan');
            $mst_jenis_hambatan_id = $request->post('mst_jenis_hambatan_id');
            $record_id = $request->post('record_id');
            $tipe_record = $request->post('tipe_record');

            $mesin = \common\models\ar\MstMesinProses::findOne($mesinId);
            if (!$mesin) {
                Yii::$app->session->setFlash('error', 'Mesin tidak valid.');
                return $this->redirect(['input-produksi', 'jenis_mesin' => $request->post('jenis_mesin'), 'mesin_id' => $mesinId, 'tanggal' => $tanggal, 'shift' => $shift, 'pembagian_hari' => $pembagian_hari]);
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $kpp = null;
                if (!empty($nk_no)) {
                    $kpp = \common\models\ar\TrnKartuProsesPrinting::findOne(['nomor_kartu' => $nk_no]);
                }

                if (($jenis_input === 'Produksi' || $jenis_input === 'Percobaan') && $kpp) {
                    $mstProcess = \common\models\ar\MstProcessPrinting::findOne(['nama_proses' => 'Printing']);
                    if (!$mstProcess) {
                        $mstProcess = \common\models\ar\MstProcessPrinting::find()->orderBy('order')->one();
                    }

                    if ($mstProcess) {
                        $kpProcess = \common\models\ar\KartuProcessPrintingProcess::findOne([
                            'kartu_process_id' => $kpp->id,
                            'process_id' => $mstProcess->id
                        ]);
                        if (!$kpProcess) {
                            $kpProcess = new \common\models\ar\KartuProcessPrintingProcess();
                            $kpProcess->kartu_process_id = $kpp->id;
                            $kpProcess->process_id = $mstProcess->id;
                        }

                        $vals = $kpProcess->isNewRecord ? [] : (Json::decode($kpProcess->value) ?: []);
                        $vals['tanggal'] = $tanggal;
                        $vals['no_mesin'] = $mesin->nama_mesin;
                        $vals['shift_group'] = $shift;
                        $vals['start'] = $start;
                        $vals['stop'] = $stop;
                        $vals['panjang_greige'] = $panjang_greige;
                        $vals['panjang_jadi'] = $panjang_jadi;
                        $vals['keterangan'] = $keterangan;

                        $kpProcess->value = Json::encode($vals);
                        if (!$kpProcess->save(false)) {
                            throw new \Exception('Gagal menyimpan data proses kartu.');
                        }
                    }
                }

                $model = null;
                if (!empty($record_id) && $tipe_record === 'rekap') {
                    $model = \common\models\ar\TrnProduksiMesinPrinting::findOne($record_id);
                }

                if (!$model && $kpp) {
                    $model = \common\models\ar\TrnProduksiMesinPrinting::findOne([
                        'kartu_proses_id' => $kpp->id,
                        'tanggal' => $tanggal,
                        'shift' => $shift,
                        'pembagian_hari' => $pembagian_hari,
                    ]);
                }

                if (!$model) {
                    $model = new \common\models\ar\TrnProduksiMesinPrinting();
                }

                $model->jenis_input = $jenis_input;
                $model->tanggal = $tanggal;
                $model->shift = $shift;
                $model->pembagian_hari = $pembagian_hari;
                $model->start = $start;
                $model->stop = $stop;
                $model->mst_mesin_proses_id = $mesin->id;
                $model->kartu_proses_id = $kpp ? $kpp->id : null;

                if ($kpp) {
                    $model->wo_id = $kpp->wo_id;
                    $model->wo_no = $kpp->wo ? $kpp->wo->no : '';
                    $model->nk_no = $kpp->nomor_kartu;
                } else {
                    if (!empty($wo_no)) {
                        $wo = \common\models\ar\TrnWo::findOne(['no' => $wo_no]);
                        if ($wo) {
                            $model->wo_id = $wo->id;
                        }
                    }
                    $model->wo_no = $wo_no;
                    $model->nk_no = $nk_no;
                }

                $model->design = $design;
                $model->motif = $motif;
                $model->warna = $warna;
                $model->jumlah_pesanan = $jumlah_pesanan;
                $model->realisasi = $realisasi;
                $model->kurang = $kurang;
                $model->panjang_greige = $panjang_greige;
                $model->panjang_jadi = $panjang_jadi;
                $model->keterangan = $keterangan;
                $model->mst_jenis_hambatan_id = !empty($mst_jenis_hambatan_id) ? $mst_jenis_hambatan_id : null;

                if (!$model->save()) {
                    throw new \Exception('Gagal menyimpan data rekap: ' . Json::encode($model->getErrors()));
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Data input produksi berhasil disimpan.');
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect([
                'input-produksi',
                'jenis_mesin' => $request->post('jenis_mesin'),
                'mesin_id' => $mesinId,
                'tanggal' => $tanggal,
                'shift' => $shift,
                'pembagian_hari' => $pembagian_hari
            ]);
        }

        return $this->redirect(['input-produksi']);
    }

    public function actionHapusInputProduksi($id, $tipe)
    {
        $request = Yii::$app->request;
        $jenis_mesin = $request->get('jenis_mesin');
        $mesin_id = $request->get('mesin_id');
        $tanggal = $request->get('tanggal');
        $shift = $request->get('shift');
        $pembagian_hari = $request->get('pembagian_hari');

        if ($tipe === 'rekap') {
            $model = \common\models\ar\TrnProduksiMesinPrinting::findOne($id);
            if ($model) {
                if ($model->kartu_proses_id) {
                    $mstProcess = \common\models\ar\MstProcessPrinting::findOne(['nama_proses' => 'Printing']);
                    if (!$mstProcess) {
                        $mstProcess = \common\models\ar\MstProcessPrinting::find()->orderBy('order')->one();
                    }
                    if ($mstProcess) {
                        $kpProcess = \common\models\ar\KartuProcessPrintingProcess::findOne([
                            'kartu_process_id' => $model->kartu_proses_id,
                            'process_id' => $mstProcess->id
                        ]);
                        if ($kpProcess) {
                            $kpProcess->delete();
                        }
                    }
                }
                $model->delete();
                Yii::$app->session->setFlash('success', 'Data input berhasil dihapus.');
            }
        } elseif ($tipe === 'card') {
            $mstProcess = \common\models\ar\MstProcessPrinting::findOne(['nama_proses' => 'Printing']);
            if (!$mstProcess) {
                $mstProcess = \common\models\ar\MstProcessPrinting::find()->orderBy('order')->one();
            }
            if ($mstProcess) {
                $kpProcess = \common\models\ar\KartuProcessPrintingProcess::findOne([
                    'kartu_process_id' => $id,
                    'process_id' => $mstProcess->id
                ]);
                if ($kpProcess) {
                    $kpProcess->delete();
                }
            }

            \common\models\ar\TrnProduksiMesinPrinting::deleteAll(['kartu_proses_id' => $id]);
            Yii::$app->session->setFlash('success', 'Data input kartu berhasil dihapus.');
        }

        return $this->redirect([
            'input-produksi',
            'jenis_mesin' => $jenis_mesin,
            'mesin_id' => $mesin_id,
            'tanggal' => $tanggal,
            'shift' => $shift,
            'pembagian_hari' => $pembagian_hari
        ]);
    }

    public function actionGetNkDetails($nk)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $model = \common\models\ar\TrnKartuProsesPrinting::findOne(['nomor_kartu' => $nk]);
            if ($model) {
                $warna = ($model->woColor && $model->woColor->moColor) ? $model->woColor->moColor->color : '';
                $panjangGreige = $model->getTrnKartuProsesPrintingItems()->sum('panjang_m') ?: 0;
                
                $realisasi = 0;
                if ($model->wo_color_id) {
                    $cards = \common\models\ar\TrnKartuProsesPrinting::find()
                        ->where(['wo_color_id' => $model->wo_color_id])
                        ->andWhere(['status' => \common\models\ar\TrnKartuProsesPrinting::STATUS_DELIVERED])
                        ->all();
                    foreach ($cards as $card) {
                        $realisasi += (float)$card->getTrnKartuProsesPrintingItems()->sum('panjang_m');
                    }
                }

                $qtyOrder = $model->woColor ? $model->woColor->qtyFinishToMeter : 0;

                return [
                    'success' => true,
                    'wo_no' => $model->wo ? $model->wo->no : '',
                    'design' => ($model->wo && $model->wo->mo) ? $model->wo->mo->design : '',
                    'motif' => ($model->wo && $model->wo->greige) ? $model->wo->greige->nama_kain : '',
                    'warna' => $warna,
                    'jumlah_pesanan' => number_format((float)$qtyOrder, 2, '.', ''),
                    'realisasi' => number_format((float)$realisasi, 2, '.', ''),
                    'kurang' => number_format((float)($qtyOrder - $realisasi), 2, '.', ''),
                    'panjang_greige' => number_format((float)$panjangGreige, 2, '.', ''),
                ];
            }
            return ['success' => false, 'message' => 'NK tidak ditemukan.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()];
        }
    }

    public function actionGetNks($q, $wo_no = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $query = \common\models\ar\TrnKartuProsesPrinting::find()
            ->where(['like', 'nomor_kartu', $q])
            ->andWhere(['status' => \common\models\ar\TrnKartuProsesPrinting::STATUS_DELIVERED]);
        
        if ($wo_no) {
            $wo = \common\models\ar\TrnWo::findOne(['no' => $wo_no]);
            if ($wo) {
                $query->andWhere(['wo_id' => $wo->id]);
            } else {
                $query->andWhere('1=0');
            }
        }
        
        $nks = $query->limit(20)->all();
        
        $out = [];
        foreach ($nks as $nk) {
            $out[] = ['id' => $nk->nomor_kartu, 'text' => $nk->nomor_kartu];
        }
        return ['results' => $out];
    }

    public function actionGetWos($q)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $wos = \common\models\ar\TrnWo::find()
            ->joinWith('scGreige', false)
            ->where(['like', 'trn_wo.no', $q])
            ->andWhere(['trn_sc_greige.process' => \common\models\ar\TrnScGreige::PROCESS_PRINTING])
            ->andWhere(['trn_wo.status' => \common\models\ar\TrnWo::STATUS_APPROVED])
            ->limit(20)
            ->all();
        
        $out = [];
        foreach ($wos as $wo) {
            $out[] = ['id' => $wo->no, 'text' => $wo->no];
        }
        return ['results' => $out];
    }

    public function actionGetWoDetails($wo_no)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $wo = \common\models\ar\TrnWo::findOne(['no' => $wo_no]);
        if ($wo) {
             $colors = [];
             foreach ($wo->trnWoColors as $wc) {
                 if ($wc->moColor) {
                     $realisasi = 0;
                     $cards = \common\models\ar\TrnKartuProsesPrinting::find()
                         ->where(['wo_color_id' => $wc->id])
                         ->andWhere(['status' => \common\models\ar\TrnKartuProsesPrinting::STATUS_DELIVERED])
                         ->all();
                     foreach ($cards as $card) {
                         $realisasi += (float)$card->getTrnKartuProsesPrintingItems()->sum('panjang_m');
                     }

                     $colors[] = [
                         'color' => $wc->moColor->color,
                         'qty_finish_yard' => number_format((float)$wc->qtyFinishToMeter, 2, '.', ''),
                         'realisasi' => number_format((float)$realisasi, 2, '.', ''),
                     ];
                 }
             }

            return [
                'success' => true,
                'design' => $wo->mo ? $wo->mo->design : '',
                'motif' => $wo->greige ? $wo->greige->nama_kain : '',
                'colors' => $colors,
            ];
        }
        return ['success' => false];
    }

    protected function findModel($id)
    {
        if (($model = TrnKartuProsesPrinting::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

