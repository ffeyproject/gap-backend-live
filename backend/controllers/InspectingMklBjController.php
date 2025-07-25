<?php

namespace backend\controllers;

use backend\models\form\InspectingMklBjHeaderForm;
use backend\models\form\InspectingMklBjItemsForm;
use kartik\mpdf\Pdf;
use Yii;
use common\models\ar\TrnGudangJadi;
use common\models\ar\InspectingMklBjItems;
use common\models\ar\InspectingMklBj;
use common\models\ar\InspectingMklBjSearch;
use common\models\ar\TrnScGreige;
use common\models\ar\MstGreigeGroup;
use yii\base\BaseObject;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseVarDumper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * InspectingMklBjController implements the CRUD actions for InspectingMklBj model.
 */
class InspectingMklBjController extends Controller
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
     * Lists all InspectingMklBj models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InspectingMklBjSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single InspectingMklBj model.
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
     * Creates a new InspectingMklBj model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws HttpException
     * @throws \Throwable
     * @throws yii\db\Exception
     */
    public function actionCreate()
    {
        $model = new InspectingMklBj([
            /*'wo_id' => '4',
            'wo_color_id' => '5',
            'tgl_inspeksi' => '2021-07-06',
            'tgl_kirim' => '2021-07-06',
            'no_lot' => 'No Lot',
            'jenis' => '1',
            'satuan' => '1',*/
        ]);
        $modelItem = new InspectingMklBjItems();

        if (Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            if($model->load(Yii::$app->request->post())){
                if($model->validate()){
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if(! ($flag = $model->save(false))){
                            $transaction->rollBack();
                            throw new HttpException(500, 'Gagal, coba lagi. (1)');
                        }

                        foreach (Json::decode(Yii::$app->request->post('items')) as $item) {
                            $modelItem = new InspectingMklBjItems([
                                'inspecting_id' => $model->id,
                                'grade' => $item['grade'],
                                'defect' => $item['defect'],
                                'lot_no' => $item['lot_no'],
                                'join_piece' => $item['join_piece'],
                                'qty' => $item['qty'],
                                'note' => $item['note'],
                            ]);

                            if(!($flag = $modelItem->save(false))){
                                $transaction->rollBack();
                                throw new HttpException(500, 'Gagal, coba lagi. (2)');
                            }
                        }

                        $query = InspectingMklBjItems::find();
                        $getItemBasedOnInspectingId = $query->where(['=', 'inspecting_id', $model->id])->all();
                        foreach ($getItemBasedOnInspectingId as $gIBOII) {
                            $qty_sum = $query->where(['=', 'join_piece', $gIBOII->join_piece])->andWhere(['=', 'inspecting_id', $model->id])->sum('qty');
                            $qty_count = $query->where(['=', 'join_piece', $gIBOII->join_piece])->andWhere(['=', 'inspecting_id', $model->id])->count();
                            $is_head = $query->orderBy('is_head DESC')->orderBy('id ASC')
                                            ->where(['=', 'join_piece', $gIBOII->join_piece])
                                            ->andWhere(['=', 'inspecting_id', $model->id])
                                            ->andWhere(['<>', 'join_piece', ""])->one();
                            $gIBOII['qty_sum'] = ($is_head && ($is_head['id'] <> $gIBOII['id'])) ? NULL : ($gIBOII['join_piece'] == NULL || $gIBOII['join_piece'] == "" ? $gIBOII['qty'] : $qty_sum);
                            $gIBOII['qr_code'] = 'MKL-'.$gIBOII['inspecting_id'].'-'.$gIBOII['id'];
                            $gIBOII['is_head'] = ($is_head && ($is_head['id'] <> $gIBOII['id'])) ? 0 : 1;
                            $gIBOII['qty_count'] = ($is_head && ($is_head['id'] <> $gIBOII['id'])) ? 0 : ($gIBOII['join_piece'] == NULL || $gIBOII['join_piece'] == "" ? 1 : $qty_count);
                            $gIBOII->save();
                        }

                        if($flag){
                            $transaction->commit();
                            return ['success'=>true, 'redirect'=>Url::to(['view', 'id'=>$model->id])];
                        }
                    }catch (\Throwable $t){
                        $transaction->rollBack();
                        throw $t;
                    }
                }

                $result = [];
                foreach ($model->getErrors() as $attribute => $errors) {
                    $result[Html::getInputId($model, $attribute)] = $errors;
                }
                return ['validation' => $result];
            }
        }

        return $this->render('create', [
            'model' => $model,
            'modelItem' => $modelItem
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelItem = new InspectingMklBjItems();
        $items = ArrayHelper::toArray($model->getItems()->orderBy('id ASC')->all());

        if (Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            if($model->load(Yii::$app->request->post())){
                if($model->validate()){
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if(! ($flag = $model->save(false))){
                            $transaction->rollBack();
                            throw new HttpException(500, 'Gagal, coba lagi. (1)');
                        }

                        $dI = Json::decode(Yii::$app->request->post('deletedItems'));
                        if(count($dI) > 0) {
                            for ($i=0; $i < count($dI); $i++) { 
                                if ($dI[$i] <> 0) {
                                    Yii::$app->db->createCommand()->delete(InspectingMklBjItems::tableName(), ['=', 'id', $dI[$i]])->execute();
                                }
                            }
                        }

                        $dataItems = Json::decode(Yii::$app->request->post('items'));
                        foreach ($dataItems as $item) {
                            $transaction = Yii::$app->db->beginTransaction();
                            try {
                                if ($item['id'] == 0) {
                                    Yii::$app->db->createCommand()->insert(InspectingMklBjItems::tableName(), [
                                        'inspecting_id' => $model->id,
                                        'grade' => $item['grade'],
                                        'defect' => $item['defect'],
                                        'lot_no' => $item['lot_no'],
                                        'join_piece' => $item['join_piece'],
                                        'qty' => $item['qty'],
                                        'note' => $item['note'],
                                    ])->execute();
                                } else {
                                    $query = $this->findItem($item['id']);
                                        $query['inspecting_id'] = $query->inspecting_id;
                                        $query['grade'] = $item['grade'];
                                        $query['defect'] = $item['defect'];
                                        $query['lot_no'] = $item['lot_no'];
                                        $query['join_piece'] = $item['join_piece'];
                                        $query['qty'] = $item['qty'];
                                        $query['qty_sum'] = $query->is_head == 1 ? $item['qty'] : NULL;
                                        $query['note'] = $item['note'];
                                        $query['qr_code_desc'] = $query->qr_code_desc;
                                        $query['qr_print_at'] = $query->qr_print_at;
                                    $query->save();
                                }
                                $transaction->commit();
                            }catch (\Throwable $t){
                                $transaction->rollBack();
                                throw $t;
                            }
                        }

                        $query2 = InspectingMklBjItems::find();
                        $getItemBasedOnInspectingId = $query2->where(['=', 'inspecting_id', $model->id])->all();
                        foreach ($getItemBasedOnInspectingId as $gIBOII) {
                            $transaction = Yii::$app->db->beginTransaction();
                            try {
                                $qty_count = $query2->where(['=', 'join_piece', $gIBOII->join_piece])->andWhere(['=', 'inspecting_id', $model->id])->count();
                                $is_head = $query2->orderBy('is_head DESC')->orderBy('id ASC')
                                            ->where(['=', 'join_piece', $gIBOII->join_piece])
                                            ->andWhere(['=', 'inspecting_id', $model->id])
                                            ->andWhere(['<>', 'join_piece', ""])->one();
                                $qty_sum = $query2->where(['=', 'join_piece', $gIBOII->join_piece])->andWhere(['=', 'inspecting_id', $model->id])->sum('qty');
                                $gIBOII['qty_sum'] = ($is_head && ($is_head['id'] <> $gIBOII['id'])) ? NULL : ($gIBOII['join_piece'] == NULL || $gIBOII['join_piece'] == "" ? $gIBOII['qty'] : $qty_sum);
                                $gIBOII['is_head'] = ($is_head && ($is_head['id'] <> $gIBOII['id'])) ? 0 : 1;
                                $gIBOII['qr_code'] = ($gIBOII['qr_code'] <> NULL || $gIBOII['qr_code']) ? $gIBOII['qr_code'] : 'MKL-'.$gIBOII['inspecting_id'].'-'.$gIBOII['id'];
                                $gIBOII['qty_count'] = ($is_head && ($is_head['id'] <> $gIBOII['id'])) ? 0 : ($gIBOII['join_piece'] == NULL || $gIBOII['join_piece'] == "" ? 1 : $qty_count);
                                $gIBOII->save();
                                $transaction->commit();
                            } catch (\Throwable $th) {
                                $transaction->rollBack();
                                throw $th;
                            }
                        }

                        if($flag){
                            $transaction->commit();
                            return ['success'=>true, 'redirect'=>Url::to(['view', 'id'=>$model->id])];
                        }
                    }catch (\Throwable $t){
                        $transaction->rollBack();
                        throw $t;
                    }
                }

                $result = [];
                foreach ($model->getErrors() as $attribute => $errors) {
                    $result[Html::getInputId($model, $attribute)] = $errors;
                }
                return ['validation' => $result];
            }
        }

        return $this->render('update', [
            'model' => $model,
            'modelItem' => $modelItem,
            'items' => $items
        ]);
    }

    /**
     * Upgrades an existing InspectingMklBj model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpgrade($id)
    {
        $model = $this->findModel($id);

        if($model->status != InspectingMklBj::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk diupgrade.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $modelItem = new InspectingMklBjItems();

        $items = [];
        foreach ($model->getItems()->orderBy('id')->all() as $inspectingMklbjItem) {
            $items[] = [
                'id' => $inspectingMklbjItem->id,
                'grade' => $inspectingMklbjItem->grade,
                'gradeLabel' => $inspectingMklbjItem::gradeOptions()[$inspectingMklbjItem->grade],
                'grade_up' => $inspectingMklbjItem->grade_up,
                'gradeupLabel' => $inspectingMklbjItem->grade_up ? $inspectingMklbjItem::gradeOptions()[$inspectingMklbjItem->grade_up] : '',
                'defect' => $inspectingMklbjItem->defect,
                'lot_no' => $inspectingMklbjItem->lot_no,
                'qty' => $inspectingMklbjItem->qty,
                'join_piece' => $inspectingMklbjItem->join_piece,
                'note' => $inspectingMklbjItem->note
            ];
        }

        if (Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            if($model->load(Yii::$app->request->post())){
                if($model->validate()){
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if(! ($flag = $model->save(false))){
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
                            $query_one['qty'] = $item['qty'] ? $item['qty'] : $query_one->qty;
                            $query_one['note'] = $item['note'] ? $item['note'] : $query_one->note;
                            $query_one->save();
                        }

                        $query = InspectingMklBjItems::find();
                        $getItemBasedOnInspectingId = $query->where(['=', 'inspecting_id', $model->id])->all();
                        foreach ($getItemBasedOnInspectingId as $gIBOII) {
                            $qty_sum = $query->where(['=', 'join_piece', $gIBOII->join_piece])->andWhere(['=', 'inspecting_id', $model->id])->sum('qty');
                            $qty_count = $query->where(['=', 'join_piece', $gIBOII->join_piece])->andWhere(['=', 'inspecting_id', $model->id])->count();
                            $is_head = $query->orderBy('is_head DESC')
                                            ->where(['=', 'join_piece', $gIBOII->join_piece])
                                            ->andWhere(['=', 'inspecting_id', $model->id])
                                            ->andWhere(['<>', 'join_piece', ""])->one();
                            $gIBOII['qty_sum'] = ($is_head && ($is_head['id'] <> $gIBOII['id'])) ? NULL : ($gIBOII['join_piece'] == NULL || $gIBOII['join_piece'] == "" ? $gIBOII['qty'] : $qty_sum);
                            $gIBOII['qr_code'] = 'MKL-'.$gIBOII['inspecting_id'].'-'.$gIBOII['id'];
                            $gIBOII['is_head'] = ($is_head && ($is_head['id'] <> $gIBOII['id'])) ? 0 : 1;
                            $gIBOII['qty_count'] = ($is_head && ($is_head['id'] <> $gIBOII['id'])) ? 0 : ($gIBOII['join_piece'] == NULL || $gIBOII['join_piece'] == "" ? 1 : $qty_count);
                            $gIBOII->save();
                        }

                        if($flag){
                            $transaction->commit();
                            return ['success'=>true, 'redirect'=>Url::to(['view', 'id'=>$model->id])];
                        }
                    }catch (\Throwable $t){
                        $transaction->rollBack();
                        throw $t;
                    }
                }

                $result = [];
                foreach ($model->getErrors() as $attribute => $errors) {
                    $result[Html::getInputId($model, $attribute)] = $errors;
                }
                return ['validation' => $result];
            }
        }

        return $this->render('upgrade', [
            'model' => $model,
            'modelItem' => $modelItem,
            'items' => $items
        ]);
    }

    /**
     * Deletes an existing InspectingMklBj model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid untuk dihapus.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        InspectingMklBjItems::deleteAll(['inspecting_id'=>$model->id]);
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

        $model->status = $model::STATUS_POSTED;
        if(empty($model->no_urut)){
            $model->setNomor();
            $model->save(false, ['status', 'no_urut', 'no']);

            // $inspectingItems = $model->getItems()->orderBy('id ASC')->all();
            // foreach ($inspectingItems as $iI) {
            //     $stock = $this->findItemInStock($iI->id);
            //     $stock->trans_from = 'MKL';
            //     $stock->id_from = $iI->inspecting_id;
            //     $stock->qr_code = $iI->qr_code;
            //     $stock->qr_code_desc = $iI->qr_code_desc;
            //     $stock->save();
            // }
        }else{
            $model->save(false, ['status']);
        }

        Yii::$app->session->setFlash('success', 'Posting berhasil.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
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

        // if($model->status == $model::STATUS_DRAFT){
        //    $pdf->methods['SetHeader'] = 'Inspecting Makloon & Barang Jadi | ID:'.$model->id.' | DRAFT';
        //}else{
        //    if($model->status == $model::STATUS_POSTED){
        //        $pdf->methods['SetHeader'] = 'Inspecting Makloon & Barang Jadi - | ID:'.$model->id.' | NO:'.$model->no;
        //    }else $pdf->methods['SetHeader'] = 'Inspecting Makloon & Barang Jadi - | ID:'.$model->id.' | MENUNGGU PERSETUJUAN';
        //}
	
	$pdf->methods['SetHeader'] = '| PACKING LIST |';
        $pdf->methods['SetFooter'] = 'Page {PAGENO}';

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    // bagussona
    // public function actionQr($id, $param3, $param4, $param5)
    // {
    //     $model = $this->findItem($id);
    //     $create_qr =  'MKL-'.$model->inspecting_id.'-'.$model->id;

    //     $is_design_or_article = NULL;
    //     if ($model->inspecting->jenis == 1) { //1 == dyeing
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

    //     $inspectingMklbjItemCount = [];
    //     $query = InspectingMklBjItems::find();
    //     $getItemBasedOnInspectingId = $query->where(['=', 'inspecting_id', $model->inspecting_id])->orderBy('id ASC')->all();
    //     foreach ($getItemBasedOnInspectingId as $gIBOII) {
    //         array_push($inspectingMklbjItemCount, $gIBOII->id);
    //     }
    //     $key = array_search ($model->id, $inspectingMklbjItemCount);
    //     $data = [];
    //     $data['qr_code'] = $model->qr_code ? $model->qr_code : $create_qr;
    //     $data['no_wo'] = $model->inspecting && $model->inspecting->wo && $model->inspecting->wo->no ? $model->inspecting->wo->no : '-';
    //     $data['k3l_code'] = $model->inspecting && $model->inspecting->k3l_code ? $model->inspecting->k3l_code : '-';
    //     $data['color'] = $model->inspecting && $model->inspecting->moColor ? $model->inspecting->moColor->color : '-';
    //     $data['is_design_or_artikel'] = $is_design_or_article ? $is_design_or_article : '-';
    //     $data['length'] = str_replace(' ', '', $model->qty_sum.' '.($model->inspecting->satuan == 1 ? 'YDS / '.$getMeter.' M' : ($model->inspecting->satuan == 2 ? 'M' : ($model->inspecting->satuan == 3 ? 'PCS' : 'KG'))));
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
    //     $data['qr_code_desc'] = $model->qr_code_desc ? ($model->qr_code_desc == $qr_code_desc ? $model->qr_code_desc : $qr_code_desc) : $qr_code_desc;

    //     $transaction = Yii::$app->db->beginTransaction();
    //     try {
    //         $query = $this->findItem($id);
    //         $query['qr_code'] = $query->qr_code ? $query->qr_code : $create_qr;
    //         $query['qr_code_desc'] = $query->qr_code_desc ? ($query->qr_code_desc == $qr_code_desc ? $query->qr_code_desc : $qr_code_desc) : $qr_code_desc;
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
        $create_qr = 'MKL-' . $model->inspecting_id . '-' . $model->id;

        // Cek artikel / design
        if ($model->inspecting->jenis == 1) {
            $is_design_or_article = $model->inspecting->wo->mo->article;
        } else {
            $articleIsNotNull = $model->inspecting->wo->mo->article ? '/' : '';
            $is_design_or_article = $model->inspecting->wo->mo->article . $articleIsNotNull . $model->inspecting->wo->mo->design;
        }

        $getWidth = $model->inspecting->wo && $model->inspecting->wo->scGreige && $model->inspecting->wo->scGreige->lebar_kain
            ? TrnScGreige::lebarKainOptions()[$model->inspecting->wo->scGreige->lebar_kain]
            : '-';

        $query_builder = new Query;
        $grade_alias = $query_builder
            ->select('*')
            ->from('wms_grade')
            ->where(['=', 'grade_from', ($model->grade_up !== null ? $model->grade_up : $model->grade)])
            ->one();
        $getGrade = $grade_alias['grade_to'];

        // Konversi meter
        $roundUp = ($param5 != 0);
        $getMeter = $roundUp
            ? round($model->qty_sum * 0.9144, 1)
            : round($model->qty_sum * 0.9144, 2);

        // Menentukan urutan berdasarkan no_urut jika ada
        $inspectingMklbjItemCount = [];
        $hasNoUrut = InspectingMklBjItems::find()
            ->where(['inspecting_id' => $model->inspecting_id])
            ->andWhere(['IS NOT', 'no_urut', null])
            ->exists();

        $orderedItems = InspectingMklBjItems::find()
            ->where(['inspecting_id' => $model->inspecting_id])
            ->orderBy($hasNoUrut ? 'no_urut ASC' : 'id ASC')
            ->all();

        foreach ($orderedItems as $item) {
            $inspectingMklbjItemCount[] = $item->id;
        }

        $key = array_search($model->id, $inspectingMklbjItemCount);

        // Data untuk QR
        $data = [];
        $data['qr_code'] = $model->qr_code ?: $create_qr;
        $data['no_wo'] = $model->inspecting->wo->no ?? '-';
        $data['k3l_code'] = $model->inspecting->k3l_code ?? '-';
        $data['color'] = $model->inspecting->moColor->color ?? '-';
        $data['is_design_or_artikel'] = $is_design_or_article ?: '-';
        $data['length'] = str_replace(' ', '', $model->qty_sum . ' ' . ($model->inspecting->satuan == 1
            ? 'YDS / ' . $getMeter . ' M'
            : ($model->inspecting->satuan == 2
                ? 'M'
                : ($model->inspecting->satuan == 3
                    ? 'PCS'
                    : 'KG'))));
        $data['no_lot'] = $model->inspecting->no_lot ? $model->inspecting->no_lot . '/' . ($key + 1) : '-';
        $data['qty_count'] = str_pad($model->qty_count, 3, '0', STR_PAD_LEFT);
        $data['grade'] = $getWidth . '"/' . $getGrade;
        $data['defect'] = str_replace(',', '|', $model->defect);
        $data['param3'] = $param3;
        $data['param4'] = $param4;

        $production = $param3 == 1 ? 'MADE IN INDONESIA' : '';
        $regisk3l = $param4 == 1 ? 'REGISTRASI K3L!' . $data['k3l_code'] : '';

        $qr_code_desc = $regisk3l .
            '!' . $data['no_wo'] .
            '!' . $data['is_design_or_artikel'] .
            '!' . $data['color'] .
            '!' . $data['no_lot'] .
            '!' . $data['length'] .
            '!' . $data['grade'] .
            '!' . $data['defect'] .
            '!' . $production;

        $data['qr_code_desc'] = $model->qr_code_desc
            ? ($model->qr_code_desc == $qr_code_desc ? $model->qr_code_desc : $qr_code_desc)
            : $qr_code_desc;

        // Simpan ke DB
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $query = $this->findItem($id);
            $query->qr_code = $query->qr_code ?: $create_qr;
            $query->qr_code_desc = $query->qr_code_desc == $qr_code_desc ? $query->qr_code_desc : $qr_code_desc;
            $query->qr_print_at = $query->qr_print_at ?: date('Y-m-d H:i:s');
            $query->save();
            $transaction->commit();
        } catch (\Throwable $t) {
            $transaction->rollBack();
            throw $t;
        }

        // Render PDF
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


    public function actionQrAllWithoutAttribute($id, $param1, $param2, $param6)
    {
        $model = $this->findModel($id);
        $data = [];
        $itemsQuery = $model->getItems();
        $inspectingId = $model->id;

        $hasNoUrut = \common\models\ar\InspectingMklBjItems::find()
            ->where(['inspecting_id' => $inspectingId])
            ->andWhere(['IS NOT', 'no_urut', null])
            ->exists();

        $items = $itemsQuery
            ->orderBy($hasNoUrut ? 'no_urut ASC' : 'id ASC')
            ->all();
        foreach ($items as $key => $iI) {
            $getMeter = round($iI->qty_sum * 0.9144, 1);
            $create_qr = 'MKL-'.$iI->inspecting->id.'-'.$iI->id;
            if ($iI->is_head == 1) {
                // $countKey = strlen($iI->qty_count) == 1 ? '00' : (strlen($iI->qty_count) == 2 ? '0' : '');
                $countItems = strlen($iI->qty_count) == 1 ? '00' : (strlen($iI->qty_count) == 2 ? '0' : '');

                $is_design_or_article = NULL;
                if ($iI->inspecting->jenis == 1) { //1 == dyeing
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
                $color = $iI->inspecting && $iI->inspecting->moColor->color ? $iI->inspecting->moColor->color : '-';
                $k3l_code = $iI->inspecting && $iI->inspecting->k3l_code ? $iI->inspecting->k3l_code : '-';
                $length = str_replace(' ', '', $iI->qty_sum.' '.($iI->inspecting->satuan == 1 ? 'YDS / '.$getMeter.' M' : ($iI->inspecting->satuan == 2 ? 'M' : ($iI->inspecting->satuan == 3 ? 'PCS' : 'KG'))));
                $no_lot = $iI->inspecting && $iI->inspecting->no_lot ? $iI->inspecting->no_lot.'/'.($key+1) : '-';
                $qty_count = $countItems.($key+1).'/'.$countItems.count($model->items);
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

    public function actionQrAll($id, $param1, $param2,$param6)
    {
        $model = $this->findModel($id);
        $data = [];
        $itemsQuery = $model->getItems();
        $inspectingId = $model->id;

        $hasNoUrut = \common\models\ar\InspectingMklBjItems::find()
            ->where(['inspecting_id' => $inspectingId])
            ->andWhere(['IS NOT', 'no_urut', null])
            ->exists();

        $items = $itemsQuery
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
            $create_qr = 'MKL-'.$iI->inspecting->id.'-'.$iI->id;
            if ($iI->is_head == 1) {
                // $countKey = strlen($iI->qty_count) == 1 ? '00' : (strlen($iI->qty_count) == 2 ? '0' : '');
                $countItems = strlen($iI->qty_count) == 1 ? '00' : (strlen($iI->qty_count) == 2 ? '0' : '');

                $is_design_or_article = NULL;
                if ($iI->inspecting->jenis == 1) { //1 == dyeing
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
                $color = $iI->inspecting && $iI->inspecting->moColor->color ? $iI->inspecting->moColor->color : '-';
                $k3l_code = $iI->inspecting && $iI->inspecting->k3l_code ? $iI->inspecting->k3l_code : '-';
                $length = str_replace(' ', '', $iI->qty_sum.' '.($iI->inspecting->satuan == 1 ? 'YDS / '.$getMeter.' M' : ($iI->inspecting->satuan == 2 ? 'M' : ($iI->inspecting->satuan == 3 ? 'PCS' : 'KG'))));
                $no_lot = $iI->inspecting && $iI->inspecting->no_lot ? $iI->inspecting->no_lot.'/'.($key+1) : '-';
                $qty_count = $countItems.($key+1).'/'.$countItems.count($model->items);
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
    // public function actionQrAll($id, $param1, $param2,$param6)
    // {
    //     $model = $this->findModel($id);
    //     $data = [];
    //     foreach ($model->getItems()->orderBy('id ASC')->all() as $key => $iI) {
    //         $roundUp = true;
    //         if($param6 == 0){
    //             $roundUp = false;
    //         }
    //         if($roundUp){
    //             $getMeter = round($iI->qty_sum * 0.9144, 1);
    //         }else{
    //             $getMeter = round($iI->qty_sum * 0.9144, 2);
    //         }
    //         $create_qr = 'MKL-'.$iI->inspecting->id.'-'.$iI->id;
    //         if ($iI->is_head == 1) {
    //             // $countKey = strlen($iI->qty_count) == 1 ? '00' : (strlen($iI->qty_count) == 2 ? '0' : '');
    //             $countItems = strlen($iI->qty_count) == 1 ? '00' : (strlen($iI->qty_count) == 2 ? '0' : '');

    //             $is_design_or_article = NULL;
    //             if ($iI->inspecting->jenis == 1) { //1 == dyeing
    //                 $is_design_or_article = $iI->inspecting->wo->mo->article;
    //             } else { //2 == printing && //3 == pfp
    //                 $articleIsNotNull = $iI->inspecting->wo->mo->article ? '/' : '';
    //                 $is_design_or_article = $iI->inspecting->wo->mo->article.$articleIsNotNull.$iI->inspecting->wo->mo->design;
    //             }

    //             $getWidth = $iI->inspecting->wo && $iI->inspecting->wo->scGreige && $iI->inspecting->wo->scGreige->lebar_kain ? TrnScGreige::lebarKainOptions()[$iI->inspecting->wo->scGreige->lebar_kain] : '-';
    //             $query_builder = new Query;
    //             $grade_alias = $query_builder->select('*')->from('wms_grade')->where(['=', 'grade_from', ($iI->grade_up <> NULL ? $iI->grade_up : $iI->grade)])->one();
    //             // $getGrade = $iI->gradeOptions()[($iI->grade_up <> NULL ? $iI->grade_up : $iI->grade)];
    //             $getGrade = $grade_alias['grade_to'];

    //             $no_wo = $iI->inspecting && $iI->inspecting->wo && $iI->inspecting->wo->no ? $iI->inspecting->wo->no : '-';
    //             $color = $iI->inspecting && $iI->inspecting->moColor->color ? $iI->inspecting->moColor->color : '-';
    //             $k3l_code = $iI->inspecting && $iI->inspecting->k3l_code ? $iI->inspecting->k3l_code : '-';
    //             $length = str_replace(' ', '', $iI->qty_sum.' '.($iI->inspecting->satuan == 1 ? 'YDS / '.$getMeter.' M' : ($iI->inspecting->satuan == 2 ? 'M' : ($iI->inspecting->satuan == 3 ? 'PCS' : 'KG'))));
    //             $no_lot = $iI->inspecting && $iI->inspecting->no_lot ? $iI->inspecting->no_lot.'/'.($key+1) : '-';
    //             $qty_count = $countItems.($key+1).'/'.$countItems.count($model->items);
    //             $grade = $getWidth.'"/'.$getGrade;
    //             // $motif_greige = $iI->inspecting->wo->mo->scGreige->greigeGroup->nama_kain;
    //             $defect = str_replace(',', '|', $iI->defect);

    //             $production = $param1 == 1 ? 'MADE IN INDONESIA' : ''; // <-- jika ceklis, di munculkan, jika tidak di ceklis tidak di munculkan
    //             $regisk3l = $param2 == 1 ? 'REGISTRASI K3L!'.$k3l_code : ''; // <-- jika ceklis, di munculkan, jika tidak di ceklis tidak di munculkan

    //             $qr_code_desc = $regisk3l.
    //                             '!'.$no_wo.
    //                             '!'.$is_design_or_article.
    //                             '!'.$color.
    //                             '!'.$no_lot.
    //                             '!'.$length.
    //                             '!'.$grade.
    //                             // '!'.$motif_greige.
    //                             '!'.$defect;
    //                             '!'.$production;

    //             $data[] = [
    //                 'param1' => $param1,
    //                 'param2' => $param2,
    //                 'qr_code' => $iI->qr_code ? $iI->qr_code : $create_qr,
    //                 'no_wo' => $no_wo,
    //                 'color' => $color,
    //                 'k3l_code' => $k3l_code,
    //                 'is_design_or_artikel' => $is_design_or_article ? $is_design_or_article : '-',
    //                 'length' => $length,
    //                 'no_lot' => $no_lot,
    //                 'qty_count' => $qty_count,
    //                 'grade' => $grade,
    //                 'qr_code_desc' => $iI->qr_code_desc ? ($iI->qr_code_desc == $qr_code_desc ? $iI->qr_code_desc : $qr_code_desc) : $qr_code_desc,
    //             ];

    //             $transaction = Yii::$app->db->beginTransaction();
    //             try {
    //                 $iI->qr_code = $iI->qr_code ? ($iI->qr_code == $create_qr ? $iI->qr_code : $create_qr) : $create_qr;
    //                 $iI->qr_code_desc = $iI->qr_code_desc ? ($iI->qr_code_desc == $qr_code_desc ? $iI->qr_code_desc : $qr_code_desc) : $qr_code_desc;
    //                 $iI->qr_print_at = $iI->qr_print_at ? $iI->qr_print_at : date('Y-m-d H:i:s');
    //                 $iI->save();
    //                 $transaction->commit();
    //             }catch (\Throwable $t){
    //                 $transaction->rollBack();
    //                 throw $t;
    //             }
    //         }
    //     }
    //     $content = $this->renderPartial('qr-all', ['model' => $data]);
    //     // setup kartik\mpdf\Pdf component
    //     $pdf = new Pdf([
    //         'mode' => Pdf::MODE_BLANK,
    //         'format' => [100,50], //THERMAL 100mm x 50mm
    //         'orientation' => Pdf::ORIENT_PORTRAIT,
    //         'destination' => Pdf::DEST_BROWSER,
    //         'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
    //         'methods' => [
    //             'SetTitle'=>'InspectId: '.$model['id'],
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

    protected function findItem($id)
    {
        if (($model = InspectingMklBjItems::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the InspectingMklBj model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InspectingMklBj the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InspectingMklBj::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findItemInStock($id)
    {
        if (($model = TrnGudangJadi::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}