<?php

namespace backend\controllers;

use common\models\ar\MstGreigeGroup;
use common\models\ar\MutasiExFinishAlt;
use common\models\ar\MutasiExFinishAltItem;
use common\models\ar\TrnWo;
use common\models\User;
use Yii;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnGudangJadiSearch;
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
        //$dataProvider->pagination->pageSize = 3;

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
}
