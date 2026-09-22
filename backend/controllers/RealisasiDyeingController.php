<?php
namespace backend\controllers;

use backend\models\form\CatatanProsesForm;
use backend\models\form\HasilTesGosokForm;
use common\models\ar\KartuProcessDyeingProcess;
use common\models\ar\MstProcessDyeing;
use common\models\ar\TrnKartuProsesDyeingItem;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWo;
use common\models\ar\TrnWoSearch;
use common\models\ar\TrnWoColor;
use common\models\ar\TrnWoColorSearch;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use Yii;
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnKartuProsesDyeingSearch;
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
class RealisasiDyeingController extends Controller
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
    public function actionRekap()
    {
        $searchModel = new TrnKartuProsesDyeingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['>', 'trn_kartu_proses_dyeing.status', TrnKartuProsesDyeing::STATUS_POSTED]);

        return $this->render('rekap', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Ini adalah fungsi untuk menuju halaman rekap formated
     * halaman tersebut adalah halaman yang menampilkan data rekap dyeing sesuai fromat permintaan dari divisi dyeing
     */
    public function actionRekapFormated()
    {
        $searchModel = new TrnKartuProsesDyeingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['>=', 'trn_kartu_proses_dyeing.status', TrnKartuProsesDyeing::STATUS_POSTED]);

        $dataProvider->query->andWhere(['<=', 'trn_kartu_proses_dyeing.status', TrnKartuProsesDyeing::STATUS_DELIVERED]);
        $dataProvider->query->andWhere(['=', 'trn_wo.jenis_order', TrnSc::JENIS_ORDER_FRESH_ORDER]);

        $dataProvider->sort->defaultOrder = [
            'woNo' => SORT_ASC,
            'motif' => SORT_ASC,
            'warna' => SORT_ASC,
        ];

        $dataProvider->query->orderBy(['LENGTH(trn_wo.no)' => SORT_ASC, 'trn_wo.no' => SORT_ASC])
            ->addOrderBy(['mst_greige.nama_kain' => SORT_ASC])
            ->addOrderBy(['moColor.color' => SORT_ASC]);

        // WO Disetujui yang belum buka Kartu Proses
        $searchModelNoNk = new TrnWoColorSearch();
        $searchModelNoNk->woYear = $searchModel->woYear;
        $dataProviderNoNk = $searchModelNoNk->search(Yii::$app->request->queryParams);
        $dataProviderNoNk->query->andWhere(['=', 'trn_wo.status', TrnWo::STATUS_APPROVED]);
        $dataProviderNoNk->query->andWhere(['=', 'trn_sc_greige.process', TrnScGreige::PROCESS_DYEING]);
        $dataProviderNoNk->query->andWhere(['=', 'trn_wo.jenis_order', TrnSc::JENIS_ORDER_FRESH_ORDER]);
        $dataProviderNoNk->query->andWhere(['not exists', (new \yii\db\Query())
            ->select('id')
            ->from('trn_kartu_proses_dyeing')
            ->where('trn_kartu_proses_dyeing.wo_id = trn_wo.id')
        ]);
        $dataProviderNoNk->query->join('CROSS JOIN', 'generate_series(1, GREATEST(1, CAST(COALESCE(trn_wo_color.qty, 1) AS integer))) AS batch_num');
        $dataProviderNoNk->query->indexBy(function($row) {
            static $idx = 0;
            return $idx++;
        });
        $dataProviderNoNk->query->orderBy(['LENGTH(trn_wo.no)' => SORT_ASC, 'trn_wo.no' => SORT_ASC]);

        if (empty($searchModel->woYear)) {
            $dataProvider->query->andWhere('0=1');
            $dataProviderNoNk->query->andWhere('0=1');
        }

        return $this->render('rekap-formated', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'searchModelNoNk' => $searchModelNoNk,
            'dataProviderNoNk' => $dataProviderNoNk,
        ]);
    }

    /**
     * Rekap Dyeing untuk Processing (format sama dengan rekap-formated ditambah field Tanggal Kirim dari WO)
     * Menyatukan data yang sudah ada nomor kartu dan belum ada nomor kartu dalam 1 tabel.
     * @return mixed
     */
    public function actionRekapProcessing()
    {
        $searchModel = new TrnKartuProsesDyeingSearch();
        $dataProviderKp = $searchModel->search(Yii::$app->request->queryParams);

        $dataProviderKp->query->andWhere(['>=', 'trn_kartu_proses_dyeing.status', TrnKartuProsesDyeing::STATUS_POSTED]);
        $dataProviderKp->query->andWhere(['<=', 'trn_kartu_proses_dyeing.status', TrnKartuProsesDyeing::STATUS_DELIVERED]);
        $dataProviderKp->query->andWhere(['=', 'trn_wo.jenis_order', TrnSc::JENIS_ORDER_FRESH_ORDER]);

        $models = [];
        if (!empty($searchModel->woYear)) {
            $dataProviderKp->pagination = false;
            $modelsKp = $dataProviderKp->getModels();

            // WO Disetujui yang belum buka Kartu Proses
            $searchModelNoNk = new TrnWoColorSearch();
            $searchModelNoNk->woYear = $searchModel->woYear;
            $searchModelNoNk->woMonth = $searchModel->woMonth;
            $paramsNoNk = ['TrnWoColorSearch' => [
                'woYear' => $searchModel->woYear,
                'woMonth' => $searchModel->woMonth,
                'woNo' => $searchModel->woNo,
                'customerName' => $searchModel->customerName,
                'greigeName' => $searchModel->motif,
                'dateRangeWo' => $searchModel->woDateRange,
            ]];
            $dataProviderNoNk = $searchModelNoNk->search($paramsNoNk);
            $dataProviderNoNk->query->andWhere(['=', 'trn_wo.status', TrnWo::STATUS_APPROVED]);
            $dataProviderNoNk->query->andWhere(['=', 'trn_sc_greige.process', TrnScGreige::PROCESS_DYEING]);
            $dataProviderNoNk->query->andWhere(['=', 'trn_wo.jenis_order', TrnSc::JENIS_ORDER_FRESH_ORDER]);
            $dataProviderNoNk->query->andWhere(['not exists', (new \yii\db\Query())
                ->select('id')
                ->from('trn_kartu_proses_dyeing')
                ->where('trn_kartu_proses_dyeing.wo_id = trn_wo.id')
            ]);
            $dataProviderNoNk->query->join('CROSS JOIN', 'generate_series(1, GREATEST(1, CAST(COALESCE(trn_wo_color.qty, 1) AS integer))) AS batch_num');
            $dataProviderNoNk->query->joinWith(['moColor']);

            if (!empty($searchModel->warna)) {
                $dataProviderNoNk->query->andFilterWhere(['ilike', 'trn_mo_color.color', $searchModel->warna]);
            }
            if (!empty($searchModel->woTglKirimRange)) {
                $from = substr($searchModel->woTglKirimRange, 0, 10);
                $to = substr($searchModel->woTglKirimRange, 14);
                if ($from == $to) {
                    $dataProviderNoNk->query->andFilterWhere(['trn_wo.tgl_kirim' => $from]);
                } else {
                    $dataProviderNoNk->query->andFilterWhere(['between', 'trn_wo.tgl_kirim', $from, $to]);
                }
            }
            if (!empty($searchModel->nomor_kartu) || !empty($searchModel->dateRangeMasukPacking)) {
                $dataProviderNoNk->query->andWhere('0=1');
            }

            $dataProviderNoNk->pagination = false;
            $modelsNoNk = $dataProviderNoNk->getModels();

            foreach ($modelsKp as $m) {
                $models[] = $m;
            }

            foreach ($modelsNoNk as $mColor) {
                $dummy = new TrnKartuProsesDyeing();
                $dummy->id = null;
                $dummy->wo_id = $mColor->wo_id;
                $dummy->wo_color_id = $mColor->id;
                $dummy->nomor_kartu = null;
                $dummy->populateRelation('wo', $mColor->wo);
                $dummy->populateRelation('sc', $mColor->sc);
                $dummy->populateRelation('woColor', $mColor);
                $models[] = $dummy;
            }

            // Sort all models consistently
            usort($models, function($a, $b) {
                $woNoA = $a->wo ? $a->wo->no : '';
                $woNoB = $b->wo ? $b->wo->no : '';
                $lenA = strlen($woNoA);
                $lenB = strlen($woNoB);
                if ($lenA !== $lenB) return $lenA <=> $lenB;
                $cmpWo = strcmp($woNoA, $woNoB);
                if ($cmpWo !== 0) return $cmpWo;

                $motifA = $a->wo ? $a->wo->greigeNamaKain : '';
                $motifB = $b->wo ? $b->wo->greigeNamaKain : '';
                $cmpMotif = strcmp($motifA, $motifB);
                if ($cmpMotif !== 0) return $cmpMotif;

                $colorA = ($a->woColor && $a->woColor->moColor) ? $a->woColor->moColor->color : '';
                $colorB = ($b->woColor && $b->woColor->moColor) ? $b->woColor->moColor->color : '';
                $cmpColor = strcmp($colorA, $colorB);
                if ($cmpColor !== 0) return $cmpColor;

                $nkA = $a->nomor_kartu ?: '';
                $nkB = $b->nomor_kartu ?: '';
                return strcmp($nkA, $nkB);
            });
        }

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $models,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $this->render('rekap-processing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionRekapFormatedNoNk()
    {
        $searchModel = new TrnWoColorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['=', 'trn_wo.status', TrnWo::STATUS_APPROVED]);

        $dataProvider->query->andWhere(['=', 'trn_sc_greige.process', TrnScGreige::PROCESS_DYEING]);

        $dataProvider->query->andWhere(['not exists', (new \yii\db\Query())
            ->select('id')
            ->from('trn_kartu_proses_dyeing')
            ->where('trn_kartu_proses_dyeing.wo_id = trn_wo.id')
        ]);

        return $this->render('rekap-formated-no-nk', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

        /**
     * Lists all TrnKartuProsesDyeing models.
     * @return mixed
     */
    public function actionRekapOutstandingBukaanDyeing()
    {
        $searchModel = new TrnWoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', 'trn_wo.status', TrnWo::STATUS_APPROVED]);
        $dataProvider->query->andWhere(['=', 'trn_sc_greige.process', TrnScGreige::PROCESS_DYEING]);

        return $this->render('rekap-outstanding-bukaan-dyeing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Export Rekap Processing to Excel (Spreadsheet XML 2003)
     */
    public function actionExportProcessing()
    {
        return $this->exportRekap(true);
    }

    /**
     * Export Rekap Formated to Excel (Spreadsheet XML 2003)
     */
    public function actionExportFormated()
    {
        return $this->exportRekap(false);
    }

    /**
     * Export WO Disetujui (Belum Ada Kartu Proses) to Excel for Processing
     */
    public function actionExportNoNkProcessing()
    {
        return $this->exportNoNk(true);
    }

    /**
     * Export WO Disetujui (Belum Ada Kartu Proses) to Excel for Formated
     */
    public function actionExportNoNkFormated()
    {
        return $this->exportNoNk(false);
    }

    /**
     * Compute merge spans for rows based on wo_id and warna
     * @param array $rows Array of associative arrays, each must contain 'wo_id' and 'warna'
     * @return array Array of merge info per row
     */
    protected function computeRowMerges(array $rows)
    {
        $n = count($rows);
        $merges = array_fill(0, $n, [
            'wo_merge' => 0,
            'wo_skip' => false,
            'warna_merge' => 0,
            'warna_skip' => false,
        ]);

        $i = 0;
        while ($i < $n) {
            $woId = $rows[$i]['wo_id'];
            $j = $i + 1;
            while ($j < $n && (string)$rows[$j]['wo_id'] === (string)$woId) {
                $merges[$j]['wo_skip'] = true;
                $j++;
            }
            $merges[$i]['wo_merge'] = $j - $i - 1;

            // Within this WO group, group identical colors
            $wStart = $i;
            while ($wStart < $j) {
                $warna = $rows[$wStart]['warna'];
                $wEnd = $wStart + 1;
                while ($wEnd < $j && (string)$rows[$wEnd]['warna'] === (string)$warna) {
                    $merges[$wEnd]['warna_skip'] = true;
                    $wEnd++;
                }
                $merges[$wStart]['warna_merge'] = $wEnd - $wStart - 1;
                $wStart = $wEnd;
            }

            $i = $j;
        }

        return $merges;
    }

    /**
     * High performance export specifically for WO Disetujui (Belum Ada NK)
     * @param bool $isProcessing
     */
    protected function exportNoNk($isProcessing = false)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $searchModelNoNk = new TrnWoColorSearch();
        $dataProviderNoNk = $searchModelNoNk->search(Yii::$app->request->queryParams);
        $dataProviderNoNk->query->andWhere(['=', 'trn_wo.status', TrnWo::STATUS_APPROVED]);
        $dataProviderNoNk->query->andWhere(['=', 'trn_sc_greige.process', TrnScGreige::PROCESS_DYEING]);
        $dataProviderNoNk->query->andWhere(['=', 'trn_wo.jenis_order', TrnSc::JENIS_ORDER_FRESH_ORDER]);
        $dataProviderNoNk->query->andWhere(['not exists', (new \yii\db\Query())
            ->select('id')
            ->from('trn_kartu_proses_dyeing')
            ->where('trn_kartu_proses_dyeing.wo_id = trn_wo.id')
        ]);
        $dataProviderNoNk->query->join('CROSS JOIN', 'generate_series(1, GREATEST(1, CAST(COALESCE(trn_wo_color.qty, 1) AS integer))) AS batch_num');
        $dataProviderNoNk->query->indexBy(function($row) {
            static $idx = 0;
            return $idx++;
        });
        $dataProviderNoNk->query->orderBy(['LENGTH(trn_wo.no)' => SORT_ASC, 'trn_wo.no' => SORT_ASC]);

        if (empty($searchModelNoNk->woYear)) {
            $dataProviderNoNk->query->andWhere('0=1');
        }

        $dataProviderNoNk->pagination = false;
        /** @var TrnWoColor[] $modelsNoNk */
        $modelsNoNk = $dataProviderNoNk->getModels();

        $rowsData = [];
        $woCache = [];
        foreach ($modelsNoNk as $modelColor) {
            $woId = $modelColor->wo_id;
            if (!isset($woCache[$woId])) {
                $wo = $modelColor->wo;
                $sc = $modelColor->sc;
                    $mFinish = ($wo && is_numeric($wo->colorQtyFinish)) ? Yii::$app->formatter->asDecimal($wo->colorQtyFinish) : '-';
                    $yFinish = ($wo && is_numeric($wo->colorQtyFinishToYard)) ? Yii::$app->formatter->asDecimal($wo->colorQtyFinishToYard) : '-';
                    $woCache[$woId] = [
                        'woNo' => $wo ? $wo->no : '',
                        'buyer' => $sc ? $sc->customerName : '',
                        'motif' => $wo ? $wo->greigeNamaKain : '',
                        'handling' => ($wo && $wo->handling) ? $wo->handling->name : '-',
                        'batchTotal' => $wo ? $wo->colorQty : 0,
                        'jmlPanjang' => $wo ? ($mFinish . 'M / ' . $yFinish . 'Y') : '',
                        'tglWo' => ($wo && $wo->date) ? date('d/m/y', strtotime($wo->date)) : '',
                        'tglKirim' => ($wo && $wo->tgl_kirim) ? date('d/m/y', strtotime($wo->tgl_kirim)) : '',
                    ];
            }
            $moColor = $modelColor->moColor;
            $warna = $moColor ? $moColor->color : '-';

            $rowsData[] = [
                'wo_id' => $woId,
                'woInfo' => $woCache[$woId],
                'warna' => $warna,
            ];
        }
        $merges = $this->computeRowMerges($rowsData);

        $title = $isProcessing ? 'WO_Disetujui_Belum_Ada_NK_Processing' : 'WO_Disetujui_Belum_Ada_NK';
        $filename = $title . '_' . ($searchModelNoNk->woYear ?: date('Y')) . '_' . date('Ymd_His') . '.xls';

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
        header('Pragma: public');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        echo ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
        echo ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
        echo ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        echo ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";

        // Styles
        echo ' <Styles>' . "\n";
        echo '  <Style ss:ID="Default" ss:Name="Normal">' . "\n";
        echo '   <Alignment ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Font ss:FontName="Calibri" ss:Size="10" ss:Color="#000000"/>' . "\n";
        echo '  </Style>' . "\n";

        echo '  <Style ss:ID="HeaderWarning">' . "\n";
        echo '   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#555555"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#555555"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#555555"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#555555"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#FFFFFF"/>' . "\n";
        echo '   <Interior ss:Color="#D58512" ss:Pattern="Solid"/>' . "\n";
        echo '  </Style>' . "\n";

        echo '  <Style ss:ID="TextCenter">' . "\n";
        echo '   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Font ss:FontName="Calibri" ss:Size="10"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '  </Style>' . "\n";

        echo '  <Style ss:ID="TextLeft">' . "\n";
        echo '   <Alignment ss:Horizontal="Left" ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Font ss:FontName="Calibri" ss:Size="10"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '  </Style>' . "\n";

        echo '  <Style ss:ID="NumInt">' . "\n";
        echo '   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>' . "\n";
        echo '   <NumberFormat ss:Format="#,##0"/>' . "\n";
        echo '   <Font ss:FontName="Calibri" ss:Size="10"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '  </Style>' . "\n";

        echo ' </Styles>' . "\n";

        echo ' <Worksheet ss:Name="WO_Disetujui">' . "\n";
        echo '  <Table>' . "\n";

        // Column widths
        echo '   <Column ss:Width="35"/>' . "\n";  // #
        echo '   <Column ss:Width="120"/>' . "\n"; // Nomor WO
        echo '   <Column ss:Width="150"/>' . "\n"; // Buyer
        echo '   <Column ss:Width="160"/>' . "\n"; // Motif
        echo '   <Column ss:Width="130"/>' . "\n"; // Handling
        echo '   <Column ss:Width="90"/>' . "\n";  // Batch Total
        echo '   <Column ss:Width="130"/>' . "\n"; // Jml Panjang
        echo '   <Column ss:Width="130"/>' . "\n"; // Warna
        echo '   <Column ss:Width="90"/>' . "\n";  // Tgl WO
        if ($isProcessing) {
            echo '   <Column ss:Width="90"/>' . "\n";  // Tgl Kirim
        }
        echo '   <Column ss:Width="110"/>' . "\n"; // NK

        // Header Row
        echo '   <Row ss:Height="25">' . "\n";
        echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">#</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">Nomor WO</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">Buyer</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">Motif</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">Handling</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">BATCH TOTAL</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">JML PANJANG</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">Warna</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">TANGGAL WO</Data></Cell>' . "\n";
        if ($isProcessing) {
            echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">TANGGAL KIRIM</Data></Cell>' . "\n";
        }
        echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">NK</Data></Cell>' . "\n";
        echo '   </Row>' . "\n";

        $no = 1;
        foreach ($rowsData as $idx => $r) {
            $m = $merges[$idx];
            $woInfo = $r['woInfo'];
            $warna = $r['warna'];
            $woMergeAttr = $m['wo_merge'] > 0 ? ' ss:MergeDown="' . $m['wo_merge'] . '"' : '';
            $warnaMergeAttr = $m['warna_merge'] > 0 ? ' ss:MergeDown="' . $m['warna_merge'] . '"' : '';

            echo '   <Row ss:Height="18">' . "\n";
            echo '    <Cell ss:Index="1" ss:StyleID="TextCenter"><Data ss:Type="Number">' . $no++ . '</Data></Cell>' . "\n";
            if (!$m['wo_skip']) {
                echo '    <Cell ss:Index="2"' . $woMergeAttr . ' ss:StyleID="TextLeft"><Data ss:Type="String">' . htmlspecialchars($woInfo['woNo'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                echo '    <Cell ss:Index="3"' . $woMergeAttr . ' ss:StyleID="TextLeft"><Data ss:Type="String">' . htmlspecialchars($woInfo['buyer'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                echo '    <Cell ss:Index="4"' . $woMergeAttr . ' ss:StyleID="TextLeft"><Data ss:Type="String">' . htmlspecialchars($woInfo['motif'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                echo '    <Cell ss:Index="5"' . $woMergeAttr . ' ss:StyleID="TextLeft"><Data ss:Type="String">' . htmlspecialchars($woInfo['handling'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                echo '    <Cell ss:Index="6"' . $woMergeAttr . ' ss:StyleID="NumInt"><Data ss:Type="Number">' . $woInfo['batchTotal'] . '</Data></Cell>' . "\n";
                echo '    <Cell ss:Index="7"' . $woMergeAttr . ' ss:StyleID="TextCenter"><Data ss:Type="String">' . htmlspecialchars($woInfo['jmlPanjang'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
            }
            if (!$m['warna_skip']) {
                echo '    <Cell ss:Index="8"' . $warnaMergeAttr . ' ss:StyleID="TextLeft"><Data ss:Type="String">' . htmlspecialchars($warna, ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
            }
            if (!$m['wo_skip']) {
                echo '    <Cell ss:Index="9"' . $woMergeAttr . ' ss:StyleID="TextCenter"><Data ss:Type="String">' . htmlspecialchars($woInfo['tglWo'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                if ($isProcessing) {
                    echo '    <Cell ss:Index="10"' . $woMergeAttr . ' ss:StyleID="TextCenter"><Data ss:Type="String">' . htmlspecialchars($woInfo['tglKirim'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                }
            }
            $nkColIndex = $isProcessing ? 11 : 10;
            echo '    <Cell ss:Index="' . $nkColIndex . '" ss:StyleID="TextCenter"><Data ss:Type="String">Belum Ada NK</Data></Cell>' . "\n";
            echo '   </Row>' . "\n";
        }

        echo '  </Table>' . "\n";
        echo ' </Worksheet>' . "\n";
        echo '</Workbook>' . "\n";

        exit;
    }

    /**
     * High performance streaming export for Rekap Dyeing
     * @param bool $isProcessing
     */
    protected function exportRekap($isProcessing = false)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $searchModel = new TrnKartuProsesDyeingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['>=', 'trn_kartu_proses_dyeing.status', TrnKartuProsesDyeing::STATUS_POSTED]);
        $dataProvider->query->andWhere(['<=', 'trn_kartu_proses_dyeing.status', TrnKartuProsesDyeing::STATUS_DELIVERED]);
        $dataProvider->query->andWhere(['=', 'trn_wo.jenis_order', TrnSc::JENIS_ORDER_FRESH_ORDER]);
        $dataProvider->query->orderBy(['LENGTH(trn_wo.no)' => SORT_ASC, 'trn_wo.no' => SORT_ASC])
            ->addOrderBy(['mst_greige.nama_kain' => SORT_ASC])
            ->addOrderBy(['moColor.color' => SORT_ASC]);

        if (empty($searchModel->woYear)) {
            $dataProvider->query->andWhere('0=1');
        }

        $dataProvider->pagination = false;
        /** @var TrnKartuProsesDyeing[] $models */
        $models = $dataProvider->getModels();

        // 1. Bulk fetch process data for all models
        $kpIds = [];
        foreach ($models as $m) {
            $kpIds[] = $m->id;
        }

        $processesMap = [];
        $itemsSumMap = [];
        $inspectingMap = [];

        if (!empty($kpIds)) {
            // Bulk fetch processes (1, 3, 8, 15, 18, 19, 21, 11)
            $rawProcesses = (new \yii\db\Query())
                ->from(KartuProcessDyeingProcess::tableName())
                ->where(['kartu_process_id' => $kpIds, 'process_id' => [1, 3, 8, 15, 18, 19, 21, 11]])
                ->all();
            foreach ($rawProcesses as $rp) {
                $processesMap[$rp['kartu_process_id']][$rp['process_id']] = Json::decode($rp['value']);
            }

            // Bulk fetch greige length sum
            $rawSums = (new \yii\db\Query())
                ->select(['kartu_process_id', 'total_panjang' => 'SUM(panjang_m)'])
                ->from(TrnKartuProsesDyeingItem::tableName())
                ->where(['kartu_process_id' => $kpIds])
                ->groupBy('kartu_process_id')
                ->all();
            foreach ($rawSums as $rs) {
                $itemsSumMap[$rs['kartu_process_id']] = $rs['total_panjang'];
            }

            // Bulk fetch inspecting totals
            $rawInspecting = (new \yii\db\Query())
                ->select(['kartu_process_dyeing_id' => 'ti.kartu_process_dyeing_id', 'total_qty' => 'SUM(ii.qty)'])
                ->from(['ti' => \common\models\ar\TrnInspecting::tableName()])
                ->innerJoin(['ii' => \common\models\ar\InspectingItem::tableName()], 'ii.inspecting_id = ti.id')
                ->where(['ti.kartu_process_dyeing_id' => $kpIds, 'ti.status' => \common\models\ar\TrnInspecting::STATUS_DELIVERED])
                ->groupBy('ti.kartu_process_dyeing_id')
                ->all();
            foreach ($rawInspecting as $ri) {
                $inspectingMap[$ri['kartu_process_dyeing_id']] = $ri['total_qty'];
            }
        }

        // 2. Fetch WO Disetujui belum ada NK
        $modelsNoNk = [];
        if (!empty($searchModel->woYear)) {
            $searchModelNoNk = new TrnWoColorSearch();
            $searchModelNoNk->woYear = $searchModel->woYear;
            $searchModelNoNk->woMonth = $searchModel->woMonth;
            $paramsNoNk = ['TrnWoColorSearch' => [
                'woYear' => $searchModel->woYear,
                'woMonth' => $searchModel->woMonth,
                'woNo' => $searchModel->woNo,
                'customerName' => $searchModel->customerName,
                'greigeName' => $searchModel->motif,
                'dateRangeWo' => $searchModel->woDateRange,
            ]];
            $dataProviderNoNk = $searchModelNoNk->search($paramsNoNk);
            $dataProviderNoNk->query->andWhere(['=', 'trn_wo.status', TrnWo::STATUS_APPROVED]);
            $dataProviderNoNk->query->andWhere(['=', 'trn_sc_greige.process', TrnScGreige::PROCESS_DYEING]);
            $dataProviderNoNk->query->andWhere(['=', 'trn_wo.jenis_order', TrnSc::JENIS_ORDER_FRESH_ORDER]);
            $dataProviderNoNk->query->andWhere(['not exists', (new \yii\db\Query())
                ->select('id')
                ->from('trn_kartu_proses_dyeing')
                ->where('trn_kartu_proses_dyeing.wo_id = trn_wo.id')
            ]);
            $dataProviderNoNk->query->join('CROSS JOIN', 'generate_series(1, GREATEST(1, CAST(COALESCE(trn_wo_color.qty, 1) AS integer))) AS batch_num');
            $dataProviderNoNk->query->joinWith(['moColor']);

            if (!empty($searchModel->warna)) {
                $dataProviderNoNk->query->andFilterWhere(['ilike', 'trn_mo_color.color', $searchModel->warna]);
            }
            if (!empty($searchModel->woTglKirimRange)) {
                $from = substr($searchModel->woTglKirimRange, 0, 10);
                $to = substr($searchModel->woTglKirimRange, 14);
                if ($from == $to) {
                    $dataProviderNoNk->query->andFilterWhere(['trn_wo.tgl_kirim' => $from]);
                } else {
                    $dataProviderNoNk->query->andFilterWhere(['between', 'trn_wo.tgl_kirim', $from, $to]);
                }
            }
            if (!empty($searchModel->nomor_kartu) || !empty($searchModel->dateRangeMasukPacking)) {
                $dataProviderNoNk->query->andWhere('0=1');
            }

            $dataProviderNoNk->pagination = false;
            $modelsNoNk = $dataProviderNoNk->getModels();
        }

        $title = $isProcessing ? 'Dyeing_Processing' : 'Realisasi_Dyeing_Formated';
        $monthSuffix = !empty($searchModel->woMonth) ? ('_' . $searchModel->woMonth) : '';
        $filename = $title . '_' . ($searchModel->woYear ?: date('Y')) . $monthSuffix . '_' . date('Ymd_His') . '.xls';

        // Set response headers for streaming Excel
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
        header('Pragma: public');

        // XML Spreadsheet 2003 template
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        echo ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
        echo ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
        echo ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        echo ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";

        // Styles
        echo ' <Styles>' . "\n";
        echo '  <Style ss:ID="Default" ss:Name="Normal">' . "\n";
        echo '   <Alignment ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Font ss:FontName="Calibri" ss:Size="10" ss:Color="#000000"/>' . "\n";
        echo '  </Style>' . "\n";

        echo '  <Style ss:ID="Header">' . "\n";
        echo '   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#555555"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#555555"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#555555"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#555555"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#FFFFFF"/>' . "\n";
        echo '   <Interior ss:Color="#337AB7" ss:Pattern="Solid"/>' . "\n";
        echo '  </Style>' . "\n";

        echo '  <Style ss:ID="HeaderWarning">' . "\n";
        echo '   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#555555"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#555555"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#555555"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#555555"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#FFFFFF"/>' . "\n";
        echo '   <Interior ss:Color="#F0AD4E" ss:Pattern="Solid"/>' . "\n";
        echo '  </Style>' . "\n";

        echo '  <Style ss:ID="TextCenter">' . "\n";
        echo '   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Font ss:FontName="Calibri" ss:Size="10"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '  </Style>' . "\n";

        echo '  <Style ss:ID="TextLeft">' . "\n";
        echo '   <Alignment ss:Horizontal="Left" ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
        echo '   <Font ss:FontName="Calibri" ss:Size="10"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '  </Style>' . "\n";

        echo '  <Style ss:ID="NumDec">' . "\n";
        echo '   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>' . "\n";
        echo '   <NumberFormat ss:Format="#,##0.00"/>' . "\n";
        echo '   <Font ss:FontName="Calibri" ss:Size="10"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '  </Style>' . "\n";

        echo '  <Style ss:ID="NumInt">' . "\n";
        echo '   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>' . "\n";
        echo '   <NumberFormat ss:Format="#,##0"/>' . "\n";
        echo '   <Font ss:FontName="Calibri" ss:Size="10"/>' . "\n";
        echo '   <Borders>' . "\n";
        echo '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D3D3D3"/>' . "\n";
        echo '   </Borders>' . "\n";
        echo '  </Style>' . "\n";

        echo '  <Style ss:ID="SectionTitle">' . "\n";
        echo '   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>' . "\n";
        echo '   <Font ss:FontName="Calibri" ss:Size="12" ss:Bold="1" ss:Color="#333333"/>' . "\n";
        echo '  </Style>' . "\n";

        echo ' </Styles>' . "\n";

        echo ' <Worksheet ss:Name="Rekap">' . "\n";
        echo '  <Table>' . "\n";

        // Columns definition with widths
        echo '   <Column ss:Width="30"/>' . "\n";  // #
        echo '   <Column ss:Width="110"/>' . "\n"; // Nomor WO
        echo '   <Column ss:Width="130"/>' . "\n"; // Buyer
        echo '   <Column ss:Width="140"/>' . "\n"; // Motif
        echo '   <Column ss:Width="120"/>' . "\n"; // Handling
        echo '   <Column ss:Width="80"/>' . "\n";  // Batch Total
        echo '   <Column ss:Width="120"/>' . "\n"; // Jml Panjang
        echo '   <Column ss:Width="120"/>' . "\n"; // Warna
        echo '   <Column ss:Width="80"/>' . "\n";  // Tgl WO
        if ($isProcessing) {
            echo '   <Column ss:Width="80"/>' . "\n";  // Tgl Kirim
        }
        echo '   <Column ss:Width="120"/>' . "\n"; // NK
        echo '   <Column ss:Width="90"/>' . "\n";  // Panjang Greige
        echo '   <Column ss:Width="70"/>' . "\n";  // PSP
        echo '   <Column ss:Width="70"/>' . "\n";  // Relaxing
        echo '   <Column ss:Width="70"/>' . "\n";  // DYEING
        echo '   <Column ss:Width="70"/>' . "\n";  // DY 1
        echo '   <Column ss:Width="70"/>' . "\n";  // DY 2
        echo '   <Column ss:Width="70"/>' . "\n";  // DY 3
        echo '   <Column ss:Width="80"/>' . "\n";  // TOPING LEVEL
        echo '   <Column ss:Width="70"/>' . "\n";  // PACKING
        echo '   <Column ss:Width="90"/>' . "\n";  // Panjang Jadi
        echo '   <Column ss:Width="90"/>' . "\n";  // Total Qty Gudang

        // Header Row
        echo '   <Row ss:Height="25">' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">#</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">Nomor WO</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">Buyer</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">Motif</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">Handling</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">BATCH TOTAL</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">JML PANJANG</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">Warna</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">TANGGAL WO</Data></Cell>' . "\n";
        if ($isProcessing) {
            echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">TANGGAL KIRIM</Data></Cell>' . "\n";
        }
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">NK</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">Panjang Greige</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">PSP</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">Relaxing</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">DYEING</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">DY 1</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">DY 2</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">DY 3</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">TOPING LEVEL</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">PACKING</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">Panjang Jadi</Data></Cell>' . "\n";
        echo '    <Cell ss:StyleID="Header"><Data ss:Type="String">Total Qty Gudang</Data></Cell>' . "\n";
        echo '   </Row>' . "\n";

        // Helper function to extract process date
        $getProcessDate = function($kpId, $procId) use ($processesMap) {
            if (isset($processesMap[$kpId][$procId]['tanggal']) && !empty($processesMap[$kpId][$procId]['tanggal'])) {
                return date('d/m/y', strtotime($processesMap[$kpId][$procId]['tanggal']));
            }
            return '';
        };

        // Table 1 Rows
        $rowsTable1 = [];
        $woCache = [];
        foreach ($models as $model) {
            $woId = $model->wo_id;
            if (!isset($woCache[$woId])) {
                $wo = $model->wo;
                $sc = $model->sc;
                $mFinish = ($wo && is_numeric($wo->colorQtyFinish)) ? Yii::$app->formatter->asDecimal($wo->colorQtyFinish) : '-';
                $yFinish = ($wo && is_numeric($wo->colorQtyFinishToYard)) ? Yii::$app->formatter->asDecimal($wo->colorQtyFinishToYard) : '-';
                $woCache[$woId] = [
                    'woNo' => $wo ? $wo->no : '',
                    'buyer' => $sc ? $sc->customerName : '',
                    'motif' => $wo ? $wo->greigeNamaKain : '',
                    'handling' => ($wo && $wo->handling) ? $wo->handling->name : '-',
                    'batchTotal' => $wo ? $wo->colorQty : 0,
                    'jmlPanjang' => $wo ? ($mFinish . 'M / ' . $yFinish . 'Y') : '',
                    'tglWo' => ($wo && $wo->date) ? date('d/m/y', strtotime($wo->date)) : '',
                    'tglKirim' => ($wo && $wo->tgl_kirim) ? date('d/m/y', strtotime($wo->tgl_kirim)) : '',
                ];
            }
            $woColor = $model->woColor;
            $moColor = $woColor ? $woColor->moColor : null;
            $warna = $moColor ? $moColor->color : '';

            $nk = $model->nomor_kartu ?: '';
            $panjangGreige = isset($itemsSumMap[$model->id]) ? (float)$itemsSumMap[$model->id] : null;
            $psp = $getProcessDate($model->id, 1);
            $relaxing = $getProcessDate($model->id, 3);
            $dyeing = $getProcessDate($model->id, 8);
            $dy1 = $getProcessDate($model->id, 15);
            $dy2 = $getProcessDate($model->id, 18);
            $dy3 = $getProcessDate($model->id, 19);
            $topingLevel = $getProcessDate($model->id, 21);
            $packing = '';
            if (!empty($model->approved_at)) {
                if (is_numeric($model->approved_at) && (int)$model->approved_at > 100000000) {
                    $packing = date('d/m/y', (int)$model->approved_at);
                } elseif (!is_numeric($model->approved_at) && strtotime($model->approved_at)) {
                    $packing = date('d/m/y', strtotime($model->approved_at));
                }
            }
            $panjangJadi = (isset($processesMap[$model->id][11]['panjang_jadi']) && is_numeric($processesMap[$model->id][11]['panjang_jadi'])) ? (float)$processesMap[$model->id][11]['panjang_jadi'] : null;
            $totalQtyGudang = isset($inspectingMap[$model->id]) ? (float)$inspectingMap[$model->id] : null;

            $rowsTable1[] = [
                'wo_id' => $woId,
                'woInfo' => $woCache[$woId],
                'warna' => $warna,
                'nk' => $nk,
                'panjangGreige' => $panjangGreige,
                'psp' => $psp,
                'relaxing' => $relaxing,
                'dyeing' => $dyeing,
                'dy1' => $dy1,
                'dy2' => $dy2,
                'dy3' => $dy3,
                'topingLevel' => $topingLevel,
                'packing' => $packing,
                'panjangJadi' => $panjangJadi,
                'totalQtyGudang' => $totalQtyGudang,
            ];
        }

        // For Processing: include WO Disetujui Belum Ada NK in the same table
        if ($isProcessing && !empty($modelsNoNk)) {
            foreach ($modelsNoNk as $modelColor) {
                $woId = $modelColor->wo_id;
                if (!isset($woCache[$woId])) {
                    $wo = $modelColor->wo;
                    $sc = $modelColor->sc;
                    $mFinish = ($wo && is_numeric($wo->colorQtyFinish)) ? Yii::$app->formatter->asDecimal($wo->colorQtyFinish) : '-';
                    $yFinish = ($wo && is_numeric($wo->colorQtyFinishToYard)) ? Yii::$app->formatter->asDecimal($wo->colorQtyFinishToYard) : '-';
                    $woCache[$woId] = [
                        'woNo' => $wo ? $wo->no : '',
                        'buyer' => $sc ? $sc->customerName : '',
                        'motif' => $wo ? $wo->greigeNamaKain : '',
                        'handling' => ($wo && $wo->handling) ? $wo->handling->name : '-',
                        'batchTotal' => $wo ? $wo->colorQty : 0,
                        'jmlPanjang' => $wo ? ($mFinish . 'M / ' . $yFinish . 'Y') : '',
                        'tglWo' => ($wo && $wo->date) ? date('d/m/y', strtotime($wo->date)) : '',
                        'tglKirim' => ($wo && $wo->tgl_kirim) ? date('d/m/y', strtotime($wo->tgl_kirim)) : '',
                    ];
                }
                $moColor = $modelColor->moColor;
                $warna = $moColor ? $moColor->color : '-';

                $rowsTable1[] = [
                    'wo_id' => $woId,
                    'woInfo' => $woCache[$woId],
                    'warna' => $warna,
                    'nk' => 'Belum Ada NK',
                    'panjangGreige' => null,
                    'psp' => '',
                    'relaxing' => '',
                    'dyeing' => '',
                    'dy1' => '',
                    'dy2' => '',
                    'dy3' => '',
                    'topingLevel' => '',
                    'packing' => '',
                    'panjangJadi' => null,
                    'totalQtyGudang' => null,
                ];
            }

            // Sort unified table by WO No length, WO No, motif, warna, nk
            usort($rowsTable1, function($a, $b) {
                $woNoA = $a['woInfo']['woNo'];
                $woNoB = $b['woInfo']['woNo'];
                $lenA = strlen($woNoA);
                $lenB = strlen($woNoB);
                if ($lenA !== $lenB) return $lenA <=> $lenB;
                $cmpWo = strcmp($woNoA, $woNoB);
                if ($cmpWo !== 0) return $cmpWo;

                $motifA = $a['woInfo']['motif'];
                $motifB = $b['woInfo']['motif'];
                $cmpMotif = strcmp($motifA, $motifB);
                if ($cmpMotif !== 0) return $cmpMotif;

                $cmpColor = strcmp($a['warna'], $b['warna']);
                if ($cmpColor !== 0) return $cmpColor;

                return strcmp($a['nk'], $b['nk']);
            });
        }

        $mergesTable1 = $this->computeRowMerges($rowsTable1);

        $no = 1;
        foreach ($rowsTable1 as $idx => $r) {
            $m = $mergesTable1[$idx];
            $woInfo = $r['woInfo'];
            $warna = $r['warna'];
            $woMergeAttr = $m['wo_merge'] > 0 ? ' ss:MergeDown="' . $m['wo_merge'] . '"' : '';
            $warnaMergeAttr = $m['warna_merge'] > 0 ? ' ss:MergeDown="' . $m['warna_merge'] . '"' : '';

            echo '   <Row ss:Height="18">' . "\n";
            echo '    <Cell ss:Index="1" ss:StyleID="TextCenter"><Data ss:Type="Number">' . $no++ . '</Data></Cell>' . "\n";
            if (!$m['wo_skip']) {
                echo '    <Cell ss:Index="2"' . $woMergeAttr . ' ss:StyleID="TextLeft"><Data ss:Type="String">' . htmlspecialchars($woInfo['woNo'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                echo '    <Cell ss:Index="3"' . $woMergeAttr . ' ss:StyleID="TextLeft"><Data ss:Type="String">' . htmlspecialchars($woInfo['buyer'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                echo '    <Cell ss:Index="4"' . $woMergeAttr . ' ss:StyleID="TextLeft"><Data ss:Type="String">' . htmlspecialchars($woInfo['motif'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                echo '    <Cell ss:Index="5"' . $woMergeAttr . ' ss:StyleID="TextLeft"><Data ss:Type="String">' . htmlspecialchars($woInfo['handling'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                echo '    <Cell ss:Index="6"' . $woMergeAttr . ' ss:StyleID="NumInt"><Data ss:Type="Number">' . $woInfo['batchTotal'] . '</Data></Cell>' . "\n";
                echo '    <Cell ss:Index="7"' . $woMergeAttr . ' ss:StyleID="TextCenter"><Data ss:Type="String">' . htmlspecialchars($woInfo['jmlPanjang'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
            }
            if (!$m['warna_skip']) {
                echo '    <Cell ss:Index="8"' . $warnaMergeAttr . ' ss:StyleID="TextLeft"><Data ss:Type="String">' . htmlspecialchars($warna, ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
            }
            if (!$m['wo_skip']) {
                echo '    <Cell ss:Index="9"' . $woMergeAttr . ' ss:StyleID="TextCenter"><Data ss:Type="String">' . htmlspecialchars($woInfo['tglWo'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                if ($isProcessing) {
                    echo '    <Cell ss:Index="10"' . $woMergeAttr . ' ss:StyleID="TextCenter"><Data ss:Type="String">' . htmlspecialchars($woInfo['tglKirim'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                }
            }

            $cIdx = $isProcessing ? 11 : 10;
            echo '    <Cell ss:Index="' . ($cIdx++) . '" ss:StyleID="TextLeft"><Data ss:Type="String">' . htmlspecialchars($r['nk'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
            
            if ($r['panjangGreige'] !== null && is_numeric($r['panjangGreige']) && (float)$r['panjangGreige'] > 0) {
                echo '    <Cell ss:Index="' . ($cIdx++) . '" ss:StyleID="NumDec"><Data ss:Type="Number">' . (float)$r['panjangGreige'] . '</Data></Cell>' . "\n";
            } else {
                echo '    <Cell ss:Index="' . ($cIdx++) . '" ss:StyleID="TextCenter"><Data ss:Type="String">-</Data></Cell>' . "\n";
            }
            
            echo '    <Cell ss:Index="' . ($cIdx++) . '" ss:StyleID="TextCenter"><Data ss:Type="String">' . htmlspecialchars($r['psp'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
            echo '    <Cell ss:Index="' . ($cIdx++) . '" ss:StyleID="TextCenter"><Data ss:Type="String">' . htmlspecialchars($r['relaxing'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
            echo '    <Cell ss:Index="' . ($cIdx++) . '" ss:StyleID="TextCenter"><Data ss:Type="String">' . htmlspecialchars($r['dyeing'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
            echo '    <Cell ss:Index="' . ($cIdx++) . '" ss:StyleID="TextCenter"><Data ss:Type="String">' . htmlspecialchars($r['dy1'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
            echo '    <Cell ss:Index="' . ($cIdx++) . '" ss:StyleID="TextCenter"><Data ss:Type="String">' . htmlspecialchars($r['dy2'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
            echo '    <Cell ss:Index="' . ($cIdx++) . '" ss:StyleID="TextCenter"><Data ss:Type="String">' . htmlspecialchars($r['dy3'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
            echo '    <Cell ss:Index="' . ($cIdx++) . '" ss:StyleID="TextCenter"><Data ss:Type="String">' . htmlspecialchars($r['topingLevel'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
            echo '    <Cell ss:Index="' . ($cIdx++) . '" ss:StyleID="TextCenter"><Data ss:Type="String">' . htmlspecialchars($r['packing'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
            
            if ($r['panjangJadi'] !== null && is_numeric($r['panjangJadi']) && (float)$r['panjangJadi'] > 0) {
                echo '    <Cell ss:Index="' . ($cIdx++) . '" ss:StyleID="NumDec"><Data ss:Type="Number">' . (float)$r['panjangJadi'] . '</Data></Cell>' . "\n";
            } else {
                echo '    <Cell ss:Index="' . ($cIdx++) . '" ss:StyleID="TextCenter"><Data ss:Type="String">-</Data></Cell>' . "\n";
            }
            
            if ($r['totalQtyGudang'] !== null && is_numeric($r['totalQtyGudang']) && (float)$r['totalQtyGudang'] > 0) {
                echo '    <Cell ss:Index="' . ($cIdx++) . '" ss:StyleID="NumDec"><Data ss:Type="Number">' . (float)$r['totalQtyGudang'] . '</Data></Cell>' . "\n";
            } else {
                echo '    <Cell ss:Index="' . ($cIdx++) . '" ss:StyleID="TextCenter"><Data ss:Type="String">-</Data></Cell>' . "\n";
            }
            
            echo '   </Row>' . "\n";
        }

        echo '  </Table>' . "\n";
        echo ' </Worksheet>' . "\n";

        // Worksheet 2 (WO Disetujui Belum Ada NK) for Formated only
        if (!$isProcessing && !empty($modelsNoNk)) {
            $rowsTable2 = [];
            foreach ($modelsNoNk as $modelColor) {
                $woId = $modelColor->wo_id;
                if (!isset($woCache[$woId])) {
                    $wo = $modelColor->wo;
                    $sc = $modelColor->sc;
                    $woCache[$woId] = [
                        'woNo' => $wo ? $wo->no : '',
                        'buyer' => $sc ? $sc->customerName : '',
                        'motif' => $wo ? $wo->greigeNamaKain : '',
                        'handling' => ($wo && $wo->handling) ? $wo->handling->name : '-',
                        'batchTotal' => $wo ? $wo->colorQty : 0,
                        'jmlPanjang' => $wo ? (Yii::$app->formatter->asDecimal($wo->colorQtyFinish) . 'M / ' . Yii::$app->formatter->asDecimal($wo->colorQtyFinishToYard) . 'Y') : '',
                        'tglWo' => ($wo && $wo->date) ? date('d/m/y', strtotime($wo->date)) : '',
                        'tglKirim' => ($wo && $wo->tgl_kirim) ? date('d/m/y', strtotime($wo->tgl_kirim)) : '',
                    ];
                }
                $moColor = $modelColor->moColor;
                $warna = $moColor ? $moColor->color : '-';

                $rowsTable2[] = [
                    'wo_id' => $woId,
                    'woInfo' => $woCache[$woId],
                    'warna' => $warna,
                ];
            }
            $mergesTable2 = $this->computeRowMerges($rowsTable2);

            echo ' <Worksheet ss:Name="WO_Disetujui_Belum_Ada_NK">' . "\n";
            echo '  <Table>' . "\n";

            // Column widths
            echo '   <Column ss:Width="35"/>' . "\n";  // #
            echo '   <Column ss:Width="120"/>' . "\n"; // Nomor WO
            echo '   <Column ss:Width="150"/>' . "\n"; // Buyer
            echo '   <Column ss:Width="160"/>' . "\n"; // Motif
            echo '   <Column ss:Width="130"/>' . "\n"; // Handling
            echo '   <Column ss:Width="90"/>' . "\n";  // Batch Total
            echo '   <Column ss:Width="130"/>' . "\n"; // Jml Panjang
            echo '   <Column ss:Width="130"/>' . "\n"; // Warna
            echo '   <Column ss:Width="90"/>' . "\n";  // Tgl WO
            if ($isProcessing) {
                echo '   <Column ss:Width="90"/>' . "\n";  // Tgl Kirim
            }
            echo '   <Column ss:Width="110"/>' . "\n"; // NK

            // Header Table 2
            echo '   <Row ss:Height="25">' . "\n";
            echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">#</Data></Cell>' . "\n";
            echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">Nomor WO</Data></Cell>' . "\n";
            echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">Buyer</Data></Cell>' . "\n";
            echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">Motif</Data></Cell>' . "\n";
            echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">Handling</Data></Cell>' . "\n";
            echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">BATCH TOTAL</Data></Cell>' . "\n";
            echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">JML PANJANG</Data></Cell>' . "\n";
            echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">Warna</Data></Cell>' . "\n";
            echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">TANGGAL WO</Data></Cell>' . "\n";
            if ($isProcessing) {
                echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">TANGGAL KIRIM</Data></Cell>' . "\n";
            }
            echo '    <Cell ss:StyleID="HeaderWarning"><Data ss:Type="String">NK</Data></Cell>' . "\n";
            echo '   </Row>' . "\n";

            $no2 = 1;
            foreach ($rowsTable2 as $idx => $r) {
                $m = $mergesTable2[$idx];
                $woInfo = $r['woInfo'];
                $warna = $r['warna'];
                $woMergeAttr = $m['wo_merge'] > 0 ? ' ss:MergeDown="' . $m['wo_merge'] . '"' : '';
                $warnaMergeAttr = $m['warna_merge'] > 0 ? ' ss:MergeDown="' . $m['warna_merge'] . '"' : '';

                echo '   <Row ss:Height="18">' . "\n";
                echo '    <Cell ss:Index="1" ss:StyleID="TextCenter"><Data ss:Type="Number">' . $no2++ . '</Data></Cell>' . "\n";
                if (!$m['wo_skip']) {
                    echo '    <Cell ss:Index="2"' . $woMergeAttr . ' ss:StyleID="TextLeft"><Data ss:Type="String">' . htmlspecialchars($woInfo['woNo'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                    echo '    <Cell ss:Index="3"' . $woMergeAttr . ' ss:StyleID="TextLeft"><Data ss:Type="String">' . htmlspecialchars($woInfo['buyer'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                    echo '    <Cell ss:Index="4"' . $woMergeAttr . ' ss:StyleID="TextLeft"><Data ss:Type="String">' . htmlspecialchars($woInfo['motif'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                    echo '    <Cell ss:Index="5"' . $woMergeAttr . ' ss:StyleID="TextLeft"><Data ss:Type="String">' . htmlspecialchars($woInfo['handling'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                    echo '    <Cell ss:Index="6"' . $woMergeAttr . ' ss:StyleID="NumInt"><Data ss:Type="Number">' . $woInfo['batchTotal'] . '</Data></Cell>' . "\n";
                    echo '    <Cell ss:Index="7"' . $woMergeAttr . ' ss:StyleID="TextCenter"><Data ss:Type="String">' . htmlspecialchars($woInfo['jmlPanjang'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                }
                if (!$m['warna_skip']) {
                    echo '    <Cell ss:Index="8"' . $warnaMergeAttr . ' ss:StyleID="TextLeft"><Data ss:Type="String">' . htmlspecialchars($warna, ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                }
                if (!$m['wo_skip']) {
                    echo '    <Cell ss:Index="9"' . $woMergeAttr . ' ss:StyleID="TextCenter"><Data ss:Type="String">' . htmlspecialchars($woInfo['tglWo'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                    if ($isProcessing) {
                        echo '    <Cell ss:Index="10"' . $woMergeAttr . ' ss:StyleID="TextCenter"><Data ss:Type="String">' . htmlspecialchars($woInfo['tglKirim'], ENT_QUOTES, 'UTF-8') . '</Data></Cell>' . "\n";
                    }
                }
                $nkColIndex = $isProcessing ? 11 : 10;
                echo '    <Cell ss:Index="' . $nkColIndex . '" ss:StyleID="TextCenter"><Data ss:Type="String">Belum Ada NK</Data></Cell>' . "\n";
                echo '   </Row>' . "\n";
            }

            echo '  </Table>' . "\n";
            echo ' </Worksheet>' . "\n";
        }

        echo '</Workbook>' . "\n";

        exit;
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
}
