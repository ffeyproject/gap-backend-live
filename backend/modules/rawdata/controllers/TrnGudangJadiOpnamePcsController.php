<?php

namespace backend\modules\rawdata\controllers;

use Yii;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadi;
use backend\modules\rawdata\models\TrnGudangJadiOpnamePcs;
use backend\modules\rawdata\models\TrnGudangJadiOpnamePcsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;

/**
 * TrnGudangJadiOpnamePcsController implements the CRUD actions for TrnGudangJadiOpnamePcs model in Raw Data module.
 */
class TrnGudangJadiOpnamePcsController extends Controller
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
     * Lists all TrnGudangJadiOpnamePcs models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnGudangJadiOpnamePcsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrnGudangJadiOpnamePcs model.
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
     * Creates a new TrnGudangJadiOpnamePcs model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrnGudangJadiOpnamePcs();

        if ($model->load(Yii::$app->request->post())) {
            // Otomatis mengikuti satuan Gudang Jadi jika belum diset atau ada relasi
            if (!empty($model->id_trn_gudang_jadi) && empty($model->unit)) {
                $gj = TrnGudangJadi::findOne($model->id_trn_gudang_jadi);
                if ($gj && !empty($gj->unit)) {
                    $model->unit = (string)$gj->unit;
                }
            }
            if ($model->save()) {
                // Sinkronkan status ke master trn_gudang_jadi jika terhubung
                if (!empty($model->id_trn_gudang_jadi)) {
                    $gjStatus = ($model->status == TrnGudangJadiOpnamePcs::STATUS_OUT)
                        ? TrnGudangJadi::STATUS_OUT
                        : TrnGudangJadi::STATUS_STOCK;

                    $updateData = [
                        'status' => $gjStatus,
                        'updated_at' => time(),
                        'updated_by' => Yii::$app->user->id ?? null,
                    ];
                    if (!empty($model->locs_code)) {
                        $updateData['locs_code'] = $model->locs_code;
                    }
                    TrnGudangJadi::updateAll($updateData, ['id' => $model->id_trn_gudang_jadi]);
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnGudangJadiOpnamePcs model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // Otomatis sinkronkan/ikuti satuan dari master Gudang Jadi jika ada relasi
        if ($model->gudangJadi && !empty($model->gudangJadi->unit)) {
            if (empty($model->unit) || !is_numeric($model->unit)) {
                $model->unit = (string)$model->gudangJadi->unit;
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Sinkronkan status ke master trn_gudang_jadi jika terhubung
            if (!empty($model->id_trn_gudang_jadi)) {
                $gjStatus = ($model->status == TrnGudangJadiOpnamePcs::STATUS_OUT)
                    ? TrnGudangJadi::STATUS_OUT
                    : TrnGudangJadi::STATUS_STOCK;

                $updateData = [
                    'status' => $gjStatus,
                    'updated_at' => time(),
                    'updated_by' => Yii::$app->user->id ?? null,
                ];
                if (!empty($model->locs_code)) {
                    $updateData['locs_code'] = $model->locs_code;
                }
                TrnGudangJadi::updateAll($updateData, ['id' => $model->id_trn_gudang_jadi]);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TrnGudangJadiOpnamePcs model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * AJAX endpoint to get Gudang Jadi details (including unit/satuan).
     * @param integer $id
     * @return array
     */
    public function actionGetGudangJadi($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $gj = TrnGudangJadi::findOne($id);
        if ($gj === null) {
            return [
                'success' => false,
                'message' => 'Data Gudang Jadi ID ' . $id . ' tidak ditemukan.',
            ];
        }

        $units = MstGreigeGroup::unitOptions();
        $unitName = isset($units[$gj->unit]) ? $units[$gj->unit] : (string)$gj->unit;

        return [
            'success' => true,
            'id' => $gj->id,
            'unit' => (string)$gj->unit,
            'unit_name' => $unitName,
            'qty' => $gj->qty,
            'qr_code' => $gj->qr_code,
            'qr_code_desc' => $gj->qr_code_desc,
            'locs_code' => $gj->locs_code,
            'wo_no' => ($gj->wo) ? $gj->wo->no : '-',
            'color' => $gj->color ?: '-',
            'buyer' => ($gj->wo && $gj->wo->mo && $gj->wo->mo->scGreige && $gj->wo->mo->scGreige->sc && $gj->wo->mo->scGreige->sc->cust) ? $gj->wo->mo->scGreige->sc->cust->name : '-',
        ];
    }

    /**
     * Finds the TrnGudangJadiOpnamePcs model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnGudangJadiOpnamePcs the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnGudangJadiOpnamePcs::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
