<?php

namespace backend\controllers;

use common\models\ar\TrnStockGreige;
use Yii;
use common\models\ar\TrnKartuProsesMaklonItem;
use common\models\ar\TrnKartuProsesMaklonItemSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnKartuProsesMaklonItemController implements the CRUD actions for TrnKartuProsesMaklonItem model.
 */
class TrnKartuProsesMaklonItemController extends Controller
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
     * Lists all TrnKartuProsesMaklonItem models.
     * @return mixed
     */
    /*public function actionIndex()
    {
        $searchModel = new TrnKartuProsesMaklonItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }*/

    /**
     * Displays a single TrnKartuProsesMaklonItem model.
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
     * Total item pada kartu proses tidak boleh lebih dari panjang 1 batch greige nya, kecuali kelebihan pertama.
     * Masing2 total tube kiri dan tube kanan tidak boleh lebih dari setengah panjang 1 batch greige nya, kecuali kelebihan pertama.
     * Pemeriksaan diatas dilakukan setiap penambahan item kartu proses
     * @param $processId
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws HttpException
     */
    public function actionCreate($processId)
    {
        if(Yii::$app->request->isAjax){
            $model = new TrnKartuProsesMaklonItem([
                'kartu_process_id'=>$processId,
                'date'=>date('Y-m-d')
            ]);

            $kartuProses = $model->kartuProcess;

            if($kartuProses === null){
                throw new ForbiddenHttpException('ID Kartu Proses Tidak Valid.');
            }

            if($kartuProses->status != $kartuProses::STATUS_DRAFT){
                throw new ForbiddenHttpException('Status Kartu Proses Tidak Valid.');
            }

            $model->wo_id = $kartuProses->wo_id;
            $model->mo_id = $kartuProses->mo_id;
            $model->sc_greige_id = $kartuProses->sc_greige_id;
            $model->sc_id = $kartuProses->sc_id;
            $greige = $model->wo->greige;
            $perBatch = (float)$greige->group->qty_per_batch;
            $perBatchInPercent = 0.02 * $perBatch; //dua persen dari satu batch
            $perBatchToleransiAtas = $perBatch + $perBatchInPercent;

            if ($model->load(Yii::$app->request->post())) {
                if(!$model->validate()){
                    $result = [];
                    // The code below comes from ActiveForm::validate(). We do not need to validate the model
                    // again, as it was already validated by save(). Just collect the messages.
                    foreach ($model->getErrors() as $attribute => $errors) {
                        $result[Html::getInputId($model, $attribute)] = $errors;
                    }

                    return $this->asJson(['validation' => $result]);
                }

                $model->panjang_m = (float)$model->stock->panjang_m;
                $total = $kartuProses->getTrnKartuProsesMaklonItems()->sum('panjang_m');
                $total = $total !== null ? $total : 0;
                $total += $model->panjang_m;
                if($total > $perBatchToleransiAtas){
                    //kelebihan diatas 2% dari panjang per batch dibagi dua diizinkan.
                    throw new ForbiddenHttpException('Jumlah greige tidak valid, hanya kelebihan sebanyak 2% dari per batch dibagi dua yang diizinkan.');
                }

                if($model->save(false)){
                    return $this->asJson(['success' => true]);
                }

                $result = [];
                // The code below comes from ActiveForm::validate(). We do not need to validate the model
                // again, as it was already validated by save(). Just collect the messages.
                foreach ($model->getErrors() as $attribute => $errors) {
                    $result[Html::getInputId($model, $attribute)] = $errors;
                }

                return $this->asJson(['validation' => $result]);
            }

            //Data option list stock-------------------------------------------------------------
            $selectedItems = TrnKartuProsesMaklonItem::find()
                ->select(['id', 'stock_id'])
                ->where(['kartu_process_id'=>$processId])
                ->asArray()
                ->all();
            $selectedItemsIds = ArrayHelper::getColumn($selectedItems, 'stock_id');

            $qStockOptionList = TrnStockGreige::find()
                ->with('greige')
                ->where(['status'=>TrnStockGreige::STATUS_VALID, 'greige_id'=>$model->wo->greige_id])
                ->andWhere(['asal_greige'=>$kartuProses->asal_greige])
            ;
            if(!empty($selectedItemsIds)){
                $qStockOptionList->andWhere(['not in', 'id', $selectedItemsIds]);
            }
            $stockOptionList = $qStockOptionList->asArray()->all();
            $stockOptionListMap = ArrayHelper::map($stockOptionList, 'id', function($data) use ($greige){
                $panjang = Yii::$app->formatter->asDecimal($data['panjang_m']);
                return 'Greige: '.$data['greige']['nama_kain'].
                    ' | Grade: '.TrnStockGreige::gradeOptions()[$data['grade']].
                    ' | Lapak: '.$data['no_lapak'].
                    ' | Panjang: '.$panjang.'M | No Doc.: '.$data['no_document'].
                    ' | No. MC.: '.$data['no_set_lusi']
                    ;
            });

            $asalGreige = TrnStockGreige::asalGreigeOptions()[$kartuProses->asal_greige];
            $jenisGudang = TrnStockGreige::jenisGudangOptions()[$model->wo->mo->jenis_gudang];
            $searchHint = "Mencari Greige {$greige->nama_kain}, Status Valid, Asal Greige {$asalGreige}, Jenis Gudang {$jenisGudang}.";
            //Data option list stock-------------------------------------------------------------

            return $this->renderAjax('create', [
                'model'=>$model,
                'stockOptionListMap'=>$stockOptionListMap,
                'searchHint'=>$searchHint
            ]);
        }

        throw new ForbiddenHttpException('Not allowed');
    }

    /**
     * Updates an existing TrnKartuProsesMaklonItem model.
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
     * Deletes an existing TrnKartuProsesMaklonItem model.
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

        $kartuProses = $model->kartuProcess;
        if($kartuProses->status != $kartuProses::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status Kartu Proses ini tidak valid untuk dihapus.');
            return $this->redirect(['/trn-kartu-proses-maklon/view', 'id'=>$kartuProses->id]);
        }

        $model->delete();

        Yii::$app->session->setFlash('success', 'Item berhasil dihapus.');
        return $this->redirect(['/trn-kartu-proses-maklon/view', 'id'=>$kartuProses->id]);
    }

    /**
     * Finds the TrnKartuProsesMaklonItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnKartuProsesMaklonItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnKartuProsesMaklonItem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
