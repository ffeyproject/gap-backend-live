<?php

namespace backend\controllers;

use backend\models\form\InspectingHeaderForm;
use backend\models\form\InspectingItemsForm;
use backend\models\search\AnalisaPengirimanProduksi;
use common\models\ar\DefectInspectingItem;
use common\models\ar\InspectingItem;
// use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnInspecting;
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnKartuProsesDyeingSearch;
use common\models\ar\TrnKartuProsesPrinting;
use common\models\ar\TrnKartuProsesPrintingSearch;
use common\models\ar\TrnMemoRepair;
use common\models\ar\TrnMemoRepairSearch;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnWo;
use common\models\ar\MstGreigeGroup;
use kartik\mpdf\Pdf;
use Yii;
use common\models\ar\TrnInspectingSearch;
use yii\db\Query;
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
// use yii\helpers\BaseVarDumper;

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
                        $modelInspecting->k3l_code = $modelHeader->k3l_code;
                        $modelInspecting->defect = $modelHeader->defect;
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
                        $modelInspecting->k3l_code = $modelHeader->k3l_code;
                        $modelInspecting->defect = $modelHeader->defect;
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
                        $modelInspecting->k3l_code = $modelHeader->k3l_code;
                        $modelInspecting->defect = $modelHeader->defect;
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
                $modelInspecting->jenis_inspek = $modelHeader->jenis_inspek;

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
                            'defect' => $item['defect'],
                            'lot_no' => $item['lot_no'],
                            'join_piece' => $item['join_piece'],
                            'qty' => $item['ukuran'],
                            'note' => $item['keterangan'],
                        ]);

                        if(!($flag = $modelItem->save(false))){
                            $transaction->rollBack();
                            throw new HttpException(500, 'Gagal, coba lagi. (2)');
                        }
                    }
                    $query = InspectingItem::find();
                    $getItemBasedOnInspectingId = $query->where(['=', 'inspecting_id', $modelInspecting->id])->all();
                    foreach ($getItemBasedOnInspectingId as $gIBOII) {
                        $qty_sum = $query->where(['=', 'join_piece', $gIBOII->join_piece])->andWhere(['=', 'inspecting_id', $modelInspecting->id])->sum('qty');
                        $qty_count = $query->where(['=', 'join_piece', $gIBOII->join_piece])->andWhere(['=', 'inspecting_id', $modelInspecting->id])->count();
                        $is_head = $query->orderBy('is_head DESC')
                                        ->where(['=', 'join_piece', $gIBOII->join_piece])
                                        ->andWhere(['=', 'inspecting_id', $modelInspecting->id])
                                        ->andWhere(['<>', 'join_piece', ""])->one();
                        $gIBOII['qty_sum'] = ($is_head && ($is_head['id'] <> $gIBOII['id'])) ? NULL : ($gIBOII['join_piece'] == NULL || $gIBOII['join_piece'] == "" ? $gIBOII['qty'] : $qty_sum);
                        $gIBOII['qr_code'] = 'INS-'.$gIBOII['inspecting_id'].'-'.$gIBOII['id'];
                        $gIBOII['is_head'] = ($is_head && ($is_head['id'] <> $gIBOII['id'])) ? 0 : 1;
                        $gIBOII['qty_count'] = ($is_head && ($is_head['id'] <> $gIBOII['id'])) ? 0 : ($gIBOII['join_piece'] == NULL || $gIBOII['join_piece'] == "" ? 1 : $qty_count);
                        $gIBOII->save();
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

    public function actionUpgrade($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk diupgrade.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        Yii::$app->session->setFlash('info',
            'Mencari kartu proses yang berstatus approved (sudah diterima oleh bagian penerimaan).'
        );

        $modelHeader = new InspectingHeaderForm([
            'tgl_kirim' => $model->date,
            'tgl_inspeksi' => $model->tanggal_inspeksi,
            'no_lot' => $model->no_lot,
            'defect' => $model->defect,
            'status' => $model->unit,
        ]);

        $nomorKartu = '';
        $k3l_code = '';
        $kombinasi = '-';

        if($model->memo_repair_id !== null){
            $modelHeader->kartu_proses_id = $model->memo_repair_id;
            $nomorKartu = $model->memoRepair->no;
            $k3l_code = $model->k3l_code;
            $modelHeader->jenis_order = 'memo_repair';
        }else{
            switch ($model->scGreige->process){
                case TrnScGreige::PROCESS_DYEING:
                    $modelHeader->kartu_proses_id = $model->kartu_process_dyeing_id;
                    $nomorKartu = $model->kartuProcessDyeing->no;
                    $k3l_code = $model->k3l_code;
                    $modelHeader->jenis_order = 'dyeing';
                    $kombinasi = $model->kartuProcessDyeing->woColor->moColor->color;
                    break;
                case TrnScGreige::PROCESS_PRINTING:
                    $modelHeader->kartu_proses_id = $model->kartu_process_printing_id;
                    $nomorKartu = $model->kartuProcessPrinting->no;
                    $k3l_code = $model->k3l_code;
                    $modelHeader->jenis_order = 'printing';
                    $kombinasi = $model->kartuProcessPrinting->woColor->moColor->color;
                    break;
            }
        }

        $modelItem = new InspectingItemsForm();

        $items = [];
        $inspectingId = $model->id;
        $hasNoUrut = \common\models\ar\InspectingItem::find()
        ->where(['inspecting_id' => $inspectingId])
        ->andWhere(['IS NOT', 'no_urut', null])
        ->exists();

        $inspectItems = $model->getInspectingItems()
        ->orderBy($hasNoUrut ? 'no_urut ASC' : 'id ASC')
        ->all();
        foreach ($inspectItems as $inspectingItem) {
            $items[] = [
                'id' => $inspectingItem->id,
                'grade' => $inspectingItem->grade,
                'gradeLabel' => $inspectingItem::gradeOptions()[$inspectingItem->grade],
                'grade_up' => $inspectingItem->grade_up,
                'gradeupLabel' => $inspectingItem->grade_up ? $inspectingItem::gradeOptions()[$inspectingItem->grade_up] : '',
                'defect' => $inspectingItem->defect,
                'lot_no' => $inspectingItem->lot_no,
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
                        $modelInspecting->k3l_code = $modelHeader->k3l_code;
                        $modelInspecting->defect = $modelHeader->defect;
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
                        $modelInspecting->k3l_code = $modelHeader->k3l_code;
                        $modelInspecting->defect = $modelHeader->defect;
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
                        $modelInspecting->k3l_code = $modelHeader->k3l_code;
                        $modelInspecting->defect = $modelHeader->defect;
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

                    foreach (Json::decode(Yii::$app->request->post('items')) as $item) {
                        $query_one = $this->findItem($item['id']);
                        $query_one['grade'] = $item['grade'] ? $item['grade'] : $query_one->grade;
                        $query_one['grade_up'] = $item['grade_up'] ? $item['grade_up'] : $query_one->grade_up;
                        $query_one['join_piece'] = $item['join_piece'] ? $item['join_piece'] : $query_one->join_piece;
                        $query_one['defect'] = $item['defect'] ? $item['defect'] : $query_one->defect;
                        $query_one['lot_no'] = $item['lot_no'] ? $item['lot_no'] : $query_one->lot_no;
                        $query_one['qty'] = $item['ukuran'] ? $item['ukuran'] : $query_one->ukuran;
                        $query_one['note'] = $item['keterangan'] ? $item['keterangan'] : $query_one->note;
                        $query_one->save();
                    }

                    $query = InspectingItem::find();
                    $getItemBasedOnInspectingId = $query->where(['=', 'inspecting_id', $modelInspecting->id])->all();
                    foreach ($getItemBasedOnInspectingId as $gIBOII) {
                        $qty_sum = $query->where(['=', 'join_piece', $gIBOII->join_piece])->andWhere(['=', 'inspecting_id', $modelInspecting->id])->sum('qty');
                        $qty_count = $query->where(['=', 'join_piece', $gIBOII->join_piece])->andWhere(['=', 'inspecting_id', $modelInspecting->id])->count();
                        $is_head = $query->orderBy('is_head DESC')
                                        ->where(['=', 'join_piece', $gIBOII->join_piece])
                                        ->andWhere(['=', 'inspecting_id', $modelInspecting->id])
                                        ->andWhere(['<>', 'join_piece', ""])->one();
                        $gIBOII['qty_sum'] = ($is_head && ($is_head['id'] <> $gIBOII['id'])) ? NULL : ($gIBOII['join_piece'] == NULL || $gIBOII['join_piece'] == "" ? $gIBOII['qty'] : $qty_sum);
                        $gIBOII['qr_code'] = 'INS-'.$gIBOII['inspecting_id'].'-'.$gIBOII['id'];
                        $gIBOII['is_head'] = ($is_head && ($is_head['id'] <> $gIBOII['id'])) ? 0 : 1;
                        $gIBOII['qty_count'] = ($is_head && ($is_head['id'] <> $gIBOII['id'])) ? 0 : ($gIBOII['join_piece'] == NULL || $gIBOII['join_piece'] == "" ? 1 : $qty_count);
                        $gIBOII->save();
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

        return $this->render('upgrade', [
            'model' => $model,
            'modelHeader' => $modelHeader,
            'modelItem' => $modelItem,
            'nomorKartu' => $nomorKartu,
            'kombinasi' => $kombinasi,
            'k3l_code' => $k3l_code,
            'items' => $items
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
    public function actionUpdate($id)
    {
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
            'defect' => $model->defect,
            'status' => $model->unit,
            'jenis_inspek' => $model->jenis_inspek,
        ]);

        $nomorKartu = '';
        $k3l_code = '';
        $kombinasi = '-';
        $jenis_inspek = '';

        if($model->memo_repair_id !== null){
            $modelHeader->kartu_proses_id = $model->memo_repair_id;
            $nomorKartu = $model->memoRepair->no;
            $k3l_code = $model->k3l_code;
            $jenis_inspek = $model->jenis_inspek;
            $modelHeader->jenis_order = 'memo_repair';
        }else{
            switch ($model->scGreige->process){
                case TrnScGreige::PROCESS_DYEING:
                    $modelHeader->kartu_proses_id = $model->kartu_process_dyeing_id;
                    $nomorKartu = $model->kartuProcessDyeing->no;
                    $k3l_code = $model->k3l_code;
                    $modelHeader->jenis_order = 'dyeing';
                    $kombinasi = $model->kartuProcessDyeing->woColor->moColor->color;
                     
                    break;
                case TrnScGreige::PROCESS_PRINTING:
                    $modelHeader->kartu_proses_id = $model->kartu_process_printing_id;
                    $nomorKartu = $model->kartuProcessPrinting->no;
                    $k3l_code = $model->k3l_code;
                    $modelHeader->jenis_order = 'printing';
                    $kombinasi = $model->kartuProcessPrinting->woColor->moColor->color;
                     
                    break;
            }
        }

        $modelItem = new InspectingItemsForm();

        $items = [];
        $inspectingId = $model->id;
        $hasNoUrut = \common\models\ar\InspectingItem::find()
        ->where(['inspecting_id' => $inspectingId])
        ->andWhere(['IS NOT', 'no_urut', null])
        ->exists();

        $inspectItems = $model->getInspectingItems()
        ->orderBy($hasNoUrut ? 'no_urut ASC' : 'id ASC')
        ->all();
        foreach ($inspectItems as $inspectingItem) {
            $items[] = [
                'item_id' => $inspectingItem->id,
                'grade' => $inspectingItem->grade,
                'gradeLabel' => $inspectingItem::gradeOptions()[$inspectingItem->grade],
                'defect' => $inspectingItem->defect,
                'lot_no' => $inspectingItem->lot_no,
                'ukuran' => $inspectingItem->qty,
                'join_piece' => $inspectingItem->join_piece,
                'keterangan' => $inspectingItem->note,
                'qr_code' => $inspectingItem->qr_code
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
                        $modelInspecting->k3l_code = $modelHeader->k3l_code;
                        $modelInspecting->defect = $modelHeader->defect;
                        $modelInspecting->wo_id = $kp->wo_id;
                        $modelInspecting->mo_id = $kp->mo_id;
                        $modelInspecting->sc_greige_id = $kp->sc_greige_id;
                        $modelInspecting->sc_id = $kp->sc_id;
                        $modelInspecting->kombinasi = $kp->woColor->moColor->color;
                         $modelInspecting->jenis_inspek = $modelHeader->jenis_inspek;
                        break;
                    case 'printing':
                        $kp = TrnKartuProsesPrinting::findOne($modelHeader->kartu_proses_id);
                        if($kp === null){
                            throw new ForbiddenHttpException('ID Proses tidak valid');
                        }

                        $modelInspecting->kartu_process_printing_id = $modelHeader->kartu_proses_id;
                        $modelInspecting->jenis_process = TrnScGreige::PROCESS_PRINTING;
                        $modelInspecting->k3l_code = $modelHeader->k3l_code;
                        $modelInspecting->defect = $modelHeader->defect;
                        $modelInspecting->wo_id = $kp->wo_id;
                        $modelInspecting->mo_id = $kp->mo_id;
                        $modelInspecting->sc_greige_id = $kp->sc_greige_id;
                        $modelInspecting->sc_id = $kp->sc_id;
                        $modelInspecting->kombinasi = $kp->woColor->moColor->color;
                         $modelInspecting->jenis_inspek = $modelHeader->jenis_inspek;
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
                        $modelInspecting->k3l_code = $modelHeader->k3l_code;
                        $modelInspecting->defect = $modelHeader->defect;
                        $modelInspecting->wo_id = $modelMemoRepair->wo_id;
                        $modelInspecting->mo_id = $modelMemoRepair->mo_id;
                        $modelInspecting->sc_greige_id = $modelMemoRepair->sc_greige_id;
                        $modelInspecting->sc_id = $modelMemoRepair->sc_id;
                        $modelInspecting->kombinasi = '-';
                        $modelInspecting->jenis_inspek = $modelHeader->jenis_inspek;
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
                $modelInspecting->jenis_inspek = $modelHeader->jenis_inspek;

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if(!($flag = $modelInspecting->save(false))){
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal, coba lagi. (1)');
                    }

                    $dI = Json::decode(Yii::$app->request->post('deletedItems'));
                    if(count($dI) > 0) {
                        for ($i=0; $i < count($dI); $i++) { 
                            if ($dI[$i] <> 0) {
                                Yii::$app->db->createCommand()->delete(InspectingItem::tableName(), ['=', 'id', $dI[$i]])->execute();
                            }
                        }
                    }

                    foreach (Json::decode(Yii::$app->request->post('items')) as $item) {
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            if ($item['item_id'] == 0) {
                                $modelItem = new InspectingItem([
                                    'inspecting_id' => $modelInspecting->id,
                                    'grade' => $item['grade'],
                                    'join_piece' => $item['join_piece'],
                                    'defect' => $item['defect'],
                                    'lot_no' => $item['lot_no'],
                                    'qty' => $item['ukuran'],
                                    'note' => $item['keterangan'],
                                ]);
                                $modelItem->save();
                                $transaction->commit();
                            } else {
                                $query = $this->findItem($item['item_id']);
                                    $query['inspecting_id'] = $modelInspecting->id;
                                    $query['grade'] = $item['grade'];
                                    $query['defect'] = $item['defect'];
                                    $query['lot_no'] = $item['lot_no'];
                                    $query['join_piece'] = $item['join_piece'];
                                    $query['qty'] = $item['ukuran'];
                                    $query['qty_sum'] = $query->is_head == 1 ? $item['ukuran'] : NULL;
                                    $query['note'] = $item['keterangan'];
                                    $query['qr_code_desc'] = $query->qr_code_desc;
                                    $query['qr_print_at'] = $query->qr_print_at;
                                    $query->save();
                                    $transaction->commit();
                            }

                        } catch (\Throwable $t){
                            $transaction->rollBack();
                            throw $t;
                        }
                    }

                    $query2 = InspectingItem::find();
                    $getItemBasedOnInspectingId = $query2->where(['=', 'inspecting_id', $modelInspecting->id])->all();
                    foreach ($getItemBasedOnInspectingId as $gIBOII) {
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            $qty_count = $query2->where(['=', 'join_piece', $gIBOII->join_piece])->andWhere(['=', 'inspecting_id', $modelInspecting->id])->count();
                            $qty_sum = $query2->where(['=', 'join_piece', $gIBOII->join_piece])->andWhere(['=', 'inspecting_id', $modelInspecting->id])->sum('qty');
                            $is_head = $query2->orderBy('is_head DESC')->orderBy('id ASC')
                                            ->where(['=', 'join_piece', $gIBOII->join_piece])
                                            ->andWhere(['=', 'inspecting_id', $modelInspecting->id])
                                            ->andWhere(['<>', 'join_piece', ""])->one();
                            $gIBOII['qty_sum'] = ($is_head && ($is_head['id'] <> $gIBOII['id'])) ? NULL : ($gIBOII['join_piece'] == NULL || $gIBOII['join_piece'] == "" ? $gIBOII['qty'] : $qty_sum);
                            $gIBOII['is_head'] = ($is_head && ($is_head['id'] <> $gIBOII['id'])) ? 0 : 1;
                            // $gIBOII['qr_code'] = ($gIBOII['qr_code'] <> NULL || $gIBOII['qr_code']) ? $gIBOII['qr_code'] : 'INS-'.$gIBOII['inspecting_id'].'-'.$gIBOII['id'];
                            $gIBOII['qr_code'] = !empty($gIBOII['qr_code']) ? $gIBOII['qr_code'] : 'INS-'.$gIBOII['inspecting_id'].'-'.$gIBOII['id'];
                            $gIBOII['qty_count'] = ($is_head && ($is_head['id'] <> $gIBOII['id'])) ? 0 : ($gIBOII['join_piece'] == NULL || $gIBOII['join_piece'] == "" ? 1 : $qty_count);
                            $gIBOII->save();
                            $transaction->commit();
                        }catch (\Throwable $t){
                            $transaction->rollBack();
                            throw $t;
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
            'k3l_code' => $k3l_code,
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

            // $inspectingItems = $model->getInspectingItems()->orderBy('id ASC')->all();
            // foreach ($inspectingItems as $iI) {
            //     $stock = $this->findItemInStock($iI->id);
            //     $stock->trans_from = 'INS';
            //     $stock->id_from = $iI->inspecting_id;
            //     $stock->qr_code = $iI->qr_code;
            //     $stock->qr_code_desc = $iI->qr_code_desc;
            //     $stock->save();
            // }

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
        $searchModel = new TrnKartuProsesDyeingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['IN', 'trn_kartu_proses_dyeing.status', [
            TrnKartuProsesDyeing::STATUS_APPROVED,
            TrnKartuProsesDyeing::STATUS_INSPECTED,
        ]]);
        
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

    // bagussona
    // public function actionQr($id, $param3, $param4, $param5)
    // {
    //     $model = $this->findItem($id);
    //     $create_qr =  'INS-'.$model->inspecting_id.'-'.$model->id;

    //     $is_design_or_article = NULL;
    //     if ($model->inspecting->jenis_process == 1) { //1 == dyeing
    //         $is_design_or_article = $model->inspecting->wo->mo->article;
    //     } else { //2 == printing && //3 == pfp
    //         $articleIsNotNull = $model->inspecting->wo->mo->article ? '/' : '';
    //         $is_design_or_article = $model->inspecting->wo->mo->article.$articleIsNotNull.$model->inspecting->wo->mo->design;
    //     }

    //     $getWidth = $model->inspecting->wo && $model->inspecting->wo->scGreige && $model->inspecting->wo->scGreige->lebar_kain ? TrnScGreige::lebarKainOptions()[$model->inspecting->wo->scGreige->lebar_kain] : '-';
    //     $query_builder = new Query;
    //     $grade_alias = $query_builder->select('*')->from('wms_grade')->where(['=', 'grade_from', ($model->grade_up <> NULL ? $model->grade_up : $model->grade)])->one();
    //     // $getGrade = $model->gradeOptions()[($model->grade_up <> NULL ? $model->grade_up : $model->grade)];
    //     $getGrade = $grade_alias['grade_to'];
    //     $roundUp = true;
    //     if($param5 == 0){
    //         $roundUp = false;
    //     }
    //     if($roundUp){
    //         $getMeter = round($model->qty_sum * 0.9144,1);
    //     }else{
    //         $getMeter = round($model->qty_sum * 0.9144,2);

    //     }

    //     $inspectingItemCount = [];
    //     $query = InspectingItem::find();
    //     $getItemBasedOnInspectingId = $query->where(['=', 'inspecting_id', $model->inspecting_id])->orderBy('id ASC')->all();
    //     foreach ($getItemBasedOnInspectingId as $gIBOII) {
    //         array_push($inspectingItemCount, $gIBOII->id);
    //     }
    //     $key = array_search ($model->id, $inspectingItemCount);
    //     $data = [];
    //     $data['qr_code'] = $model->qr_code ? $model->qr_code : $create_qr;
    //     $data['no_wo'] = $model->inspecting && $model->inspecting->wo && $model->inspecting->wo->no ? $model->inspecting->wo->no : '-';
    //     $data['k3l_code'] = $model->inspecting && $model->inspecting->k3l_code ? $model->inspecting->k3l_code : '-';
    //     $data['color'] = $model->inspecting && $model->inspecting->kombinasi ? $model->inspecting->kombinasi : '-';
    //     $data['is_design_or_artikel'] = $is_design_or_article ? $is_design_or_article : '-';
    //     $data['length'] = str_replace(' ', '', $model->qty_sum.' '.($model->inspecting->unit == 1 ? 'YDS / '.$getMeter.' M' : ($model->inspecting->unit == 2 ? 'M' : ($model->inspecting->unit == 3 ? 'PCS' : 'KG'))));
    //     $data['no_lot'] = $model->inspecting && $model->inspecting->no_lot ? $model->inspecting->no_lot.'/'.($key+1) : '-';
    //     $data['qty_count'] = strlen($model->qty_count) == 1 ? '00' : (strlen($model->qty_count) == 2 ? '0' : '').$model->qty_count;
    //     $data['grade'] = $getWidth.'"/'.$getGrade;
    //     // $data['motif_greige'] = $model->inspecting->wo->mo->scGreige->greigeGroup->nama_kain;
    //     $data['defect'] = str_replace(',', '|', $model->defect);
    //     $data['param3'] = $param3;
    //     $data['param4'] = $param4;

    //     $production = $param3 == 1 ? 'MADE IN INDONESIA' : '';
    //     $regisk3l = $param4 == 1 ? 'REGISTRASI K3L!'.$data['k3l_code'] : '';

    //     $qr_code_desc = $regisk3l.
    //                     '!'.$data['no_wo'].
    //                     '!'.$data['is_design_or_artikel'].
    //                     '!'.$data['color'].
    //                     '!'.$data['no_lot'].
    //                     '!'.$data['length'].
    //                     '!'.$data['grade'].
    //                     // '!'.$data['motif_greige'].
    //                     '!'.$data['defect'].
    //                     '!'.$production;
    //     $data['qr_code_desc'] = $model->qr_code_desc ? ($qr_code_desc == $model->qr_code_desc ? $model->qr_code_desc : $qr_code_desc) : $qr_code_desc;

    //     $transaction = Yii::$app->db->beginTransaction();
    //     try {
    //         $query = $this->findItem($id);
    //         $query['qr_code'] = $query->qr_code ? $query->qr_code : $create_qr;
    //         $query['qr_code_desc'] = $query->qr_code_desc ? ($qr_code_desc == $query->qr_code_desc ? $query->qr_code_desc : $qr_code_desc) : $qr_code_desc;
    //         $query['qr_print_at'] = $query->qr_print_at ? $query->qr_print_at : date('Y-m-d H:i:s');
    //         $query->save();
    //         $transaction->commit();
    //     }catch (\Throwable $t){
    //         $transaction->rollBack();
    //         throw $t;
    //     }

    //     $content = $this->renderPartial('qr', ['model' => $data]);
    //     // setup kartik\mpdf\Pdf component
    //     $pdf = new Pdf([
    //         'mode' => Pdf::MODE_BLANK,
    //         'format' => [100,50], //THERMAL 100mm x 50mm
    //         'orientation' => Pdf::ORIENT_PORTRAIT,
    //         'destination' => Pdf::DEST_BROWSER,
    //         'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
    //         'cssInline' => '
    //             table {
    //                 width: 100%;
    //                 font-size:10px;
    //                 border-spacing: 0;
    //                 letter-spacing: 0px;
    //             } 
    //             th, td {
    //                 padding: 0.1em 0em;
    //                 vertical-align: top;
    //             }
    //             table.bordered th, table.bordered td, td.bordered, th.bordered {
    //                 border: 0.1px solid black;
    //                 padding: 0.1em 0.1em;
    //                 vertical-align: middle;
    //             }
    //         ',
    //         'methods' => [
    //             'SetTitle'=>$data['qr_code'],
    //         ],
    //         // 'options' => [
    //         //     'setAutoTopMargin' => 'stretch'
    //         // ],
    //         // 'marginHeader' => 0,
    //         // 'marginFooter' => 0,
    //         'marginTop' => 0,
    //         'marginRight' => 0,
    //         'marginBottom' => 0,
    //         'marginLeft' => 0,
    //         'content' => $content,
    //     ]);

    //     return $pdf->render();
    // }

    public function actionQr($id, $param3, $param4, $param5)
    {
        $model = $this->findItem($id);
        $create_qr = 'INS-' . $model->inspecting_id . '-' . $model->id;

        // Penentuan design / article
        $is_design_or_article = null;
        if ($model->inspecting->jenis_process == 1) {
            $is_design_or_article = $model->inspecting->wo->mo->article;
        } else {
            $articleIsNotNull = $model->inspecting->wo->mo->article ? '/' : '';
            $is_design_or_article = $model->inspecting->wo->mo->article . $articleIsNotNull . $model->inspecting->wo->mo->design;
        }

        // Ambil lebar kain
        $getWidth = ($model->inspecting->wo && $model->inspecting->wo->scGreige && $model->inspecting->wo->scGreige->lebar_kain)
            ? TrnScGreige::lebarKainOptions()[$model->inspecting->wo->scGreige->lebar_kain]
            : '-';

        // Ambil grade
        $query_builder = new \yii\db\Query;
        $grade_alias = $query_builder->select('*')
            ->from('wms_grade')
            ->where(['=', 'grade_from', ($model->grade_up !== null ? $model->grade_up : $model->grade)])
            ->one();
        $getGrade = $grade_alias['grade_to'] ?? '-';

        // Konversi meter
        $roundUp = $param5 != 0;
        $getMeter = round($model->qty_sum * 0.9144, $roundUp ? 1 : 2);

        // === CEK urutan: pakai no_urut jika tersedia ===
        $inspectingId = $model->inspecting_id;
        $query = InspectingItem::find()->where(['inspecting_id' => $inspectingId]);

        $hasNoUrut = clone $query;
        $hasNoUrut = $hasNoUrut->andWhere(['IS NOT', 'no_urut', null])->exists();

        $items = $query
            ->orderBy($hasNoUrut ? 'no_urut ASC' : 'id ASC')
            ->all();

        // Cari posisi (index) dari current item
        $itemIds = [];
        foreach ($items as $item) {
            $itemIds[] = $item->id;
        }
        $key = array_search($model->id, $itemIds);

        // === SIAPKAN DATA QR ===
        $data = [];
        $data['qr_code'] = $model->qr_code ?: $create_qr;
        $data['no_wo'] = $model->inspecting->wo->no ?? '-';
        $data['k3l_code'] = $model->inspecting->k3l_code ?? '-';
        $data['color'] = $model->inspecting->kombinasi ?? '-';
        $data['is_design_or_artikel'] = $is_design_or_article ?: '-';
        $data['length'] = str_replace(' ', '', $model->qty_sum . ' ' . (
            $model->inspecting->unit == 1 ? 'YDS / ' . $getMeter . ' M' :
            ($model->inspecting->unit == 2 ? 'M' :
            ($model->inspecting->unit == 3 ? 'PCS' : 'KG'))
        ));
        $data['no_lot'] = $model->inspecting->no_lot ? $model->inspecting->no_lot . '/' . ($key + 1) : '-';
        $data['qty_count'] = strlen($model->qty_count) == 1 ? '00' : (strlen($model->qty_count) == 2 ? '0' : '') . $model->qty_count;
        $data['grade'] = $getWidth . '"/' . $getGrade;
        $data['defect'] = str_replace(',', '|', $model->defect);
        $data['param3'] = $param3;
        $data['param4'] = $param4;

        // Tambahan teks
        $production = $param3 == 1 ? 'MADE IN INDONESIA' : '';
        $regisk3l = $param4 == 1 ? 'REGISTRASI K3L!' . $data['k3l_code'] : '';

        // Final QR description
        $qr_code_desc = $regisk3l .
            '!' . $data['no_wo'] .
            '!' . $data['is_design_or_artikel'] .
            '!' . $data['color'] .
            '!' . $data['no_lot'] .
            '!' . $data['length'] .
            '!' . $data['grade'] .
            '!' . $data['defect'] .
            '!' . $production;

        $data['qr_code_desc'] = $model->qr_code_desc && $model->qr_code_desc == $qr_code_desc
            ? $model->qr_code_desc
            : $qr_code_desc;

        // === SIMPAN DATA QR KE DATABASE ===
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->qr_code = $model->qr_code ?: $create_qr;
            $model->qr_code_desc = $model->qr_code_desc == $qr_code_desc ? $model->qr_code_desc : $qr_code_desc;
            $model->qr_print_at = $model->qr_print_at ?: date('Y-m-d H:i:s');
            $model->save();
            $transaction->commit();
        } catch (\Throwable $t) {
            $transaction->rollBack();
            throw $t;
        }

        // === CETAK PDF ===
        $content = $this->renderPartial('qr', ['model' => $data]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_BLANK,
            'format' => [100, 50],
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '
                table {
                    width: 100%;
                    font-size:10px;
                    border-spacing: 0;
                    letter-spacing: 0px;
                } 
                th, td {
                    padding: 0.1em 0em;
                    vertical-align: top;
                }
                table.bordered th, table.bordered td, td.bordered, th.bordered {
                    border: 0.1px solid black;
                    padding: 0.1em 0.1em;
                    vertical-align: middle;
                }
            ',
            'methods' => [
                'SetTitle' => $data['qr_code'],
            ],
            'marginTop' => 0,
            'marginRight' => 0,
            'marginBottom' => 0,
            'marginLeft' => 0,
            'content' => $content,
        ]);

        return $pdf->render();
    }


    public function actionQrAllWithoutAttribute($id, $param1, $param2)
    {
        $model = $this->findModel($id);
        $data = [];

        // CEK apakah ada no_urut
        $inspectingId = $model->id;
        $hasNoUrut = \common\models\ar\InspectingItem::find()
            ->where(['inspecting_id' => $inspectingId])
            ->andWhere(['IS NOT', 'no_urut', null])
            ->exists();

        // Ambil item dengan urutan no_urut atau id
        $items = $model->getInspectingItems()
            ->orderBy($hasNoUrut ? 'no_urut ASC' : 'id ASC')
            ->all();
        
         foreach ($items as $key => $iI) {
            $getMeter = round($iI->qty_sum * 0.9144, 1);
            $create_qr = 'INS-'.$iI->inspecting->id.'-'.$iI->id;
            if ($iI->is_head == 1) {
                $countKey = strlen($iI->qty_count) == 1 ? '00' : (strlen($iI->qty_count) == 2 ? '0' : '');
                $countItems = strlen($iI->qty_count) == 1 ? '00' : (strlen($iI->qty_count) == 2 ? '0' : '');

                $is_design_or_article = NULL;
                if ($iI->inspecting->jenis_process == 1) { //1 == dyeing
                    $is_design_or_article = $iI->inspecting->wo->mo->article;
                } else { //2 == printing && //3 == pfp
                    $articleIsNotNull = $iI->inspecting->wo->mo->article ? '/' : '';
                    $is_design_or_article = $iI->inspecting->wo->mo->article.$articleIsNotNull.$iI->inspecting->wo->mo->design;
                }

                $getWidth = $iI->inspecting->wo && $iI->inspecting->wo->scGreige && $iI->inspecting->wo->scGreige->lebar_kain ? TrnScGreige::lebarKainOptions()[$iI->inspecting->wo->scGreige->lebar_kain] : '-';
                $query_builder = new Query;
                $grade_alias = $query_builder->select('*')->from('wms_grade')->where(['=', 'grade_from', ($iI->grade_up <> NULL ? $iI->grade_up : $iI->grade)])->one();
                // $getGrade = $iI->gradeOptions()[($iI->grade_up <> NULL ? $iI->grade_up : $iI->grade)];
                $getGrade = $grade_alias['grade_to'];

                $no_wo = $iI->inspecting && $iI->inspecting->wo && $iI->inspecting->wo->no ? $iI->inspecting->wo->no : '-';
                $color = $iI->inspecting && $iI->inspecting->kombinasi ? $iI->inspecting->kombinasi : '-';
                $k3l_code = $iI->inspecting && $iI->inspecting->k3l_code ? $iI->inspecting->k3l_code : '-';
                $length = str_replace(' ', '', $iI->qty_sum.' '.($iI->inspecting->unit == 1 ? 'YDS / '.$getMeter.' M' : ($iI->inspecting->unit == 2 ? 'M' : ($iI->inspecting->unit == 3 ? 'PCS' : 'KG'))));
                $no_lot = $iI->inspecting && $iI->inspecting->no_lot ? $iI->inspecting->no_lot.'/'.($key+1) : '-';
                $qty_count = $countKey.($key+1).'/'.$countItems.count($model->inspectingItems);
                $grade = $getWidth.'"/'.$getGrade;
                // $motif_greige = $iI->inspecting->wo->mo->scGreige->greigeGroup->nama_kain;
                $defect = str_replace(',', '|', $iI->defect);

                $production = $param1 == 1 ? 'MADE IN INDONESIA' : '';
                $regisk3l = $param2 == 1 ? 'REGISTRASI K3L!'.$k3l_code : '';

                $qr_code_desc = $regisk3l.
                                '!'.$no_wo.
                                '!'.$is_design_or_article.
                                '!'.$color.
                                '!'.$no_lot.
                                '!'.$length.
                                '!'.$grade.
                                // '!'.$motif_greige.
                                '!'.$defect;
                                '!'.$production;

                $data[] = [
                    'param1' => $param1,
                    'param2' => $param2,
                    'qr_code' => $iI->qr_code ? $iI->qr_code : $create_qr,
                    'no_wo' => $no_wo,
                    'color' => $color,
                    'k3l_code' => $k3l_code,
                    'is_design_or_artikel' => $is_design_or_article ? $is_design_or_article : '-',
                    'length' => $length,
                    'no_lot' => $no_lot,
                    'qty_count' => $qty_count,
                    'grade' => $grade,
                    'qr_code_desc' => $iI->qr_code_desc ? ($iI->qr_code_desc == $qr_code_desc ? $iI->qr_code_desc : $qr_code_desc) : $qr_code_desc,
                ];

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $iI->qr_code = $iI->qr_code ? ($iI->qr_code == $create_qr ? $iI->qr_code : $create_qr) : $create_qr;
                    $iI->qr_code_desc = $iI->qr_code_desc ? ($iI->qr_code_desc == $qr_code_desc ? $iI->qr_code_desc : $qr_code_desc) : $qr_code_desc;
                    $iI->qr_print_at = $iI->qr_print_at ? $iI->qr_print_at : date('Y-m-d H:i:s');
                    $iI->save();
                    $transaction->commit();
                }catch (\Throwable $t){
                    $transaction->rollBack();
                    throw $t;
                }
            }
        }

        $content = $this->renderPartial('qr-all-without-attribute', ['model' => $data]);
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_BLANK,
            'format' => [100,50], //THERMAL 100mm x 50mm
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'methods' => [
                'SetTitle'=>'InspectId: '.$model['id'],
            ],
            // 'options' => [
            //     'setAutoTopMargin' => 'stretch'
            // ],
            // 'marginHeader' => 0,
            // 'marginFooter' => 0,
            'marginTop' => 0,
            'marginRight' => 0,
            'marginBottom' => 0,
            'marginLeft' => 0,
            'content' => $content,
        ]);

        return $pdf->render();
    }

    public function actionQrAll($id, $param1, $param2, $param6, $param8)
    {
        $model = $this->findModel($id);
        $data = [];

        // CEK apakah ada no_urut
        $inspectingId = $model->id;
        $hasNoUrut = \common\models\ar\InspectingItem::find()
            ->where(['inspecting_id' => $inspectingId])
            ->andWhere(['IS NOT', 'no_urut', null])
            ->exists();

        // Ambil item dengan urutan no_urut atau id
        $items = $model->getInspectingItems()
            ->orderBy($hasNoUrut ? 'no_urut ASC' : 'id ASC')
            ->all();
        
         foreach ($items as $key => $iI) {
            $roundUp = true;
            if($param6 == 0){
                $roundUp = false;
            }
            if($roundUp){
                $getMeter = round($iI->qty_sum * 0.9144, 1);
            }else{
                $getMeter = round($iI->qty_sum * 0.9144, 2);
            }
            
            $create_qr = 'INS-'.$iI->inspecting->id.'-'.$iI->id;
            if ($iI->is_head == 1) {
                $countKey = strlen($iI->qty_count) == 1 ? '00' : (strlen($iI->qty_count) == 2 ? '0' : '');
                $countItems = strlen($iI->qty_count) == 1 ? '00' : (strlen($iI->qty_count) == 2 ? '0' : '');

                $is_design_or_article = NULL;
                if ($iI->inspecting->jenis_process == 1) { //1 == dyeing
                    $is_design_or_article = $iI->inspecting->wo->mo->article;
                } else { //2 == printing && //3 == pfp
                    $articleIsNotNull = $iI->inspecting->wo->mo->article ? '/' : '';
                    $is_design_or_article = $iI->inspecting->wo->mo->article.$articleIsNotNull.$iI->inspecting->wo->mo->design;
                }

                $getWidth = $iI->inspecting->wo && $iI->inspecting->wo->scGreige && $iI->inspecting->wo->scGreige->lebar_kain ? TrnScGreige::lebarKainOptions()[$iI->inspecting->wo->scGreige->lebar_kain] : '-';
                $query_builder = new Query;
                $grade_alias = $query_builder->select('*')->from('wms_grade')->where(['=', 'grade_from', ($iI->grade_up <> NULL ? $iI->grade_up : $iI->grade)])->one();
                // $getGrade = $iI->gradeOptions()[($iI->grade_up <> NULL ? $iI->grade_up : $iI->grade)];
                $getGrade = $grade_alias['grade_to'];

                $no_wo = $iI->inspecting && $iI->inspecting->wo && $iI->inspecting->wo->no ? $iI->inspecting->wo->no : '-';
                $color = $iI->inspecting && $iI->inspecting->kombinasi ? $iI->inspecting->kombinasi : '-';
                $k3l_code = $iI->inspecting && $iI->inspecting->k3l_code ? $iI->inspecting->k3l_code : '-';
                if ($param8 == 1) {
                    $length = str_replace(' ', '', $iI->qty_sum.' '.($iI->inspecting->unit == 1 ? 'YDS / '.$getMeter.' M' : ($iI->inspecting->unit == 2 ? 'M' : ($iI->inspecting->unit == 3 ? 'PCS' : 'KG'))));
                } else {
                    $length = str_replace(' ', '', $iI->qty_sum.' '.($iI->inspecting->unit == 1 ? 'YDS' : ($iI->inspecting->unit == 2 ? 'M' : ($iI->inspecting->unit == 3 ? 'PCS' : 'KG'))));
                }
                $no_lot = $iI->inspecting && $iI->inspecting->no_lot ? $iI->inspecting->no_lot.'/'.($key+1) : '-';
                $qty_count = $countKey.($key+1).'/'.$countItems.count($model->inspectingItems);
                $grade = $getWidth.'"/'.$getGrade;
                // $motif_greige = $iI->inspecting->wo->mo->scGreige->greigeGroup->nama_kain;
                $defect = str_replace(',', '|', $iI->defect);

                $production = $param1 == 1 ? 'MADE IN INDONESIA' : ''; // <-- jika ceklis, di munculkan, jika tidak di ceklis tidak di munculkan
                $regisk3l = $param2 == 1 ? 'REGISTRASI K3L!'.$k3l_code : ''; // <-- jika ceklis, di munculkan, jika tidak di ceklis tidak di munculkan

                $qr_code_desc = $regisk3l.
                                '!'.$no_wo.
                                '!'.$is_design_or_article.
                                '!'.$color.
                                '!'.$no_lot.
                                '!'.$length.
                                '!'.$grade.
                                // '!'.$motif_greige.
                                '!'.$defect;
                                '!'.$production;

                $data[] = [
                    'param1' => $param1,
                    'param2' => $param2,
                    'qr_code' => $iI->qr_code ? $iI->qr_code : $create_qr,
                    'no_wo' => $no_wo,
                    'color' => $color,
                    'k3l_code' => $k3l_code,
                    'is_design_or_artikel' => $is_design_or_article ? $is_design_or_article : '-',
                    'length' => $length,
                    'no_lot' => $no_lot,
                    'qty_count' => $qty_count,
                    'grade' => $grade,
                    'qr_code_desc' => $iI->qr_code_desc ? ($iI->qr_code_desc == $qr_code_desc ? $iI->qr_code_desc : $qr_code_desc) : $qr_code_desc,
                ];

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $iI->qr_code = $iI->qr_code ? ($iI->qr_code == $create_qr ? $iI->qr_code : $create_qr) : $create_qr;
                    $iI->qr_code_desc = $iI->qr_code_desc ? ($iI->qr_code_desc == $qr_code_desc ? $iI->qr_code_desc : $qr_code_desc) : $qr_code_desc;
                    $iI->qr_print_at = $iI->qr_print_at ? $iI->qr_print_at : date('Y-m-d H:i:s');
                    $iI->save();
                    $transaction->commit();
                }catch (\Throwable $t){
                    $transaction->rollBack();
                    throw $t;
                }
            }
        }

        $content = $this->renderPartial('qr-all', ['model' => $data]);
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_BLANK,
            'format' => [100,50], //THERMAL 100mm x 50mm
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'methods' => [
                'SetTitle'=>'InspectId: '.$model['id'],
            ],
            // 'options' => [
            //     'setAutoTopMargin' => 'stretch'
            // ],
            // 'marginHeader' => 0,
            // 'marginFooter' => 0,
            'marginTop' => 0,
            'marginRight' => 0,
            'marginBottom' => 0,
            'marginLeft' => 0,
            'content' => $content,
        ]);

        return $pdf->render();
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
                body{font-family: Calibri; font-weight: bold; font-size:9.5px; letter-spacing: 0px;}
                table {font-family: Calibri; font-weight: bold; width: 100%; font-size:9.5px; border-spacing: 0; letter-spacing: 0px;} th, td {padding: 0.1em 0em; vertical-align: top;}
                table.bordered th, table.bordered td, td.bordered, th.bordered {border: 0.1px solid black; padding: 0.1em 0.1em; vertical-align: middle;}
             ',
            // set mPDF properties on the fly
            //'options' => ['title' => 'Sales Contract - '.$model->id],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHTMLHeader'=>$this->renderPartial('print/header', ['model' => $model, 'config'=>$config]),
                'SetTitle'=>'Inspecting',
            ],
            'marginHeader' => 4,
            'marginFooter' => 4,
            'marginTop' => 4,
            'marginRight' => 4,
            'marginBottom' => 4,
            'marginLeft' => 4,
            'options' => [
                'setAutoTopMargin' => 'stretch'
            ],
            // your html content input
            'content' => $content,
        ]);

        // if($model->status == $model::STATUS_DRAFT){
        //     $pdf->methods['SetHeader'] = 'Inspecting | ID:'.$model->id.' | DRAFT';
        // }else{
        //     if($model->status == $model::STATUS_APPROVED){
        //         $pdf->methods['SetHeader'] = 'Inspecting - | ID:'.$model->id.' | NO:'.$model->no;
        //     }else $pdf->methods['SetHeader'] = 'Inspecting - | ID:'.$model->id.' | MENUNGGU PERSETUJUAN';
        // }

        $pdf->methods['SetHeader'] = '| PACKING LIST |';
        $pdf->methods['SetFooter'] = 'Page {PAGENO}';

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    public function actionAnalisaPengirimanProduksi()
    {
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

    protected function findItem($id)
    {
        if (($model = InspectingItem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    // protected function findItemInStock($id)
    // {
    //     if (($model = TrnGudangJadi::findOne($id)) !== null) {
    //         return $model;
    //     }

    //     throw new NotFoundHttpException('The requested page does not exist.');
    // }

    public function actionHapusSemuaDefect($id)
    {
        $model = $this->findModel($id);
        
        // ambil semua item dari inspecting ini
        $items = $model->inspectingItems;
        $itemIds = array_column($items, 'id');

        if (empty($itemIds)) {
            Yii::$app->session->setFlash('info', 'Tidak ada item yang memiliki defect.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            //  Hapus semua defect yang terkait dengan item-item ini
            $deleted = DefectInspectingItem::deleteAll(['inspecting_item_id' => $itemIds]);

            //  Update kolom updated_at dan updated_by
            $model->updated_at = time(); // integer timestamp
            $model->updated_by = Yii::$app->user->id;

            if (!$model->save(false, ['updated_at', 'updated_by'])) {
                throw new \yii\web\HttpException(500, 'Gagal memperbarui waktu/user terakhir.');
            }

            $transaction->commit();

            if ($deleted) {
                Yii::$app->session->setFlash('success', "Semua kode defect ($deleted baris) berhasil dihapus.");
            } else {
                Yii::$app->session->setFlash('warning', 'Tidak ada defect yang ditemukan untuk dihapus.');
            }

        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Gagal menghapus defect: ' . $e->getMessage());
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

}