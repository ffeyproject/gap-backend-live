<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\KartuProcessDyeingProcess;
use common\models\ar\ActionLogKartuDyeing;
use common\models\ar\TrnWo;

class ProcessingRekapQoController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'update-keterangan' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists the Quality Objective (Qo) data and summary.
     */
    public function actionIndex()
    {
        // Default to previous month so that the evaluated month is the current month
        $defaultMonth = date('Y-m', strtotime('first day of -1 month'));
        $selectedMonth = Yii::$app->request->get('month', $defaultMonth);

        $data = $this->getQoData($selectedMonth);

        return $this->render('index', [
            'selectedMonth' => $selectedMonth,
            'stats' => $data['stats'],
            'dataProvider' => $data['dataProvider'],
            'rawRecords' => $data['rawRecords'],
        ]);
    }

    /**
     * Updates the qo_keterangan column for a specific card.
     */
    public function actionUpdateKeterangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $keterangan = Yii::$app->request->post('keterangan');

        $model = TrnKartuProsesDyeing::findOne($id);
        if ($model !== null) {
            $model->qo_keterangan = $keterangan;
            if ($model->save(false, ['qo_keterangan'])) {
                return ['success' => true, 'message' => 'Keterangan berhasil disimpan.'];
            }
        }

        return ['success' => false, 'message' => 'Gagal menyimpan keterangan.'];
    }

    /**
     * Exports the Qo data to Excel.
     */
    public function actionExportExcel($month)
    {
        $data = $this->getQoData($month);
        $records = $data['rawRecords'];
        $stats = $data['stats'];

        $filename = "rekap-qo-" . $month . "-" . date('Ymd_His') . ".xls";

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Expires: 0');

        // Let's render Excel view using standard HTML table format
        return $this->renderPartial('excel', [
            'month' => $month,
            'records' => $records,
            'stats' => $stats
        ]);
    }

    /**
     * Helper to fetch and calculate Qo data.
     */
    protected function getQoData($selectedMonth)
    {
        // Calculate the next month range for Buka Greige query
        $time = strtotime($selectedMonth . '-01');
        $startBukaGreige = date('Y-m-01', $time);
        $endBukaGreige = date('Y-m-t', $time);
        $queryMonthYear = date('Y-m', $time);

        // Filter using jsonb cast to search inside JSON value for PostgreSQL
        $likePattern = $queryMonthYear . '-%';
        $processes = KartuProcessDyeingProcess::find()
            ->where(['process_id' => 1])
            ->andWhere(new \yii\db\Expression("CAST(value AS jsonb)->>'tanggal' LIKE :tgl", [':tgl' => $likePattern]))
            ->all();

        // Filter and collect matching card IDs and their Buka Greige dates
        $matchingCards = [];
        foreach ($processes as $proc) {
            $val = Json::decode($proc->value);
            if (isset($val['tanggal'])) {
                $tgl = $val['tanggal'];
                if ($tgl >= $startBukaGreige && $tgl <= $endBukaGreige) {
                    $matchingCards[$proc->kartu_process_id] = $tgl;
                }
            }
        }

        $cardIds = array_keys($matchingCards);

        $stats = [
            'wo' => ['tercapai' => 0, 'tidak_tercapai' => 0, 'total' => 0, 'persentase' => 0],
            'batch' => ['tercapai' => 0, 'tidak_tercapai' => 0, 'total' => 0, 'persentase' => 0],
        ];

        $records = [];

        if (!empty($cardIds)) {
            // Fetch ActionLogKartuDyeing for packing date
            $logs = ActionLogKartuDyeing::find()
                ->where(['in', 'kartu_proses_id', $cardIds])
                ->andWhere(['action_name' => 'masuk_verpacking'])
                ->orderBy(['created_at' => SORT_ASC])
                ->all();

            $packDateMap = [];
            foreach ($logs as $log) {
                if (!isset($packDateMap[$log->kartu_proses_id])) {
                    $timestamp = is_numeric($log->created_at) ? $log->created_at : strtotime($log->created_at);
                    $packDateMap[$log->kartu_proses_id] = date('Y-m-d', (int)$timestamp);
                }
            }

            // Fetch cards
            $cards = TrnKartuProsesDyeing::find()
                ->where(['in', 'trn_kartu_proses_dyeing.id', $cardIds])
                ->joinWith(['wo.sc.cust', 'woColor.moColor'])
                ->all();

            $woCardResults = []; // Keep track of each WO's achievements: [wo_id => [is_tercapai_1, is_tercapai_2, ...]]

            foreach ($cards as $card) {
                // Populate packing date if not set from logs
                if (!isset($packDateMap[$card->id])) {
                    if (!empty($card->approved_history)) {
                        $history = Json::decode($card->approved_history);
                        if (is_array($history) && !empty($history)) {
                            $times = [];
                            foreach ($history as $h) {
                                if (isset($h['time'])) {
                                    $times[] = $h['time'];
                                }
                            }
                            if (!empty($times)) {
                                sort($times);
                                $timestamp = is_numeric($times[0]) ? $times[0] : strtotime($times[0]);
                                $packDateMap[$card->id] = date('Y-m-d', (int)$timestamp);
                            }
                        }
                    }
                    if (!isset($packDateMap[$card->id]) && !empty($card->approved_at)) {
                        $timestamp = is_numeric($card->approved_at) ? $card->approved_at : strtotime($card->approved_at);
                        $packDateMap[$card->id] = date('Y-m-d', (int)$timestamp);
                    }
                }

                $bukaDateStr = $matchingCards[$card->id];
                $bukaTime = strtotime($bukaDateStr);
                $targetTime = (int)$bukaTime + 14 * 24 * 3600; // 14 days limit

                $isPacked = isset($packDateMap[$card->id]);
                $packDateStr = $isPacked ? $packDateMap[$card->id] : null;

                $isTercapai = false;
                if ($isPacked) {
                    $packTime = strtotime($packDateStr);
                    if ($packTime <= $targetTime) {
                        $isTercapai = true;
                    }
                }

                // Update batch stats
                if ($isTercapai) {
                    $stats['batch']['tercapai']++;
                } else {
                    $stats['batch']['tidak_tercapai']++;
                }
                $stats['batch']['total']++;

                // Track WO achievements
                if ($card->wo_id) {
                    if (!isset($woCardResults[$card->wo_id])) {
                        $woCardResults[$card->wo_id] = [];
                    }
                    $woCardResults[$card->wo_id][] = $isTercapai;
                }

                // If not reached packing or packed but late, add to listing
                if (!$isTercapai) {
                    $warna = ($card->woColor && $card->woColor->moColor) ? $card->woColor->moColor->color : '-';
                    $buyer = ($card->wo && $card->wo->sc) ? $card->wo->sc->customerCode : '-';

                    $records[] = [
                        'id' => $card->id,
                        'tanggal_wo' => $card->wo ? $card->wo->date : '-',
                        'buyer' => $buyer,
                        'wo_no' => $card->wo ? $card->wo->no : '-',
                        'motif' => $card->wo ? $card->wo->greigeNamaKain : '-',
                        'warna' => $warna,
                        'nk' => $card->nomor_kartu,
                        'buka_greige' => $bukaDateStr,
                        'tgl_packing' => $packDateStr ? date('d-M-Y', strtotime($packDateStr)) : 'Belum Packing',
                        'keterangan' => $card->qo_keterangan,
                    ];
                }
            }

            // Calculate WO stats
            foreach ($woCardResults as $woId => $results) {
                // A WO is Tercapai only if ALL its cards opened in this month reached packing within 14 days
                $woTercapai = true;
                foreach ($results as $res) {
                    if (!$res) {
                        $woTercapai = false;
                        break;
                    }
                }

                if ($woTercapai) {
                    $stats['wo']['tercapai']++;
                } else {
                    $stats['wo']['tidak_tercapai']++;
                }
                $stats['wo']['total']++;
            }
        }

        // Calculate Percentages
        if ($stats['wo']['total'] > 0) {
            $stats['wo']['persentase'] = round(($stats['wo']['tidak_tercapai'] / $stats['wo']['total']) * 100, 1);
        }
        if ($stats['batch']['total'] > 0) {
            $stats['batch']['persentase'] = round(($stats['batch']['tidak_tercapai'] / $stats['batch']['total']) * 100, 1);
        }

        // Return DataProvider for gridview
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $records,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => false,
        ]);

        return [
            'stats' => $stats,
            'dataProvider' => $dataProvider,
            'rawRecords' => $records,
        ];
    }
}
