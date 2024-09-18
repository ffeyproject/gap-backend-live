<?php
namespace backend\controllers;

use backend\models\form\CatatanProsesForm;
use common\models\ar\KartuProcessCelupProcess;
use common\models\ar\MstProcessCelup;
use common\models\ar\MstProcessDyeing;
use common\models\ar\TrnStockGreige;
use common\models\User;
use Yii;
use common\models\ar\TrnKartuProsesCelup;
use common\models\ar\TrnKartuProsesCelupSearch;
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
 * TrnKartuProsesCelupController implements the CRUD actions for TrnKartuProsesCelup model.
 */
class ProcessingCelupController extends Controller
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
     * Lists all TrnKartuProsesCelup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnKartuProsesCelupSearch(['status'=>TrnKartuProsesCelup::STATUS_DELIVERED]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnKartuProsesCelup model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
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
        foreach ($model->kartuProcessCelupProcesses as $i=>$kartuProcessCelupProcess) {
            if($kartuProcessCelupProcess->value !== null){
                $dataProcess = Json::decode($kartuProcessCelupProcess->value);
                if(isset($dataProcess['pengulangan']) && !empty($dataProcess['pengulangan'])){
                    $processUlang = [
                        'nama_proses'=>'',
                        'header'=>[],
                        'pengulangan'=>[]
                    ];

                    $headers = [];
                    $attrs = $kartuProcessCelupProcess->process->attributes;
                    unset($attrs['id']); unset($attrs['order']); unset($attrs['created_at']); unset($attrs['created_by']); unset($attrs['updated_at']); unset($attrs['updated_by']); unset($attrs['max_pengulangan']);
                    foreach ($attrs as $key=>$attr) {
                        if($key === 'nama_proses'){
                            $processUlang['nama_proses'] = $attr;
                            unset($attrs['nama_proses']);
                        }else{
                            if($attr){
                                $headers[$key] = $kartuProcessCelupProcess->getAttributeLabel($key);
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

            $pcModel = KartuProcessCelupProcess::findOne(['kartu_process_id'=>$model->id, 'process_id'=>$proses_id]);
            if($pcModel === null){
                $datas = [];
                $pcModel = new KartuProcessCelupProcess(['kartu_process_id'=>$model->id, 'process_id'=>$proses_id]);
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

            $pcModel = KartuProcessCelupProcess::findOne(['kartu_process_id'=>$model->id, 'process_id'=>$proses_id]);
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
                    $modelKp = TrnKartuProsesCelup::findOne($model->kartu_proses_id);
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
            foreach ($model->trnKartuProsesCelupItems as $item) {
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
                    'pengirim' => User::findOne($model->created_by)->full_name,
                    'mengetahui' => $model->approvedBy->full_name,
                    'note' => 'From Kartu Proses Celup',
                    'date' => date('Y-m-d'),
                    'status' => TrnStockGreige::STATUS_VALID,
                    'jenis_gudang' => TrnStockGreige::JG_PFP,
                    'grade' => TrnStockGreige::GRADE_NG,
                    'panjang_m' => $item->panjang_m,
                    'created_at' => $ts,
                    'created_by' => $model->approved_by,
                    'updated_at' => $ts,
                    'updated_by' => $model->approved_by,
                    'color' => $model->orderCelup->color
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

    /**
     * Finds the TrnKartuProsesCelup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnKartuProsesCelup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnKartuProsesCelup::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
