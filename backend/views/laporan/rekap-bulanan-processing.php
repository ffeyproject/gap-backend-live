<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $bulan int */
/* @var $tahun int */
/* @var $data array */

$this->title = 'Rekap Produksi Bulanan Processing';
$this->params['breadcrumbs'][] = ['label' => 'Processing', 'url' => '#'];
$this->params['breadcrumbs'][] = ['label' => 'Rekap', 'url' => '#'];
$this->params['breadcrumbs'][] = $this->title;

$bulanOptions = [];
for($i=1; $i<=12; $i++) {
    $bulanOptions[$i] = date('F', mktime(0, 0, 0, $i, 10));
}
$tahunOptions = [];
$currentYear = date('Y');
for($i=$currentYear-2; $i<=$currentYear+2; $i++) {
    $tahunOptions[$i] = $i;
}

$val = function($key) use ($data) {
    return isset($data[$key]) ? (float)$data[$key] : 0;
};
$fmt = function($num) {
    return number_format(round($num), 0, ',', '.');
};
$fmtPct = function($num) {
    return number_format($num, 2, ',', '.');
};

// Calculations
$hisakaTotal = $val('hisaka_proses_celup') + $val('hisaka_proses_dasar_celup');
$hisakaToppingPct = $hisakaTotal > 0 ? ($val('hisaka_proses_topping') / $hisakaTotal * 100) : 0;
$hisakaLevelPct = $hisakaTotal > 0 ? ($val('hisaka_proses_level') / $hisakaTotal * 100) : 0;
$hisakaStrippingPct = $hisakaTotal > 0 ? ($val('hisaka_proses_stripping') / $hisakaTotal * 100) : 0;

$fukushinTotal = $val('fukushin_proses_continuous');
$fukushinCuciPct = $fukushinTotal > 0 ? ($val('fukushin_perbaikan_cuci_ulang') / $fukushinTotal * 100) : 0;

$stenterPfp = $val('stenter_setting_set_pfp');
$stenterSetUlangPfpPct = $stenterPfp > 0 ? ($val('stenter_setting_set_ulang_pfp') / $stenterPfp * 100) : 0;

$stenterRfDyeing = $val('stenter_resin_rf_dyeing');
$stenterRfUlangDyeingPct = $stenterRfDyeing > 0 ? ($val('stenter_resin_rf_ulang_dyeing') / $stenterRfDyeing * 100) : 0;

$stenterRfPrinting = $val('stenter_resin_rf_printing');
$stenterRfUlangPrintingPct = $stenterRfPrinting > 0 ? ($val('stenter_resin_rf_ulang_printing') / $stenterRfPrinting * 100) : 0;

?>
<style>
    .rekap-container {
        font-family: sans-serif;
        color: #1a1a1a;
        background-color: #f8f9fa;
        padding: 20px;
    }
    .rekap-section {
        background: #fff;
        border: 1px solid #ddd;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .rekap-section h4 {
        text-transform: uppercase;
        border-bottom: 2px solid #ccc;
        padding-bottom: 5px;
        margin-top: 0;
        font-weight: bold;
    }
    .rekap-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }
    .rekap-table th, .rekap-table td {
        border-bottom: 1px solid #eee;
        padding: 6px 4px;
        text-align: left;
    }
    .rekap-table th {
        font-weight: bold;
        text-transform: uppercase;
        background-color: #f4f4f4;
    }
    .rekap-row {
        display: flex;
    }
    .rekap-label {
        width: 180px;
        display: inline-block;
    }
    .rekap-colon {
        width: 20px;
        display: inline-block;
        text-align: center;
    }
    .rekap-val {
        width: 120px;
        text-align: left;
    }
    .rekap-val-pct {
        width: 60px;
        color: #d9534f;
        font-weight: bold;
    }
    .mt-10 { margin-top: 10px; }
    .row-flex { display: flex; flex-wrap: wrap; margin: -10px; }
    .col-flex { padding: 10px; flex: 1; min-width: 300px; }
</style>

<div class="rekap-bulanan-processing-form">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Filter Bulan & Tahun</h3>
        </div>
        <div class="box-body">
            <?= Html::beginForm(['rekap-bulanan-processing'], 'get', ['class' => 'form-inline']) ?>
                <div class="form-group">
                    <label>Bulan: </label>
                    <?= Html::dropDownList('bulan', $bulan, $bulanOptions, ['class' => 'form-control', 'onchange' => 'this.form.submit()']) ?>
                </div>
                <div class="form-group" style="margin-left:15px;">
                    <label>Tahun: </label>
                    <?= Html::dropDownList('tahun', $tahun, $tahunOptions, ['class' => 'form-control', 'onchange' => 'this.form.submit()']) ?>
                </div>
            <?= Html::endForm() ?>
        </div>
    </div>

    <div class="rekap-container">
        <div class="row-flex">
            <!-- LEFT COLUMN -->
            <div class="col-flex">
                <!-- HISAKA -->
                <div class="rekap-section">
                    <h4>HISAKA</h4>
                    <div class="rekap-row">
                        <span class="rekap-label">Total produksi</span><span class="rekap-colon">:</span>
                        <span class="rekap-val"><?= $fmt($val('hisaka_total_produksi')) ?></span>
                    </div>
                    <div class="rekap-row mt-10">
                        <span class="rekap-label">Jml masuk packing</span><span class="rekap-colon">:</span>
                        <span class="rekap-val"><?= $fmt($val('hisaka_jml_masuk_packing')) ?></span>
                    </div>

                    <table class="rekap-table mt-10">
                        <tr>
                            <th>Proses</th>
                            <th>QTT (mtr)</th>
                            <th>%</th>
                        </tr>
                        <tr>
                            <td>Celup</td>
                            <td><?= $fmt($val('hisaka_proses_celup')) ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Dasar celup</td>
                            <td><?= $fmt($val('hisaka_proses_dasar_celup')) ?></td>
                            <td></td>
                        </tr>
                        <tr style="background:#f8f9fa;">
                            <td>Topping</td>
                            <td><?= $fmt($val('hisaka_proses_topping')) ?></td>
                            <td class="rekap-val-pct"><?= $fmtPct($hisakaToppingPct) ?></td>
                        </tr>
                        <tr style="background:#f8f9fa;">
                            <td>Level</td>
                            <td><?= $fmt($val('hisaka_proses_level')) ?></td>
                            <td class="rekap-val-pct"><?= $fmtPct($hisakaLevelPct) ?></td>
                        </tr>
                        <tr style="background:#f8f9fa;">
                            <td>Stripping</td>
                            <td><?= $fmt($val('hisaka_proses_stripping')) ?></td>
                            <td class="rekap-val-pct"><?= $fmtPct($hisakaStrippingPct) ?></td>
                        </tr>
                        <tr>
                            <td>Relax</td>
                            <td><?= $fmt($val('hisaka_proses_relax')) ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>PFP</td>
                            <td><?= $fmt($val('hisaka_proses_pfp')) ?></td>
                            <td></td>
                        </tr>
                    </table>

                    <div class="rekap-row mt-10">
                        <span class="rekap-label">Hari kerja</span><span class="rekap-colon">:</span>
                        <span class="rekap-val"><?= $val('hisaka_hari_kerja') ?> HARI</span>
                    </div>
                    <div class="rekap-row mt-10">
                        <span class="rekap-label">Total batch</span><span class="rekap-colon">:</span>
                        <span class="rekap-val"><?= $fmt($val('hisaka_total_batch')) ?></span>
                    </div>

                    <h5 style="background:#e9ecef; padding:5px; margin-top:15px; font-weight:bold">RATA" PER HARI</h5>
                    <table class="rekap-table">
                        <tr>
                            <td>Batch</td>
                            <td><?= $fmt($val('hisaka_rata_per_hari_batch')) ?> X</td>
                        </tr>
                        <tr>
                            <td>Kartu</td>
                            <td><?= $fmt($val('hisaka_rata_per_hari_kartu')) ?> X</td>
                        </tr>
                        <tr>
                            <td>Jumbo</td>
                            <td><?= $fmt($val('hisaka_rata_per_hari_jumbo')) ?> X</td>
                        </tr>
                    </table>
                </div>

                <!-- FUKUSHIN & WASHING -->
                <div class="rekap-section">
                    <h4>FUKUSHIN & WASHING</h4>
                    <table class="rekap-table">
                        <tr>
                            <th>Proses</th>
                            <th>QTT</th>
                        </tr>
                        <tr>
                            <td>Continuous</td>
                            <td><?= $fmt($val('fukushin_proses_continuous')) ?></td>
                        </tr>
                    </table>
                    <table class="rekap-table">
                        <tr>
                            <th>Perbaikan</th>
                            <th>QTT</th>
                            <th>%</th>
                        </tr>
                        <tr>
                            <td>Cuci ulang</td>
                            <td><?= $fmt($val('fukushin_perbaikan_cuci_ulang')) ?></td>
                            <td class="rekap-val-pct"><?= $fmtPct($fukushinCuciPct) ?></td>
                        </tr>
                    </table>
                </div>

                <!-- STEAMER -->
                <div class="rekap-section">
                    <h4>STEAMER</h4>
                    <table class="rekap-table">
                        <tr>
                            <th>Proses</th>
                            <th>QTT</th>
                        </tr>
                        <tr>
                            <td>Redox</td>
                            <td><?= $fmt($val('steamer_proses_redox')) ?></td>
                        </tr>
                        <tr>
                            <td>Disperse</td>
                            <td><?= $fmt($val('steamer_proses_disperse')) ?></td>
                        </tr>
                    </table>
                </div>
                
                <!-- PADDING -->
                <div class="rekap-section">
                    <h4>PADDING</h4>
                    <table class="rekap-table">
                        <tr>
                            <th>Proses</th>
                            <th>QTT</th>
                        </tr>
                        <tr>
                            <td>Dyeing</td>
                            <td><?= $fmt($val('padding_proses_dyeing')) ?></td>
                        </tr>
                        <tr>
                            <td>Printing</td>
                            <td><?= $fmt($val('padding_proses_printing')) ?></td>
                        </tr>
                    </table>
                </div>

            </div>
            
            <!-- RIGHT COLUMN -->
            <div class="col-flex">
                <!-- STENTER -->
                <div class="rekap-section">
                    <h4>STENTER</h4>
                    
                    <table class="rekap-table">
                        <tr>
                            <th>Produksi</th>
                            <th>QTT</th>
                        </tr>
                        <tr>
                            <td>Stenter 3</td>
                            <td><?= $fmt($val('stenter_produksi_stenter3')) ?></td>
                        </tr>
                        <tr>
                            <td>Stenter 4</td>
                            <td><?= $fmt($val('stenter_produksi_stenter4')) ?></td>
                        </tr>
                        <tr>
                            <td>Stenter 5</td>
                            <td><?= $fmt($val('stenter_produksi_stenter5')) ?></td>
                        </tr>
                        <tr>
                            <td>Stenter 6</td>
                            <td><?= $fmt($val('stenter_produksi_stenter6')) ?></td>
                        </tr>
                    </table>

                    <table class="rekap-table">
                        <tr>
                            <th>Setting</th>
                            <th>QTT</th>
                            <th>%</th>
                        </tr>
                        <tr>
                            <td>Set dyeing</td>
                            <td><?= $fmt($val('stenter_setting_set_dyeing')) ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Set ulang dyeing</td>
                            <td><?= $fmt($val('stenter_setting_set_ulang_dyeing')) ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Set PFP</td>
                            <td><?= $fmt($val('stenter_setting_set_pfp')) ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Set ulang PFP</td>
                            <td><?= $fmt($val('stenter_setting_set_ulang_pfp')) ?></td>
                            <td class="rekap-val-pct"><?= $fmtPct($stenterSetUlangPfpPct) ?></td>
                        </tr>
                        <tr>
                            <td>Set non WR</td>
                            <td><?= $fmt($val('stenter_setting_set_non_wr')) ?></td>
                            <td></td>
                        </tr>
                    </table>

                    <table class="rekap-table">
                        <tr>
                            <th>Resin</th>
                            <th>QTT</th>
                            <th>%</th>
                        </tr>
                        <tr>
                            <td>R/F dyeing</td>
                            <td><?= $fmt($val('stenter_resin_rf_dyeing')) ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>R/F ulang dyeing</td>
                            <td><?= $fmt($val('stenter_resin_rf_ulang_dyeing')) ?></td>
                            <td class="rekap-val-pct"><?= $fmtPct($stenterRfUlangDyeingPct) ?></td>
                        </tr>
                        <tr style="background:#f8f9fa;">
                            <td style="padding-left:15px">R/F ulang murni</td>
                            <td><?= $fmt($val('stenter_resin_rf_ulang_murni')) ?></td>
                            <td></td>
                        </tr>
                        <tr style="background:#f8f9fa;">
                            <td style="padding-left:15px">Setelah perbaikan</td>
                            <td><?= $fmt($val('stenter_resin_setelah_perbaikan')) ?></td>
                            <td></td>
                        </tr>
                        <tr style="background:#f8f9fa;">
                            <td style="padding-left:15px">Jet black ulang</td>
                            <td><?= $fmt($val('stenter_resin_jet_black_ulang')) ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>R/F printing</td>
                            <td><?= $fmt($val('stenter_resin_rf_printing')) ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>R/F ulang printing</td>
                            <td><?= $fmt($val('stenter_resin_rf_ulang_printing')) ?></td>
                            <td class="rekap-val-pct"><?= $fmtPct($stenterRfUlangPrintingPct) ?></td>
                        </tr>
                    </table>

                </div>

                <!-- PACKING -->
                <div class="rekap-section">
                    <h4>PACKING</h4>
                    <div class="rekap-row">
                        <span class="rekap-label">Printing</span><span class="rekap-colon">:</span>
                        <span class="rekap-val"><?= $fmt($val('packing_printing')) ?></span>
                    </div>
                    <div class="rekap-row mt-10">
                        <span class="rekap-label">Dyeing</span><span class="rekap-colon">:</span>
                        <span class="rekap-val"><?= $fmt($val('packing_dyeing')) ?></span>
                    </div>
                    <div class="rekap-row mt-10">
                        <span class="rekap-label">Makloon</span><span class="rekap-colon">:</span>
                        <span class="rekap-val"><?= $fmt($val('packing_makloon')) ?></span>
                    </div>
                    <div class="rekap-row mt-10" style="border-top:1px solid #ccc; padding-top:10px;">
                        <span class="rekap-label" style="font-weight:bold">Total packing</span><span class="rekap-colon">:</span>
                        <span class="rekap-val" style="font-weight:bold"><?= $fmt($val('packing_total_packing')) ?></span>
                    </div>
                    <div class="rekap-row mt-10" style="background:#e9ecef; padding:5px;">
                        <span class="rekap-label" style="font-weight:bold; width:220px;">Hasil kirim barang ke gd. jadi</span><span class="rekap-colon">:</span>
                        <span class="rekap-val" style="font-weight:bold"><?= $fmt($val('packing_hasil_kirim')) ?></span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
