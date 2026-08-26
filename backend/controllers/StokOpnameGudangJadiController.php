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
     * Render list of pcs items for a given opname_code, locs_code, motif, color, grade & status via AJAX modal
     * @return string
     */
    public function actionListPcsAjax()
    {
        $opname_code = Yii::$app->request->get('opname_code');
        $locs_code = Yii::$app->request->get('locs_code');
        $motif = Yii::$app->request->get('motif');
        $color = Yii::$app->request->get('color');
        $grade = Yii::$app->request->get('grade');
        $status = Yii::$app->request->get('status');

        $query = TrnGudangJadiOpnamePcs::find()
            ->alias('t')
            ->leftJoin(['gj' => 'trn_gudang_jadi'], 't.id_trn_gudang_jadi = gj.id')
            ->leftJoin(['wo' => 'trn_wo'], 'gj.wo_id = wo.id')
            ->leftJoin(['g_mst' => 'mst_greige'], 'wo.greige_id = g_mst.id')
            ->leftJoin(['mo' => 'trn_mo'], 'wo.mo_id = mo.id')
            ->leftJoin(['sc_g' => 'trn_sc_greige'], 'mo.sc_greige_id = sc_g.id')
            ->leftJoin(['g_group' => 'mst_greige_group'], 'sc_g.greige_group_id = g_group.id');

        if ($opname_code !== null && $opname_code !== '') {
            $query->andWhere(['t.opname_code' => $opname_code]);
        }

        if ($locs_code !== null && $locs_code !== '') {
            $query->andWhere(['t.locs_code' => $locs_code]);
        }

        if ($grade !== null && $grade !== '') {
            $query->andWhere(['t.grade' => (int)$grade]);
        }

        if ($status !== null && $status !== '') {
            $query->andWhere(['t.status' => (int)$status]);
        }

        if (!empty($color) && $color !== '-') {
            $query->andWhere(['gj.color' => $color]);
        } elseif ($color === '-') {
            $query->andWhere(['or', ['gj.color' => null], ['gj.color' => '']]);
        }

        if (!empty($motif) && $motif !== '-') {
            $query->andWhere(['or',
                ['g_group.nama_kain' => $motif],
                ['g_mst.nama_kain' => $motif],
                ['t.qr_code_desc' => $motif]
            ]);
        }

        $models = $query->orderBy(['t.id' => SORT_ASC])->all();

        return $this->renderAjax('_list_pcs_modal', [
            'models' => $models,
            'groupInfo' => [
                'opname_code' => $opname_code,
                'locs_code' => $locs_code,
                'motif' => $motif,
                'color' => $color,
                'grade' => $grade,
                'status' => $status,
            ]
        ]);
    }

    /**
     * Print Lembar Palet per Lokasi (Format 10 Kolom Piece Length, Grade & Total)
     * @param string $locs_code
     * @param string|null $opname_code
     * @return string
     */
    public function actionPrintLokasi($locs_code, $opname_code = null)
    {
        $query = TrnGudangJadiOpnamePcs::find()
            ->alias('t')
            ->leftJoin(['gj' => 'trn_gudang_jadi'], 't.id_trn_gudang_jadi = gj.id')
            ->leftJoin(['wo' => 'trn_wo'], 'gj.wo_id = wo.id')
            ->leftJoin(['g_mst' => 'mst_greige'], 'wo.greige_id = g_mst.id')
            ->leftJoin(['mo' => 'trn_mo'], 'wo.mo_id = mo.id')
            ->leftJoin(['sc_g' => 'trn_sc_greige'], 'mo.sc_greige_id = sc_g.id')
            ->leftJoin(['g_group' => 'mst_greige_group'], 'sc_g.greige_group_id = g_group.id');

        if ($locs_code !== null && $locs_code !== '') {
            $query->andWhere(['t.locs_code' => $locs_code]);
        }

        if ($opname_code !== null && $opname_code !== '') {
            $query->andWhere(['t.opname_code' => $opname_code]);
        }

        $models = $query->orderBy(['t.id' => SORT_ASC])->all();

        // Grouping data by (motif, color, grade)
        $groupsMap = [];
        $notes = [];
        $totalSummary = [
            'total_pcs' => count($models),
            'total_qty' => 0,
            'grades' => []
        ];

        foreach ($models as $m) {
            $motif = ($m->gudangJadi && $m->gudangJadi->wo) ? $m->gudangJadi->wo->greigeNamaKain : (!empty($m->qr_code_desc) ? $m->qr_code_desc : '-');
            $color = ($m->gudangJadi && !empty($m->gudangJadi->color)) ? $m->gudangJadi->color : '-';
            $gradeName = $m->gradeName;
            $qty = (float)$m->qty;

            // Kumpulkan catatan jika ada note / remark / hasil pemotongan
            if (!empty($m->remark)) {
                $notes[] = $m->remark;
            }
            if ($m->gudangJadi && !empty($m->gudangJadi->note)) {
                $notes[] = $m->gudangJadi->note;
            }

            $groupKey = $motif . '||' . $color . '||' . $gradeName;

            if (!isset($groupsMap[$groupKey])) {
                $groupsMap[$groupKey] = [
                    'motif' => $motif,
                    'color' => $color,
                    'grade_name' => $gradeName,
                    'pieces' => [],
                    'total_pcs' => 0,
                    'total_qty' => 0,
                ];
            }

            $groupsMap[$groupKey]['pieces'][] = [
                'id' => $m->id,
                'qty' => $qty,
                'qr_code' => $m->qr_code,
            ];
            $groupsMap[$groupKey]['total_pcs']++;
            $groupsMap[$groupKey]['total_qty'] += $qty;

            $totalSummary['total_qty'] += $qty;
            if (!isset($totalSummary['grades'][$gradeName])) {
                $totalSummary['grades'][$gradeName] = ['pcs' => 0, 'qty' => 0];
            }
            $totalSummary['grades'][$gradeName]['pcs']++;
            $totalSummary['grades'][$gradeName]['qty'] += $qty;
        }

        $notes = array_values(array_unique($notes));

        return $this->render('print-lokasi', [
            'locs_code' => $locs_code,
            'opname_code' => $opname_code,
            'groups' => array_values($groupsMap),
            'totalSummary' => $totalSummary,
            'notes' => $notes,
        ]);
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
