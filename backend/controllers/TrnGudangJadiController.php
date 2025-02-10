<?php

namespace backend\controllers;

use common\models\ar\{ MstGreigeGroup, MutasiExFinishAlt, MutasiExFinishAltItem };
use common\models\ar\{ TrnGudangJadi, TrnGudangJadiSearch, TrnWo, TrnScGreige, TrnStockGreige };
use common\models\ar\{ TrnInspecting, InspectingMklBj, TrnBeliKainJadi, TrnTerimaMakloonProcess, TrnTerimaMakloonFinish, TrnReturBuyer }; //header_source
use common\models\User;
use kartik\mpdf\Pdf;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseVarDumper;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TrnGudangJadiController implements the CRUD actions for TrnGudangJadi model.
 */
class TrnGudangJadiController extends Controller
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
     * Lists all TrnGudangJadi models.
     * @return mixed
     */
    public function actionRekap()
    {
        $searchModel = new \backend\models\TrnGudangJadiSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('rekap', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all TrnGudangJadi models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnGudangJadiSearch(['status'=>TrnGudangJadi::STATUS_STOCK]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort->defaultOrder = ['qr_print_at' => SORT_ASC];

        // $dataProvider->pagination->pageSize = 10;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionMutasiExFinish(){
        if(Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $data = Yii::$app->request->post('data');
            $modelMutasi = new MutasiExFinishAlt([
                'no_referensi' => $data['ref'],
                'pemohon' => $data['pemohon'],
                'created_at' => time(),
                'created_by' => Yii::$app->user->id,
                'updated_at' => time(),
                'updated_by' => Yii::$app->user->id,
            ]);

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if(!$flag = $modelMutasi->save(false)){
                    $transaction->rollBack();
                    throw new HttpException(500, 'Gagal, coba lagi. (1)');
                }

                foreach ($data['ids'] as $id) {
                    $gdJadi = TrnGudangJadi::findOne($id);
                    if($gdJadi !== null){
                        if($gdJadi->status === $gdJadi::STATUS_STOCK){
                            $gdJadi->status = $gdJadi::STATUS_MUTASI_EF;
                            if(!$flag = $gdJadi->save(false, ['status'])){
                                $transaction->rollBack();
                                throw new HttpException(500, 'Gagal, coba lagi. (2)');
                            }
                        }
                    }

                    $modelItem = new MutasiExFinishAltItem([
                        'mutasi_id' => $modelMutasi->id,
                        'gudang_jadi_id' => $id,
                        'grade' => $gdJadi->grade,
                        'qty' => $gdJadi->qty,
                    ]);
                    if(!$flag = $modelItem->save(false)){
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal, coba lagi. (3)');
                    }
                }

                if($flag){
                    $transaction->commit();
                    return true;
                }
            }catch (\Throwable $t){
                $transaction->rollBack();
                throw $t;
            }
        }

        throw new MethodNotAllowedHttpException('Metode tidak diizinkan.');
    }

    public function actionSetSiapKirim(){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $datas = Yii::$app->request->post('formData');
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = false;
                $getCust = [];
                foreach ($datas as $data) {
                    $stock = TrnGudangJadi::findOne($data);
                    if($stock->status != TrnGudangJadi::STATUS_STOCK){
                        $transaction->rollBack();
                        throw new NotAcceptableHttpException('salah satu stock statusnya tidak valid.');
                    }

                    $stock->status = TrnGudangJadi::STATUS_SIAP_KIRIM;
                    if(!$flag = $stock->save(false, ['status'])){
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal memproses, coba lagi.');
                    }

                    $getCust[$stock->wo->sc->cust->id] = [
                        'cust_id' => $stock->wo->sc->cust->id,
                        'cust_name' => $stock->wo->sc->cust->name
                    ];
                }

                if($flag){
                    $header = [
                        "Content-Type:application/json",
                        "Authorization: key=AAAA7rHxAxw:APA91bEX6eI2Oj-oWsOGCRqmshQip-FT2TOr9n2L0X0U-ExYRalg_ZgtVcw1sEotmZkapjJ4dkeumVNGMGCiCfElhIPrntoAMTdt_ypn2HOXQPaciaP10nPNqVDlzqL-HbfsUVUvOJFA"
                    ];

                    foreach ($getCust as $gC) {
                        $arr = [
                            "to" => "/topics/all",
                            "notification"    => [
                                "title"         => "Pemberitahuan",
                                "message"       => "Picking List",
                                "body"          => "Picking List",
                                "id"            => $gC['cust_id'],
                                "name_customer"  => $gC['cust_name'],
                                'vibrate'       => 1,
                                'sound'         => 1,
                                "click_action"  => "OPEN_ACTIVITY"
                            ],
                            "data"  => [
                                "title"     => "Pemberitahuan",
                                "message"   => "Picking List",
                                "id"        => $gC['cust_id'],
                                "name_customer"  => $gC['cust_name'],
                                'vibrate'   => 1,
                                'sound'     => 1
                            ]
                        ];

                        $fcm = json_encode($arr);

                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => $fcm,
                            CURLOPT_HTTPHEADER =>$header,
                        ));

                        $response = curl_exec($curl);
                        if ($response === false) {
                            throw new NotAcceptableHttpException("Gagal mengirim notifikasi");
                        }
                        // $err = curl_error($curl);
                        // curl_close($curl);
                        // $data_curl = json_decode($response,true);
                    }

                    $transaction->commit();
                    return true;
                }
            }catch (\Throwable $t){
                $transaction->rollBack();
                throw $t;
            }
        }

        throw new MethodNotAllowedHttpException('Metode tidak diizinkan.');
    }

    public function actionPindahGudang(){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $postData = Yii::$app->request->post();

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = false;

                foreach ($postData['ids'] as $data) {
                    $stock = TrnGudangJadi::findOne($data);
                    if($stock->status != TrnGudangJadi::STATUS_STOCK){
                        $transaction->rollBack();
                        throw new NotAcceptableHttpException('Status setiap item harus "STOCK". Salah satu item statusnya tidak valid.');
                    }

                    $stock->jenis_gudang = $postData['jenis_gudang'];
                    if(!$flag = $stock->save(false, ['jenis_gudang'])){
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal memproses, coba lagi.');
                    }
                }

                if($flag){
                    $transaction->commit();
                    return true;
                }
            }catch (\Throwable $t){
                $transaction->rollBack();
                throw $t;
            }
        }

        throw new MethodNotAllowedHttpException('Metode tidak diizinkan.');
    }

    public function actionSetStock(){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $datas = Yii::$app->request->post('formData');

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = false;
                foreach ($datas as $data) {
                    $stock = TrnGudangJadi::findOne($data);
                    if($stock->status != TrnGudangJadi::STATUS_SIAP_KIRIM){
                        $transaction->rollBack();
                        throw new NotAcceptableHttpException('Status setiap item harus "STOCK". Salah satu item statusnya tidak valid.');
                    }

                    $stock->status = TrnGudangJadi::STATUS_STOCK;
                    if(!$flag = $stock->save(false, ['status'])){
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal memproses, coba lagi.');
                    }
                }

                if($flag){
                    $transaction->commit();
                    return true;
                }
            }catch (\Throwable $t){
                $transaction->rollBack();
                throw $t;
            }
        }

        throw new MethodNotAllowedHttpException('Metode tidak diizinkan.');
    }

    /**
     * Displays a single TrnGudangJadi model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TrnGudangJadi model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    /*public function actionCreate()
    {
        $model = new TrnGudangJadi();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }*/

    /**
     * Updates an existing TrnGudangJadi model.
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
     * Deletes an existing TrnGudangJadi model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/

    /**
     * Finds the TrnGudangJadi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnGudangJadi the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnGudangJadi::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    // bagussona
    public function actionQr($id, $param1, $param2, $param3)
    {
        $model = $this->findModel($id);
        $is_design_or_atikel = NULL;
        $no_lot = NULL;
        $getWidth = $model->wo && $model->wo->scGreige && $model->wo->scGreige->lebar_kain ? TrnScGreige::lebarKainOptions()[$model->wo->scGreige->lebar_kain] : '-';
        $query_builder = new Query;
        $grade_alias = $query_builder->select('*')->from('wms_grade')->where(['=', 'grade_from', $model->grade])->one();
        // $getGrade = $model->gradeName ? $model->gradeName : '-';
        $getGrade = $grade_alias['grade_to'];

        $roundUp = true;
        if($param3 == 0){
            $roundUp = false;
        }
        if($roundUp){
            $getMeter = round($model->qty * 0.9144, 1);
        }else{
            $getMeter = round($model->qty * 0.9144, 2);
        }
        if ($model->source == 1) { //1 == SOURCE_PACKING
            $getTableData = (new \yii\db\Query())->from(TrnInspecting::tableName())
                ->select('*')
                ->where(['no'=>$model->source_ref])
                ->one();
            if (!$getTableData) {
                $getTableData = (new \yii\db\Query())->from(InspectingMklBj::tableName())
                    ->select('*')
                    ->where(['no'=>$model->source_ref])
                    ->one();
            }

            if (array_key_exists('jenis_process', $getTableData)) {
                if ($getTableData['jenis_process'] == 1) { //1 == dyeing
                    $is_design_or_atikel = $model->wo->mo->article;
                } else { //2 == printing && //3 == pfp
                    $articleIsNotNull = $model->wo->mo->article ? '/' : '';
                    $is_design_or_atikel = $model->wo->mo->article.$articleIsNotNull.$model->wo->mo->design;
                }

                // $is_design_or_atikel = $getTableData['jenis_process'] == 1 ? ($model->grade == 1 ? $model->wo->mo->article : $model->wo->greige->group->nama_kain) : $model->wo->mo->design;
            } else {
                if ($getTableData['jenis'] == 1) { //1 == dyeing
                    $is_design_or_atikel = $model->wo->mo->article;
                } else { //2 == printing && //3 == pfp
                    $articleIsNotNull = $model->wo->mo->article ? '/' : '';
                    $is_design_or_atikel = $model->wo->mo->article.$articleIsNotNull.$model->wo->mo->design;
                }

                // $is_design_or_atikel = $getTableData['jenis'] == 1 ? ($model->grade == 1 ? $model->wo->mo->article : $model->wo->greige->group->nama_kain) : $model->wo->mo->design;
            }
            
            $no_lot = $getTableData['no_lot'];
        } else {
            if ($model->wo->mo->process == 1) { //1 == dyeing
                $is_design_or_atikel = $model->wo->mo->article;
            } else { //2 == printing && //3 == pfp
                $articleIsNotNull = $model->wo->mo->article ? '/' : '';
                $is_design_or_atikel = $model->wo->mo->article.$articleIsNotNull.$model->wo->mo->design;
            }

            // $is_design_or_atikel = $model->wo->mo->process == 1 ? ($model->grade == 1 ? $model->wo->mo->article : $model->wo->greige->group->nama_kain) : $model->wo->mo->design;
        }
        
        $data = [];
        $data['qr_code'] = $model->qr_code ? $model->qr_code : 'STK-'.$model->id;
        $data['no_wo'] = $model->wo->no;
        $data['k3l_code'] = '-';
        $data['color'] = $model->color;
        $data['is_design_or_artikel'] = $is_design_or_atikel ? $is_design_or_atikel : '-';
        $data['length'] = str_replace(' ', '', $model->qty.' '.($model->unit == 1 ? 'YDS / '.$getMeter.' M' : ($model->unit == 2 ? 'M' : 'KG')));
        $data['no_lot'] = $no_lot ? $no_lot : '-';
        $data['qty_count'] = 0;
        $data['grade'] = $getWidth.'"/'.$getGrade;
        // $data['motif_greige'] = $model->wo->mo->scGreige->greigeGroup->nama_kain;
        $data['jenis_gudang'] = $model->jenis_gudang;
        // $data['defect'] = str_replace(',', '|', $model->defect);
        $data['param1'] = $param1;
        $data['param2'] = $param2;

        // $production = $model->jenis_gudang == 2 ? '-' : 'MADE IN INDONESIA';

        $production = $param1 == 1 ? 'MADE IN INDONESIA' : '';
        $regisk3l = $param2 == 1 ? 'REGISTRASI K3L!'.$data['k3l_code'] : '';

        $qr_code_desc = $regisk3l.
                        '!'.$data['no_wo'].
                        '!'.$data['is_design_or_artikel'].
                        '!'.$data['color'].
                        '!'.$data['no_lot'].
                        '!'.$data['length'].
                        '!'.$data['grade'].
                        // '!'.$data['motif_greige'].
                        '!'.$production;
        $data['qr_code_desc'] = $model->qr_code_desc ? ($model->qr_code_desc == $qr_code_desc ? $model->qr_code_desc : $qr_code_desc) : $qr_code_desc;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $query = $this->findModel($id);
            $query['qr_code'] = $query->qr_code ? $query->qr_code : 'STK-'.$model->id;
            $query['qr_code_desc'] = $query->qr_code_desc ? ($query->qr_code_desc == $qr_code_desc ? $query->qr_code_desc : $qr_code_desc) : $qr_code_desc;
            $query['qr_print_at'] = $query->qr_print_at ? $query->qr_print_at : date('Y-m-d H:i:s');
            $query->save();
            $transaction->commit();
        }catch (\Throwable $t){
            $transaction->rollBack();
            throw $t;
        }

        // $data['qty_count'] = strlen($model['qty_count']) == 1 ? '00'.$model['qty_count'] : (strlen($model['qty_count']) == 2 ? '0'.$model['qty_count'] : $model['qty_count']);
        $content = $this->renderPartial('qr', ['model' => $data]);
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_BLANK,
            'format' => [100,50], //THERMAL 100mm x 50mm
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
                'SetTitle'=>$data['qr_code'],
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

    public function actionPrintLabel($locs_code)
    {
        $model = (new \yii\db\Query())->from(TrnGudangJadi::tableName())
            ->select([
                'trn_gudang_jadi.id tgj_id',
                'trn_gudang_jadi.wo_id',
                'trn_gudang_jadi.source',
                'trn_gudang_jadi.source_ref',
                'trn_gudang_jadi.unit',
                'trn_gudang_jadi.qty',
                'trn_gudang_jadi.grade',
                'trn_gudang_jadi.locs_code',
                'trn_wo.no tw_no_wo',
                'trn_wo.id tw_id',
                'trn_mo.id tm_id',
                'trn_mo.article tm_article',
                'trn_mo.design tm_design',
                'trn_mo.process tm_process',
            ])->leftJoin('trn_wo', 'trn_gudang_jadi.wo_id = trn_wo.id')->leftJoin('trn_mo', 'trn_wo.mo_id = trn_mo.id')
            ->where(['locs_code'=>$locs_code])->all();

        $arr = [];
        foreach ($model as $m) {
            if ($m['source'] == 1) { //1 == SOURCE_PACKING
                $getTableData = (new \yii\db\Query())->from(TrnInspecting::tableName())
                    ->select('jenis_process')
                    ->where(['no'=>$m['source_ref']])
                    ->one();
                if (!$getTableData) {
                    $getTableData = (new \yii\db\Query())->from(InspectingMklBj::tableName())
                        ->select('jenis')
                        ->where(['no'=>$m['source_ref']])
                        ->one();
                }

                if (array_key_exists('jenis_process', $getTableData)) {
                    if ($getTableData['jenis_process'] == 1) { //1 == dyeing
                        $is_design_or_atikel = $m['tm_article'];
                    } else { //2 == printing && //3 == pfp
                        $articleIsNotNull = $m['tm_article'] ? '/' : '';
                        $is_design_or_atikel = $m['tm_article'].$articleIsNotNull.$m['tm_design'];
                    }
                } else {
                    if ($getTableData['jenis'] == 1) { //1 == dyeing
                        $is_design_or_atikel = $m['tm_article'];
                    } else { //2 == printing && //3 == pfp
                        $articleIsNotNull = $m['tm_article'] ? '/' : '';
                        $is_design_or_atikel = $m['tm_article'].$articleIsNotNull.$m['tm_design'];
                    }
                }
            } else {
                if ($m['tm_process'] == 1) { //1 == dyeing
                    $is_design_or_atikel = $m['tm_article'];
                } else { //2 == printing && //3 == pfp
                    $articleIsNotNull = $m['tm_article'] ? '/' : '';
                    $is_design_or_atikel = $m['tm_article'].$articleIsNotNull.$m['tm_design'];
                }
            }

            $arr[] = [
                'id' => mt_rand(1000,10000),
                'no_wo' => $m['tw_no_wo'],
                'locs_code' => $m['locs_code'],
                'nama_barang' => $is_design_or_atikel,
                'qty' => $m['qty'],
                'grade' => TrnStockGreige::gradeOptions()[$m['grade']],
                'unit' => MstGreigeGroup::unitOptions()[$m['unit']],
            ];
        }

        return $this->render('print-label', [
            'model' => $arr,
            'header' => [
                'location' => $locs_code
            ]
        ]);
    }

}