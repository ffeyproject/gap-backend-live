<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $tanggal string */
/* @var $produksiHisakaRows array */
/* @var $totalStandardCards int */
/* @var $totalStandardBatches float|int */
/* @var $totalRepairCards int */
/* @var $totalRepairBatches float|int */
/* @var $totalAllCards int */
/* @var $totalAllBatches float|int */
/* @var $hisakaRepair array */
/* @var $shiftRows array */
/* @var $totalJumboBatches float|int */
/* @var $shiftTableTotal float|int */
/* @var $hambatanItems array */
/* @var $persiapanShifts array */
/* @var $winchShifts array */
/* @var $fukushinShifts array */
/* @var $washingShifts array */
/* @var $slangerShifts array */
/* @var $paddingDetail array */
/* @var $paddingShifts array */
/* @var $masukPackingDyeing int */
/* @var $masukPackingPrinting int */
/* @var $produksiPrintingLength float|int */
/* @var $produksiDigitalLength float|int */
/* @var $cuciMesinHisaka array */
/* @var $wipDyeingCount int */
/* @var $isPrint bool */

$this->title = 'Laporan Produksi Harian Processing';
$this->params['breadcrumbs'][] = ['label' => 'Processing', 'url' => '#'];
$this->params['breadcrumbs'][] = $this->title;

$fmt = function($num) {
    if ($num === null || $num == 0) return '0';
    if (is_float($num)) {
        if (fmod($num, 1.0) == 0.5) {
            return number_format($num, 1, ',', '.');
        }
        return number_format(round($num), 0, ',', '.');
    }
    return number_format($num, 0, ',', '.');
};

$fmtDate = function($date) {
    return date('d F Y', strtotime($date));
};
?>

<style>
    /* Screen styling */
    .filter-box {
        margin-bottom: 15px;
    }
    .report-paper {
        background: #fff;
        width: 210mm;
        min-height: 297mm;
        margin: 10px auto;
        padding: 5mm 6mm;
        border: 1px solid #ccc;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        box-sizing: border-box;
        position: relative;
        color: #000;
        font-family: Arial, sans-serif;
    }
    .section-title {
        font-size: 8px;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 2px;
        border-bottom: 1px solid #000;
        padding-bottom: 1px;
    }
    table.report-table {
        border-collapse: collapse;
        width: 100%;
        font-size: 7.5px;
        margin-bottom: 4px;
        color: #000;
    }
    table.report-table th, table.report-table td {
        border: 1px solid #000;
        padding: 1px 2px;
        text-align: left;
        height: 11px;
        box-sizing: border-box;
    }
    table.report-table th {
        background-color: #f0f0f0;
        font-weight: bold;
        text-align: center;
        text-transform: uppercase;
    }
    .text-center { text-align: center !important; }
    .text-right { text-align: right !important; }
    .text-bold { font-weight: bold !important; }
    .bg-shading { background-color: #ffeeba !important; font-weight: bold !important; }
    
    /* Print specific styling */
    @media print {
        .main-header, .main-sidebar, .content-header, .breadcrumb, .no-print, .btn, .filter-box {
            display: none !important;
        }
        .content-wrapper {
            margin-left: 0 !important;
            background-color: #fff !important;
            padding: 0 !important;
        }
        body {
            background: #fff !important;
            color: #000 !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .report-paper {
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            box-shadow: none !important;
            box-sizing: border-box;
        }
    }
</style>

<?php if (!$isPrint): ?>
<div class="filter-box no-print">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Filter Tanggal Laporan</h3>
        </div>
        <div class="box-body">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <?= Html::beginForm(['rekap-harian-processing'], 'get', ['class' => 'form-inline', 'style' => 'display:inline-block;']) ?>
                    <div class="form-group">
                        <label>Pilih Tanggal: </label>
                        <?= Html::input('date', 'tanggal', $tanggal, ['class' => 'form-control', 'onchange' => 'this.form.submit()']) ?>
                    </div>
                <?= Html::endForm() ?>
                <div>
                    <button onclick="window.print()" class="btn btn-success"><i class="fa fa-print"></i> Print via Browser</button>
                    <?= Html::a('<i class="fa fa-file-pdf-o"></i> Export PDF', ['rekap-harian-processing', 'tanggal' => $tanggal, 'print' => 1], ['class' => 'btn btn-danger', 'target' => '_blank']) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="report-paper">
    <!-- HEADER -->
    <div class="report-header">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 2px solid #000; padding-bottom: 1px; margin-bottom: 5px;">
            <div style="font-size: 9px; font-weight: bold;">LAPORAN PRODUKSI HARIAN</div>
            <div style="font-size: 10px; font-weight: bold; text-transform: uppercase;"><?= $fmtDate($tanggal) ?></div>
            <div style="font-size: 8px; font-weight: bold;">GAP-FRM-PMC-04</div>
        </div>
    </div>

    <!-- MAIN TWO-COLUMN BODY -->
    <div class="report-body" style="display: flex; gap: 8px; margin-bottom: 4px;">
        
        <!-- LEFT COLUMN (64% width) -->
        <div style="width: 64%; display: flex; flex-direction: column; gap: 4px;">
            
            <!-- PRODUKSI HISAKA -->
            <div>
                <div class="section-title">PRODUKSI HISAKA</div>
                <div style="display: flex; gap: 8px;">
                    <div style="flex: 3;">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>PROSES</th>
                                    <th>BATCH</th>
                                    <th>KARTU</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produksiHisakaRows as $proses => $row): ?>
                                <tr>
                                    <td><?= Html::encode($proses) ?></td>
                                    <td class="text-center"><?= $fmt($row['batches']) ?></td>
                                    <td class="text-center"><?= $fmt($row['cards']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="text-bold">
                                    <td>TOTAL</td>
                                    <td class="text-center"><?= $fmt($totalStandardBatches) ?></td>
                                    <td class="text-center"><?= $fmt($totalStandardCards) ?></td>
                                </tr>
                                <tr class="text-bold" style="color: red;">
                                    <td>TOTAL+PRK</td>
                                    <td class="text-center"><?= $fmt($totalAllBatches) ?></td>
                                    <td class="text-center"><?= $fmt($totalAllCards) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="flex: 2;">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>SHIFT</th>
                                    <th>BATCH</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($shiftRows as $shift => $batches): ?>
                                <tr>
                                    <td class="text-center"><?= Html::encode($shift) ?></td>
                                    <td class="text-center"><?= $fmt($batches) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td>JUMBO</td>
                                    <td class="text-center"><?= $fmt($totalJumboBatches) ?></td>
                                </tr>
                                <tr class="text-bold" style="color: red;">
                                    <td>TOTAL</td>
                                    <td class="text-center"><?= $fmt($shiftTableTotal) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- PERBAIKAN HISAKA -->
            <div>
                <div class="section-title">PERBAIKAN HISAKA</div>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 20px;">NO</th>
                            <th style="width: 70px;">NO. WO</th>
                            <th>MOTIF GREIGE</th>
                            <th>WARNA</th>
                            <th style="width: 35px;">NK</th>
                            <th style="width: 35px;">QTY</th>
                            <th style="width: 70px;">PROSES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (empty($hisakaRepair)):
                        ?>
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>
                        <?php 
                        else:
                            $i = 1;
                            foreach ($hisakaRepair as $p):
                                $isTopping = (stripos($p['proses'], 'Toping') !== false) || (stripos($p['proses'], 'Topping') !== false);
                                $rowClass = $isTopping ? 'bg-shading' : '';
                        ?>
                        <tr class="<?= $rowClass ?>">
                            <td class="text-center"><?= $i++ ?></td>
                            <td><?= Html::encode($p['wo_no']) ?></td>
                            <td><?= Html::encode($p['motif']) ?></td>
                            <td><?= Html::encode($p['warna']) ?></td>
                            <td class="text-center"><?= Html::encode($p['nk']) ?></td>
                            <td class="text-right"><?= $fmt($p['panjang']) ?></td>
                            <td><?= Html::encode($p['proses']) ?></td>
                        </tr>
                        <?php 
                            endforeach;
                        endif; 
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- PERSIAPAN & CONTINUOUS WINCH -->
            <div style="display: flex; gap: 8px;">
                <div style="flex: 1;">
                    <div class="section-title">PERSIAPAN</div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>SHIFT</th>
                                <th>JML</th>
                                <th>DY</th>
                                <th>PR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $persiapanTotalJml = 0;
                            $persiapanTotalDyCount = 0;
                            $persiapanTotalDyLen = 0;
                            $persiapanTotalPrCount = 0;
                            $persiapanTotalPrLen = 0;
                            $shiftsList = ['A', 'B'];
                            foreach ($shiftsList as $s):
                                $row = isset($persiapanShifts[$s]) ? $persiapanShifts[$s] : ['jml' => 0, 'dy_count' => 0, 'dy_len' => 0, 'pr_count' => 0, 'pr_len' => 0];
                                $persiapanTotalJml += $row['jml'];
                                $persiapanTotalDyCount += $row['dy_count'];
                                $persiapanTotalDyLen += $row['dy_len'];
                                $persiapanTotalPrCount += $row['pr_count'];
                                $persiapanTotalPrLen += $row['pr_len'];
                            ?>
                            <tr>
                                <td class="text-center"><?= $s ?></td>
                                <td class="text-right"><?= $fmt($row['jml']) ?></td>
                                <td class="text-center"><?= $fmt($row['dy_count']) ?></td>
                                <td class="text-center"><?= $fmt($row['pr_count']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="text-bold" style="color: red;">
                                <td>TOTAL</td>
                                <td class="text-right"><?= $fmt($persiapanTotalJml) ?></td>
                                <td class="text-center"><?= $persiapanTotalDyCount . '(' . $fmt($persiapanTotalDyLen) . ')' ?></td>
                                <td class="text-center"><?= $persiapanTotalPrCount . '(' . $fmt($persiapanTotalPrLen) . ')' ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="flex: 1;">
                    <div class="section-title">CONTINUOUS WINCH</div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>SHIFT</th>
                                <th>JML</th>
                                <th>ULANG</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $winchTotalJml = 0;
                            $winchTotalUlang = 0;
                            $winchShiftsList = ['B', 'A'];
                            foreach ($winchShiftsList as $s):
                                $row = isset($winchShifts[$s]) ? $winchShifts[$s] : ['jml' => 0, 'ulang' => 0];
                                $winchTotalJml += $row['jml'];
                                $winchTotalUlang += $row['ulang'];
                            ?>
                            <tr>
                                <td class="text-center"><?= $s ?></td>
                                <td class="text-right"><?= $fmt($row['jml']) ?></td>
                                <td class="text-center"><?= $fmt($row['ulang']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="text-bold" style="color: red;">
                                <td>TOTAL</td>
                                <td class="text-right"><?= $fmt($winchTotalJml) ?></td>
                                <td class="text-center"><?= $fmt($winchTotalUlang) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- BO FUKUSHIN & WASHING -->
            <div style="display: flex; gap: 8px;">
                <div style="flex: 1;">
                    <div class="section-title">BO FUKUSHIN</div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>SHIFT</th>
                                <th>JML</th>
                                <th>BATCH</th>
                                <th>BO UL.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $fukushinTotalJml = 0;
                            $fukushinTotalBatch = 0;
                            $fukushinTotalBoUl = 0;
                            $fukushinShiftsList = ['A', 'B', 'C'];
                            foreach ($fukushinShiftsList as $s):
                                $row = isset($fukushinShifts[$s]) ? $fukushinShifts[$s] : ['jml' => 0, 'batch' => 0, 'bo_ul' => 0];
                                $fukushinTotalJml += $row['jml'];
                                $fukushinTotalBatch += $row['batch'];
                                $fukushinTotalBoUl += $row['bo_ul'];
                            ?>
                            <tr>
                                <td class="text-center"><?= $s ?></td>
                                <td class="text-right"><?= $fmt($row['jml']) ?></td>
                                <td class="text-center"><?= $fmt($row['batch']) ?></td>
                                <td class="text-center"><?= $fmt($row['bo_ul']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="text-bold" style="color: red;">
                                <td>TOTAL</td>
                                <td class="text-right"><?= $fmt($fukushinTotalJml) ?></td>
                                <td class="text-center"><?= $fmt($fukushinTotalBatch) ?></td>
                                <td class="text-center"><?= $fmt($fukushinTotalBoUl) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="flex: 1;">
                    <div class="section-title">WASHING</div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>SHIFT</th>
                                <th>JML</th>
                                <th>GUL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $washingTotalJml = 0;
                            $washingTotalGul = 0;
                            $washingShiftsList = ['A', 'B', 'C'];
                            foreach ($washingShiftsList as $s):
                                $row = isset($washingShifts[$s]) ? $washingShifts[$s] : ['jml' => 0, 'gul' => 0];
                                $washingTotalJml += $row['jml'];
                                $washingTotalGul += $row['gul'];
                            ?>
                            <tr>
                                <td class="text-center"><?= $s ?></td>
                                <td class="text-right"><?= $fmt($row['jml']) ?></td>
                                <td class="text-center"><?= $fmt($row['gul']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="text-bold" style="color: red;">
                                <td>TOTAL</td>
                                <td class="text-right"><?= $fmt($washingTotalJml) ?></td>
                                <td class="text-center"><?= $fmt($washingTotalGul) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- SLANGER -->
            <div>
                <div class="section-title">SLANGER</div>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>SHIFT</th>
                            <th>MC 1+2</th>
                            <th>MC 3</th>
                            <th>MC 4</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $slangerTotalMc12 = 0;
                        $slangerTotalMc3 = 0;
                        $slangerTotalMc4 = 0;
                        $slangerTotal = 0;
                        $slangerShiftsList = ['C', 'D', 'A'];
                        foreach ($slangerShiftsList as $s):
                            $row = isset($slangerShifts[$s]) ? $slangerShifts[$s] : ['mc_1_2' => 0, 'mc_3' => 0, 'mc_4' => 0];
                            $rowTotal = $row['mc_1_2'] + $row['mc_3'] + $row['mc_4'];
                            $slangerTotalMc12 += $row['mc_1_2'];
                            $slangerTotalMc3 += $row['mc_3'];
                            $slangerTotalMc4 += $row['mc_4'];
                            $slangerTotal += $rowTotal;
                        ?>
                        <tr>
                            <td class="text-center"><?= $s ?></td>
                            <td class="text-center"><?= $fmt($row['mc_1_2']) ?></td>
                            <td class="text-center"><?= $fmt($row['mc_3']) ?></td>
                            <td class="text-center"><?= $fmt($row['mc_4']) ?></td>
                            <td class="text-center text-bold"><?= $fmt($rowTotal) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="text-bold" style="color: red;">
                            <td>TOTAL</td>
                            <td class="text-center"><?= $fmt($slangerTotalMc12) ?></td>
                            <td class="text-center"><?= $fmt($slangerTotalMc3) ?></td>
                            <td class="text-center"><?= $fmt($slangerTotalMc4) ?></td>
                            <td class="text-center"><?= $fmt($slangerTotal) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <!-- RIGHT COLUMN (36% width - HAMBATAN HISAKA) -->
        <div style="width: 36%; display: flex; flex-direction: column;">
            <div class="section-title">HAMBATAN HISAKA</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 25px;">MC</th>
                        <th style="width: 65px;">WAKTU</th>
                        <th>KETERANGAN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (empty($hambatanItems)):
                    ?>
                    <tr>
                        <td colspan="3" class="text-center" style="font-size: 7px;">Tidak ada hambatan</td>
                    </tr>
                    <?php 
                    else:
                        foreach ($hambatanItems as $item):
                            $mcName = $item->mstMesinProses ? $item->mstMesinProses->nama_mesin : '';
                            $mcNum = preg_replace('/[^0-9]/', '', $mcName);
                            if (empty($mcNum)) $mcNum = $mcName;
                            $waktu = $item->start_time . ' - ' . $item->stop_time;
                    ?>
                    <tr>
                        <td class="text-center"><?= Html::encode($mcNum) ?></td>
                        <td class="text-center" style="font-size: 7px;"><?= Html::encode($waktu) ?></td>
                        <td style="font-size: 7px;"><?= Html::encode($item->keterangan) ?></td>
                    </tr>
                    <?php 
                        endforeach;
                    endif; 
                    ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- BOTTOM PADDING & LAIN-LAIN SECTION -->
    <div style="border-top: 1.5px solid #000; padding-top: 4px; display: flex; gap: 8px;">
        
        <!-- PADDING TABLES (64% width) -->
        <div style="width: 64%; display: flex; flex-direction: column; gap: 3px;">
            <div class="section-title">PADDING</div>
            
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 20px;">NO</th>
                        <th style="width: 70px;">NO. WO</th>
                        <th>MOTIF</th>
                        <th>WARNA</th>
                        <th style="width: 35px;">NK</th>
                        <th style="width: 35px;">QTY</th>
                        <th style="width: 35px;">SATUAN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (empty($paddingDetail)):
                    ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data</td>
                    </tr>
                    <?php 
                    else:
                        $i = 1;
                        foreach ($paddingDetail as $p):
                    ?>
                    <tr>
                        <td class="text-center"><?= $i++ ?></td>
                        <td><?= Html::encode($p['wo_no']) ?></td>
                        <td><?= Html::encode($p['motif']) ?></td>
                        <td><?= Html::encode($p['warna']) ?></td>
                        <td class="text-center"><?= Html::encode($p['nk']) ?></td>
                        <td class="text-right"><?= $fmt($p['panjang']) ?></td>
                        <td class="text-center">M</td>
                    </tr>
                    <?php 
                        endforeach;
                    endif; 
                    ?>
                </tbody>
            </table>

            <table class="report-table" style="font-size: 6.5px; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 30px;">SHIFT</th>
                        <th rowspan="2" style="width: 30px;">JML</th>
                        <th colspan="2">PADDING PR</th>
                        <th colspan="2">PADDING DY</th>
                        <th colspan="2">PRK DY</th>
                        <th colspan="2">PRK PR</th>
                        <th colspan="2">TES</th>
                    </tr>
                    <tr>
                        <th style="font-size: 5.5px;">JML</th>
                        <th style="font-size: 5.5px;">KARTU</th>
                        <th style="font-size: 5.5px;">JML</th>
                        <th style="font-size: 5.5px;">KARTU</th>
                        <th style="font-size: 5.5px;">JML</th>
                        <th style="font-size: 5.5px;">KARTU</th>
                        <th style="font-size: 5.5px;">JML</th>
                        <th style="font-size: 5.5px;">KARTU</th>
                        <th style="font-size: 5.5px;">JML</th>
                        <th style="font-size: 5.5px;">KARTU</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $padTotalJml = 0;
                    $padTotalPrJml = 0; $padTotalPrCard = 0;
                    $padTotalDyJml = 0; $padTotalDyCard = 0;
                    $padTotalPrkDyJml = 0; $padTotalPrkDyCard = 0;
                    $padTotalPrkPrJml = 0; $padTotalPrkPrCard = 0;
                    $padTotalTesJml = 0; $padTotalTesCard = 0;
                    
                    $paddingShiftsList = ['B', 'C', 'A'];
                    foreach ($paddingShiftsList as $s):
                        $row = isset($paddingShifts[$s]) ? $paddingShifts[$s] : [
                            'jml' => 0,
                            'pad_pr_jml' => 0, 'pad_pr_card' => 0,
                            'pad_dy_jml' => 0, 'pad_dy_card' => 0,
                            'prk_dy_jml' => 0, 'prk_dy_card' => 0,
                            'prk_pr_jml' => 0, 'prk_pr_card' => 0,
                            'tes_jml' => 0, 'tes_card' => 0
                        ];
                        
                        $padTotalJml += $row['jml'];
                        $padTotalPrJml += $row['pad_pr_jml']; $padTotalPrCard += $row['pad_pr_card'];
                        $padTotalDyJml += $row['pad_dy_jml']; $padTotalDyCard += $row['pad_dy_card'];
                        $padTotalPrkDyJml += $row['prk_dy_jml']; $padTotalPrkDyCard += $row['prk_dy_card'];
                        $padTotalPrkPrJml += $row['prk_pr_jml']; $padTotalPrkPrCard += $row['prk_pr_card'];
                        $padTotalTesJml += $row['tes_jml']; $padTotalTesCard += $row['tes_card'];
                    ?>
                    <tr>
                        <td class="text-center"><?= $s ?></td>
                        <td class="text-right"><?= $fmt($row['jml']) ?></td>
                        <td class="text-right"><?= $fmt($row['pad_pr_jml']) ?></td>
                        <td class="text-center"><?= $fmt($row['pad_pr_card']) ?></td>
                        <td class="text-right"><?= $fmt($row['pad_dy_jml']) ?></td>
                        <td class="text-center"><?= $fmt($row['pad_dy_card']) ?></td>
                        <td class="text-right"><?= $fmt($row['prk_dy_jml']) ?></td>
                        <td class="text-center"><?= $fmt($row['prk_dy_card']) ?></td>
                        <td class="text-right"><?= $fmt($row['prk_pr_jml']) ?></td>
                        <td class="text-center"><?= $fmt($row['prk_pr_card']) ?></td>
                        <td class="text-right"><?= $fmt($row['tes_jml']) ?></td>
                        <td class="text-center"><?= $fmt($row['tes_card']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="text-bold" style="color: red;">
                        <td>TOTAL</td>
                        <td class="text-right"><?= $fmt($padTotalJml) ?></td>
                        <td class="text-right"><?= $fmt($padTotalPrJml) ?></td>
                        <td class="text-center"><?= $fmt($padTotalPrCard) ?></td>
                        <td class="text-right"><?= $fmt($padTotalDyJml) ?></td>
                        <td class="text-center"><?= $fmt($padTotalDyCard) ?></td>
                        <td class="text-right"><?= $fmt($padTotalPrkDyJml) ?></td>
                        <td class="text-center"><?= $fmt($padTotalPrkDyCard) ?></td>
                        <td class="text-right"><?= $fmt($padTotalPrkPrJml) ?></td>
                        <td class="text-center"><?= $fmt($padTotalPrkPrCard) ?></td>
                        <td class="text-right"><?= $fmt($padTotalTesJml) ?></td>
                        <td class="text-center"><?= $fmt($padTotalTesCard) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- LAIN-LAIN BOX (36% width) -->
        <div style="width: 36%; display: flex; flex-direction: column; justify-content: flex-end; margin-bottom: 2px;">
            <div class="lain-lain-box" style="border: 1px solid #000; padding: 4px; font-size: 7.5px; line-height: 1.35; font-family: Arial, sans-serif; box-sizing: border-box;">
                <div class="text-bold" style="border-bottom: 1.2px solid #000; margin-bottom: 3px; font-size: 8px;">LAIN-LAIN</div>
                <table style="width: 100%; border: none; font-size: 7.5px; margin-bottom: 0;">
                    <tr style="border: none;">
                        <td style="border: none; padding: 0 2px 0 0; width: 95px;">Masuk Packing Dyeing</td>
                        <td style="border: none; padding: 0;">= <?= $masukPackingDyeing ?>x</td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 0 2px 0 0;">Masuk Packing Printing</td>
                        <td style="border: none; padding: 0;">= <?= $masukPackingPrinting ?>x</td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 0 2px 0 0;">Produksi Printing</td>
                        <td style="border: none; padding: 0;">= <?= $fmt($produksiPrintingLength) ?></td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 0 2px 0 0;">Produksi Digital</td>
                        <td style="border: none; padding: 0;">= <?= $fmt($produksiDigitalLength) ?></td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 0 2px 0 0;">Cuci Mesin Hisaka</td>
                        <td style="border: none; padding: 0;">= <?= empty($cuciMesinHisaka) ? '-' : implode(', ', $cuciMesinHisaka) ?></td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 0 2px 0 0;">WIP Dyeing</td>
                        <td style="border: none; padding: 0;">= <?= $wipDyeingCount ?>x</td>
                    </tr>
                </table>
            </div>
        </div>

    </div>
</div>
