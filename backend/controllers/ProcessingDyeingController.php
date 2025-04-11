<?php
namespace backend\controllers;

use backend\models\form\CatatanProsesForm;
use backend\models\form\HasilTesGosokForm;
use common\models\ar\KartuProcessDyeingProcess;
use common\models\ar\KartuProcessDyeingProcessSearch;
use common\models\ar\MstProcessDyeing;
use common\models\ar\TrnKartuProsesDyeingItem;
use common\models\ar\TrnKartuProsesPfpItem;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWo;
use common\models\ar\TrnWoColor;
use common\models\ar\TrnOrderPfp;
use Yii;
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnKartuProsesPfp;
use common\models\ar\TrnKartuProsesDyeingSearch;
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
 * TrnKartuProsesDyeingController implements the CRUD actions for TrnKartuProsesDyeing model.
 */
class ProcessingDyeingController extends Controller
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
        $searchModel = new TrnKartuProsesDyeingSearch(['status'=>TrnKartuProsesDyeing::STATUS_DELIVERED]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all TrnKartuProsesDyeing models.
     * @return mixed
     */
    public function actionRekap()
    {
        $searchModel = new TrnKartuProsesDyeingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['>', 'trn_kartu_proses_dyeing.status', TrnKartuProsesDyeing::STATUS_POSTED]);

        return $this->render('rekap', [
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
        /*Yii::$app->session->setFlash('info',
            '<ul>
<li>Pembatalan hanya diizinkan jika belum ada proses yang dimulai.</li>
<li>Jika dibatalkan, Semua roll dikembalikan statusnya menjadi valid agar bisa digunakan lagi oleh kartu proses yang lain.</li>
</ul>'
        );*/

        $model = $this->findModel($id);

        $processModels = MstProcessDyeing::find()->orderBy('order')->all();

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
            foreach ($model->trnKartuProsesDyeingItems as $trnKartuProsesDyeingItem) {
                $stock = $trnKartuProsesDyeingItem->stock;
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
                    'note'=>'Gagal proses pada kartu proses dyeing No:'.$model->no
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

            $pcModel = KartuProcessDyeingProcess::findOne(['kartu_process_id'=>$model->id, 'process_id'=>$proses_id]);
            if($pcModel === null){
                $datas = [];
                $pcModel = new KartuProcessDyeingProcess(['kartu_process_id'=>$model->id, 'process_id'=>$proses_id]);
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

            $pcModel = KartuProcessDyeingProcess::findOne(['kartu_process_id'=>$model->id, 'process_id'=>$proses_id]);
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
     * Pembatalan Kartu Proses.
     * Hanya diizinkan jika belum ada proses yang dimulai (relasi $model->getKartuProcessDyeingProcesses() masih kosong).
     * Kartu Proses diubah statusnya menjadi 'Batal'
     * Semua roll greige yang ada dikembalikan statusnya menjadi valid agar bisa digunakan lagi oleh kartu proses yang lain
     * Update/kembalikan stock greige terkait, tambahkan sejumlah roll pada kartu proses yang dibatalkan
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionBatal($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DELIVERED){
            Yii::$app->session->setFlash('error', 'Status Kartu proses tidak valid.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if($model->getKartuProcessDyeingProcesses()->count('kartu_process_id') > 0){
            Yii::$app->session->setFlash('error', 'Proses sudah berjalan, tidak bisa dibatalkan.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->status = $model::STATUS_BATAL;

        $totalLength = 0;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(!($flag = $model->save(false, ['status']))){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Pembatalan Gagal, coba lagi.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            foreach ($model->trnKartuProsesDyeingItems as $trnKartuProsesDyeingItem) {
                $stock = $trnKartuProsesDyeingItem->stock;
                $stock->status = $stock::STATUS_VALID;
                if(!($flag = $stock->save(false, ['status']))){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Pembatalan Gagal, coba lagi.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                $totalLength += $stock->panjang_m;
            }

            //Update stock greige--------------------------------------------------------------------------------------------
            $wo = $model->wo;
            $mo = $wo->mo;

            switch ($mo->jenis_gudang){
                case TrnStockGreige::JG_WIP:
                    $stockAttr = 'stock_wip';
                    break;
                case TrnStockGreige::JG_PFP:
                    $stockAttr = 'stock_pfp';
                    break;
                case TrnStockGreige::JG_EX_FINISH:
                    $stockAttr = 'stock_ef';
                    break;
                default:
                    $stockAttr = 'stock';
            }
            $greigeId = $wo->greige_id;
            $sqlCmd = "UPDATE mst_greige SET {$stockAttr} = {$stockAttr} + {$totalLength} WHERE id=:id";
            $command = Yii::$app->db->createCommand($sqlCmd)->bindParam(':id', $greigeId);
            if(!$flag = $command->execute() > 0){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal, coba lagi.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
            //Update stock greige--------------------------------------------------------------------------------------------

            if($flag){
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Kartu proses berhasil dibatalkan.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
            //BaseVarDumper::dump($model, 10, true);Yii::$app->end();
        }catch (\Throwable $e){
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id' => $model->id]);
        }
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
                    $modelKp = TrnKartuProsesDyeing::findOne($model->kartu_proses_id);
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
     * @return array|bool|string|Response
     * @throws ForbiddenHttpException
     */
    public function actionAddHasilTesGosok($id){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = new HasilTesGosokForm(['kartu_proses_id'=>$id]);

            if($model->load(Yii::$app->request->post())){
                if($model->save()){
                    return ['success'=>true, 'data'=>$model->hasil_tes_gosok];
                }

                $result = [];
                // The code below comes from ActiveForm::validate(). We do not need to validate the model
                // again, as it was already validated by save(). Just collect the messages.
                foreach ($model->getErrors() as $attribute => $errors) {
                    $result[Html::getInputId($model, $attribute)] = $errors;
                }
                return ['validation' => $result];
            }

            return $this->renderAjax('add-hasil-tes-gosok', [
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
            $model->handling = $wo->handling->name;
            $model->lebar_preset = $wo->handling->lebar_preset;
            $model->lebar_finish = $wo->handling->lebar_finish;
            $model->berat_finish = $wo->handling->berat_finish;
            $model->t_density_lusi = $wo->handling->densiti_lusi;
            $model->t_density_pakan = $wo->handling->densiti_pakan;
            $model->save(false, ['wo_id','wo_color_id', 'mo_id', 'sc_id', 'handling', 'lebar_preset', 'lebar_finish', 'berat_finish', 't_density_lusi', 't_density_pakan']);

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

    public function actionSetTungguMkt($id){
        $model = $this->findModel($id);

        $model->tunggu_marketing = !$model->tunggu_marketing;

        try {
            if(!$model->save(false, ['tunggu_marketing'])){
                throw new \Exception('Gagal mengubah status tunggu marketing.');
            }

            if($model->tunggu_marketing){
                Yii::$app->session->setFlash('success', 'Berhasil diset untuk menunggu marketing.');
            }else{
                Yii::$app->session->setFlash('success', 'Berhasil dibatalkan tunggu marketing.');
            }
        }catch (\Exception $e){
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionSetTopingMatching($id){
        $model = $this->findModel($id);

        $model->toping_matching = !$model->toping_matching;

        $model->date_toping_matching = time();

        try {
            if(!$model->save(false, ['toping_matching','date_toping_matching'])){
                throw new \Exception('Gagal mengubah status tunggu marketing.');
            }

            if($model->toping_matching){
                Yii::$app->session->setFlash('success', 'Berhasil diset untuk toping matching.');
            }else{
                Yii::$app->session->setFlash('success', 'Berhasil dibatalkan toping matching.');
            }
        }catch (\Exception $e){
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Lists all TrnKartuProsesDyeing models.
     * @return mixed
     */
    public function actionRekapByProcess()
    {
        $searchModel = new KartuProcessDyeingProcessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('rekap-by-process', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Ganti Wo dari kartu proses dyeing ke Pfp.
     *
     * @param integer $id id kartu proses dyeing
     * @return array
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionGantiKePfp($id)
    {
        if (Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            //$statusValid = $model->status === $model::STATUS_DELIVERED || $model->status === $model::STATUS_INSPECTED;
            $statusValid = $model->status === $model::STATUS_DELIVERED || $model->status === $model::STATUS_POSTED;

            if(!$statusValid){
                throw new ForbiddenHttpException('Status Kartu proses tidak valid.');
            }

            $data = Yii::$app->request->post();
            $noPfp = $data['no_order_pfp'];

            $orderPfp = TrnOrderPfp::findOne(['no'=>$noPfp]);
            if ($orderPfp === null){
                throw new NotFoundHttpException('Order Pfp tidak ditemukan.');
            }

            $modelKpPfp = new TrnKartuProsesPfp([
                'greige_group_id' => $orderPfp->greige_group_id,
                'greige_id' => $orderPfp->greige_id,
                'order_pfp_id' => $orderPfp->id, // sesuaikan jika ada relasi
                'no_urut' => $model->no_urut,
                'no' => $model->no,
                'asal_greige' => $model->asal_greige,
                'dikerjakan_oleh' => $model->dikerjakan_oleh,
                'lusi' => $model->lusi,
                'pakan' => $model->pakan,
                'note' => $model->note,
                'date' => $model->date,
                'posted_at' => $model->posted_at,
                'approved_at' => $model->approved_at,
                'approved_by' => $model->approved_by,
                'delivered_at' => $model->delivered_at,
                'delivered_by' => $model->delivered_by,
                'reject_notes' => $model->reject_notes,
                'status' => $model->status,
                'created_at' => $model->created_at,
                'created_by' => $model->created_by,
                'updated_at' => $model->updated_at,
                'updated_by' => $model->updated_by,
                'berat' => $model->berat,
                'lebar' => $model->lebar,
                'k_density_lusi' => $model->k_density_lusi,
                'k_density_pakan' => $model->k_density_pakan,
                'lebar_preset' => $model->lebar_preset,
                'lebar_finish' => $model->lebar_finish,
                'berat_finish' => $model->berat_finish,
                't_density_lusi' => $model->t_density_lusi,
                't_density_pakan' => $model->t_density_pakan,
                'handling' => $model->handling,
                'no_limit_item' => $model->no_limit_item,
                'nomor_kartu' => $model->nomor_kartu,
            ]);
            

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($modelKpPfp->save(false)){
                    foreach ($model->trnKartuProsesDyeingItems as $trnKartuProsesDyeingItem) {
                        Yii::$app->db->createCommand()->insert(TrnKartuProsesPfpItem::tableName(), [
                            'greige_group_id' => $modelKpPfp->greige_group_id,
                            'greige_id' => $modelKpPfp->greige_id,
                            'order_pfp_id' => $modelKpPfp->order_pfp_id,
                            'kartu_process_id' => $modelKpPfp->id,
                            'stock_id' => $trnKartuProsesDyeingItem->stock_id,
                            'panjang_m' => $trnKartuProsesDyeingItem->panjang_m,
                            'mesin' => $trnKartuProsesDyeingItem->mesin,
                            'tube' => $trnKartuProsesDyeingItem->tube,
                            'note' => $trnKartuProsesDyeingItem->note,
                            'status' => $trnKartuProsesDyeingItem->status,
                            'date' => $modelKpPfp->date,  
                            'created_at' => $modelKpPfp->created_at,
                        ])->execute();
                    }

                    $model->status = $model::STATUS_GANTI_GREIGE_LINKED;
                    $model->save(false, ['status']);

                    $transaction->commit();

                    return ['success'=>true, 'data'=>$data];
                }
            }catch (\Throwable $t){
                $transaction->rollBack();
                throw $t;
            }
        }

        throw new ForbiddenHttpException('Not allowed');
    }
}
