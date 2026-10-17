<?php

namespace backend\modules\rawdata\controllers;

use Yii;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnGudangJadiSearch;
use common\models\ar\TrnGudangJadiOpnamePcs;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;

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
    public function actionIndex()
    {
        $searchModel = new TrnGudangJadiSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
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
    public function actionCreate()
    {
        $model = new TrnGudangJadi();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrnGudangJadi model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->status !== null) {
                $model->status = (int)$model->status;
            }
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {
                    // Sinkronkan Qty ke trn_gudang_jadi_opname_pcs sesuai relasi id_trn_gudang_jadi
                    // Status opname pcs tetap dipertahankan sesuai kondisi awalnya (tidak diubah menjadi OUT)
                    $userId = (!Yii::$app->user->isGuest) ? Yii::$app->user->id : null;
                    $updateAttributes = [
                        'qty' => $model->qty,
                        'updated_at' => time(),
                    ];
                    if ($userId !== null) {
                        $updateAttributes['updated_by'] = $userId;
                    }

                    // Jika status gudang jadi Stock, pastikan status opname pcs tidak berstatus OUT
                    if ((int)$model->status === TrnGudangJadi::STATUS_STOCK) {
                        TrnGudangJadiOpnamePcs::updateAll(
                            ['status' => TrnGudangJadiOpnamePcs::STATUS_STOCK],
                            [
                                'and',
                                ['id_trn_gudang_jadi' => $model->id],
                                ['status' => TrnGudangJadiOpnamePcs::STATUS_OUT]
                            ]
                        );
                    }

                    $syncedCount = TrnGudangJadiOpnamePcs::updateAll(
                        $updateAttributes,
                        ['id_trn_gudang_jadi' => $model->id]
                    );

                    $transaction->commit();

                    $msg = 'Data Gudang Jadi berhasil diperbarui.';
                    if ($syncedCount > 0) {
                        $msg .= " Qty pada {$syncedCount} record Stock Opname Pcs terkait juga telah disinkronkan ke {$model->qty}.";
                    }
                    Yii::$app->session->setFlash('success', $msg);

                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Gagal menyimpan: ' . Html::errorSummary($model));
                }
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage());
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TrnGudangJadi model.
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
     * AJAX endpoint to lookup inspecting item information
     * @param int $id
     * @param string $trans_from
     * @return array
     */
    public function actionGetInspectingInfo($id, $trans_from = 'INS')
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $qrCode = null;
        $qrCodeDesc = null;
        $inspectingId = null;
        $found = false;

        if ($trans_from === 'MKL') {
            $item = \common\models\ar\InspectingMklBjItems::findOne($id);
            if ($item) {
                $found = true;
                $inspectingId = $item->inspecting_id;
                $qrCode = $item->qr_code ?: ('MKL-' . $item->inspecting_id . '-' . $item->id);
                $qrCodeDesc = isset($item->qr_code_desc) ? $item->qr_code_desc : null;
            }
        } else {
            $item = \common\models\ar\InspectingItem::findOne($id);
            if ($item) {
                $found = true;
                $inspectingId = $item->inspecting_id;
                $qrCode = $item->qr_code ?: ('INS-' . $item->inspecting_id . '-' . $item->id);
                $qrCodeDesc = isset($item->qr_code_desc) ? $item->qr_code_desc : null;
            }
        }

        return [
            'success' => $found,
            'qr_code' => $qrCode,
            'qr_code_desc' => $qrCodeDesc,
            'inspecting_id' => $inspectingId,
        ];
    }

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
