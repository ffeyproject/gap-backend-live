<?php
namespace backend\controllers;

use backend\models\form\CatatanProsesForm;
use backend\models\form\HasilTesGosokForm;
use common\models\ar\KartuProcessDyeingProcess;
use common\models\ar\KartuProcessDyeingProcessSearch;
use common\models\ar\MstGreige;
use common\models\ar\MstProcessDyeing;
use common\models\ar\TrnKartuProsesDyeingItem;
use common\models\ar\TrnKartuProsesPfpItem;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWo;
use common\models\ar\TrnWoColor;
use common\models\ar\TrnOrderPfp;
use Yii;
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnKartuProsesPfp;
use common\models\ar\TrnKartuProsesDyeingSearch;
use common\models\ar\TrnStockGreigeOpname;
use yii\helpers\BaseVarDumper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TrnKartuProsesDyeingController implements the CRUD actions for TrnKartuProsesDyeing model.
 */
class ProcessingDyeingController extends Controller
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
     * Lists all TrnKartuProsesDyeing models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrnKartuProsesDyeingSearch(['status'=>TrnKartuProsesDyeing::STATUS_DELIVERED]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all TrnKartuProsesDyeing models.
     * @return mixed
     */
    public function actionRekap()
    {
        $searchModel = new TrnKartuProsesDyeingSearch();
        
        $queryParams = Yii::$app->request->queryParams;
        if (!isset($queryParams['TrnKartuProsesDyeingSearch']['woMonth']) && !isset($queryParams['TrnKartuProsesDyeingSearch'])) {
            $queryParams['TrnKartuProsesDyeingSearch']['woMonth'] = date('m');
        }
        
        $dataProvider = $searchModel->search($queryParams);
        $dataProvider->sort->defaultOrder = ['wo_id' => SORT_DESC];

        $statusRekap = Yii::$app->request->get('status_rekap', 'semua');
        
        $hasFilter = false;
        foreach ($searchModel->attributes() as $attr) {
            if ($searchModel->$attr !== null && $searchModel->$attr !== '') {
                $hasFilter = true;
                break;
            }
        }
        if (!$hasFilter) {
            $searchProps = ['woNo', 'dateRange', 'motif', 'woDateRange', 'openDateRange', 'marketingName', 'dateRangeMasukPacking', 'customerName', 'warna', 'dateRangeReadyColour', 'dateReangeTopingMatching', 'shift', 'woMonth'];
            foreach ($searchProps as $prop) {
                if ($searchModel->$prop !== null && $searchModel->$prop !== '') {
                    $hasFilter = true;
                    break;
                }
            }
        }

        // Jika tidak ada filter pencarian sama sekali, kosongkan data table terlebih dahulu
        if (!$hasFilter) {
            $dataProvider->query->andWhere('1=0');
        } else {
            if ($statusRekap === 'selesai') {
                $dataProvider->query->andWhere(['in', 'trn_kartu_proses_dyeing.status', [
                    TrnKartuProsesDyeing::STATUS_APPROVED, TrnKartuProsesDyeing::STATUS_INSPECTED, TrnKartuProsesDyeing::STATUS_GANTI_GREIGE,
                    TrnKartuProsesDyeing::STATUS_GANTI_GREIGE_LINKED, TrnKartuProsesDyeing::STATUS_BATAL, TrnKartuProsesDyeing::STATUS_ROLLING_PACKING,
                    TrnKartuProsesDyeing::STATUS_MAKE_UP_PACKING, TrnKartuProsesDyeing::STATUS_FOLDED_PACKING, TrnKartuProsesDyeing::STATUS_TERIMA_GUDANG_JADI,
                    TrnKartuProsesDyeing::STATUS_PERIKSA_PENGIRIMAN, TrnKartuProsesDyeing::STATUS_CLOSE, TrnKartuProsesDyeing::STATUS_SELVEDGE_PACKING
                ]]);
            } elseif ($statusRekap === 'on_process') {
                $dataProvider->query->andWhere(['trn_kartu_proses_dyeing.status' => TrnKartuProsesDyeing::STATUS_DELIVERED]);
            } elseif ($statusRekap === 'semua') {
                // Only show WO that already have a number assigned
                $dataProvider->query->andWhere(['IS NOT', 'trn_wo.no', null])
                    ->andWhere(['!=', 'trn_wo.no', '']);
                if (!empty($searchModel->woMonth)) {
                    $dataProvider->pagination = false;
                    $models = $dataProvider->getModels();
                    $models = $this->appendMissingWoColors($models, $searchModel);
                    $dataProvider = new \yii\data\ArrayDataProvider([
                        'allModels' => $models,
                        'pagination' => [
                            'pageSize' => 20,
                        ],
                        'sort' => [
                            'attributes' => [
                                'wo_id',
                                'woNo' => [
                                    'asc' => ['woNo' => SORT_ASC],
                                    'desc' => ['woNo' => SORT_DESC],
                                ],
                            ],
                            'defaultOrder' => [
                                'wo_id' => SORT_DESC,
                            ]
                        ],
                    ]);
                }
            }
        }

        return $this->render('rekap', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'statusRekap' => $statusRekap,
        ]);
    }

    /**
     * Export rekap data for a specific WO month to Excel (CSV format).
     * @param string $woMonth
     * @param string $status_rekap
     */
    public function actionExportExcel($woMonth, $status_rekap = 'on_process')
    {
        $searchModel = new TrnKartuProsesDyeingSearch();
        $searchModel->woMonth = $woMonth;
        $dataProvider = $searchModel->search([]);
        $dataProvider->sort->defaultOrder = ['woNo' => SORT_ASC];
        
        $query = $dataProvider->query;
        $query->joinWith(['sc', 'scGreige']);
        $query->andWhere([
            'trn_sc.jenis_order' => \common\models\ar\TrnSc::JENIS_ORDER_FRESH_ORDER,
            'trn_sc_greige.process' => \common\models\ar\TrnScGreige::PROCESS_DYEING
        ]);
        
        if ($status_rekap === 'selesai') {
            $query->andWhere(['in', 'trn_kartu_proses_dyeing.status', [
                TrnKartuProsesDyeing::STATUS_APPROVED, TrnKartuProsesDyeing::STATUS_INSPECTED, TrnKartuProsesDyeing::STATUS_GANTI_GREIGE,
                TrnKartuProsesDyeing::STATUS_GANTI_GREIGE_LINKED, TrnKartuProsesDyeing::STATUS_BATAL, TrnKartuProsesDyeing::STATUS_ROLLING_PACKING,
                TrnKartuProsesDyeing::STATUS_MAKE_UP_PACKING, TrnKartuProsesDyeing::STATUS_FOLDED_PACKING, TrnKartuProsesDyeing::STATUS_TERIMA_GUDANG_JADI,
                TrnKartuProsesDyeing::STATUS_PERIKSA_PENGIRIMAN, TrnKartuProsesDyeing::STATUS_CLOSE, TrnKartuProsesDyeing::STATUS_SELVEDGE_PACKING
            ]]);
        } elseif ($status_rekap === 'on_process') {
            $query->andWhere(['trn_kartu_proses_dyeing.status' => TrnKartuProsesDyeing::STATUS_DELIVERED]);
        }
        
        $dataProvider->pagination = false;
        $models = $dataProvider->getModels();
        
        if ($status_rekap === 'semua') {
            $models = $this->appendMissingWoColors($models, $searchModel, true);
        }
        
        $filename = "rekap-processing-dyeing-" . $woMonth . "-" . date('Ymd_His') . ".xls";
        
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $masterProcesses = \common\models\ar\MstProcessDyeing::find()
            ->where(['perbaikan' => false])
            ->orderBy('order')
            ->all();

        $hiddenColsStr = Yii::$app->request->get('hidden_cols', '');
        $hiddenCols = !empty($hiddenColsStr) ? explode(',', $hiddenColsStr) : [];
        if (in_array('col-wodaterange', $hiddenCols)) {
            $hiddenCols[] = 'col-wodaterange-2';
        }

        $allHeaders = [
            'col-id' => 'ID',
            'col-wodaterange' => 'Tgl. WO',
            'col-buyer' => 'Buyer',
            'col-wono' => 'No. WO',
            'col-motif' => 'Motif',
            'col-warna' => 'Warna',
            'col-nomor-kartu' => 'NK',
            'col-panjang-greige' => 'Panjang Greige',
            'col-berat-greige' => 'Berat Greige',
            'col-pcs' => 'Pcs',
            'col-terakhir-proses' => 'Terakhir Proses',
        ];

        foreach ($masterProcesses as $proc) {
            $colKey = 'col-' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $proc->nama_proses . ' / Shift'));
            $allHeaders[$colKey] = $proc->nama_proses;
        }

        $allHeaders['col-panjang-jadi'] = 'Panjang Jadi';
        $allHeaders['col-pack'] = 'Pack';

        // Localized Indonesian date formatting helper
        $formatIndoDate = function($dateValue) {
            if (empty($dateValue)) {
                return '';
            }
            if (is_numeric($dateValue)) {
                $time = (int)$dateValue;
            } else {
                $time = strtotime($dateValue);
            }
            if (!$time) {
                return $dateValue;
            }
            $day = date('d', $time);
            $monthNum = (int)date('m', $time);
            $year = date('Y', $time);
            $indoMonths = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agt',
                9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
            ];
            $monthName = isset($indoMonths[$monthNum]) ? $indoMonths[$monthNum] : date('M', $time);
            return "{$day}-{$monthName}-{$year}";
        };

        // Calculate columns and prepare data list first
        $visibleCols = [];
        foreach ($allHeaders as $colKey => $headerLabel) {
            if (!in_array($colKey, $hiddenCols)) {
                $visibleCols[] = $colKey;
            }
        }

        $rowDataList = [];
        foreach ($models as $idx => $model) {
            $id = $model->id;
            $tanggal = $model->wo ? $model->wo->date : '';
            $buyer = $model->sc ? $model->sc->customerCode : '';
            $woNo = $model->wo ? $model->wo->no : '';
            $motif = $model->wo ? $model->wo->greigeNamaKain : '';
            $tglKirim = $model->wo ? $model->wo->tgl_kirim : '';
            $hand = ($model->wo && $model->wo->handling) ? $model->wo->handling->name : '';
            
            $note = '';
            if ($model->wo && !empty($model->wo->note)) {
                $rawNote = $model->wo->note;
                $rawNote = html_entity_decode($rawNote, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $rawNote = strip_tags($rawNote);
                $rawNote = str_replace([chr(194).chr(160), '&nbsp;'], ' ', $rawNote);
                $rawNote = str_replace(["\r\n", "\r"], "\n", $rawNote);
                $rawNote = trim($rawNote);
                $note = preg_replace("/\n{2,}/", "\n", $rawNote);
            }

            // Memo Perubahan
            $memos = $model->wo ? $model->wo->trnWoMemos : [];
            $memoHtml = '';
            if (!empty($memos)) {
                $memoTexts = [];
                foreach ($memos as $memoModel) {
                    $mText = $memoModel->memo;
                    $mText = html_entity_decode($mText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $mText = strip_tags($mText);
                    $mText = str_replace([chr(194).chr(160), '&nbsp;'], ' ', $mText);
                    $mText = trim($mText);
                    if (!empty($mText)) {
                        $memoTexts[] = 'No. ' . $memoModel->no . ': ' . $mText;
                    }
                }
                if (!empty($memoTexts)) {
                    $memoHtml = implode('; ', $memoTexts);
                }
            }

            $cleanNum = function($val) {
                return (float)str_replace([' ', ','], ['', '.'], (string)$val);
            };
            $tFinish = $model->wo ? (Yii::$app->formatter->asDecimal($cleanNum($model->wo->colorQtyFinish), 1) .'M / '. Yii::$app->formatter->asDecimal($cleanNum($model->wo->colorQtyFinishToYard), 1).'Y') : '';
            $warna = ($model->woColor && $model->woColor->moColor) ? $model->woColor->moColor->color : '';
            $currentWoId = $model->wo_id;
            $countWarna = 0;
            foreach ($models as $m) {
                if ($m->wo_id === $currentWoId && (($m->woColor && $m->woColor->moColor) ? $m->woColor->moColor->color : '') === $warna) {
                    $countWarna++;
                }
            }
            if ($countWarna > 1 && $warna !== '') {
                $warna = $warna . ' (' . $countWarna . 'x)';
            }
            $nk = $model->nomor_kartu;
            $panjang = $model->wo ? $cleanNum($model->wo->colorQtyBatchToMeter) : 0;
            $panjangGreige = $model->getTrnKartuProsesDyeingItems()->sum('panjang_m');
            $beratGreige = $model->berat;
            $pcs = $model->getTrnKartuProsesDyeingItems()->count();
            
            $lastProcess = (new \yii\db\Query())
                ->select(['m.nama_proses'])
                ->from('kartu_process_dyeing_process k')
                ->innerJoin('mst_process_dyeing m', 'k.process_id = m.id')
                ->where(['k.kartu_process_id' => $model->id])
                ->andWhere(['is not', 'k.value', null])
                ->andWhere(['<>', 'k.value', ''])
                ->orderBy(['m.order' => SORT_DESC])
                ->one();
            $terakhirProses = $lastProcess !== false ? $lastProcess['nama_proses'] : '-';
            
            // Fetch all process values in a single high-performance query
            $processes = (new \yii\db\Query())
                ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                ->where(['kartu_process_id' => $model->id])
                ->all();
            
            $processData = [];
            foreach ($processes as $pc) {
                $processData[$pc['process_id']] = \yii\helpers\Json::decode($pc['value']);
            }
            
            $rowVals = [
                'col-id' => $id,
                'col-wodaterange' => $formatIndoDate($tanggal),
                'col-buyer' => $buyer,
                'col-wono' => $woNo,
                'col-motif' => $motif,
                'col-tgl--kirim' => $formatIndoDate($tglKirim),
                'col-hand' => $hand,
                'col-note' => $note,
                'col-memo' => $memoHtml,
                'col-t--finish' => $tFinish,
                'col-warna' => $warna,
                'col-nomor-kartu' => $nk,
                'col-panjang' => $panjang,
                'col-panjang-greige' => $panjangGreige,
                'col-berat-greige' => $beratGreige,
                'col-pcs' => $pcs,
                'col-terakhir-proses' => $terakhirProses,
            ];

            foreach ($masterProcesses as $proc) {
                $tg = '-';
                $sh = '-';
                if (isset($processData[$proc->id])) {
                    $v = $processData[$proc->id];
                    if (isset($v['tanggal']) && !empty($v['tanggal'])) {
                        $tg = $formatIndoDate($v['tanggal']);
                    }
                    if (isset($v['shift_group'])) {
                        $sh = $v['shift_group'];
                    }
                }
                $colKey = 'col-' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $proc->nama_proses . ' / Shift'));
                $rowVals[$colKey] = $tg . ' / ' . $sh;
            }
            
            $panjangJadi = 0;
            if (isset($processData[11]) && isset($processData[11]['panjang_jadi'])) {
                $panjangJadi = $processData[11]['panjang_jadi'];
            }
            
            $packDates = [];
            $logs = \common\models\ar\ActionLogKartuDyeing::find()
                ->where(['kartu_proses_id' => $model->id, 'action_name' => 'masuk_verpacking'])
                ->orderBy(['created_at' => SORT_ASC])
                ->all();
                
            foreach ($logs as $lIdx => $log) {
                $dateFormatted = $formatIndoDate($log->created_at);
                $packDates[] = 'Persetujuan Ke-' . ($lIdx + 1) . ': ' . $dateFormatted;
            }
            
            if (empty($packDates)) {
                if (!empty($model->approved_history)) {
                    $history = \yii\helpers\Json::decode($model->approved_history);
                    if (is_array($history)) {
                        foreach ($history as $lIdx => $h) {
                            if (isset($h['time'])) {
                                $dateFormatted = $formatIndoDate($h['time']);
                                $packDates[] = 'Persetujuan Ke-' . ($lIdx + 1) . ': ' . $dateFormatted;
                            }
                        }
                    }
                }
            }
            
            if (empty($packDates) && !empty($model->approved_at)) {
                $packDates[] = 'Persetujuan Ke-1: ' . $formatIndoDate($model->approved_at);
            }
            $pack = implode(', ', $packDates);
            
            $rowVals['col-panjang-jadi'] = $panjangJadi;
            $rowVals['col-pack'] = $pack;

            $rowDataList[$idx] = [
                'model' => $model,
                'processData' => $processData,
                'packDates' => $packDates,
                'rowData' => $rowVals,
            ];
        }

        // Calculate hierarchical vertical merges
        $mergeMap = [];
        $numRows = count($rowDataList);
        $mergeCols = [
            'col-id',
            'col-wodaterange',
            'col-buyer',
            'col-wono',
            'col-motif',
            'col-warna'
        ];

        foreach ($mergeCols as $colKey) {
            $r = 0;
            while ($r < $numRows) {
                $startVal = $rowDataList[$r]['rowData'][$colKey];
                $startWo = $rowDataList[$r]['rowData']['col-wono'];
                
                $span = 1;
                if (!empty($startWo) && $startVal !== '' && $startVal !== null && $startVal !== '-') {
                    for ($nextR = $r + 1; $nextR < $numRows; $nextR++) {
                        $nextVal = $rowDataList[$nextR]['rowData'][$colKey];
                        $nextWo = $rowDataList[$nextR]['rowData']['col-wono'];
                        
                        if ($nextVal === $startVal && $nextWo === $startWo) {
                            $span++;
                        } else {
                            break;
                        }
                    }
                }
                
                if ($span > 1) {
                    $mergeMap[$r][$colKey] = [
                        'MergeDown' => $span - 1,
                        'IsFirst' => true
                    ];
                    for ($k = 1; $k < $span; $k++) {
                        $mergeMap[$r + $k][$colKey] = [
                            'MergeDown' => 0,
                            'IsFirst' => false
                        ];
                    }
                }
                $r += $span;
            }
        }

        // Output XML Spreadsheet 2003
        echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        echo ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
        echo ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
        echo ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        echo ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
        
                echo ' <Styles>' . "\n";
        echo '  <Style ss:ID="Default" ss:Name="Normal">' . "\n";
        echo '   <Alignment ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>' . "\n";
        echo '   <Interior/>' . "\n";
        echo '   <NumberFormat/>' . "\n";
        echo '   <Protection/>' . "\n";
        echo '  </Style>' . "\n";
        echo '  <Style ss:ID="Header">' . "\n";
        echo '   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#777777"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#777777"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#777777"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#777777"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Bold="1" ss:Color="#222222"/>' . "\n";
        echo '   <Interior ss:Color="#ECF0F5" ss:Pattern="Solid"/>' . "\n";
        echo '  </Style>' . "\n";

        // Row shading styles
        echo '  <Style ss:ID="RowPackFilled">' . "\n";
        echo '   <Alignment ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>' . "\n";
        echo '   <Interior ss:Color="#FFFDE7" ss:Pattern="Solid"/>' . "\n";
        echo '  </Style>' . "\n";

        echo '  <Style ss:ID="RowTopingFilled">' . "\n";
        echo '   <Alignment ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>' . "\n";
        echo '   <Interior ss:Color="#FFEbee" ss:Pattern="Solid"/>' . "\n";
        echo '  </Style>' . "\n";

        echo '  <Style ss:ID="RowDyeingFilled">' . "\n";
        echo '   <Alignment ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>' . "\n";
        echo '   <Interior ss:Color="#E3F2FD" ss:Pattern="Solid"/>' . "\n";
        echo '  </Style>' . "\n";

        echo '  <Style ss:ID="RowPinkFilled">' . "\n";
        echo '   <Alignment ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>' . "\n";
        echo '   <Interior ss:Color="#FCE4EC" ss:Pattern="Solid"/>' . "\n";
        echo '  </Style>' . "\n";

        // Column-specific highlight styles
        echo '  <Style ss:ID="ColDyeingStyle">' . "\n";
        echo '   <Alignment ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#5D4037"/>' . "\n";
        echo '   <Interior ss:Color="#FFF8E1" ss:Pattern="Solid"/>' . "\n";
        echo '  </Style>' . "\n";

        echo '  <Style ss:ID="ColResinStyle">' . "\n";
        echo '   <Alignment ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#0D47A1"/>' . "\n";
        echo '   <Interior ss:Color="#BBDEFB" ss:Pattern="Solid"/>' . "\n";
        echo '  </Style>' . "\n";

        echo '  <Style ss:ID="ColHeatCutPackStyle">' . "\n";
        echo '   <Alignment ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#1B5E20"/>' . "\n";
        echo '   <Interior ss:Color="#C8E6C9" ss:Pattern="Solid"/>' . "\n";
        echo '  </Style>' . "\n";

        echo '  <Style ss:ID="ColPresetSettingStyle">' . "\n";
        echo '   <Alignment ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#880E4F"/>' . "\n";
        echo '   <Interior ss:Color="#F8BBD0" ss:Pattern="Solid"/>' . "\n";
        echo '  </Style>' . "\n";

        echo '  <Style ss:ID="ColTopingRcStyle">' . "\n";
        echo '   <Alignment ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#E65100"/>' . "\n";
        echo '   <Interior ss:Color="#FFE0B2" ss:Pattern="Solid"/>' . "\n";
        echo '  </Style>' . "\n";

        echo '  <Style ss:ID="ColNomorKartuGreenStyle">' . "\n";
        echo '   <Alignment ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Bold="1" ss:Color="#1B5E20"/>' . "\n";
        echo '   <Interior ss:Color="#C8E6C9" ss:Pattern="Solid"/>' . "\n";
        echo '  </Style>' . "\n";
        echo '  <Style ss:ID="NoteStyle">' . "\n";
        echo '   <Alignment ss:Vertical="Center" ss:Horizontal="Left" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#9C27B0" ss:Bold="1"/>' . "\n";
        echo '   <Interior ss:Color="#FAFAFA" ss:Pattern="Solid"/>' . "\n";
        echo '  </Style>' . "\n";
        
        echo '  <Style ss:ID="MemoStyle">' . "\n";
        echo '   <Alignment ss:Vertical="Center" ss:Horizontal="Left" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#1565C0"/>' . "\n";
        echo '   <Interior ss:Color="#FAFAFA" ss:Pattern="Solid"/>' . "\n";
        echo '  </Style>' . "\n";
        
        echo '  <Style ss:ID="NoteRowStyle">' . "\n";
        echo '   <Alignment ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Interior ss:Color="#FAFAFA" ss:Pattern="Solid"/>' . "\n";
        echo '  </Style>' . "\n";

        echo ' </Styles>' . "\n";
        
        echo ' <Worksheet ss:Name="Rekap">' . "\n";
        $noteRowsCount = 0;
        $numRows = count($rowDataList);
        for ($i = 0; $i < $numRows; $i++) {
            $rowItem = $rowDataList[$i];
            $currentWoNo = $rowItem['rowData']['col-wono'];
            $nextRowItem = isset($rowDataList[$i + 1]) ? $rowDataList[$i + 1] : null;
            $nextWoNo = $nextRowItem ? $nextRowItem['rowData']['col-wono'] : null;
            
            if ($currentWoNo !== $nextWoNo) {
                if ($rowItem['model']->wo) {
                    $noteRowsCount++;
                }
            }
        }

        $expandedColumnCount = count($visibleCols);
        $expandedRowCount = count($rowDataList) + 1 + $noteRowsCount; // +1 for Header row, plus Note/Memo rows
        echo '  <Table ss:ExpandedColumnCount="' . $expandedColumnCount . '" ss:ExpandedRowCount="' . $expandedRowCount . '">' . "\n";
        
        // Output column widths (all process columns set to uniform 110 width)
        $columnWidths = [
            'col-id' => 50,
            'col-wodaterange' => 90,
            'col-buyer' => 80,
            'col-wono' => 110,
            'col-motif' => 130,
            'col-warna' => 100,
            'col-nomor-kartu' => 100,
            'col-panjang-greige' => 100,
            'col-berat-greige' => 90,
            'col-pcs' => 50,
            'col-terakhir-proses' => 120,
            'col-panjang-jadi' => 100,
            'col-pack' => 200,
        ];
        foreach ($visibleCols as $colKey) {
            $w = isset($columnWidths[$colKey]) ? $columnWidths[$colKey] : 110;
            echo '   <Column ss:Width="' . $w . '"/>' . "\n";
        }
        
        // Output headers row
        echo '   <Row ss:Height="25" ss:StyleID="Header">' . "\n";
        foreach ($allHeaders as $colKey => $headerLabel) {
            if (!in_array($colKey, $hiddenCols)) {
                $cleanLabel = htmlspecialchars(trim(strip_tags($headerLabel)), ENT_QUOTES | ENT_XML1, 'UTF-8');
                echo '    <Cell><Data ss:Type="String">' . $cleanLabel . '</Data></Cell>' . "\n";
            }
        }
        echo '   </Row>' . "\n";

        foreach ($rowDataList as $idx => $rowItem) {
            $model = $rowItem['model'];
            $processData = $rowItem['processData'];
            $packDates = $rowItem['packDates'];
            $rowData = $rowItem['rowData'];

            // Compute row and cell-specific background coloring styles exactly matched with web UI
            $isPackFilled = !empty($packDates) || !empty($model->approved_at);

            $isOrangeFilled = false;
            if (!$isPackFilled) {
                $orangeProcessIds = \yii\helpers\ArrayHelper::getColumn(
                    \common\models\ar\MstProcessDyeing::find()
                        ->where(['nama_proses' => [
                            'Perbaikan JB',
                            'Toping 1', 'Toping 2', 'Toping 3', 'Toping 4',
                            'Cuci Ulang',
                            'RC 1', 'RC 2', 'RC 3', 'RC 4', 'RC 5',
                            'Toping Level',
                            'Celup Rayon',
                            'Tarik Ulang',
                            'RF Ulang 1', 'RF Ulang 2', 'RF Ulang 3', 'RF Ulang 4'
                        ]])
                        ->all(),
                    'id'
                );
                if (!empty($orangeProcessIds)) {
                    foreach ($orangeProcessIds as $pId) {
                        if (isset($processData[$pId])) {
                            $v = $processData[$pId];
                            if (!empty($v['tanggal']) || !empty($v['shift_group'])) {
                                $isOrangeFilled = true;
                                break;
                            }
                        }
                    }
                }
            }

            $isDyeingFilled = false;
            if (!$isPackFilled && !$isOrangeFilled) {
                $dyeingProcess = \common\models\ar\MstProcessDyeing::findOne(['nama_proses' => 'Dyeing']);
                if ($dyeingProcess !== null && isset($processData[$dyeingProcess->id])) {
                    $v = $processData[$dyeingProcess->id];
                    if (!empty($v['tanggal']) || !empty($v['shift_group'])) {
                        $isDyeingFilled = true;
                    }
                }
            }

            // Determine if Preset or Setting is filled (Pink) - Disabled per user request
            $isPinkFilled = false;

            $rowStyleID = 'Default';
            if ($isPackFilled) {
                $rowStyleID = 'RowPackFilled';
            } elseif ($isOrangeFilled) {
                $rowStyleID = 'RowTopingFilled';
            } elseif ($isDyeingFilled) {
                $rowStyleID = 'RowDyeingFilled';
            } elseif ($isPinkFilled) {
                $rowStyleID = 'RowPinkFilled';
            }

            // Cell-specific relaxing/scutcher highlight for NK column
            $hasRelax = false;
            $hasScutcher = false;
            $relaxProcess = \common\models\ar\MstProcessDyeing::findOne(['nama_proses' => 'Relaxing']);
            $scutcherProcess = \common\models\ar\MstProcessDyeing::findOne(['nama_proses' => 'Scutcher Relaxing']);
            if ($relaxProcess !== null && isset($processData[$relaxProcess->id])) {
                $v = $processData[$relaxProcess->id];
                if (!empty($v['tanggal']) || !empty($v['shift_group'])) {
                    $hasRelax = true;
                }
            }
            if ($scutcherProcess !== null && isset($processData[$scutcherProcess->id])) {
                $v = $processData[$scutcherProcess->id];
                if (!empty($v['tanggal']) || !empty($v['shift_group'])) {
                    $hasScutcher = true;
                }
            }
            $isNkHighlighted = (!$isPackFilled && $hasRelax && $hasScutcher);

            // Process column custom highlights
            $processHighlightStyles = [];
            foreach ($masterProcesses as $proc) {
                $colKey = 'col-' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $proc->nama_proses . ' / Shift'));
                $hasVal = false;
                if (isset($processData[$proc->id])) {
                    $v = $processData[$proc->id];
                    if (!empty($v['tanggal']) || !empty($v['shift_group'])) {
                        $hasVal = true;
                    }
                }
                if ($hasVal) {
                    $nama = $proc->nama_proses;
                    if ($nama === 'Dyeing') {
                        $processHighlightStyles[$colKey] = 'ColDyeingStyle';
                    } elseif ($nama === 'Resin Finish') {
                        $processHighlightStyles[$colKey] = 'ColResinStyle';
                    } elseif ($nama === 'Heat Cut') {
                        $processHighlightStyles[$colKey] = 'ColHeatCutPackStyle';
                    } elseif (in_array($nama, ['Preset', 'Setting', 'Setting-2'])) {
                        $processHighlightStyles[$colKey] = 'ColPresetSettingStyle';
                    } elseif (in_array($nama, [
                        'Perbaikan JB',
                        'Toping 1', 'Toping 2', 'Toping 3', 'Toping 4',
                        'Cuci Ulang',
                        'RC 1', 'RC 2', 'RC 3', 'RC 4', 'RC 5',
                        'Toping Level',
                        'Celup Rayon',
                        'Tarik Ulang',
                        'RF Ulang 1', 'RF Ulang 2', 'RF Ulang 3', 'RF Ulang 4'
                    ])) {
                        $processHighlightStyles[$colKey] = 'ColTopingRcStyle';
                    }
                }
            }

            echo '   <Row>' . "\n";
            $excelColIndex = 1;
            foreach ($visibleCols as $colKey) {
                $isMerged = isset($mergeMap[$idx][$colKey]);
                $isFirst = $isMerged ? $mergeMap[$idx][$colKey]['IsFirst'] : true;
                $mergeSpans = $isMerged ? $mergeMap[$idx][$colKey]['MergeDown'] : 0;
                
                if (!$isFirst) {
                    $excelColIndex++;
                    continue;
                }
                
                $val = isset($rowData[$colKey]) ? $rowData[$colKey] : '';
                
                $cellAttrs = ' ss:Index="' . $excelColIndex . '"';
                if ($mergeSpans > 0) {
                    $cellAttrs .= ' ss:MergeDown="' . $mergeSpans . '"';
                }
                
                // Determine style ID dynamically at cell-level to prevent row/cell merging conflicts in Excel
                $cellStyleName = '';
                if ($isPackFilled) {
                    $cellStyleName = 'RowPackFilled';
                } elseif ($colKey === 'col-nomor-kartu' && $isNkHighlighted) {
                    $cellStyleName = 'ColNomorKartuGreenStyle';
                } elseif (isset($processHighlightStyles[$colKey])) {
                    $cellStyleName = $processHighlightStyles[$colKey];
                } elseif ($rowStyleID !== 'Default') {
                    $cellStyleName = $rowStyleID;
                }
                
                if (!empty($cellStyleName)) {
                    $cellAttrs .= ' ss:StyleID="' . $cellStyleName . '"';
                }
                
                if (is_numeric($val) && strpos($val, '0') !== 0) {
                    echo '    <Cell' . $cellAttrs . '><Data ss:Type="Number">' . $val . '</Data></Cell>' . "\n";
                } else {
                    $valCleaned = htmlspecialchars($val, ENT_QUOTES | ENT_XML1, 'UTF-8');
                    echo '    <Cell' . $cellAttrs . '><Data ss:Type="String">' . $valCleaned . '</Data></Cell>' . "\n";
                }
                $excelColIndex++;
            }
            echo '   </Row>' . "\n";
            
            // Print Note/Memo row if WO number changes and there is a note (exactly matching the web UI afterRow logic)
            $nextRowItem = isset($rowDataList[$idx + 1]) ? $rowDataList[$idx + 1] : null;
            $currentWoNo = $rowData['col-wono'];
            $nextWoNo = $nextRowItem ? $nextRowItem['rowData']['col-wono'] : null;
            
            if ($currentWoNo !== $nextWoNo) {
                if ($model->wo) {
                    echo '   <Row ss:Height="45">' . "\n";
                    
                    // Column 1 (ID): Empty cell
                    echo '    <Cell ss:Index="1" ss:StyleID="NoteRowStyle"><Data ss:Type="String"></Data></Cell>' . "\n";
                    
                    // Column 2: Note container with WO Metadata. We use ss:MergeAcross="3" (to span columns 2, 3, 4, 5)
                    $tglKirim = isset($rowData['col-tgl--kirim']) ? $rowData['col-tgl--kirim'] : '';
                    $hand = isset($rowData['col-hand']) ? $rowData['col-hand'] : '';
                    $tFinish = isset($rowData['col-t--finish']) ? $rowData['col-t--finish'] : '';
                    $panjang = isset($rowData['col-panjang']) ? $rowData['col-panjang'] : '';
                    $noteVal = isset($rowData['col-note']) ? $rowData['col-note'] : '';
                    
                    $metaString = implode(', ', array_filter([$tglKirim, $hand, $tFinish, $panjang . ' M'], function($val) {
                        return $val !== '' && $val !== null;
                    }));
                    
                    if (!empty($noteVal)) {
                        $metaString .= "\n\nNote:\n" . $noteVal;
                    }
                    
                    $cleanNoteVal = htmlspecialchars($metaString, ENT_QUOTES | ENT_XML1, 'UTF-8');
                    echo '    <Cell ss:Index="2" ss:MergeAcross="3" ss:StyleID="NoteStyle"><Data ss:Type="String">' . $cleanNoteVal . '</Data></Cell>' . "\n";
                    
                    // Column 6: Memo container. We use ss:MergeAcross="5" (to span columns 6, 7, 8, 9, 10, 11)
                    $memoVal = isset($rowData['col-memo']) ? $rowData['col-memo'] : '';
                    if (!empty($memoVal)) {
                        $memoValClean = strip_tags($memoVal);
                        $memoValClean = html_entity_decode($memoValClean, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        $cleanMemoVal = 'Memo Perubahan WO: ' . htmlspecialchars($memoValClean, ENT_QUOTES | ENT_XML1, 'UTF-8');
                    } else {
                        $cleanMemoVal = 'Tidak ada Memo Perubahan WO';
                    }
                    echo '    <Cell ss:Index="6" ss:MergeAcross="5" ss:StyleID="MemoStyle"><Data ss:Type="String">' . $cleanMemoVal . '</Data></Cell>' . "\n";
                    
                    // Remaining columns (index 12 to count($visibleCols)) are printed as empty cells
                    $totalCols = count($visibleCols);
                    for ($c = 12; $c <= $totalCols; $c++) {
                        echo '    <Cell ss:Index="' . $c . '" ss:StyleID="NoteRowStyle"><Data ss:Type="String"></Data></Cell>' . "\n";
                    }
                    echo '   </Row>' . "\n";
                }
            }
        }
        
        echo '  </Table>' . "\n";
        echo '  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">' . "\n";
        echo '   <Selected/>' . "\n";
        echo '   <FreezePanes/>' . "\n";
        echo '   <SplitHorizontal>1</SplitHorizontal>' . "\n";
        echo '   <TopRowBottomPane>1</TopRowBottomPane>' . "\n";
        echo '   <ActivePane>2</ActivePane>' . "\n";
        echo '  </WorksheetOptions>' . "\n";
        echo ' </Worksheet>' . "\n";
        echo '</Workbook>' . "\n";
        exit();
    }

    /**
     * Displays a single TrnKartuProsesDyeing model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
                /*Yii::$app->session->setFlash('info',
                    '<ul>
        <li>Pembatalan hanya diizinkan jika belum ada proses yang dimulai.</li>
        <li>Jika dibatalkan, Semua roll dikembalikan statusnya menjadi valid agar bisa digunakan lagi oleh kartu proses yang lain.</li>
        </ul>'
                );*/

        $model = $this->findModel($id);
        $moJetBlack = ($model->mo !== null && $model->mo->jet_black);
        $nonJetblackProcesses = MstProcessDyeing::find()->where(['use_jetblack' => false, 'perbaikan' => false])->orderBy('order')->all();
        $jetblackProcesses = MstProcessDyeing::find()->where(['use_jetblack' => true, 'perbaikan' => false])->orderBy('order')->all();

        $processModels = [];
        $resinFinishIndex = -1;
        foreach ($nonJetblackProcesses as $proc) {
            $processModels[] = $proc;
            if (trim($proc->nama_proses) === 'Resin Finish') {
                $resinFinishIndex = count($processModels);
            }
        }

        if ($moJetBlack && !empty($jetblackProcesses)) {
            if ($resinFinishIndex !== -1) {
                array_splice($processModels, $resinFinishIndex, 0, $jetblackProcesses);
            } else {
                $processModels = array_merge($processModels, $jetblackProcesses);
            }
        }

        $attrsLabels = [];
        if($processModels !== null){
            $attrsLabels = $processModels[0]->attributeLabels();
            unset($attrsLabels['order']); unset($attrsLabels['created_at']); unset($attrsLabels['created_by']); unset($attrsLabels['updated_at']); unset($attrsLabels['updated_by']); unset($attrsLabels['max_pengulangan']);
            //BaseVarDumper::dump($attrsLabels, 10, true);Yii::$app->end();
        }

        //Data pengulangan tiap-tiap proses
        $processesUlang = [];
        foreach ($model->kartuProcessDyeingProcesses as $i=>$kartuProcessDyeingProcess) {
            if($kartuProcessDyeingProcess->value !== null){
                $dataProcess = Json::decode($kartuProcessDyeingProcess->value);
                if(isset($dataProcess['pengulangan']) && !empty($dataProcess['pengulangan'])){
                    $processUlang = [
                        'nama_proses'=>'',
                        'header'=>[],
                        'pengulangan'=>[]
                    ];

                    $headers = [];
                    $attrs = $kartuProcessDyeingProcess->process->attributes;
                    unset($attrs['id']); unset($attrs['order']); unset($attrs['created_at']); unset($attrs['created_by']); unset($attrs['updated_at']); unset($attrs['updated_by']); unset($attrs['max_pengulangan']);
                    foreach ($attrs as $key=>$attr) {
                        if($key === 'nama_proses'){
                            $processUlang['nama_proses'] = $attr;
                            unset($attrs['nama_proses']);
                        }else{
                            if($attr){
                                $headers[$key] = $kartuProcessDyeingProcess->getAttributeLabel($key);
                            }
                        }
                    }
                    $processUlang['header'] = $headers;

                    foreach ($dataProcess['pengulangan'] as $j=>$pengulangan) {
                        $data = [
                            'head'=>['time'=>$pengulangan['time'], 'memo'=>$pengulangan['memo'], 'by'=>$pengulangan['by'], 'data'=>[]]
                        ];
                        $pengulanganData = $pengulangan['data'];
                        foreach ($headers as $key=>$header) {
                            if(isset($pengulanganData[$key])){
                                $data['data'][$key] = $pengulanganData[$key];
                            }else{
                                $data['data'][$key] = null;
                            }
                        }
                        $processUlang['pengulangan'][] = $data;
                    }

                    $processesUlang[] = $processUlang;
                }
            }
        }
        //BaseVarDumper::dump($processesUlang, 10, true);Yii::$app->end();
        //Data pengulangan tiap-tiap proses

        return $this->render('view', [
            'model' => $model,
            'attrsLabels' => $attrsLabels,
            'processModels' => $processModels,
            'processesUlang' => $processesUlang
        ]);
    }

    /**
     * Updates an existing TrnKartuProsesDyeing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     */
    public function actionGantiGreige($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if($model->status != $model::STATUS_DELIVERED){
                throw new NotFoundHttpException('Status Kartu Proses tidak valid, proses tidak bisa dilanjutkan.');
            }

            $memoPg = Yii::$app->request->post('data');
            if(empty($memoPg)){
                throw new ForbiddenHttpException('Memo penggantian greige wajib diisi.');
            }

            $model->status = $model::STATUS_GANTI_GREIGE;
            $model->memo_pg = $memoPg;
            $model->memo_pg_at = time();
            $model->memo_pg_by = Yii::$app->user->id;

            $greigeId = $model->wo->greige_id;
            $totalPanjang  = 0;

            /* @var $newStocks TrnStockGreige[]*/
            $newStocks = [];

            //siapkan stok semua roll untuk ditambahkan ke gudang wip
            foreach ($model->trnKartuProsesDyeingItems as $trnKartuProsesDyeingItem) {
                $stock = $trnKartuProsesDyeingItem->stock;
                $newStock = new TrnStockGreige();
                $newStock->load([$newStock->formName()=>$stock->attributes]);
                $newStock->setAttributes([
                    'created_at' => null,
                    'created_by' => null,
                    'updated_at' => null,
                    'updated_by' => null,
                    'jenis_gudang'=>TrnStockGreige::JG_WIP,
                    'status'=>TrnStockGreige::STATUS_VALID,
                    'date'=>date('Y-m-d'),
                    'note'=>'Gagal proses pada kartu proses dyeing No:'.$model->no
                ]);
                $newStocks[] = $newStock;
                $totalPanjang += $newStock->panjang_m;
            }//siapkan stok semua roll untuk ditambahkan ke gudang wip

            $transaction = Yii::$app->db->beginTransaction();
            try {
                //tambahkan stok semua roll ke gudang wip
                foreach ($newStocks as $newStock) {
                    if(!$flag = $newStock->save()){
                        $transaction->rollBack();
                        throw new HttpException(500, Json::encode($newStock->attributes));
                        //throw new HttpException(500, 'Gagal membuat memo penggantian greige, coba lagi.');
                    }
                }
                //tambahkan stok semua roll ke gudang wip

                //sesuaikan jumlah stok pada maste greige
                $cmd = "UPDATE mst_greige SET stock_wip=stock_wip+{$totalPanjang} WHERE id=:id";
                $command = Yii::$app->db->createCommand($cmd)->bindParam(':id', $greigeId);
                if(!$flag = $command->execute() > 0){
                    $transaction->rollBack();
                    throw new HttpException(500, 'Gagal membuat memo penggantian greige, coba lagi.');
                }
                //sesuaikan jumlah stok pada maste greige

                if(!$flag = $model->save(false, ['status', 'memo_pg', 'memo_pg_at', 'memo_pg_by'])){
                    $transaction->rollBack();
                    throw new HttpException(500, 'Gagal membuat memo penggantian greige, coba lagi.');
                }

                if($flag){
                    $transaction->commit();
                    return true;
                }
            }catch (\Throwable $e){
                $transaction->rollBack();
                throw $e;
            }
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * @param $id
     * @param $proses_id
     * @param $attr
     * @return bool
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionJalankanProses($id, $proses_id, $attr)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if(in_array($model->status, [$model::STATUS_DRAFT, $model::STATUS_POSTED, $model::STATUS_BATAL])){
                throw new ForbiddenHttpException('Kartu proses tidak valid.');
            }

            $pcModel = KartuProcessDyeingProcess::findOne(['kartu_process_id'=>$model->id, 'process_id'=>$proses_id]);
            if($pcModel === null){
                $datas = [];
                $pcModel = new KartuProcessDyeingProcess(['kartu_process_id'=>$model->id, 'process_id'=>$proses_id]);
            }else{
                $datas = Json::decode($pcModel->value);
            }

            $mstProcessModel = $pcModel->process;

            $datas[$attr] = Yii::$app->request->post('data');

            if ($attr === 'no_mesin') {
                $machineName = trim(Yii::$app->request->post('data'));
                if (!empty($machineName)) {
                    $exist = \common\models\ar\MstMesinProses::findOne(['nama_mesin' => $machineName]);
                    if ($exist === null) {
                        $newMachine = new \common\models\ar\MstMesinProses();
                        $newMachine->nama_mesin = $machineName;
                        $firstMapped = \common\models\ar\MstMesinProses::find()
                            ->innerJoin('mst_process_dyeing_mesin', 'mst_mesin_proses.id = mst_process_dyeing_mesin.mst_mesin_proses_id')
                            ->where(['mst_process_dyeing_mesin.mst_process_dyeing_id' => $proses_id])
                            ->one();
                        if ($firstMapped !== null) {
                            $newMachine->model_mesin = $firstMapped->model_mesin;
                        }
                        if ($newMachine->save(false)) {
                            Yii::$app->db->createCommand()->insert('mst_process_dyeing_mesin', [
                                'mst_process_dyeing_id' => $proses_id,
                                'mst_mesin_proses_id' => $newMachine->id
                            ])->execute();
                        }
                    }
                }
            }

            $pcModel->value = Json::encode($datas);

            $label = $pcModel->getAttributeLabel($attr);
            $lblBtn = $datas[$attr].' <span class="glyphicon glyphicon-pencil text-warning" aria-hidden="true"></span>';
            switch ($attr){
                case 'tanggal':
                    $btn = Html::a($lblBtn, ['jalankan-proses', 'id'=>$model->id, 'proses_id'=>$mstProcessModel->id, 'attr'=>$attr], [
                        'onclick' => 'setDateInput(event, "'.$label.' '.$mstProcessModel->nama_proses.'");',
                        'title' => 'Set '.$label.' '.$mstProcessModel->nama_proses
                    ]);
                    break;
                case 'start':
                case 'stop':
                    $btn = Html::a($lblBtn, ['jalankan-proses', 'id'=>$model->id, 'proses_id'=>$mstProcessModel->id, 'attr'=>$attr], [
                        'onclick' => 'setTimeInput(event, "Waktu '.$label.' '.$mstProcessModel->nama_proses.'");',
                        'title' => 'Set '.$label.' '.$mstProcessModel->nama_proses
                    ]);
                    break;
                case 'shift_group':
                    $btn = Html::a($lblBtn, ['jalankan-proses', 'id'=>$model->id, 'proses_id'=>$mstProcessModel->id, 'attr'=>$attr], [
                        'onclick' => 'setShiftGroupInput(event, "'.$label.' '.$mstProcessModel->nama_proses.'", "'.$datas[$attr].'");',
                        'title' => 'Set '.$label.' '.$mstProcessModel->nama_proses
                    ]);
                    break;
                case 'no_mesin':
                    $btn = Html::a($lblBtn, ['jalankan-proses', 'id'=>$model->id, 'proses_id'=>$mstProcessModel->id, 'attr'=>$attr], [
                        'onclick' => 'setNoMesinInput(event, "'.$label.' '.$mstProcessModel->nama_proses.'", '.$mstProcessModel->id.', "'.$datas[$attr].'");',
                        'title' => 'Set '.$label.' '.$mstProcessModel->nama_proses
                    ]);
                    break;
                default:
                    $btn = Html::a($lblBtn, ['jalankan-proses', 'id'=>$model->id, 'proses_id'=>$mstProcessModel->id, 'attr'=>$attr], [
                        'onclick' => 'setTextInput(event, "'.$label.' '.$mstProcessModel->nama_proses.'");',
                        'title' => 'Set '.$label.' '.$mstProcessModel->nama_proses
                    ]);
            }

            if($pcModel->save(false)){
                return $btn;
            }else{
                throw new HttpException(500, 'Gagal, coba lagi.');
            }
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * @param $id
     * @param $proses_id
     * @return bool
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionReProses($id, $proses_id){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if(in_array($model->status, [$model::STATUS_DRAFT, $model::STATUS_POSTED, $model::STATUS_BATAL])){
                throw new ForbiddenHttpException('Kartu proses tidak valid.');
            }

            $pcModel = KartuProcessDyeingProcess::findOne(['kartu_process_id'=>$model->id, 'process_id'=>$proses_id]);
            if($pcModel === null){
                throw new ForbiddenHttpException('Belum pernah diproses, tidak bisa diulang.');
            }

            if($pcModel->process->max_pengulangan < 1){
                throw new ForbiddenHttpException('Proses ini tidak bisa diulang.');
            }

            $pengulangans = [];
            $oldData = Json::decode($pcModel->value);
            if(isset($oldData['pengulangan']) && !empty($oldData['pengulangan'])){
                $kuotaPengulangan = $pcModel->process->max_pengulangan;
                if($kuotaPengulangan <= count($oldData['pengulangan'])){
                    throw new ForbiddenHttpException('Pengulangan sudah mencapai kuota <strong>'.$kuotaPengulangan.' kali</strong> pengulangan.');
                }

                $pengulangans = $oldData['pengulangan'];
                unset($oldData['pengulangan']);
            }

            $pengulangans[] = [
                'memo'=>Yii::$app->request->post('data'),
                'time'=>time(),
                'by'=>Yii::$app->user->id,
                'data'=>$oldData
            ];

            $pcModel->value = Json::encode(['pengulangan'=>$pengulangans]);
            if($pcModel->save(false)){
                return true;
            }else{
                throw new HttpException(500, 'Gagal, coba lagi.');
            }
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    protected function logKartuDyeing($actionName, $kartuProsesId, $description = null)
    {
        Yii::$app->db->createCommand()->insert('action_log_kartu_dyeing', [
            'user_id'       => Yii::$app->user->id,
            'username'      => Yii::$app->user->identity->username ?? null,
            'kartu_proses_id' => $kartuProsesId,
            'action_name'   => $actionName,
            'description'   => $description,
            'ip'            => Yii::$app->request->userIP,
            'user_agent'    => Yii::$app->request->userAgent,
            'created_at'    => date('Y-m-d H:i:s'),
        ])->execute();
    }

    /**
     * Deletes an existing KartuProsesDyeing model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionApprove($id)
    {
        $model = $this->findModel($id);

        $activeChildCards = TrnKartuProsesDyeing::find()
            ->where([
                'kartu_proses_id' => $model->id,
                'status' => TrnKartuProsesDyeing::STATUS_DELIVERED
            ])
            ->all();

        $canApprove = ($model->status == $model::STATUS_DELIVERED) || 
                      ($model->status == $model::STATUS_APPROVED && !empty($activeChildCards)) ||
                      ($model->status == $model::STATUS_BATAL && !empty($activeChildCards));

        if (!$canApprove) {
            Yii::$app->session->setFlash('error', 'Status Kartu proses tidak valid untuk disetujui.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        //di bypass saja, tidak perlu divalidasi
        /*if(!$model->isAllProcessDone){
            Yii::$app->session->setFlash('error', 'Proses belum selesai.');
            return $this->redirect(['view', 'id' => $model->id]);
        }*/

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $isFirstApproval = ($model->status != $model::STATUS_APPROVED);

            if ($isFirstApproval) {
                $model->status = $model::STATUS_APPROVED;
                $model->approved_at = time();
                $model->approved_by = Yii::$app->user->id;
            }

            // Determine approval note and prepare history entry
            $approveChildIds = Yii::$app->request->post('approve_child_ids', []);
            $approvedChildNames = [];

            // Approve chosen split child cards - merge their rolls back to parent!
            if (!empty($approveChildIds)) {
                foreach ($approveChildIds as $childId) {
                    $child = TrnKartuProsesDyeing::findOne($childId);
                    if ($child && $child->status == TrnKartuProsesDyeing::STATUS_DELIVERED) {
                        $approvedChildNames[] = $child->nomor_kartu;

                        // 1. Transfer all items of this split card back to the parent card
                        foreach ($child->trnKartuProsesDyeingItems as $item) {
                            $item->kartu_process_id = $model->id;
                            if (!$item->save(false)) {
                                throw new \Exception("Gagal mengembalikan item dari kartu split {$child->nomor_kartu} ke kartu induk.");
                            }
                        }

                        // 2. Set the split child card status to STATUS_APPROVED and record approved details
                        $child->status = TrnKartuProsesDyeing::STATUS_APPROVED;
                        $child->approved_at = time();
                        $child->approved_by = Yii::$app->user->id;
                        
                        $childHistory = [];
                        if (!empty($child->approved_history)) {
                            $childHistory = Json::decode($child->approved_history);
                        }
                        $childHistory[] = [
                            'time' => $child->approved_at,
                            'by' => $child->approved_by,
                            'note' => "Disetujui & digabungkan kembali ke induk {$model->nomor_kartu}"
                        ];
                        $child->approved_history = Json::encode($childHistory);

                        // Save child status and history (keep child note completely untouched!)
                        if (!$child->save(false, ['status', 'approved_at', 'approved_by', 'approved_history'])) {
                            throw new \Exception("Gagal memproses persetujuan kartu split {$child->nomor_kartu}.");
                        }

                        $this->logKartuDyeing(
                            'split_approved',
                            $child->id,
                            "Kartu split disetujui dan item digabungkan kembali ke kartu induk '{$model->nomor_kartu}'"
                        );

                        // Log "masuk_verpacking" entry for EACH approved split child card on the parent card!
                        $this->logKartuDyeing(
                            'masuk_verpacking',
                            $model->id,
                            "Kartu Split {$child->nomor_kartu} disetujui & masuk verpacking"
                        );
                    }
                }
            }

            // Log "masuk_verpacking" for standard first approval if there are no split children
            if ($isFirstApproval && empty($approvedChildNames)) {
                $this->logKartuDyeing(
                    'masuk_verpacking',
                    $model->id,
                    "Kartu Proses {$model->nomor_kartu} disetujui & masuk verpacking"
                );
            }

            // Always append to parent's approved_history
            $history = [];
            if (!empty($model->approved_history)) {
                $history = Json::decode($model->approved_history);
            }

            if (!empty($approvedChildNames)) {
                $appNote = "Menyetujui Kartu Split: " . implode(', ', $approvedChildNames);
            } else {
                $appNote = "Persetujuan Kartu Induk";
            }

            $history[] = [
                'time' => time(),
                'by' => Yii::$app->user->id,
                'note' => $appNote
            ];
            $model->approved_history = Json::encode($history);

            // Save the parent model (original note remains untouched!)
            if ($isFirstApproval) {
                if (!$model->save(false, ['status', 'approved_at', 'approved_by', 'approved_history'])) {
                    throw new \Exception('Gagal menyetujui kartu induk.');
                }
            } else {
                if (!$model->save(false, ['approved_history'])) {
                    throw new \Exception('Gagal memperbarui riwayat persetujuan kartu induk.');
                }
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Berhasil disetujui, proses bisa dilanjutkan ke tahap inspecting.');
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Gagal menyetujui kartu proses: ' . $e->getMessage());
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Pembatalan Kartu Proses.
     * Hanya diizinkan jika belum ada proses yang dimulai (relasi $model->getKartuProcessDyeingProcesses() masih kosong).
     * Kartu Proses diubah statusnya menjadi 'Batal'
     * Semua roll greige yang ada dikembalikan statusnya menjadi valid agar bisa digunakan lagi oleh kartu proses yang lain
     * Update/kembalikan stock greige terkait, tambahkan sejumlah roll pada kartu proses yang dibatalkan
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionBatal($id)
    {
        $model = $this->findModel($id);

        if($model->status != $model::STATUS_DELIVERED){
            Yii::$app->session->setFlash('error', 'Status Kartu proses tidak valid.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        // if($model->getKartuProcessDyeingProcesses()->count('kartu_process_id') > 0){
        //     Yii::$app->session->setFlash('error', 'Proses sudah berjalan, tidak bisa dibatalkan.');
        //     return $this->redirect(['view', 'id' => $model->id]);
        // }

        $model->status = $model::STATUS_BATAL;

        $totalLength = 0;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(!($flag = $model->save(false, ['status']))){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Pembatalan Gagal, coba lagi.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            foreach ($model->trnKartuProsesDyeingItems as $trnKartuProsesDyeingItem) {
                $stock = $trnKartuProsesDyeingItem->stock;
                $stock->status = $stock::STATUS_VALID;
                if(!($flag = $stock->save(false, ['status']))){
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Pembatalan Gagal, coba lagi.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                $totalLength += $stock->panjang_m;
            }

            //Update stock greige--------------------------------------------------------------------------------------------
            $wo = $model->wo;
            $mo = $wo->mo;

            switch ($mo->jenis_gudang){
                case TrnStockGreige::JG_WIP:
                    $stockAttr = 'stock_wip';
                    break;
                case TrnStockGreige::JG_PFP:
                    $stockAttr = 'stock_pfp';
                    break;
                case TrnStockGreige::JG_EX_FINISH:
                    $stockAttr = 'stock_ef';
                    break;
                default:
                    $stockAttr = 'stock';
            }
            $greigeId = $wo->greige_id;
            $sqlCmd = "UPDATE mst_greige SET {$stockAttr} = {$stockAttr} + {$totalLength} WHERE id=:id";
            $command = Yii::$app->db->createCommand($sqlCmd)->bindParam(':id', $greigeId);
            if(!$flag = $command->execute() > 0){
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal, coba lagi.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
            //Update stock greige--------------------------------------------------------------------------------------------

            if($flag){
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Kartu proses berhasil dibatalkan.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
            //BaseVarDumper::dump($model, 10, true);Yii::$app->end();
        }catch (\Throwable $e){
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    /**
     * @param $id
     * @return array|bool|string|Response
     * @throws ForbiddenHttpException
     */
    public function actionAddCatatanProses($id){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = new CatatanProsesForm(['kartu_proses_id'=>$id]);

            if($model->load(Yii::$app->request->post())){
                if($model->validate()){
                    $modelKp = TrnKartuProsesDyeing::findOne($model->kartu_proses_id);
                    if($modelKp === null){
                        $model->addError('kartu_proses_id', 'Kartu Proses Id tidak valid');
                    }else{
                        $modelKp->note = $model->note;
                        $modelKp->save(false, ['note']);
                        return ['success'=>true, 'data'=>$model->note];
                    }
                }

                $result = [];
                // The code below comes from ActiveForm::validate(). We do not need to validate the model
                // again, as it was already validated by save(). Just collect the messages.
                foreach ($model->getErrors() as $attribute => $errors) {
                    $result[Html::getInputId($model, $attribute)] = $errors;
                }
                return ['validation' => $result];
            }

            return $this->renderAjax('add-catatan-proses', [
                'model'=>$model,
            ]);
        }

        throw new ForbiddenHttpException('Not allowed');
    }

    /**
     * @param $id
     * @return array|bool|string|Response
     * @throws ForbiddenHttpException
     */
    public function actionAddHasilTesGosok($id){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = new HasilTesGosokForm(['kartu_proses_id'=>$id]);

            if($model->load(Yii::$app->request->post())){
                if($model->save()){
                    return ['success'=>true, 'data'=>$model->hasil_tes_gosok];
                }

                $result = [];
                // The code below comes from ActiveForm::validate(). We do not need to validate the model
                // again, as it was already validated by save(). Just collect the messages.
                foreach ($model->getErrors() as $attribute => $errors) {
                    $result[Html::getInputId($model, $attribute)] = $errors;
                }
                return ['validation' => $result];
            }

            return $this->renderAjax('add-hasil-tes-gosok', [
                'model'=>$model,
            ]);
        }

        throw new ForbiddenHttpException('Not allowed');
    }

    /**
     * @param $id
     * @return bool
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionGantiWo($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if($model->status != $model::STATUS_DELIVERED){
                throw new ForbiddenHttpException('Kartu proses tidak valid, tidak bisa diproses.');
            }

            $nomorWo = Yii::$app->request->post('data');
            if(empty($nomorWo)){
                throw new ForbiddenHttpException('Nomor WO kosong, tidak bisa diproses.');
            }

            $wo = TrnWo::findOne(['no'=>$nomorWo]);

            if($wo === null){
                throw new NotFoundHttpException('WO dengan nomor yang dimasukan tidak ditemukan.');
            }

            $oldWoNo = $model->wo ? $model->wo->no : '-';

            $model->wo_id = $wo->id;
            $model->wo_color_id = TrnWoColor::find()->select('id')->where(['wo_id'=>$wo->id])->asArray()->one()['id'];
            $model->mo_id = $wo->mo_id;
            $model->sc_id = $wo->sc_id;
            $model->handling = $wo->handling->name;
            $model->lebar_preset = $wo->handling->lebar_preset;
            $model->lebar_finish = $wo->handling->lebar_finish;
            $model->berat_finish = $wo->handling->berat_finish;
            $model->t_density_lusi = $wo->handling->densiti_lusi;
            $model->t_density_pakan = $wo->handling->densiti_pakan;
            $model->save(false, ['wo_id','wo_color_id', 'mo_id', 'sc_id', 'handling', 'lebar_preset', 'lebar_finish', 'berat_finish', 't_density_lusi', 't_density_pakan']);

            $this->logKartuDyeing(
                'ganti_wo',
                $model->id,
                "Mengubah WO dari '{$oldWoNo}' menjadi '{$wo->no}'"
            );

            return true;
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * @param $id
     * @return bool
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionGantiWarna($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            if($model->status != $model::STATUS_DELIVERED){
                throw new ForbiddenHttpException('Kartu proses tidak valid, tidak bisa diproses.');
            }

            $post = Yii::$app->request->post('data');
            if(empty($post)){
                throw new ForbiddenHttpException('Keterangan kosong, tidak bisa diproses.');
            }

            $oldColor = ($model->woColor && $model->woColor->moColor) ? $model->woColor->moColor->color : '-';

            $model->wo_color_id = $post;
            $model->save(false, ['wo_color_id']);

            $newWoColor = TrnWoColor::findOne($post);
            $newColor = ($newWoColor && $newWoColor->moColor) ? $newWoColor->moColor->color : '-';

            $this->logKartuDyeing(
                'ganti_warna',
                $model->id,
                "Mengubah Warna dari '{$oldColor}' menjadi '{$newColor}'"
            );

            return true;
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * Splits an existing TrnKartuProsesDyeing model into two child cards.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSplit($id)
    {
        $model = $this->findModel($id);

        if ($model->status != $model::STATUS_DELIVERED) {
            throw new ForbiddenHttpException('Kartu proses tidak valid, tidak bisa di-split.');
        }

        if (Yii::$app->request->isPost) {
            $selectedItemIds = Yii::$app->request->post('selected_items', []);
            $allItems = $model->trnKartuProsesDyeingItems;
            $allItemIds = array_map(function($item) { return $item->id; }, $allItems);

            if (empty($selectedItemIds)) {
                Yii::$app->session->setFlash('error', 'Anda harus memilih setidaknya satu item untuk dipindahkan ke kartu split.');
                return $this->render('split', [
                    'model' => $model,
                    'items' => $allItems,
                ]);
            }

            $remainingItemIds = array_diff($allItemIds, $selectedItemIds);
            if (empty($remainingItemIds)) {
                Yii::$app->session->setFlash('error', 'Anda harus menyisakan setidaknya satu item pada kartu induk (jangan pilih semua item).');
                return $this->render('split', [
                    'model' => $model,
                    'items' => $allItems,
                ]);
            }

            // Parse parent nomor_kartu (e.g., "1/26" -> "1S1/26" & "1S2/26")
            $nomor_kartu = $model->nomor_kartu;
            $parts = explode('/', $nomor_kartu);
            $part1 = $parts[0];
            $part2 = isset($parts[1]) ? '/' . $parts[1] : '';

            // Find the next two available split suffixes (S1, S2, etc.)
            $seq = 1;
            $s1_nomor = '';
            while (true) {
                $s1_nomor = $part1 . 'S' . $seq . $part2;
                $exists = TrnKartuProsesDyeing::find()->where(['nomor_kartu' => $s1_nomor])->exists();
                if (!$exists) {
                    break;
                }
                $seq++;
            }

            $seq2 = $seq + 1;
            $s2_nomor = '';
            while (true) {
                $s2_nomor = $part1 . 'S' . $seq2 . $part2;
                $exists = TrnKartuProsesDyeing::find()->where(['nomor_kartu' => $s2_nomor])->exists();
                if (!$exists) {
                    break;
                }
                $seq2++;
            }

            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                $attributes = $model->attributes;
                unset($attributes['id']);
                unset($attributes['created_at']);
                unset($attributes['created_by']);
                unset($attributes['updated_at']);
                unset($attributes['updated_by']);

                // Create split child card S1 (selected items)
                $s1 = new TrnKartuProsesDyeing();
                $s1->attributes = $attributes;
                $s1->nomor_kartu = $s1_nomor;
                $s1->kartu_proses_id = $model->id;
                $s1->status = TrnKartuProsesDyeing::STATUS_DELIVERED;
                $s1->setNomor();
                if (!$s1->save()) {
                    throw new \Exception('Gagal membuat kartu split pertama: ' . implode(', ', $s1->getFirstErrors()));
                }

                // Create split child card S2 (remaining items)
                $s2 = new TrnKartuProsesDyeing();
                $s2->attributes = $attributes;
                $s2->nomor_kartu = $s2_nomor;
                $s2->kartu_proses_id = $model->id;
                $s2->status = TrnKartuProsesDyeing::STATUS_DELIVERED;
                $s2->setNomor();
                if (!$s2->save()) {
                    throw new \Exception('Gagal membuat kartu split kedua: ' . implode(', ', $s2->getFirstErrors()));
                }

                // Move selected items to S1
                foreach ($selectedItemIds as $itemId) {
                    $item = TrnKartuProsesDyeingItem::findOne($itemId);
                    if ($item && $item->kartu_process_id == $model->id) {
                        $item->kartu_process_id = $s1->id;
                        if (!$item->save(false)) {
                            throw new \Exception('Gagal memindahkan item ke kartu split pertama.');
                        }
                    }
                }

                // Move remaining items to S2
                foreach ($remainingItemIds as $itemId) {
                    $item = TrnKartuProsesDyeingItem::findOne($itemId);
                    if ($item && $item->kartu_process_id == $model->id) {
                        $item->kartu_process_id = $s2->id;
                        if (!$item->save(false)) {
                            throw new \Exception('Gagal memindahkan item ke kartu split kedua.');
                        }
                    }
                }
                // Log the action
                $this->logKartuDyeing('split', $model->id, "Kartu di-split menjadi {$s1_nomor} dan {$s2_nomor}.");
                $this->logKartuDyeing('split_child', $s1->id, "Kartu split terbentuk dari induk '{$model->nomor_kartu}'");
                $this->logKartuDyeing('split_child', $s2->id, "Kartu split terbentuk dari induk '{$model->nomor_kartu}'");

                $transaction->commit();
                Yii::$app->session->setFlash('success', "Kartu proses berhasil di-split menjadi {$s1_nomor} dan {$s2_nomor}. Keduanya dalam status DELIVERED.");
                return $this->redirect(['view', 'id' => $model->id]);

            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal memproses split kartu: ' . $e->getMessage());
            }
        }

        return $this->render('split', [
            'model' => $model,
            'items' => $model->trnKartuProsesDyeingItems,
        ]);
    }

    /**
     * Finds the TrnKartuProsesDyeing model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrnKartuProsesDyeing the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrnKartuProsesDyeing::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionSetTungguMkt($id){
        $model = $this->findModel($id);

        $model->tunggu_marketing = !$model->tunggu_marketing;

        try {
            if(!$model->save(false, ['tunggu_marketing'])){
                throw new \Exception('Gagal mengubah status tunggu marketing.');
            }

            if($model->tunggu_marketing){
                Yii::$app->session->setFlash('success', 'Berhasil diset untuk menunggu marketing.');
            }else{
                Yii::$app->session->setFlash('success', 'Berhasil dibatalkan tunggu marketing.');
            }
        }catch (\Exception $e){
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionSetTopingMatching($id){
        $model = $this->findModel($id);

        $model->toping_matching = !$model->toping_matching;

        $model->date_toping_matching = time();

        try {
            if(!$model->save(false, ['toping_matching','date_toping_matching'])){
                throw new \Exception('Gagal mengubah status tunggu marketing.');
            }

            if($model->toping_matching){
                Yii::$app->session->setFlash('success', 'Berhasil diset untuk toping matching.');
            }else{
                Yii::$app->session->setFlash('success', 'Berhasil dibatalkan toping matching.');
            }
        }catch (\Exception $e){
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Lists all TrnKartuProsesDyeing models.
     * @return mixed
     */
    public function actionRekapByProcess()
    {
        $searchModel = new KartuProcessDyeingProcessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('rekap-by-process', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Ganti Wo dari kartu proses dyeing ke Pfp.
     *
     * @param integer $id id kartu proses dyeing
     * @return array
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionGantiKePfp($id)
    {
        if (Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);

            //$statusValid = $model->status === $model::STATUS_DELIVERED || $model->status === $model::STATUS_INSPECTED;
            $statusValid = $model->status === $model::STATUS_DELIVERED || $model->status === $model::STATUS_POSTED;

            if(!$statusValid){
                throw new ForbiddenHttpException('Status Kartu proses tidak valid.');
            }

            $data = Yii::$app->request->post();
            $noPfp = $data['no_order_pfp'];

            $orderPfp = TrnOrderPfp::findOne(['no'=>$noPfp]);
            if ($orderPfp === null){
                throw new NotFoundHttpException('Order Pfp tidak ditemukan.');
            }

            $modelKpPfp = new TrnKartuProsesPfp([
                'greige_group_id' => $orderPfp->greige_group_id,
                'greige_id' => $orderPfp->greige_id,
                'order_pfp_id' => $orderPfp->id, // sesuaikan jika ada relasi
                'no_urut' => $model->no_urut,
                'no' => $model->no,
                'asal_greige' => $model->asal_greige,
                'dikerjakan_oleh' => $model->dikerjakan_oleh,
                'lusi' => $model->lusi,
                'pakan' => $model->pakan,
                'note' => $model->note,
                'date' => $model->date,
                'posted_at' => $model->posted_at,
                'approved_at' => $model->approved_at,
                'approved_by' => $model->approved_by,
                'delivered_at' => $model->delivered_at,
                'delivered_by' => $model->delivered_by,
                'reject_notes' => $model->reject_notes,
                'status' => $model->status,
                'created_at' => $model->created_at,
                'created_by' => $model->created_by,
                'updated_at' => $model->updated_at,
                'updated_by' => $model->updated_by,
                'berat' => $model->berat,
                'lebar' => $model->lebar,
                'k_density_lusi' => $model->k_density_lusi,
                'k_density_pakan' => $model->k_density_pakan,
                'lebar_preset' => $model->lebar_preset,
                'lebar_finish' => $model->lebar_finish,
                'berat_finish' => $model->berat_finish,
                't_density_lusi' => $model->t_density_lusi,
                't_density_pakan' => $model->t_density_pakan,
                'handling' => $model->handling,
                'no_limit_item' => $model->no_limit_item,
                'nomor_kartu' => $model->nomor_kartu,
            ]);
            

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($modelKpPfp->save(false)){
                    foreach ($model->trnKartuProsesDyeingItems as $trnKartuProsesDyeingItem) {
                        Yii::$app->db->createCommand()->insert(TrnKartuProsesPfpItem::tableName(), [
                            'greige_group_id' => $modelKpPfp->greige_group_id,
                            'greige_id' => $modelKpPfp->greige_id,
                            'order_pfp_id' => $modelKpPfp->order_pfp_id,
                            'kartu_process_id' => $modelKpPfp->id,
                            'stock_id' => $trnKartuProsesDyeingItem->stock_id,
                            'panjang_m' => $trnKartuProsesDyeingItem->panjang_m,
                            'mesin' => $trnKartuProsesDyeingItem->mesin,
                            'tube' => $trnKartuProsesDyeingItem->tube,
                            'note' => $trnKartuProsesDyeingItem->note,
                            'status' => $trnKartuProsesDyeingItem->status,
                            'date' => $modelKpPfp->date,  
                            'created_at' => $modelKpPfp->created_at,
                        ])->execute();
                    }

                    $model->status = $model::STATUS_GANTI_GREIGE_LINKED;
                    $model->save(false, ['status']);

                    $transaction->commit();

                    return ['success'=>true, 'data'=>$data];
                }
            }catch (\Throwable $t){
                $transaction->rollBack();
                throw $t;
            }
        }

        throw new ForbiddenHttpException('Not allowed');
    }

    // public function actionKembaliStock($id)
    // {
    //     $model = $this->findModel($id);

    //     $transaction = Yii::$app->db->beginTransaction();
    //     try {
    //         // update status kartu proses jadi BATAL
    //         $model->status = $model::STATUS_BATAL;
    //         $model->save(false, ['status']);

    //         // rollback stok greige
    //         $totalLength = 0;
    //         foreach ($model->trnKartuProsesDyeingItems as $item) {
    //             $stock = $item->stock;
    //             if ($stock) {
    //                 // panggil rollback, tanpa blokir "sudah proses"
    //                 $stock->rollbackToValid();

    //                 // update note
    //                 $stock->note = 'dikembalikan processing dari NK : ' . $model->nomor_kartu;
    //                 $stock->save(false, ['note']);
                    
    //                 $totalLength += $stock->panjang_m;
    //             }
    //         }

    //         // update mst greige (stock & available)
    //         $mstGreige = $model->wo->greige;
    //         if ($mstGreige) {
    //             $mstGreige->addBackToStock($totalLength);
    //         }

    //         $transaction->commit();
    //         Yii::$app->session->setFlash('success', 'Kartu proses berhasil dibatalkan. Stok greige sudah dikembalikan.');
    //     } catch (\Throwable $e) {
    //         $transaction->rollBack();
    //         Yii::$app->session->setFlash('error', 'Gagal membatalkan kartu proses: '.$e->getMessage());
    //     }

    //     return $this->redirect(['view', 'id' => $model->id]);
    // }

    public function actionKembaliStock($id)
    {
        $model = $this->findModel($id);

        // Cek apakah sudah ada proses "Buka Greige"
        $sudahBukaGreige = KartuProcessDyeingProcess::find()
            ->where(['kartu_process_id' => $model->id, 'process_id' => 1])
            ->exists();

        if ($sudahBukaGreige) {
            Yii::$app->session->setFlash('error', 'Maaf, Kartu Proses ini sudah di Buka Greige.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 1. Update status kartu proses menjadi BATAL
            $model->status = $model::STATUS_BATAL;
            $model->save(false, ['status']);

            $trnStockGreigeIds = [];

            // 2. Loop semua item kartu proses
            foreach ($model->trnKartuProsesDyeingItems as $item) {
                $stockItem = $item->stock; // relasi (bisa TrnStockGreigeOpname, TrnStockGreige, atau null)

                // Tentukan greige_id
                $greigeId = $stockItem->greige_id ?? $item->greige_id ?? null;
                if ($greigeId === null) {
                    continue;
                }

                $mstGreige = MstGreige::findOne($greigeId);
                if ($mstGreige === null) {
                    continue;
                }

                // === LOGIKA PEMBEDAAN ===
                if ($stockItem instanceof \common\models\ar\TrnStockGreigeOpname) {
                    // ✅ Sudah ada di opname
                    $mstGreige->addBackToStockOpname($stockItem->panjang_m);

                    // update status + note opname
                    $stockItem->status = TrnStockGreigeOpname::STATUS_VALID;
                    $stockItem->note   = 'Dikembalikan processing dari NK : ' . $model->nomor_kartu;
                    $stockItem->save(false, ['status','note']);

                    // ambil FK ke TrnStockGreige
                    if ($stockItem->stock_greige_id) {
                        $trnStockGreigeIds[] = $stockItem->stock_greige_id;
                    }

                } elseif ($stockItem instanceof \common\models\ar\TrnStockGreige) {
                    // ✅ Hanya ada di stock greige biasa
                    $mstGreige->addBackToStock($item->panjang_m);

                    // update note di stock
                    $stockItem->note = 'Dikembalikan processing dari NK : ' . $model->nomor_kartu;
                    $stockItem->save(false, ['note']);

                    $trnStockGreigeIds[] = $stockItem->id;

                } else {
                    // ✅ Benar-benar tidak ada relasi (stockItem null)
                    $mstGreige->addBackToStock($item->panjang_m);
                }
            }

            // 3. Update status semua TrnStockGreige yang dikembalikan
            if (!empty($trnStockGreigeIds)) {
                TrnStockGreige::updateAll(
                    [
                        'status' => TrnStockGreige::STATUS_VALID,
                        'note'   => 'Dikembalikan dari NK : ' . $model->nomor_kartu,
                    ],
                    ['id' => $trnStockGreigeIds]
                );
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Kartu proses berhasil dibatalkan. Stok greige sudah dikembalikan.');
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Gagal membatalkan kartu proses: ' . $e->getMessage());
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionDuplicateBulk()
    {
        $ids = Yii::$app->request->post('ids', []);
        if (empty($ids)) {
            Yii::$app->session->setFlash('error', 'Tidak ada data yang dipilih.');
            return $this->redirect(['trn-gudang-stock-opname/index-duplicate']);
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $count = 0;
            $targetColumns = array_keys(TrnStockGreige::getTableSchema()->columns);
            $exclude = ['id', 'created_at', 'updated_at', 'updated_by'];

            foreach ($ids as $id) {
                $opname = TrnStockGreigeOpname::findOne($id);
                if (!$opname) continue;

                $data = array_intersect_key($opname->attributes, array_flip($targetColumns));
                foreach ($exclude as $ex) {
                    unset($data[$ex]);
                }
                $data['created_at'] = time();
                $data['created_by'] = Yii::$app->user->id ?? null;

                $new = new TrnStockGreige();
                $new->setAttributes($data, false);
                if (!$new->save(false)) {
                    throw new \Exception('Gagal menyimpan data untuk opname id: ' . $id);
                }
                $count++;
            }

            $transaction->commit();

            // ✅ gunakan setFlash dengan format seperti contohmu
            Yii::$app->session->setFlash('success', "$count data berhasil diduplikasi ke TrnStockGreige.");
            return $this->redirect(['trn-gudang-stock-opname/index-duplicate']);

        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error("Error duplicate-bulk: " . $e->getMessage(), __METHOD__);

            Yii::$app->session->setFlash(
                'error',
                'Terjadi error saat proses duplikasi: ' . $e->getMessage()
            );
            return $this->redirect(['trn-gudang-stock-opname/index-duplicate']);
        }
    }

    private function appendMissingWoColors($models, $searchModel, $isExport = false)
    {
        if (empty($searchModel->woMonth)) {
            return $models;
        }
        
        $currentYear = date('Y');
        $woMonthStr = "{$currentYear}-{$searchModel->woMonth}";
        
        $woColorsQuery = \common\models\ar\TrnWoColor::find()
            ->joinWith(['wo.greige', 'moColor', 'wo.sc.cust', 'wo.sc.marketing mkt', 'wo.scGreige'])
            ->where(new \yii\db\Expression("TO_CHAR(trn_wo.date, 'YYYY-MM') = :wo_month", [':wo_month' => $woMonthStr]))
            ->andWhere(['IS NOT', 'trn_wo.no', null])
            ->andWhere(['!=', 'trn_wo.no', '']);
            
        if ($isExport) {
            $woColorsQuery->andWhere([
                'trn_sc.jenis_order' => \common\models\ar\TrnSc::JENIS_ORDER_FRESH_ORDER,
                'trn_sc_greige.process' => \common\models\ar\TrnScGreige::PROCESS_DYEING
            ]);
        }
            
        if (!empty($searchModel->woNo)) {
            $woColorsQuery->andFilterWhere(['ilike', 'trn_wo.no', $searchModel->woNo]);
        }
        if (!empty($searchModel->motif)) {
            $woColorsQuery->andFilterWhere(['ilike', 'mst_greige.nama_kain', $searchModel->motif]);
        }
        if (!empty($searchModel->warna)) {
            $woColorsQuery->andFilterWhere(['ilike', 'mo_color.color', $searchModel->warna]);
        }
        if (!empty($searchModel->customerName)) {
            $woColorsQuery->andFilterWhere(['ilike', 'mst_customer.cust_no', $searchModel->customerName]);
        }
        if (!empty($searchModel->woDateRange)) {
            $from_date = substr($searchModel->woDateRange, 0, 10);
            $to_date = substr($searchModel->woDateRange, 14);
            if ($from_date == $to_date) {
                $woColorsQuery->andFilterWhere(['trn_wo.date' => $from_date]);
            } else {
                $woColorsQuery->andFilterWhere(['between', 'trn_wo.date', $from_date, $to_date]);
            }
        }
        if (!empty($searchModel->marketingName)) {
            $woColorsQuery->andFilterWhere(['ilike', 'mkt.full_name', $searchModel->marketingName]);
        }
        
        $woColors = $woColorsQuery->all();
        
        foreach ($woColors as $mc) {
            $targetQty = ceil((float) $mc->qty);
            if ($targetQty <= 0) continue;
            
            $existingCount = \common\models\ar\TrnKartuProsesDyeing::find()
                ->where(['wo_color_id' => $mc->id])
                ->count();
                
            $missingCount = $targetQty - $existingCount;
            
            for ($i = 0; $i < $missingCount; $i++) {
                $dummy = new \common\models\ar\TrnKartuProsesDyeing();
                $dummy->wo_id = $mc->wo_id;
                $dummy->wo_color_id = $mc->id;
                if ($mc->wo) {
                    $dummy->sc_id = $mc->wo->sc_id;
                    $dummy->mo_id = $mc->wo->mo_id;
                    $dummy->sc_greige_id = $mc->wo->sc_greige_id;
                    $dummy->populateRelation('wo', $mc->wo);
                    if ($mc->wo->sc) {
                        $dummy->populateRelation('sc', $mc->wo->sc);
                    }
                }
                $dummy->status = 0;
                $dummy->nomor_kartu = '-';
                $dummy->populateRelation('woColor', $mc);
                
                $models[] = $dummy;
            }
        }
        
        usort($models, function($a, $b) {
            $woA = $a->wo ? $a->wo->no : '';
            $woB = $b->wo ? $b->wo->no : '';
            if ($woA === $woB) {
                $colA = ($a->woColor && $a->woColor->moColor) ? $a->woColor->moColor->color : '';
                $colB = ($b->woColor && $b->woColor->moColor) ? $b->woColor->moColor->color : '';
                return strcmp($colA, $colB);
            }
            return strcmp($woA, $woB);
        });
        
        return $models;
    }

    public function actionGetMachinesByProcess($process_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $process = MstProcessDyeing::findOne($process_id);
        if ($process !== null) {
            $machines = $process->mstMesinProseses;
            return \yii\helpers\ArrayHelper::toArray($machines, [
                \common\models\ar\MstMesinProses::class => ['id', 'nama_mesin']
            ]);
        }
        return [];
    }

    /**
     * Rekap Proses Mesin page
     * @return string
     */
    public function actionRekapProsesMesin()
    {
        $mesinId = Yii::$app->request->get('mesin_id');
        $tanggal = Yii::$app->request->get('tanggal', date('Y-m-d'));

        // Get all unique model_mesin values for the dropdown
        $modelMesins = \common\models\ar\MstMesinProses::find()
            ->select('model_mesin')
            ->distinct()
            ->where(['not', ['model_mesin' => null]])
            ->andWhere(['not', ['model_mesin' => '']])
            ->orderBy(['model_mesin' => SORT_ASC])
            ->column();

        // Get machines for the selected model
        $selectedModel = Yii::$app->request->get('model_mesin', '');
        $machines = [];
        if ($selectedModel) {
            $machines = \common\models\ar\MstMesinProses::find()
                ->where(['model_mesin' => $selectedModel])
                ->orderBy(new \yii\db\Expression("CASE WHEN nama_mesin ~ '^[0-9]+$' THEN 0 ELSE 1 END, CASE WHEN nama_mesin ~ '^[0-9]+$' THEN CAST(nama_mesin AS INTEGER) ELSE 0 END, nama_mesin"))
                ->all();
        }

        // Get the selected machine object
        $mesin = null;
        if ($mesinId) {
            $mesin = \common\models\ar\MstMesinProses::findOne($mesinId);
        }

        // Fetch kartu proses data for the selected machine on the selected date
        $kartuData = [];
        $rangkumanProses = [];
        if ($mesin) {
            // Find all process IDs mapped to the selected machine's model
            $validProcessIds = \common\models\ar\MstMesinProses::find()
                ->alias('mmp')
                ->select('mpdm.mst_process_dyeing_id')
                ->innerJoin('mst_process_dyeing_mesin mpdm', 'mmp.id = mpdm.mst_mesin_proses_id')
                ->where(['mmp.model_mesin' => $mesin->model_mesin])
                ->column();

            // Find kartu_process_dyeing_process entries filtered at DB level
            $query = KartuProcessDyeingProcess::find()
                ->alias('kp')
                ->innerJoin('trn_kartu_proses_dyeing kpd', 'kp.kartu_process_id = kpd.id')
                ->where(['>=', 'kpd.status', TrnKartuProsesDyeing::STATUS_DELIVERED])
                ->andWhere(['not', ['kpd.status' => TrnKartuProsesDyeing::STATUS_BATAL]])
                ->andWhere(['like', 'kp.value', '"no_mesin":"' . str_replace(['%', '_'], ['\%', '\_'], $mesin->nama_mesin) . '"'])
                ->andWhere(['like', 'kp.value', '"tanggal":"' . $tanggal . '"'])
                ->with(['kartuProcess.trnKartuProsesDyeingItems']);

            $allProcessRecords = $query->all();

            foreach ($allProcessRecords as $record) {
                // Skip if this process doesn't belong to the selected machine's model
                if (!in_array($record->process_id, $validProcessIds)) {
                    continue;
                }

                $values = Json::decode($record->value);
                if (!isset($values['no_mesin']) || $values['no_mesin'] !== $mesin->nama_mesin) {
                    continue;
                }
                
                if (!isset($values['tanggal']) || $values['tanggal'] !== $tanggal) {
                    continue;
                }

                $kartuProses = $record->kartuProcess;
                $process = $record->process;

                $shiftGroup = isset($values['shift_group']) ? $values['shift_group'] : '-';

                $kartuData[] = [
                    'shift_group' => $shiftGroup,
                    'no' => $kartuProses->no,
                    'nomor_kartu' => $kartuProses->nomor_kartu,
                    'motif' => $kartuProses->wo ? ($kartuProses->wo->greige ? $kartuProses->wo->greige->nama_kain : '') : '',
                    'nk' => $kartuProses->nomor_kartu,
                    'pcs' => count($kartuProses->trnKartuProsesDyeingItems),
                    'no_mc' => isset($values['no_mesin']) ? $values['no_mesin'] : '',
                    'warna' => ($kartuProses->woColor && $kartuProses->woColor->moColor) ? $kartuProses->woColor->moColor->color : '',
                    'proses' => $process ? $process->nama_proses : '',
                    'temp' => isset($values['temp']) ? $values['temp'] : '',
                    'speed' => isset($values['speed']) ? $values['speed'] : '',
                    'lebar' => isset($values['lebar_jadi']) ? $values['lebar_jadi'] : '',
                    'berat' => $kartuProses->berat,
                    'panjang_jadi' => isset($values['panjang_jadi']) ? $values['panjang_jadi'] : '',
                    'panjang_greige' => array_sum(array_column($kartuProses->trnKartuProsesDyeingItems, 'panjang_m')),
                    'keterangan' => isset($values['keterangan']) ? $values['keterangan'] : '',
                    'wo_no' => $kartuProses->wo ? $kartuProses->wo->no : '',
                    'kartu_proses_id' => $kartuProses->id,
                    'process_name' => $process ? $process->nama_proses : '',
                ];

                // Build rangkuman
                $prosesName = $process ? $process->nama_proses : 'Unknown';
                if (!isset($rangkumanProses[$prosesName])) {
                    $rangkumanProses[$prosesName] = ['count' => 0, 'total_panjang' => 0];
                }
                $rangkumanProses[$prosesName]['count']++;
                $panjangJadi = isset($values['panjang_jadi']) ? floatval($values['panjang_jadi']) : 0;
                $rangkumanProses[$prosesName]['total_panjang'] += $panjangJadi;
            }
        }

        // Sort by shift group dynamically based on request or default
        $reqPagi = Yii::$app->request->get('shift_pagi', '');
        $reqSiang = Yii::$app->request->get('shift_siang', '');
        $reqMalam = Yii::$app->request->get('shift_malam', '');

        $shiftOrder = [];
        $orderSeq = 1;

        if ($reqPagi) $shiftOrder[$reqPagi] = $orderSeq++;
        else $shiftOrder['C'] = $orderSeq++;

        if ($reqSiang) $shiftOrder[$reqSiang] = $orderSeq++;
        else $shiftOrder['D'] = $orderSeq++;

        if ($reqMalam) $shiftOrder[$reqMalam] = $orderSeq++;
        else $shiftOrder['A'] = $orderSeq++;

        usort($kartuData, function ($a, $b) use ($shiftOrder) {
            $oa = isset($shiftOrder[$a['shift_group']]) ? $shiftOrder[$a['shift_group']] : 99;
            $ob = isset($shiftOrder[$b['shift_group']]) ? $shiftOrder[$b['shift_group']] : 99;
            return $oa <=> $ob;
        });

        // Fetch hambatan for this machine on this date
        $hambatanItems = [];
        if ($mesin) {
            $hambatan = \common\models\ar\TrnHambatanMesin::find()
                ->where(['mst_mesin_proses_id' => $mesin->id, 'tanggal' => $tanggal])
                ->one();
            if ($hambatan) {
                $hambatanItems = $hambatan->trnHambatanMesinItems;
            }
        }

        return $this->render('rekap-proses-mesin', [
            'modelMesins' => $modelMesins,
            'selectedModel' => $selectedModel,
            'machines' => $machines,
            'mesinId' => $mesinId,
            'mesin' => $mesin,
            'tanggal' => $tanggal,
            'kartuData' => $kartuData,
            'rangkumanProses' => $rangkumanProses,
            'hambatanItems' => $hambatanItems,
        ]);
    }
}