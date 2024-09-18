<?php

namespace backend\controllers;

use backend\components\Converter;
use common\models\ar\MstGreigeGroup;
use common\models\ar\NomorSuratJalan;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnKirimBuyer;
use common\models\ar\TrnKirimBuyerItem;
use common\models\ar\TrnWo;
use Yii;
use common\models\ar\TrnKirimBuyerHeader;
use common\models\ar\TrnKirimBuyerHeaderSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TrnKirimBuyerHeaderController implements the CRUD actions for TrnKirimBuyerHeader model.
 */
class TrnKirimBuyerHeaderController extends Controller
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
     * Lists all TrnKirimBuyerHeader models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnKirimBuyerHeaderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnKirimBuyerHeader model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $qStocks = TrnGudangJadi::find()
            ->joinWith('wo.mo.scGreige.sc')
            ->where(['trn_gudang_jadi.status'=>TrnGudangJadi::STATUS_SIAP_KIRIM])
            ->andWhere(['trn_sc.cust_id'=>$model->customer_id])
            ->orderBy('id')
        ;

        $providerStock = new ActiveDataProvider([
            'query' => $qStocks,
            'pagination' => false,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ]
            ],
        ]);

        $dataProviderKirimBuyer = new ActiveDataProvider([
            'query' => $model->getTrnKirimBuyers(),
            'pagination' => false,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ]
            ],
        ]);

        return $this->render('view', [
            'model' => $model,
            'providerStock'=>$providerStock,
            'dataProviderKirimBuyer'=>$dataProviderKirimBuyer
        ]);
    }

    /**
     * Creates a new TrnKirimBuyerHeader model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnKirimBuyerHeader();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnKirimBuyerHeader model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnKirimBuyerHeader model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function actionAmbil($id){
        if(Yii::$app->request->isAjax){
            $model = $this->findModel($id);

            if($model->status != $model::STATUS_DRAFT){
                throw new ForbiddenHttpException('Status tidak valid, tidak bisa diproses. (1)');
            }

            Yii::$app->response->format = Response::FORMAT_JSON;

            $datas = Yii::$app->request->post('formData');

            if(empty($datas)){
                throw new ForbiddenHttpException('Data kosong, tidak bisa diproses. (2)');
            }

            /* @var $stocks TrnGudangJadi[]*/
            $stocks = TrnGudangJadi::find()->where(['id'=>$datas])->all();

            $wos = [];
            foreach ($stocks as $stock) {
                if($stock->status != $stock::STATUS_SIAP_KIRIM){
                    throw new ForbiddenHttpException('Salah satu stock tidak valid, coba lagi.');
                }

                $wo = $stock->wo;
                if(isset($wos[$wo->no])){
                    $wos[$wo->no]['items'][] = $stock;
                }else{
                    $wos[$wo->no]['wo'] = $wo;
                    $wos[$wo->no]['items'] = [$stock];
                }
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = false;

                foreach ($wos as $wo) {
                    /* @var $woModel TrnWo*/
                    $woModel = $wo['wo'];

                    $modelKirimBuyer = new TrnKirimBuyer([
                        'header_id' => $model->id,
                        'sc_id' => $woModel->sc_id,
                        'sc_greige_id' => $woModel->sc_greige_id,
                        'mo_id' => $woModel->mo_id,
                        'wo_id' => $woModel->id,
                        'nama_kain_alias' => $woModel->greige->nama_kain,
                        'unit' => $wo['items'][0]['unit'],
                    ]);

                    $kmColor = '-';
                    if(!empty($modelKirimBuyer->wo->trnWoColors)){
                        $kmColor = $modelKirimBuyer->wo->trnWoColors[0]->moColor->color;
                    }
                    $modelKirimBuyer->note = $modelKirimBuyer->sc->no_po .' '.$kmColor;

                    if(!$flag = $modelKirimBuyer->save(false)){
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal memproses, coba lagi. (1)');
                    }

                    foreach ($wo['items'] as $item) {
                        /* @var $item TrnGudangJadi*/
                        $modelKirimBuyerItem = new TrnKirimBuyerItem([
                            'kirim_buyer_id' => $modelKirimBuyer->id,
                            'stock_id' => $item->id,
                            'qty' => $item->qty,
                            'note' => '-',
                        ]);

                        if(!$flag = $modelKirimBuyerItem->save(false)){
                            $transaction->rollBack();
                            throw new HttpException(500, 'Gagal memproses, coba lagi. (2)');
                        }

                        $item->status = $item::STATUS_SURAT_JALAN;
                        if(!$flag = $item->save(false, ['status'])){
                            $transaction->rollBack();
                            throw new HttpException(500, 'Gagal memproses, coba lagi. (2)');
                        }
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
     * Updates an existing TrnKirimBuyerHeader model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function actionKembalikan($id){
        if(Yii::$app->request->isAjax){
            $model = $this->findModel($id);

            if($model->status != $model::STATUS_DRAFT){
                throw new ForbiddenHttpException('Status tidak valid, tidak bisa diproses. (1)');
            }

            Yii::$app->response->format = Response::FORMAT_JSON;

            $datas = Yii::$app->request->post('formData');

            if(empty($datas)){
                throw new ForbiddenHttpException('Data kosong, tidak bisa diproses. (2)');
            }

            $kirimBuyerIds = [];
            $kirimBuyerItemIds = [];

            /* @var $modelsStock TrnGudangJadi[]*/
            $modelsStock = [];

            $modelsKirimBuyer = $model->trnKirimBuyers;
            foreach ($modelsKirimBuyer as $modelKirimBuyer) {
                $kirimBuyerIds[] = $modelKirimBuyer->id;

                /* @var $items TrnKirimBuyerItem[]*/
                $items = $modelKirimBuyer->getTrnKirimBuyerItems()->where(['id'=>$datas])->all();
                foreach ($items as $trnKirimBuyerItem) {
                    $kirimBuyerItemIds[] = $trnKirimBuyerItem->id;
                    $modelsStock[] = $trnKirimBuyerItem->stock;
                }
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = false;

                foreach ($modelsStock as $modelStock) {
                    $modelStock->status = $modelStock::STATUS_SIAP_KIRIM;
                    if(!$flag = $modelStock->save(false, ['status'])){
                        $transaction->rollBack();
                        throw new HttpException(500, 'Gagal memproses, coba lagi. (3)');
                    }
                }

                TrnKirimBuyerItem::deleteAll(['id'=>$kirimBuyerItemIds]);

                $kirimBuyerIdsTodelete = [];

                foreach ($kirimBuyerIds as $kirimBuyerId){
                    $kirimBuyerItemsCount = TrnKirimBuyerItem::find()->where(['kirim_buyer_id'=>$kirimBuyerId])->count('id');
                    if(($kirimBuyerItemsCount >= 1) === false){
                        $kirimBuyerIdsTodelete[] = $kirimBuyerId;
                    }
                }

                if(!empty($kirimBuyerIdsTodelete)){
                    TrnKirimBuyer::deleteAll(['id'=>$kirimBuyerIdsTodelete]);
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
     * Deletes an existing TrnKirimBuyerHeader model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid, tidak bisa dihapus. (1)');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $flag = false;

            foreach ($model->trnKirimBuyers as $trnKirimBuyer) {
                foreach ($trnKirimBuyer->trnKirimBuyerItems as $trnKirimBuyerItem) {
                    $stock = $trnKirimBuyerItem->stock;
                    $stock->status = $stock::STATUS_SIAP_KIRIM;
                    if($stock->save(false, ['status']) === false){
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('error', 'Gagal memproses, coba lagi. (2)');
                        return $this->redirect(['view', 'id' => $model->id]);
                    }

                    if($trnKirimBuyerItem->delete() === false){
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('error', 'Gagal memproses, coba lagi. (3)');
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                }

                if($trnKirimBuyer->delete() === false){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal memproses, coba lagi. (4)');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }

            if($model->delete() === false){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal memproses, coba lagi. (5)');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            $transaction->commit();
            return $this->redirect(['index']);
        }catch (\Throwable $t){
            $transaction->rollBack();
            throw $t;
        }
    }

    /**
     * Deletes an existing TrnKirimBuyerHeader model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPosting($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DRAFT){
            Yii::$app->session->setFlash('error', 'Status tidak valid, tidak bisa diposting. (1)');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->status = $model::STATUS_POSTED;
        //$model->setNomor();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $modelNo = new NomorSuratJalan([
                'date' => date('Y-m-d'),
                'created_at' => time()
            ]);
            $modelNo->setNomor();
            if(!$flag = $modelNo->save(false)){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal memproses, coba lagi. (1)');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            $model->no_urut = $modelNo->no_urut;
            $model->no = $modelNo->no;
            if(!$flag = $model->save(false, ['status', 'no', 'no_urut'])){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal memproses, coba lagi. (2)');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            foreach ($model->trnKirimBuyers as $trnKirimBuyer) {
                foreach ($trnKirimBuyer->trnKirimBuyerItems as $trnKirimBuyerItem) {
                    $stock = $trnKirimBuyerItem->stock;
                    $stock->status = $stock::STATUS_OUT;
                    if(!$flag = $stock->save(false, ['status'])){
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('error', 'Gagal memproses, coba lagi. (3)');
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                }
            }

            if($flag){
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Posting berhasil.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }catch (\Throwable $t){
            $transaction->rollBack();
            throw $t;
        }
    }

    /**
     * Finds the TrnKirimBuyerHeader model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnKirimBuyerHeader the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnKirimBuyerHeader::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
