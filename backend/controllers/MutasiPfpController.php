<?php

namespace backend\controllers;

use Yii;
use common\models\ar\MutasiPfp;
use common\models\ar\MutasiPfpSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;

/**
 * MutasiPfpController implements the CRUD actions for MutasiPfp model.
 */
class MutasiPfpController extends Controller
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
     * Lists all MutasiPfp models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MutasiPfpSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MutasiPfp model.
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
     * Creates a new MutasiPfp model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $searchModel = new \common\models\ar\TrnStockGreigeSearch([
            'jenis_gudang'=>\common\models\ar\TrnStockGreige::JG_PFP,
            'status'=>\common\models\ar\TrnStockGreige::STATUS_VALID
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $model = new MutasiPfp(['date'=>date('Y-m-d')]);

        if (Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if ($model->load(Yii::$app->request->post())) {
                $items = \yii\helpers\Json::decode(Yii::$app->request->post('items'));
                if (empty($items)) {
                    return ['validation' => ['items' => 'Tidak ada item yang dipilih.']];
                }

                $firstItemStock = \common\models\ar\TrnStockGreige::findOne($items[0]['id']);
                if ($firstItemStock) {
                    $model->greige_id = $firstItemStock->greige_id;
                    $model->greige_group_id = $firstItemStock->greige->group_id;
                }

                if ($model->validate()) {
                    $transaction = Yii::$app->db->beginTransaction();

                    try {
                        if ($flag = $model->save(false)) {
                            foreach ($items as $item) {
                                $modelItem = new \common\models\ar\MutasiPfpItem([
                                    'mutasi_id' => $model->id,
                                    'stock_pfp_id' => $item['id'],
                                ]);
                                if (! ($flag = $modelItem->save(false))) {
                                    $transaction->rollBack();
                                    throw new \yii\web\HttpException(500, 'Gagal, coba lagi. (2)');
                                }
                            }
                        } else {
                            $transaction->rollBack();
                            throw new \yii\web\HttpException(500, 'Gagal, coba lagi. (1)');
                        }

                        if ($flag) {
                            $transaction->commit();
                            return ['success'=>true, 'redirect'=>\yii\helpers\Url::to(['view', 'id'=>$model->id])];
                        }
                    } catch (\Throwable $t) {
                        $transaction->rollBack();
                        throw $t;
                    }
                } else {
                    return ['validation' => \yii\widgets\ActiveForm::validate($model)];
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if($model->status != MutasiPfp::STATUS_DRAFT){
            throw new \yii\web\ForbiddenHttpException('Status tidak valid. Tidak bisa dirubah.');
        }

        $searchModel = new \common\models\ar\TrnStockGreigeSearch([
            'jenis_gudang'=>\common\models\ar\TrnStockGreige::JG_PFP,
            'status'=>\common\models\ar\TrnStockGreige::STATUS_VALID
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if (!($flag = $model->save(false))) {
                        $transaction->rollBack();
                        throw new \yii\web\HttpException(500, 'Gagal, coba lagi. (1)');
                    }

                    \common\models\ar\MutasiPfpItem::deleteAll(['mutasi_id'=>$model->id]);

                    foreach (\yii\helpers\Json::decode(Yii::$app->request->post('items')) as $item) {
                        $modelItem = new \common\models\ar\MutasiPfpItem([
                            'mutasi_id' => $model->id,
                            'stock_pfp_id' => $item['id'],
                        ]);
                        if (! ($flag = $modelItem->save(false))) {
                            $transaction->rollBack();
                            throw new \yii\web\HttpException(500, 'Gagal, coba lagi. (2)');
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        return ['success'=>true, 'redirect'=>\yii\helpers\Url::to(['view', 'id'=>$model->id])];
                    }
                }catch (\Throwable $t) {
                    $transaction->rollBack();
                    throw $t;
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Deletes an existing MutasiPfp model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model->status != MutasiPfp::STATUS_DRAFT){
            throw new \yii\web\ForbiddenHttpException('Status tidak valid. Tidak bisa dihapus.');
        }

        \common\models\ar\MutasiPfpItem::deleteAll(['mutasi_id'=>$model->id]);
        $model->delete();

        return $this->redirect(['index']);
    }

    public function actionPosting($id)
    {
        $model = $this->findModel($id);
        if($model->status != MutasiPfp::STATUS_DRAFT){
            throw new \yii\web\ForbiddenHttpException('Status tidak valid. Tidak bisa diposting.');
        }

        $model->approval_time = time();
        $model->approval_id = Yii::$app->user->id;
        $model->status = MutasiPfp::STATUS_POSTED;
        $model->setNomor();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(!($flag = $model->save(false, ['approval_time', 'approval_id', 'status', 'no_urut', 'no']))){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal menyimpan, coba lagi.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            $greigesKeluar = [];
            foreach ($model->mutasiPfpItems as $mutasiItem) {
                // Update status roll menjadi keluar gudang
                $updateStockStatus = MutasiPfp::getDb()->createCommand()->update(
                    \common\models\ar\TrnStockGreige::tableName(),
                    ['status'=>\common\models\ar\TrnStockGreige::STATUS_KELUAR_GUDANG],
                    ['id'=>$mutasiItem->stock_pfp_id]
                )->execute();
                
                if(!($flag = $updateStockStatus === 1)){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal menyimpan, coba lagi. (Gagal update status stock)');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                $stockGreige = $mutasiItem->stockPfp;
                $greige = $stockGreige->greige;

                if(!isset($greigesKeluar[$greige->id])){
                    $greigesKeluar[$greige->id] = 0;
                }
                $greigesKeluar[$greige->id] += $stockGreige->panjang_m;
            }

            if ($flag){
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Posting berhasil.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

        }catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function actionPrint($id){
        $model = $this->findModel($id);

        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('_print', ['model' => $model]);

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_BLANK,
            // A5 paper format
            'format' => 'A5',
            // landscape orientation
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '
                body{font-family: sans-serif; font-size:12px; letter-spacing: 0px;}
                table {font-family: sans-serif; width: 100%; font-size:12px; border-spacing: 0; letter-spacing: 0px;} th, td {padding: 0.2em 0.2em; vertical-align: top;}',
            // set mPDF properties on the fly
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle'=>'MUTASI PFP - '.$model->id,
                'SetFooter'=>['Page {PAGENO}'],
            ],
            'options' => [
                'title' => 'MUTASI PFP - '.$model->id,
            ],
        ]);

        if($model->status == MutasiPfp::STATUS_DRAFT){
            $pdf->methods['SetHeader'] = 'MUTASI PFP | DRAFT | ';
        }else{
            $pdf->methods['SetHeader'] = 'MUTASI PFP | NO:'.$model->no.' | ';
        }

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * Finds the MutasiPfp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MutasiPfp the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MutasiPfp::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
