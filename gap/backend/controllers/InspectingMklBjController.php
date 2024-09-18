<?php

namespace backend\controllers;

use backend\models\form\InspectingMklBjHeaderForm;
use backend\models\form\InspectingMklBjItemsForm;
use common\models\ar\InspectingMklBjItems;
use kartik\mpdf\Pdf;
use Yii;
use common\models\ar\InspectingMklBj;
use common\models\ar\InspectingMklBjSearch;
use yii\base\BaseObject;
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
                                'join_piece' => $item['join_piece'],
                                'qty' => $item['qty'],
                                'note' => $item['note'],
                            ]);

                            if(!($flag = $modelItem->save(false))){
                                $transaction->rollBack();
                                throw new HttpException(500, 'Gagal, coba lagi. (2)');
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

        return $this->render('create', [
            'model' => $model,
            'modelItem' => $modelItem
        ]);
    }

    /**
     * Updates an existing InspectingMklBj model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelItem = new InspectingMklBjItems();
        $items = ArrayHelper::toArray($model->getItems()->orderBy('id')->all());

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

                        $dataItems = Json::decode(Yii::$app->request->post('items'));

                        $oldIDs = ArrayHelper::map($items, 'id', 'id');
                        $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($dataItems, 'id', 'id')));
                        if (!empty($deletedIDs)) {
                            InspectingMklBjItems::deleteAll(['id' => $deletedIDs]);
                        }

                        foreach ($dataItems as $item) {
                            if($item['id'] === null){
                                Yii::$app->db->createCommand()->insert(InspectingMklBjItems::tableName(), [
                                    'inspecting_id' => $model->id,
                                    'grade' => $item['grade'],
                                    'join_piece' => $item['join_piece'],
                                    'qty' => $item['qty'],
                                    'note' => $item['note'],
                                ])->execute();
                            }else{
                                Yii::$app->db->createCommand()->update(InspectingMklBjItems::tableName(), [
                                    'grade' => $item['grade'],
                                    'join_piece' => $item['join_piece'],
                                    'qty' => $item['qty'],
                                    'note' => $item['note'],
                                ], ['id'=>$item['id']])->execute();
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

        if($model->status == $model::STATUS_DRAFT){
            $pdf->methods['SetHeader'] = 'Inspecting Makloon & Barang Jadi | ID:'.$model->id.' | DRAFT';
        }else{
            if($model->status == $model::STATUS_POSTED){
                $pdf->methods['SetHeader'] = 'Inspecting Makloon & Barang Jadi - | ID:'.$model->id.' | NO:'.$model->no;
            }else $pdf->methods['SetHeader'] = 'Inspecting Makloon & Barang Jadi - | ID:'.$model->id.' | MENUNGGU PERSETUJUAN';
        }

        $pdf->methods['SetFooter'] = 'Page {PAGENO}';

        // return the pdf output as per the destination setting
        return $pdf->render();
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
}
