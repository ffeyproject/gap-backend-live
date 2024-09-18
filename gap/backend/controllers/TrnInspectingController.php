<?php

namespace backend\controllers;

use backend\models\form\InspectingHeaderForm;
use backend\models\form\InspectingItemsForm;
use backend\models\search\AnalisaPengirimanProduksi;
use common\models\ar\InspectingItem;
use common\models\ar\TrnInspecting;
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnKartuProsesDyeingSearch;
use common\models\ar\TrnKartuProsesPrinting;
use common\models\ar\TrnKartuProsesPrintingSearch;
use common\models\ar\TrnMemoRepair;
use common\models\ar\TrnMemoRepairSearch;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnWo;
use kartik\mpdf\Pdf;
use Yii;
use common\models\ar\TrnInspectingSearch;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TrnInspectingController implements the CRUD actions for TrnInspecting model.
 */
class TrnInspectingController extends Controller
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
     * Lists all TrnInspecting models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnInspectingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnInspecting model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        //BaseVarDumper::dump($model->wo_id, 10, true);Yii::$app->end();

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws ForbiddenHttpException
     * @throws HttpException
     */
    public function actionCreate()
    {
        Yii::$app->session->setFlash('info',
            'Mencari kartu proses yang berstatus approved (sudah diterima oleh bagian penerimaan).'
        );

        $modelHeader = new InspectingHeaderForm();
        $modelItem = new InspectingItemsForm();

        if (Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            if($modelHeader->load(Yii::$app->request->post()) && $modelHeader->validate()){
                $modelInspecting = new TrnInspecting([
                    'date'=>date('Y-m-d'),
                ]);

                switch ($modelHeader->jenis_order){
                    case 'dyeing':
                        $kp = TrnKartuProsesDyeing::findOne($modelHeader->kartu_proses_id);
                        if($kp === null){
                            throw new ForbiddenHttpException('ID Proses tidak valid');
                        }

                        /*if(TrnInspecting::find()->where(['kartu_process_dyeing_id'=>$kp->id])->exists()){
                            throw new ForbiddenHttpException('Kartu Proses tidak valid, sudah pernah diinspect sebelumnya, mungkin masih berstatus draft. Periksa kembali.');
                        }*/

                        $modelInspecting->kartu_process_dyeing_id = $modelHeader->kartu_proses_id;
                        $modelInspecting->jenis_process = TrnScGreige::PROCESS_DYEING;
                        $modelInspecting->wo_id = $kp->wo_id;
                        $modelInspecting->mo_id = $kp->mo_id;
                        $modelInspecting->sc_greige_id = $kp->sc_greige_id;
                        $modelInspecting->sc_id = $kp->sc_id;
                        $modelInspecting->kombinasi = $kp->woColor->moColor->color;
                        break;
                    case 'printing':
                        $kp = TrnKartuProsesPrinting::findOne($modelHeader->kartu_proses_id);
                        if($kp === null){
                            throw new ForbiddenHttpException('ID Proses tidak valid');
                        }

                        /*if(TrnInspecting::find()->where(['kartu_process_printing_id'=>$kp->id])->exists()){
                            throw new ForbiddenHttpException('Kartu Proses tidak valid, sudah pernah diinspect sebelumnya, mungkin masih berstatus draft. Periksa kembali.');
                        }*/

                        $modelInspecting->kartu_process_printing_id = $modelHeader->kartu_proses_id;
                        $modelInspecting->jenis_process = TrnScGreige::PROCESS_PRINTING;
                        $modelInspecting->wo_id = $kp->wo_id;
                        $modelInspecting->mo_id = $kp->mo_id;
                        $modelInspecting->sc_greige_id = $kp->sc_greige_id;
                        $modelInspecting->sc_id = $kp->sc_id;
                        $modelInspecting->kombinasi = $kp->woColor->moColor->color;
                        break;
                    case 'memo_repair':
                        /* @var $modelMemoRepair TrnMemoRepair*/
                        $modelMemoRepair = TrnMemoRepair::find()->where([
                            'and',
                            ['id'=>$modelHeader->kartu_proses_id, 'status'=>TrnMemoRepair::STATUS_REPAIRED]
                        ])->one();
                        if(empty($modelMemoRepair)){
                            throw new ForbiddenHttpException('ID Memo Repair tidak valid');
                        }

                        $modelInspecting->memo_repair_id = $modelMemoRepair->id;
                        $modelInspecting->jenis_process = $modelMemoRepair->scGreige->process;
                        $modelInspecting->kombinasi = '-';
                        $modelInspecting->unit = $modelMemoRepair->returBuyer->unit;
                        $modelInspecting->wo_id = $modelMemoRepair->wo_id;
                        $modelInspecting->mo_id = $modelMemoRepair->mo_id;
                        $modelInspecting->sc_greige_id = $modelMemoRepair->sc_greige_id;
                        $modelInspecting->sc_id = $modelMemoRepair->sc_id;
                        $modelInspecting->kombinasi = '-';
                        break;
                    default:
                        throw new HttpException(500, 'Permintaan tidak valid.');
                }

                if($modelInspecting->kartu_process_dyeing_id === null && $modelInspecting->kartu_process_printing_id === null && $modelInspecting->memo_repair_id === null){
                    throw new ForbiddenHttpException('Referensi tidak valid. Periksa kembali.');
                }

                $modelInspecting->no_lot = $modelHeader->no_lot;
                $modelInspecting->tanggal_inspeksi = $modelHeader->tgl_inspeksi;
                $modelInspecting->date = $modelHeader->tgl_kirim;
                $modelInspecting->unit = $modelHeader->status;

                //throw new ForbiddenHttpException(Json::encode(Yii::$app->request->post()));

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if(!($flag = $modelInspecting->save(false))){
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal, coba lagi. (1)');
                    }

                    foreach (Json::decode(Yii::$app->request->post('items')) as $item) {
                        $modelItem = new InspectingItem([
                            'inspecting_id' => $modelInspecting->id,
                            'grade' => $item['grade'],
                            'join_piece' => $item['join_piece'],
                            'qty' => $item['ukuran'],
                            'note' => $item['keterangan'],
                        ]);

                        if(!($flag = $modelItem->save(false))){
                            $transaction->rollBack();
                            throw new HttpException(500, 'Gagal, coba lagi. (2)');
                        }
                    }
                    if ($flag){
                        $transaction->commit();
                        return ['success'=>true, 'redirect'=>Url::to(['view', 'id'=>$modelInspecting->id])];

                    }
                }catch (\Throwable $t){
                    $transaction->rollBack();
                    throw $t;
                }
            }

            $result = [];
            // The code below comes from ActiveForm::validate(). We do not need to validate the model
            // again, as it was already validated by save(). Just collect the messages.
            foreach ($modelHeader->getErrors() as $attribute => $errors) {
                $result[Html::getInputId($modelHeader, $attribute)] = $errors;
            }

            return ['validation' => $result];
        }

        return $this->render('create', [
            'modelHeader' => $modelHeader,
            'modelItem' => $modelItem
        ]);
    }

    /**
     * Updates an existing TrnInspecting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     */
    public function actionUpdate($id){
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk diupdate.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        Yii::$app->session->setFlash('info',
            'Mencari kartu proses yang berstatus approved (sudah diterima oleh bagian penerimaan).'
        );

        $modelHeader = new InspectingHeaderForm([
            'tgl_kirim' => $model->date,
            'tgl_inspeksi' => $model->tanggal_inspeksi,
            'no_lot' => $model->no_lot,
            'status' => $model->unit,

        ]);

        $nomorKartu = '';
        $kombinasi = '-';

        if($model->memo_repair_id !== null){
            $modelHeader->kartu_proses_id = $model->memo_repair_id;
            $nomorKartu = $model->memoRepair->no;
            $modelHeader->jenis_order = 'memo_repair';
        }else{
            switch ($model->scGreige->process){
                case TrnScGreige::PROCESS_DYEING:
                    $modelHeader->kartu_proses_id = $model->kartu_process_dyeing_id;
                    $nomorKartu = $model->kartuProcessDyeing->no;
                    $modelHeader->jenis_order = 'dyeing';
                    $kombinasi = $model->kartuProcessDyeing->woColor->moColor->color;
                    break;
                case TrnScGreige::PROCESS_PRINTING:
                    $modelHeader->kartu_proses_id = $model->kartu_process_printing_id;
                    $nomorKartu = $model->kartuProcessPrinting->no;
                    $modelHeader->jenis_order = 'printing';
                    $kombinasi = $model->kartuProcessPrinting->woColor->moColor->color;
                    break;
            }
        }

        $modelItem = new InspectingItemsForm();

        $items = [];
        foreach ($model->inspectingItems as $inspectingItem) {
            $items[] = [
                'grade' => $inspectingItem->grade,
                'gradeLabel' => $inspectingItem::gradeOptions()[$inspectingItem->grade],
                'ukuran' => $inspectingItem->qty,
                'join_piece' => $inspectingItem->join_piece,
                'keterangan' => $inspectingItem->note
            ];
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if($modelHeader->load(Yii::$app->request->post()) && $modelHeader->validate()){
                $modelInspecting = $model;

                switch ($modelHeader->jenis_order){
                    case 'dyeing':
                        $kp = TrnKartuProsesDyeing::findOne($modelHeader->kartu_proses_id);
                        if($kp === null){
                            throw new ForbiddenHttpException('ID Proses tidak valid');
                        }

                        $modelInspecting->kartu_process_dyeing_id = $modelHeader->kartu_proses_id;
                        $modelInspecting->jenis_process = TrnScGreige::PROCESS_DYEING;
                        $modelInspecting->wo_id = $kp->wo_id;
                        $modelInspecting->mo_id = $kp->mo_id;
                        $modelInspecting->sc_greige_id = $kp->sc_greige_id;
                        $modelInspecting->sc_id = $kp->sc_id;
                        $modelInspecting->kombinasi = $kp->woColor->moColor->color;
                        break;
                    case 'printing':
                        $kp = TrnKartuProsesPrinting::findOne($modelHeader->kartu_proses_id);
                        if($kp === null){
                            throw new ForbiddenHttpException('ID Proses tidak valid');
                        }

                        $modelInspecting->kartu_process_printing_id = $modelHeader->kartu_proses_id;
                        $modelInspecting->jenis_process = TrnScGreige::PROCESS_PRINTING;
                        $modelInspecting->wo_id = $kp->wo_id;
                        $modelInspecting->mo_id = $kp->mo_id;
                        $modelInspecting->sc_greige_id = $kp->sc_greige_id;
                        $modelInspecting->sc_id = $kp->sc_id;
                        $modelInspecting->kombinasi = $kp->woColor->moColor->color;
                        break;
                    case 'memo_repair':
                        /* @var $modelMemoRepair TrnMemoRepair*/
                        $modelMemoRepair = TrnMemoRepair::find()->where([
                            'and',
                            ['id'=>$modelHeader->kartu_proses_id, 'status'=>TrnMemoRepair::STATUS_REPAIRED]
                        ])->one();
                        if(empty($modelMemoRepair)){
                            throw new ForbiddenHttpException('ID Memo Repair tidak valid');
                        }

                        $modelInspecting->memo_repair_id = $modelMemoRepair->id;
                        $modelInspecting->jenis_process = $modelMemoRepair->scGreige->process;
                        $modelInspecting->kombinasi = '-';
                        $modelInspecting->unit = $modelMemoRepair->returBuyer->unit;
                        $modelInspecting->wo_id = $modelMemoRepair->wo_id;
                        $modelInspecting->mo_id = $modelMemoRepair->mo_id;
                        $modelInspecting->sc_greige_id = $modelMemoRepair->sc_greige_id;
                        $modelInspecting->sc_id = $modelMemoRepair->sc_id;
                        $modelInspecting->kombinasi = '-';
                        break;
                    default:
                        throw new HttpException(500, 'Permintaan tidak valid.');
                }

                if($modelInspecting->kartu_process_dyeing_id === null && $modelInspecting->kartu_process_printing_id === null && $modelInspecting->memo_repair_id === null){
                    throw new ForbiddenHttpException('Referensi tidak valid. Periksa kembali.');
                }

                $modelInspecting->no_lot = $modelHeader->no_lot;
                $modelInspecting->tanggal_inspeksi = $modelHeader->tgl_inspeksi;
                $modelInspecting->date = $modelHeader->tgl_kirim;
                $modelInspecting->unit = $modelHeader->status;

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if(!($flag = $modelInspecting->save(false))){
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal, coba lagi. (1)');
                    }

                    InspectingItem::deleteAll(['inspecting_id' => $model->id]);

                    foreach (Json::decode(Yii::$app->request->post('items')) as $item) {
                        $modelItem = new InspectingItem([
                            'inspecting_id' => $modelInspecting->id,
                            'grade' => $item['grade'],
                            'join_piece' => $item['join_piece'],
                            'qty' => $item['ukuran'],
                            'note' => $item['keterangan'],
                        ]);

                        if(!($flag = $modelItem->save(false))){
                            $transaction->rollBack();
                            throw new HttpException(500, 'Gagal, coba lagi. (2)');
                        }
                    }
                    if ($flag){
                        $transaction->commit();
                        return ['success'=>true, 'redirect'=>Url::to(['view', 'id'=>$modelInspecting->id])];

                    }
                }catch (\Throwable $t){
                    $transaction->rollBack();
                    throw $t;
                }
            }

            $result = [];
            // The code below comes from ActiveForm::validate(). We do not need to validate the model
            // again, as it was already validated by save(). Just collect the messages.
            foreach ($modelHeader->getErrors() as $attribute => $errors) {
                $result[Html::getInputId($modelHeader, $attribute)] = $errors;
            }

            return ['validation' => $result];
        }

        return $this->render('update', [
            'model' => $model,
            'modelHeader' => $modelHeader,
            'modelItem' => $modelItem,
            'nomorKartu' => $nomorKartu,
            'kombinasi' => $kombinasi,
            'items' => $items
        ]);
    }

    /**
     * Deletes an existing Inspecting model.
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
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk dihapus.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        InspectingItem::deleteAll(['inspecting_id'=>$model->id]);

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Inspecting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionPosting($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk diposting.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        //anggap saja sudah disetujui (bypass)
        $model->status = $model::STATUS_APPROVED;
        $model->approved_by = Yii::$app->user->id;
        $model->approved_at = time();
        $model->setNomor();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(!$flag = $model->save(false, ['status', 'approved_by', 'approved_at', 'no_urut', 'no'])){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Posting gagal, coba lagi.');
            }else{
                if($model->memo_repair_id !== null){
                    $modelMemoRepair = $model->memoRepair;
                    $modelMemoRepair->status = $modelMemoRepair::STATUS_INSPECTED;
                    if (!($flag = $modelMemoRepair->save(false, ['status']))) {
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('error', '2');
                    }
                }else{
                    switch ($model->jenis_process){
                        case TrnScGreige::PROCESS_DYEING:
                            $modelKartuProses = $model->kartuProcessDyeing;
                            $modelKartuProses->status = $modelKartuProses::STATUS_INSPECTED;
                            if (!($flag = $modelKartuProses->save(false, ['status']))) {
                                $transaction->rollBack();
                                Yii::$app->session->setFlash('error', '2');
                            }
                            break;
                        case TrnScGreige::PROCESS_PRINTING:
                            $modelKartuProses = $model->kartuProcessPrinting;
                            $modelKartuProses->status = $modelKartuProses::STATUS_INSPECTED;
                            if (!($flag = $modelKartuProses->save(false, ['status']))) {
                                $transaction->rollBack();
                                Yii::$app->session->setFlash('error', '2');
                            }
                            break;
                        default:
                            $transaction->rollBack();
                            Yii::$app->session->setFlash('error', 'Jenis proses tidak didukung.');
                    }
                }
            }

            if ($flag) {
                Yii::$app->session->setFlash('success', 'Posting berhasil.');
                $transaction->commit();
            }
        }catch (\Throwable $t) {
            $transaction->rollBack();
            throw $t;
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Lists all KartuProsesDyeing models.
     * @param $proccess
     * @param $id
     * @return mixed
     */
    public function actionReject($proccess, $id)
    {
        switch ($proccess){
            case 'printing':
                $model = TrnKartuProsesPrinting::findOne($id);
                $model->status = $model::STATUS_DELIVERED;
                $model->approved_at = null;
                $model->approved_by = null;
                $model->save(false, ['status', 'approved_at', 'approved_by']);
                Yii::$app->session->setFlash('success', 'Penolakan berhasil');
                return $this->redirect(['kartu-proses-printing']);
            case 'dyeing':
                $model = TrnKartuProsesDyeing::findOne($id);
                $model->status = $model::STATUS_DELIVERED;
                $model->approved_at = null;
                $model->approved_by = null;
                $model->save(false, ['status', 'approved_at', 'approved_by']);
                Yii::$app->session->setFlash('success', 'Penolakan berhasil');
                return $this->redirect(['kartu-proses-dyeing']);
            default:
                return $this->goBack();
        }
    }

    /**
     * Lists all KartuProsesDyeing models.
     * @return mixed
     */
    public function actionKartuProsesDyeing()
    {
        $searchModel = new TrnKartuProsesDyeingSearch(['status'=>TrnKartuProsesDyeing::STATUS_APPROVED]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('kartu-proses-dyeing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all KartuProsesDyeing models.
     * @return mixed
     */
    public function actionKartuProsesPrinting()
    {
        $searchModel = new TrnKartuProsesPrintingSearch(['status'=>TrnKartuProsesPrinting::STATUS_APPROVED]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('kartu-proses-printing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all KartuProsesDyeing models.
     * @return mixed
     */
    public function actionMemoRepair()
    {
        $searchModel = new TrnMemoRepairSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['trn_memo_repair.status'=>TrnMemoRepair::STATUS_REPAIRED]);

        return $this->render('memo-repair', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
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

            if($model->status != $model::STATUS_DRAFT){
                throw new ForbiddenHttpException('Status tidak valid, hanya status draft yang bisa diproses.');
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
            $model->mo_id = $wo->mo_id;
            $model->sc_greige_id = $wo->sc_greige_id;
            $model->sc_id = $wo->sc_id;
            $model->kombinasi = $wo->trnWoColors[0]->moColor->color;
            $model->save(false, ['wo_id', 'mo_id', 'sc_greige_id', 'sc_id', 'kombinasi']);

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

            if($model->status != $model::STATUS_DRAFT){
                throw new ForbiddenHttpException('Status tidak valid, hanya status draft yang bisa diproses.');
            }

            $post = Yii::$app->request->post('data');
            if(empty($post)){
                throw new ForbiddenHttpException('Keterangan kosong, tidak bisa diproses.');
            }

            $modelKp = null;
            if($model->kartuProcessPrinting !== null){
                $modelKp = $model->kartuProcessPrinting;
            }elseif($model->kartuProcessDyeing !== null){
                $modelKp = $model->kartuProcessDyeing;
            }

            if($modelKp === null){
                throw new InvalidArgumentException('Inspecting ini tidak terelasi dengan kartu proses manapun.');
            }

            $modelKp['wo_color_id'] = $post;
            $modelKp->save(false, ['wo_color_id']);

            $model->kombinasi = $modelKp->woColor->moColor->color;
            $model->save(false, ['kombinasi']);

            return true;
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Mpdf\MpdfException
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionPrint($id)
    {
        $model = $this->findModel($id);
        $content = $this->renderPartial('print', ['model' => $model]);

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_BLANK,
            // A4 paper format
            //'format' => Pdf::FORMAT_A4,
            'format' =>[210,148], //A5 210mm x 148mm
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '
                body{font-family: sans-serif; font-size:10px; letter-spacing: 0px;}
                table {font-family: sans-serif; width: 100%; font-size:10px; border-spacing: 0; letter-spacing: 0px;} th, td {padding: 0.1em 0em; vertical-align: top;}
                table.bordered th, table.bordered td, td.bordered, th.bordered {border: 0.1px solid black; padding: 0.1em 0.1em; vertical-align: middle;}
             ',
            // set mPDF properties on the fly
            //'options' => ['title' => 'Sales Contract - '.$model->id],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHTMLHeader'=>$this->renderPartial('print/header', ['model' => $model, 'config'=>$config]),
                'SetTitle'=>'Inspecting',
            ],
            'options' => [
                'setAutoTopMargin' => 'stretch'
            ],
            // your html content input
            'content' => $content,
        ]);

        if($model->status == $model::STATUS_DRAFT){
            $pdf->methods['SetHeader'] = 'Inspecting | ID:'.$model->id.' | DRAFT';
        }else{
            if($model->status == $model::STATUS_APPROVED){
                $pdf->methods['SetHeader'] = 'Inspecting - | ID:'.$model->id.' | NO:'.$model->no;
            }else $pdf->methods['SetHeader'] = 'Inspecting - | ID:'.$model->id.' | MENUNGGU PERSETUJUAN';
        }

        $pdf->methods['SetFooter'] = 'Page {PAGENO}';

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    public function actionAnalisaPengirimanProduksi(){
        $searchModel = new AnalisaPengirimanProduksi();
        $data = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('analisa-pengiriman-produksi', [
            'searchModel' => $searchModel,
            'data' => $data,
        ]);
    }

    /**
     * Finds the TrnInspecting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnInspecting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnInspecting::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
