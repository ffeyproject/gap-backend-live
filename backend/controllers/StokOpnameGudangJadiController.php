<?php

namespace backend\controllers;

use Yii;
use common\models\ar\TrnGudangJadiOpnamePcs;
use backend\models\TrnGudangJadiOpnamePcsSearch;
use backend\models\StokOpnameGudangJadiRekapSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * StokOpnameGudangJadiController implements CRUD & Rekap actions for TrnGudangJadiOpnamePcs.
 */
class StokOpnameGudangJadiController extends Controller
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
                    'save-location' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all TrnGudangJadiOpnamePcs models (Data Pcs).
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnGudangJadiOpnamePcsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $totalPcs = $dataProvider->getTotalCount();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totalPcs' => $totalPcs,
        ]);
    }

    /**
     * Rekap Stok Opname Gudang Jadi (by Motif, Color, Opname Code, Location, Grade & Status).
     * @return mixed
     */
    public function actionRekap()
    {
        $searchModel = new StokOpnameGudangJadiRekapSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // Summary cards
        $totalPcsAll = (new \yii\db\Query())->from('trn_gudang_jadi_opname_pcs')->count();
        $totalQtyAll = (new \yii\db\Query())->from('trn_gudang_jadi_opname_pcs')->sum('qty');
        $totalVerified = (new \yii\db\Query())->from('trn_gudang_jadi_opname_pcs')->where(['status' => TrnGudangJadiOpnamePcs::STATUS_VERIFIED])->count();
        $totalDraft = (new \yii\db\Query())->from('trn_gudang_jadi_opname_pcs')->where(['status' => TrnGudangJadiOpnamePcs::STATUS_DRAFT])->count();

        return $this->render('rekap', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totalPcsAll' => $totalPcsAll ?: 0,
            'totalQtyAll' => $totalQtyAll ?: 0,
            'totalVerified' => $totalVerified ?: 0,
            'totalDraft' => $totalDraft ?: 0,
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
     * Update location (locs_code) via AJAX
     * @return array
     */
    public function actionSaveLocation()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $location = Yii::$app->request->post('location');

        if (empty($id) || empty($location)) {
            return ['success' => false, 'message' => 'ID Stok Opname dan Lokasi harus diisi.'];
        }

        $model = $this->findModel($id);
        $model->locs_code = $location;

        if ($model->save(false, ['locs_code', 'updated_at', 'updated_by'])) {
            return ['success' => true, 'message' => 'Lokasi berhasil diperbarui.', 'location' => $model->locs_code];
        }

        return ['success' => false, 'message' => 'Gagal memperbarui lokasi.'];
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

        throw new NotFoundHttpException('Data Stok Opname tidak ditemukan.');
    }
}
