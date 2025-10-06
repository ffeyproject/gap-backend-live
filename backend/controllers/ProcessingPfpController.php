<?php
namespace backend\controllers;

use backend\models\form\CatatanProsesForm;
use backend\models\form\FinishingProcessingPfpForm;
use common\models\ar\KartuProcessPfpProcess;
use common\models\ar\MstGreige;
use common\models\ar\MstProcessPfp;
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnKartuProsesDyeingItem;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWo;
use common\models\ar\TrnWoColor;
use common\models\ar\TrnOrderPfp;
use common\models\Model;
use Yii;
use common\models\ar\TrnKartuProsesPfp;
use common\models\ar\TrnKartuProsesPfpSearch;
use common\models\ar\TrnStockGreigeOpname;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
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
 * TrnKartuProsesPfpController implements the CRUD actions for TrnKartuProsesPfp model.
 */
class ProcessingPfpController extends Controller
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
        $dataProvider->query->andWhere(['>', 'trn_kartu_proses_pfp.status', TrnKartuProsesPfp::STATUS_POSTED]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all TrnKartuProsesPfp models.
     * @return mixed
     */
    public function actionRekap()
    {
        $searchModel = new TrnKartuProsesPfpSearch(['status'=>TrnKartuProsesPfp::STATUS_DELIVERED]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('rekap', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnKartuProsesPfp model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $processModels = MstProcessPfp::find()->orderBy('order')->all();

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
                        'header'=>[],
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

            $pcModel = KartuProcessPfpProcess::findOne(['kartu_process_id'=>$model->id, 'process_id'=>$proses_id]);
            if($pcModel === null){
                $datas = [];
                $pcModel = new KartuProcessPfpProcess(['kartu_process_id'=>$model->id, 'process_id'=>$proses_id]);
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

            $pcModel = KartuProcessPfpProcess::findOne(['kartu_process_id'=>$model->id, 'process_id'=>$proses_id]);
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
                    $modelKp = TrnKartuProsesPfp::findOne($model->kartu_proses_id);
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
     * Deletes an existing KartuProsesDyeing model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionApprove($id)
    {
        if (Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            $statusValid = $model->status === $model::STATUS_DELIVERED || $model->status === $model::STATUS_INSPECTED;

            if(!$statusValid){
                throw new ForbiddenHttpException('Status Kartu proses tidak valid.');
            }

            /*if(!$model->isAllProcessDone){
                throw new ForbiddenHttpException('Proses belum selesai. Tidak Bisa diteruskan ke gudang PFP.');
            }*/

            $modelsItem = [new FinishingProcessingPfpForm];

            if(isset(Yii::$app->request->post()['FinishingProcessingPfpForm'])){
                $modelsItem = Model::createMultiple(FinishingProcessingPfpForm::classname());
                if(Model::loadMultiple($modelsItem, Yii::$app->request->post()) && Model::validateMultiple($modelsItem)){
                    //Bypass langsung berstatus inspected
                    $model->status = $model::STATUS_INSPECTED;
                    $model->approved_at = time();
                    $model->approved_by = Yii::$app->user->id;
                    $model->delivered_at = $model->approved_at;
                    $model->delivered_by = $model->approved_by;

                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if (!$flag = $model->save(false, ['status', 'approved_at', 'approved_by', 'delivered_at', 'delivered_by'])){
                            $transaction->rollBack();
                            throw new HttpException('500', 'Gagal, coba lagi. (1)');
                        }

                        $ts = time();
                        $totalLength = 0;
                        foreach ($modelsItem as $item) {
                            $cmd = Yii::$app->db->createCommand()->insert(TrnStockGreige::tableName(), [
                                'greige_group_id' => $model->greige_group_id,
                                'greige_id' => $model->greige_id,
                                'asal_greige' => $model->asal_greige,
                                'no_lapak' => '-',
                                'lot_lusi' => '-',
                                'lot_pakan' => '-',
                                'no_set_lusi' => '-',
                                'status_tsd' => TrnStockGreige::STATUS_TSD_NORMAL,
                                'no_document' => $model->no,
                                'pengirim' => $model->createdBy->full_name,
                                'mengetahui' => $model->approvedBy->full_name,
                                'note' => 'From Kartu Proses PFP No: '.$model->no,
                                'date' => date('Y-m-d'),
                                'status' => TrnStockGreige::STATUS_VALID,
                                'jenis_gudang' => TrnStockGreige::JG_PFP,
                                'pfp_jenis_gudang' => TrnStockGreige::PFP_JG_TWO,
                                'grade' => TrnStockGreige::GRADE_NG,
                                'panjang_m' => $item->qty,
                                'color' => $model->orderPfp->dasar_warna,
                                'created_at' => $ts,
                                'created_by' => $model->approved_by,
                                'updated_at' => $ts,
                                'updated_by' => $model->approved_by,
                            ])->execute();
                            if(!$flag = $cmd > 0){
                                $transaction->rollBack();
                                $transaction->rollBack();
                                throw new HttpException('500', 'Gagal, coba lagi. (2)');
                            }

                            $totalLength += $item->qty;
                        }

                        //tambah stock pfp pada greige
                        //$command = Yii::$app->db->createCommand("UPDATE mst_greige SET stock_pfp = stock_pfp + {$totalLength} WHERE id= {$model->greige_id}");
                        $command = Yii::$app->db->createCommand()->update(MstGreige::tableName(), [
                            'stock_pfp' => new Expression("stock_pfp + {$totalLength}")
                        ], ['id'=>$model->greige_id])->execute();
                        if(!$flag = $command > 0){
                            $transaction->rollBack();
                            $transaction->rollBack();
                            throw new HttpException('500', 'Gagal, coba lagi. (3)');
                        }

                        if($flag){
                            $transaction->commit();
                            return ['success'=>true];
                        }
                    }catch (\Throwable $t){
                        $transaction->rollBack();
                        throw $t;
                    }
                }

                throw new HttpException(422, 'Data validation vailed');
            }

            return $this->renderAjax('finishing', [
                'model' => $model,
                'modelsItem' => (empty($modelsItem)) ? [[new FinishingProcessingPfpForm]] : $modelsItem,
            ]);
        }

        throw new ForbiddenHttpException('Not allowed');
    }

    /**
     * Deletes an existing KartuProsesDyeing model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionApproveOld($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DELIVERED){
            Yii::$app->session->setFlash('error', 'Status Kartu proses tidak valid.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if(!$model->isAllProcessDone){
            Yii::$app->session->setFlash('error', 'Proses belum selesai.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        //Bypass langsung berstatus inspected
        $model->status = $model::STATUS_INSPECTED;
        $model->approved_at = time();
        $model->approved_by = Yii::$app->user->id;
        $model->delivered_at = $model->approved_at;
        $model->delivered_by = $model->approved_by;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(!$flag = $model->save(false, ['status', 'approved_at', 'approved_by'])){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal memproses, coba lagi.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            $ts = time();
            $totalLength = 0;
            foreach ($model->trnKartuProsesPfpItems as $item) {
                $cmd1 = Yii::$app->db->createCommand()->insert(TrnStockGreige::tableName(), [
                    'greige_group_id' => $model->greige_group_id,
                    'greige_id' => $model->greige_id,
                    'asal_greige' => $model->asal_greige,
                    'no_lapak' => '-',
                    'lot_lusi' => '-',
                    'lot_pakan' => '-',
                    'no_set_lusi' => '-',
                    'status_tsd' => TrnStockGreige::STATUS_TSD_NORMAL,
                    'no_document' => $model->no,
                    'pengirim' => $model->createdBy->full_name,
                    'mengetahui' => $model->approvedBy->full_name,
                    'note' => 'From Kartu Proses PFP',
                    'date' => date('Y-m-d'),
                    'status' => TrnStockGreige::STATUS_VALID,
                    'jenis_gudang' => TrnStockGreige::JG_PFP,
                    'pfp_jenis_gudang' => TrnStockGreige::PFP_JG_TWO,
                    'grade' => TrnStockGreige::GRADE_NG,
                    'panjang_m' => $item->panjang_m,
                    'created_at' => $ts,
                    'created_by' => $model->approved_by,
                    'updated_at' => $ts,
                    'updated_by' => $model->approved_by,

                ]);
                if(!$flag = $cmd1->execute() > 0){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal, coba lagi.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                $totalLength += $item->panjang_m;
            }

            //tambah stock pfp pada greige
            $command = Yii::$app->db->createCommand("UPDATE mst_greige SET stock_pfp = stock_pfp + {$totalLength} WHERE id= {$model->greige_id}");
            if(!$flag = $command->execute() > 0){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal, coba lagi.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            if($flag){
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Berhasil , Stok PFP berdasarkan kartu prooses ini sudah masuk ke gudang.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    public function actionGantiDyeing($id)
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
            $noWo = $data['no_wo'];
            $color = $data['color'];

            $wo = TrnWo::findOne(['no'=>$noWo]);
            if ($wo === null){
                throw new NotFoundHttpException('Wo tidak ditemukan.');
            }

            // periksa apakah wo merupakan dyeing
            if($wo->mo->scGreige->process !== TrnScGreige::PROCESS_DYEING){
                throw new NotFoundHttpException('Wo tidak valid, bukan dyeing.');
            }

            /* @var $woColor TrnWoColor*/
            $woColor = TrnWoColor::find()->joinWith('moColor')
                ->where(['trn_mo_color.color'=>$color])
                ->andWhere(['trn_wo_color.wo_id'=>$wo->id])
                ->one()
            ;
            if ($woColor === null){
                throw new NotFoundHttpException('Wo Color tidak valid.');
            }

            $modelKpDyeing = new TrnKartuProsesDyeing([
                'sc_id' => $wo->mo->scGreige->sc_id,
                'sc_greige_id' => $wo->mo->sc_greige_id,
                'mo_id' => $wo->mo_id,
                'wo_id' => $wo->id,
                'kartu_proses_id' => null,
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
                //'hasil_tes_gosok' => $model->tes,
                'wo_color_id' => $woColor->id,
                'no_limit_item' => $model->no_limit_item,
                'nomor_kartu' => $model->nomor_kartu,
            ]);

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($modelKpDyeing->save(false)){
                    foreach ($model->trnKartuProsesPfpItems as $trnKartuProsesPfpItem) {
                        Yii::$app->db->createCommand()->insert(TrnKartuProsesDyeingItem::tableName(), [
                            'sc_id' => $modelKpDyeing->sc_id,
                            'sc_greige_id' => $modelKpDyeing->sc_greige_id,
                            'mo_id' => $modelKpDyeing->mo_id,
                            'wo_id' => $modelKpDyeing->wo_id,
                            'kartu_process_id' => $modelKpDyeing->id,
                            'stock_id' => $trnKartuProsesPfpItem->stock_id,
                            'panjang_m' => $trnKartuProsesPfpItem->panjang_m,
                            'mesin' => $trnKartuProsesPfpItem->mesin,
                            'tube' => $trnKartuProsesPfpItem->tube,
                            'note' => $trnKartuProsesPfpItem->note,
                            'status' => $trnKartuProsesPfpItem->status,
                            'date' => $modelKpDyeing->date,
                            'created_at' => $modelKpDyeing->created_at,
                            'created_by' => $modelKpDyeing->created_by,
                            'updated_at' => $modelKpDyeing->updated_at,
                            'updated_by' => $modelKpDyeing->updated_by,
                        ])->execute();
                    }

                    $model->status = $model::STATUS_APPROVED;
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

    /**
     * @param integer $id
     * @return array
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionGantiMotif($id)
    {
        if (Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            $greige = MstGreige::find()->select('id, group_id')->where(['nama_kain' => Yii::$app->request->post()['nama_motif']])->asArray()->one();
            if(empty($greige)){
                throw new NotFoundHttpException('Motif tidak valid - '. Yii::$app->request->post()['nama_motif']);
            }

            $model->greige_id = $greige['id'];
            $model->greige_group_id = $greige['group_id'];
            $model->save(false, ['greige_id', 'greige_group_id']);

            return ['success'=>true, 'greige'=>$greige];
        }

        throw new ForbiddenHttpException('Not allowed');
    }

    public function actionGantiPfp($id)
    {
        if (Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            $pfp = TrnOrderPfp::find()->select('id')->where(['no' => Yii::$app->request->post()['no']])->asArray()->one();
            if(empty($pfp)){
                throw new NotFoundHttpException('Nomor Pfp tidak ada (tidak valid) - '. Yii::$app->request->post()['no']);
            }

            $model->order_pfp_id = $pfp['id'];
            $model->save(false, ['order_pfp_id']);

            return ['success'=>true, 'pfp'=>$pfp];
        }

        throw new ForbiddenHttpException('Not allowed');
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


    public function actionKembaliStock($id)
{
    $model = $this->findModel($id);

    // 1. Cek apakah sudah ada proses "Buka Greige"
    $sudahBukaGreige = KartuProcessPfpProcess::find()
        ->where(['kartu_process_id' => $model->id, 'process_id' => 1])
        ->exists();

    if ($sudahBukaGreige) {
        Yii::$app->session->setFlash('error', 'Maaf, kartu sudah di Buka Greige.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    $transaction = Yii::$app->db->beginTransaction();
    try {
        // 2. Ubah status kartu proses jadi GAGAL_PROSES
        $model->status = $model::STATUS_GAGAL_PROSES;
        $model->save(false, ['status']);

        $trnStockGreigeIds = [];

        // 3. Loop semua item PFP
        foreach ($model->trnKartuProsesPfpItems as $item) {
            $stockItem = $item->stock; // relasi harus ada di TrnKartuProsesPfpItem

            if ($stockItem === null) {
                continue;
            }

            // Ambil greige_id → prioritas dari stock, fallback ke kartu
            $greigeId = $stockItem->greige_id ?? $model->greige_id;
            if ($greigeId === null) {
                continue;
            }

            $mstGreige = \common\models\ar\MstGreige::findOne($greigeId);
            if ($mstGreige === null) {
                continue;
            }

            // === PEMBEDAAN ===
            if ($stockItem->opname instanceof \common\models\ar\TrnStockGreigeOpname) {
                // ✅ Sudah ada di opname
                $mstGreige->addBackToStockOpname($item->panjang_m);

                // update status + note opname
                $stockItem->opname->status = \common\models\ar\TrnStockGreigeOpname::STATUS_VALID;
                $stockItem->opname->note   = 'Dikembalikan dari NK PFP: ' . $model->nomor_kartu;
                $stockItem->opname->save(false, ['status','note']);

                // simpan FK ke TrnStockGreige
                if ($stockItem->opname->stock_greige_id) {
                    $trnStockGreigeIds[] = $stockItem->opname->stock_greige_id;
                }
            } else {
                // ✅ Tidak ada di opname
                $mstGreige->addBackToStock($item->panjang_m);
            }

            // update note di TrnStockGreige
            $stockItem->note = 'Dikembalikan dari NK PFP: ' . $model->nomor_kartu;
            $stockItem->save(false, ['note']);

            $trnStockGreigeIds[] = $stockItem->id;
        }

        // 4. Update semua TrnStockGreige sekaligus
        if (!empty($trnStockGreigeIds)) {
            \common\models\ar\TrnStockGreige::updateAll(
                [
                    'status' => \common\models\ar\TrnStockGreige::STATUS_VALID,
                    'note'   => 'Dikembalikan dari NK PFP: ' . $model->nomor_kartu,
                ],
                ['id' => $trnStockGreigeIds]
            );
        }

        $transaction->commit();
        Yii::$app->session->setFlash('success', 'Stok PFP berhasil dikembalikan.');
    } catch (\Throwable $e) {
        $transaction->rollBack();
        Yii::$app->session->setFlash('error', 'Gagal mengembalikan stok PFP: ' . $e->getMessage());
    }

    return $this->redirect(['view', 'id' => $model->id]);
}

}