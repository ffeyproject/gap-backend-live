<?php

namespace backend\controllers;

use common\models\ar\MstGreige;
use common\models\ar\TrnStockGreigeDaily;
use common\models\ar\TrnStockGreigeDailySearch;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * TrnStockGreigeDailyController implements the daily stock tracking and change reports for greige motifs.
 */
class TrnStockGreigeDailyController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'save-snapshot' => ['POST', 'GET'],
                ],
            ],
        ];
    }

    /**
     * Lists all daily stock records with day-to-day changes.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnStockGreigeDailySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // Ambil 10 tanggal snapshot terakhir yang ada di database
        $availableDates = (new Query())
            ->select('date')
            ->distinct()
            ->from(TrnStockGreigeDaily::tableName())
            ->orderBy(['date' => SORT_DESC])
            ->limit(10)
            ->column();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'availableDates' => $availableDates,
        ]);
    }

    /**
     * Matrix / Pivot view comparing stock progression day by day for all motifs.
     * @return mixed
     */
    public function actionMatrix()
    {
        $startDate = Yii::$app->request->get('startDate', date('Y-m-d', strtotime('-14 days')));
        $endDate = Yii::$app->request->get('endDate', date('Y-m-d'));
        $greigeId = Yii::$app->request->get('greige_id');

        // Ambil daftar tanggal yang ada di database pada rentang tersebut
        $datesQuery = (new Query())
            ->select('date')
            ->distinct()
            ->from(TrnStockGreigeDaily::tableName())
            ->where(['between', 'date', $startDate, $endDate])
            ->orderBy(['date' => SORT_ASC]);

        $dates = $datesQuery->column();

        // Ambil data stock greige daily per motif dan tanggal
        $query = (new Query())
            ->select([
                'tsgd.date',
                'tsgd.greige_id',
                'greige_nama' => 'mg.nama_kain',
                'group_nama' => 'mgg.nama_kain',
                'tsgd.total_panjang',
                'tsgd.total_roll',
            ])
            ->from(['tsgd' => TrnStockGreigeDaily::tableName()])
            ->innerJoin(['mg' => MstGreige::tableName()], 'mg.id = tsgd.greige_id')
            ->leftJoin(['mgg' => 'mst_greige_group'], 'mgg.id = tsgd.greige_group_id')
            ->where(['between', 'tsgd.date', $startDate, $endDate]);

        if (!empty($greigeId)) {
            $query->andWhere(['tsgd.greige_id' => $greigeId]);
        }

        $query->orderBy([
            new Expression("CASE WHEN mg.nama_kain ~* '^[a-z]' THEN 0 ELSE 1 END ASC"),
            'mg.nama_kain' => SORT_ASC,
            'tsgd.date' => SORT_ASC,
        ]);

        $rawRows = $query->all();

        // Format data: [greige_id => ['nama' => ..., 'group' => ..., 'data' => [date => ['qty' => ..., 'roll' => ..., 'diff_qty' => ...]]]]
        $matrixData = [];
        $motifs = [];

        foreach ($rawRows as $row) {
            $gId = $row['greige_id'];
            if (!isset($motifs[$gId])) {
                $motifs[$gId] = [
                    'id' => $gId,
                    'nama' => $row['greige_nama'],
                    'group' => $row['group_nama'],
                ];
            }
            $matrixData[$gId][$row['date']] = [
                'qty' => (float)$row['total_panjang'],
                'roll' => (int)$row['total_roll'],
            ];
        }

        // Hitung selisih antar tanggal untuk setiap motif
        foreach ($matrixData as $gId => &$datesData) {
            $prevQty = null;
            $prevRoll = null;
            foreach ($dates as $d) {
                if (isset($datesData[$d])) {
                    if ($prevQty !== null) {
                        $datesData[$d]['diff_qty'] = $datesData[$d]['qty'] - $prevQty;
                        $datesData[$d]['diff_roll'] = $datesData[$d]['roll'] - $prevRoll;
                    } else {
                        $datesData[$d]['diff_qty'] = 0;
                        $datesData[$d]['diff_roll'] = 0;
                    }
                    $prevQty = $datesData[$d]['qty'];
                    $prevRoll = $datesData[$d]['roll'];
                }
            }
        }
        unset($datesData);

        return $this->render('matrix', [
            'dates' => $dates,
            'motifs' => $motifs,
            'matrixData' => $matrixData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'greigeId' => $greigeId,
        ]);
    }

    /**
     * Action to capture/save snapshot of current stock per motif.
     * Can be triggered from /trn-stock-greige/index or this controller.
     * @return mixed
     */
    public function actionSaveSnapshot()
    {
        $date = Yii::$app->request->post('date', Yii::$app->request->get('date', date('Y-m-d')));

        try {
            $result = TrnStockGreigeDaily::snapshotStock($date);

            $msg = sprintf(
                'Berhasil menyimpan stock harian tanggal %s. Total %d motif (%s m, %d roll). Baru: %d, Terupdate: %d.',
                $result['date'],
                $result['total_motifs'],
                Yii::$app->formatter->asDecimal($result['grand_total_m']),
                $result['grand_total_roll'],
                $result['new_saved'],
                $result['updated']
            );

            if (Yii::$app->request->isAjax) {
                return $this->asJson([
                    'success' => true,
                    'message' => $msg,
                    'data' => $result,
                ]);
            }

            Yii::$app->session->setFlash('success', $msg);
        } catch (\Throwable $e) {
            $msg = 'Gagal menyimpan snapshot stock harian: ' . $e->getMessage();
            if (Yii::$app->request->isAjax) {
                return $this->asJson([
                    'success' => false,
                    'message' => $msg,
                ]);
            }
            Yii::$app->session->setFlash('error', $msg);
        }

        $returnUrl = Yii::$app->request->get('returnUrl');
        if (!empty($returnUrl)) {
            return $this->redirect($returnUrl);
        }

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing TrnStockGreigeDaily model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $date = $model->date;
        $motif = $model->greigeNamaKain;
        $model->delete();

        Yii::$app->session->setFlash('success', "Data stock harian tanggal {$date} untuk motif {$motif} berhasil dihapus.");

        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    /**
     * Export daily stock report to CSV / Excel
     * @return mixed
     */
    public function actionExportExcel()
    {
        $searchModel = new TrnStockGreigeDailySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;

        $models = $dataProvider->models;

        $filename = 'rekap_stock_greige_harian_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // UTF-8 BOM for Excel
        fputs($output, "\xEF\xBB\xBF");

        // Header
        fputcsv($output, [
            'ID',
            'Tanggal',
            'Motif / Nama Kain',
            'Group Greige',
            'Asal Greige',
            'Stock Hari Ini (m)',
            'Roll Hari Ini',
            'Stock Sebelumnya (m)',
            'Roll Sebelumnya',
            'Tambahan / Selisih (m)',
            'Tambahan / Selisih Roll',
            'Grade A (m)',
            'Grade B (m)',
            'Grade C (m)',
            'Grade D (m)',
            'Grade E (m)',
            'Grade NG (m)',
            'Waktu Simpan',
        ], ';');

        foreach ($models as $m) {
            /** @var TrnStockGreigeDaily $m */
            fputcsv($output, [
                $m->id,
                $m->date,
                $m->greigeNamaKain,
                $m->greigeGroup ? $m->greigeGroup->nama_kain : '',
                $m->asalGreigeName,
                $m->total_panjang,
                $m->total_roll,
                $m->prev_total_panjang,
                $m->prev_total_roll,
                $m->diff_panjang,
                $m->diff_roll,
                $m->grade_a,
                $m->grade_b,
                $m->grade_c,
                $m->grade_d,
                $m->grade_e,
                $m->grade_ng,
                $m->created_at ? date('Y-m-d H:i:s', $m->created_at) : '',
            ], ';');
        }

        fclose($output);
        exit();
    }

    /**
     * Finds the TrnStockGreigeDaily model based on its primary key value.
     * @param integer $id
     * @return TrnStockGreigeDaily the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnStockGreigeDaily::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang diminta tidak ditemukan.');
    }
}
