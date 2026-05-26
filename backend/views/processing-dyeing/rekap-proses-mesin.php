<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $modelMesins array */
/* @var $selectedModel string */
/* @var $machines array */
/* @var $mesinId int */
/* @var $mesin \common\models\ar\MstMesinProses|null */
/* @var $tanggal string */
/* @var $kartuData array */
/* @var $rangkumanProses array */
/* @var $hambatanItems array */

$this->title = 'Rekap Proses Mesin';
$this->params['breadcrumbs'][] = ['label' => 'Processing', 'url' => ['/processing-dyeing/index']];
$this->params['breadcrumbs'][] = $this->title;

$shiftLabels = [
    'C' => ['label' => 'PAGI', 'color' => '#28a745'],
    'D' => ['label' => 'SIANG', 'color' => '#dc3545'],
    'A' => ['label' => 'MALAM', 'color' => '#6f42c1'],
];

// Get selected shift setting from request (default empty = show all)
$shiftPagi = Yii::$app->request->get('shift_pagi', '');
$shiftSiang = Yii::$app->request->get('shift_siang', '');
$shiftMalam = Yii::$app->request->get('shift_malam', '');

$this->registerCss('
    .table-sticky-container {
        height: 60vh; /* Set clear height so scrollbar appears inside */
        max-height: 600px;
        overflow-y: auto;
        overflow-x: auto;
        position: relative;
    }
    .table-sticky-container thead th {
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #3c8dbc !important;
        color: #fff !important;
        box-shadow: 0 2px 2px -1px rgba(0,0,0,0.4);
    }
');
?>

<div class="rekap-proses-mesin">
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Filter Section -->
    <div class="box box-primary">
        <div class="box-body">
            <form method="get" action="<?= Url::to(['rekap-proses-mesin']) ?>" id="filter-form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Pilih Mesin:</label>
                            <select name="model_mesin" id="model-mesin-select" class="form-control" onchange="document.getElementById('mesin-id-input').value=''; this.form.submit()">
                                <option value="">-- Pilih Model Mesin --</option>
                                <?php foreach ($modelMesins as $mm): ?>
                                    <option value="<?= Html::encode($mm) ?>" <?= $selectedModel === $mm ? 'selected' : '' ?>><?= Html::encode($mm) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Pilih Tanggal:</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= Html::encode($tanggal) ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Tampilkan</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Shift Input Dropdowns -->
                        <div class="form-group">
                            <label>SHIFT:</label>
                            <table class="table table-bordered table-condensed" style="font-size: 12px; margin-bottom: 0;">
                                <tr>
                                    <td style="width:70px; font-weight: bold; background-color: #28a745; color: #fff;">PAGI</td>
                                    <td style="width:80px;">
                                        <select name="shift_pagi" class="form-control input-sm">
                                            <option value="">-</option>
                                            <option value="A" <?= $shiftPagi === 'A' ? 'selected' : '' ?>>A</option>
                                            <option value="B" <?= $shiftPagi === 'B' ? 'selected' : '' ?>>B</option>
                                            <option value="C" <?= $shiftPagi === 'C' ? 'selected' : '' ?>>C</option>
                                            <option value="D" <?= $shiftPagi === 'D' ? 'selected' : '' ?>>D</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:70px; font-weight: bold; background-color: #dc3545; color: #fff;">SIANG</td>
                                    <td style="width:80px;">
                                        <select name="shift_siang" class="form-control input-sm">
                                            <option value="">-</option>
                                            <option value="A" <?= $shiftSiang === 'A' ? 'selected' : '' ?>>A</option>
                                            <option value="B" <?= $shiftSiang === 'B' ? 'selected' : '' ?>>B</option>
                                            <option value="C" <?= $shiftSiang === 'C' ? 'selected' : '' ?>>C</option>
                                            <option value="D" <?= $shiftSiang === 'D' ? 'selected' : '' ?>>D</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:70px; font-weight: bold; background-color: #6f42c1; color: #fff;">MALAM</td>
                                    <td style="width:80px;">
                                        <select name="shift_malam" class="form-control input-sm">
                                            <option value="">-</option>
                                            <option value="A" <?= $shiftMalam === 'A' ? 'selected' : '' ?>>A</option>
                                            <option value="B" <?= $shiftMalam === 'B' ? 'selected' : '' ?>>B</option>
                                            <option value="C" <?= $shiftMalam === 'C' ? 'selected' : '' ?>>C</option>
                                            <option value="D" <?= $shiftMalam === 'D' ? 'selected' : '' ?>>D</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <?php if ($selectedModel && !empty($machines)): ?>
                <!-- Machine Number Selector -->
                <div class="row">
                    <div class="col-md-12">
                        <label>Mesin no.:</label>
                        <input type="hidden" name="mesin_id" id="mesin-id-input" value="<?= Html::encode($mesinId) ?>">
                        <div class="btn-group" role="group" style="flex-wrap: wrap;">
                            <?php foreach ($machines as $idx => $mc): ?>
                                <button type="button" 
                                    class="btn <?= ($mesinId == $mc->id) ? 'btn-primary' : 'btn-default' ?> btn-mesin-select"
                                    data-mesin-id="<?= $mc->id ?>"
                                    title="<?= Html::encode($mc->nama_mesin) ?>"
                                    onclick="document.getElementById('mesin-id-input').value='<?= $mc->id ?>'; this.form.submit();">
                                    <?= Html::encode($mc->nama_mesin) ?>
                                </button>
                            <?php endforeach; ?>
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="margin-left: 5px;">
                                Atur Kolom <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" id="column-toggle-menu">
                                <li><a href="#" data-col="col-shift"><input type="checkbox" checked> Shift</a></li>
                                <li><a href="#" data-col="col-wo"><input type="checkbox" checked> No. WO</a></li>
                                <li><a href="#" data-col="col-motif"><input type="checkbox" checked> Motif</a></li>
                                <li><a href="#" data-col="col-nk"><input type="checkbox" checked> NK</a></li>
                                <li><a href="#" data-col="col-pcs"><input type="checkbox" checked> PCS</a></li>
                                <li><a href="#" data-col="col-nomc"><input type="checkbox" checked> No MC</a></li>
                                <li><a href="#" data-col="col-warna"><input type="checkbox" checked> Warna</a></li>
                                <li><a href="#" data-col="col-proses"><input type="checkbox" checked> Proses</a></li>
                                <li><a href="#" data-col="col-temp"><input type="checkbox" checked> Temp°C</a></li>
                                <li><a href="#" data-col="col-panjang"><input type="checkbox" checked> Pjg Jadi</a></li>
                                <li><a href="#" data-col="col-panjang-greige"><input type="checkbox" checked> Pjg Greige</a></li>
                                <li><a href="#" data-col="col-lebar"><input type="checkbox" checked> Lebar</a></li>
                                <li><a href="#" data-col="col-berat"><input type="checkbox" checked> Berat</a></li>
                                <li><a href="#" data-col="col-keterangan"><input type="checkbox" checked> Keterangan</a></li>
                            </ul>
                        </div>
                        <?php if ($mesin): ?>
                            <span class="label label-info" style="margin-left: 10px; font-size: 14px;">
                                <i class="fa fa-cog"></i> <?= Html::encode($mesin->nama_mesin) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php 
    // Build shift mapping from user input
    $shiftMapping = []; // code => waktu label
    if ($shiftPagi) $shiftMapping[$shiftPagi] = ['label' => 'PAGI', 'color' => '#28a745'];
    if ($shiftSiang) $shiftMapping[$shiftSiang] = ['label' => 'SIANG', 'color' => '#dc3545'];
    if ($shiftMalam) $shiftMapping[$shiftMalam] = ['label' => 'MALAM', 'color' => '#6f42c1'];
    ?>

    <?php if ($mesin): ?>
    <!-- Main Data Table -->
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-table"></i> Data Proses - <?= Html::encode($mesin->nama_mesin) ?> (<?= date('d-m-Y', strtotime($tanggal)) ?>)</h3>
        </div>
        <div class="box-body table-sticky-container">
            <table class="table table-bordered table-striped table-condensed" id="tabel-rekap" style="font-size: 12px;">
                <thead style="background-color: #3c8dbc; color: #fff;">
                    <tr>
                        <th class="col-shift" style="min-width: 60px;">Shift</th>
                        <th class="col-wo" style="min-width: 120px;">No. WO</th>
                        <th class="col-motif" style="min-width: 100px;">Motif</th>
                        <th class="col-nk" style="min-width: 80px;">NK</th>
                        <th class="col-pcs" style="min-width: 50px;">PCS</th>
                        <th class="col-nomc" style="min-width: 70px;">No MC</th>
                        <th class="col-warna" style="min-width: 80px;">Warna</th>
                        <th class="col-proses" style="min-width: 80px;">Proses</th>
                        <th class="col-temp" style="min-width: 60px;">Temp°C</th>
                        <th class="col-panjang" style="min-width: 70px;">Pjg Jadi</th>
                        <th class="col-panjang-greige" style="min-width: 70px;">Pjg Greige</th>
                        <th class="col-lebar" style="min-width: 60px;">Lebar</th>
                        <th class="col-berat" style="min-width: 60px;">Berat</th>
                        <th class="col-keterangan" style="min-width: 100px;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $currentShift = null;
                    $totalMc = 0;
                    if (empty($kartuData)): ?>
                        <tr><td colspan="14" class="text-center text-muted">Tidak ada data untuk mesin dan tanggal ini.</td></tr>
                    <?php else:
                        foreach ($kartuData as $row):
                            // Use user-defined shift mapping if available, otherwise fallback
                            $sg = $row['shift_group'];
                            if (isset($shiftMapping[$sg])) {
                                $shiftInfo = $shiftMapping[$sg];
                            } elseif (isset($shiftLabels[$sg])) {
                                $shiftInfo = $shiftLabels[$sg];
                            } else {
                                $shiftInfo = ['label' => $sg, 'color' => '#999'];
                            }
                            
                            // Shift divider
                            if ($currentShift !== $row['shift_group']):
                                if ($currentShift !== null): ?>
                                    <tr><td colspan="14" style="height: 5px; background-color: #ddd; padding: 0;"></td></tr>
                                <?php endif;
                                $currentShift = $row['shift_group'];
                            endif;
                            $totalMc++;
                    ?>
                        <tr>
                            <td class="col-shift" style="font-weight:bold; color: <?= $shiftInfo['color'] ?>;">
                                <?= $shiftInfo['label'] ?> (<?= Html::encode($row['shift_group']) ?>)
                            </td>
                            <td class="col-wo"><?= Html::encode($row['wo_no']) ?></td>
                            <td class="col-motif"><?= Html::encode($row['motif']) ?></td>
                            <td class="col-nk">
                                <a href="<?= Url::to(['/processing-dyeing/view', 'id' => $row['kartu_proses_id']]) ?>" target="_blank">
                                    <?= Html::encode($row['nk']) ?>
                                </a>
                            </td>
                            <td class="col-pcs"><?= Html::encode($row['pcs']) ?></td>
                            <td class="col-nomc"><?= Html::encode($row['no_mc']) ?></td>
                            <td class="col-warna"><?= Html::encode($row['warna']) ?></td>
                            <td class="col-proses"><?= Html::encode($row['proses']) ?></td>
                            <td class="col-temp"><?= Html::encode($row['temp']) ?></td>
                            <td class="col-panjang"><?= Html::encode($row['panjang_jadi']) ?></td>
                            <td class="col-panjang-greige"><?= Html::encode($row['panjang_greige']) ?></td>
                            <td class="col-lebar"><?= Html::encode($row['lebar']) ?></td>
                            <td class="col-berat"><?= Html::encode($row['berat']) ?></td>
                            <td class="col-keterangan"><?= Html::encode($row['keterangan']) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Rangkuman Proses -->
    <?php if (!empty($rangkumanProses)): ?>
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-bar-chart"></i> Rangkuman Proses</h3>
        </div>
        <div class="box-body">
            <?php 
            $totalAll = 0;
            $totalCount = 0;
            foreach ($rangkumanProses as $prosesName => $data): 
                $totalAll += $data['total_panjang'];
                $totalCount += $data['count'];
            ?>
                <span class="label label-primary" style="font-size: 13px; margin-right: 10px; padding: 5px 10px;">
                    <?= Html::encode($prosesName) ?> <?= number_format($data['total_panjang'], 0) ?> (<?= $data['count'] ?>x)
                </span>
            <?php endforeach; ?>
            <hr style="margin: 10px 0;">
            <strong>TOTAL MC <?= $mesin ? Html::encode($mesin->nama_mesin) : '' ?>: <?= number_format($totalAll, 0) ?> (<?= $totalCount ?>x)</strong>
        </div>
    </div>
    <?php endif; ?>

    <!-- Hambatan Section -->
    <div class="box box-danger">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-exclamation-triangle"></i> Hambatan</h3>
        </div>
        <div class="box-body">
            <?php if (empty($hambatanItems)): ?>
                <p class="text-muted text-center">Tidak ada data hambatan untuk mesin dan tanggal ini.</p>
            <?php else: ?>
                <table class="table table-bordered table-condensed" style="font-size: 12px;">
                    <thead style="background-color: #f5f5f5;">
                        <tr>
                            <th style="width: 80px;">Mulai</th>
                            <th style="width: 80px;">Selesai</th>
                            <th style="width: 80px;">Shift</th>
                            <th>Jenis Hambatan</th>
                            <th style="width: 150px;">WO</th>
                            <th style="width: 100px;">NK</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hambatanItems as $item): ?>
                        <tr>
                            <td><?= Html::encode($item->start_time) ?></td>
                            <td><?= Html::encode($item->stop_time) ?></td>
                            <td><?= Html::encode($item->shift ?? '-') ?></td>
                            <td>
                                <?php 
                                $jenisNames = [];
                                foreach ($item->mstJenisHambatans as $jh) {
                                    $jenisNames[] = $jh->nama;
                                }
                                echo Html::encode(implode(', ', $jenisNames));
                                ?>
                            </td>
                            <td><?= Html::encode($item->no_wo ?? '-') ?></td>
                            <td><?= Html::encode($item->no_kartu ?? '-') ?></td>
                            <td><?= Html::encode($item->keterangan ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <?php endif; ?>
</div>

<?php
$js = <<<JS
// Column toggle functionality
$('#column-toggle-menu a').on('click', function(e) {
    e.stopPropagation();
    var colClass = $(this).data('col');
    var checkbox = $(this).find('input[type=checkbox]');
    checkbox.prop('checked', !checkbox.prop('checked'));
    
    if (checkbox.prop('checked')) {
        $('.' + colClass).show();
    } else {
        $('.' + colClass).hide();
    }
    return false;
});
JS;
$this->registerJs($js);
?>
