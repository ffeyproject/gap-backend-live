<?php

use yii\helpers\Html;
use yii\helpers\Url;

\kartik\select2\Select2Asset::register($this);

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
        z-index: 10;
        background-color: #3c8dbc !important;
        color: #fff !important;
        box-shadow: 0 1px 1px -1px rgba(0,0,0,0.4);
    }
    .table-sticky-container thead tr:nth-child(1) th {
        top: 0;
    }
    /* The second row top will be set via JS to match the exact height of the first row */
    .table-sticky-container thead tr:nth-child(2) th {
        z-index: 9; /* Slightly lower z-index than first row so first row stays on top if scrolled */
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
                                <li><a href="#" data-col="col-cek"><input type="checkbox" checked> Cek</a></li>
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
                        <th class="col-cek" style="width: 30px; text-align: center;"><i class="fa fa-check-square-o"></i></th>
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
                    <tr class="search-row" style="background-color: #f9f9f9;">
                        <th class="col-cek"></th>
                        <th class="col-shift"></th>
                        <th class="col-wo"><input type="text" class="form-control input-sm column-search" data-col="col-wo" placeholder="Cari..."></th>
                        <th class="col-motif"><input type="text" class="form-control input-sm column-search" data-col="col-motif" placeholder="Cari..."></th>
                        <th class="col-nk"><input type="text" class="form-control input-sm column-search" data-col="col-nk" placeholder="Cari..."></th>
                        <th class="col-pcs"></th>
                        <th class="col-nomc"></th>
                        <th class="col-warna"><input type="text" class="form-control input-sm column-search" data-col="col-warna" placeholder="Cari..."></th>
                        <th class="col-proses"><input type="text" class="form-control input-sm column-search" data-col="col-proses" placeholder="Cari..."></th>
                        <th class="col-temp"></th>
                        <th class="col-panjang"></th>
                        <th class="col-panjang-greige"></th>
                        <th class="col-lebar"></th>
                        <th class="col-berat"></th>
                        <th class="col-keterangan"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $currentShift = null;
                    $totalMc = 0;
                    if (empty($kartuData)): ?>
                        <tr><td colspan="15" class="text-center text-muted">Tidak ada data untuk mesin dan tanggal ini.</td></tr>
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
                                    <tr><td colspan="15" style="height: 5px; background-color: #ddd; padding: 0;"></td></tr>
                                <?php endif;
                                $currentShift = $row['shift_group'];
                            endif;
                            $totalMc++;
                    ?>
                        <?php 
                            $isStenterRow = false;
                            if (!empty($selectedModel) && stripos($selectedModel, 'stenter') !== false) {
                                $isStenterRow = true;
                            } elseif (!empty($mesin) && stripos($mesin->model_mesin, 'stenter') !== false) {
                                $isStenterRow = true;
                            }
                            $panjangValRow = $isStenterRow ? floatval($row['panjang_jadi']) : floatval($row['panjang_greige']);
                            $chkId = 'chk_' . $row['kartu_proses_id'] . '_' . md5($row['proses'] . $tanggal); 
                            $namaProsesRow = trim($row['proses'] ?? 'Unknown');
                            if ($namaProsesRow === '') $namaProsesRow = 'Unknown';
                        ?>
                        <tr class="data-row" data-shift="<?= Html::encode($row['shift_group']) ?>" data-panjang="<?= $panjangValRow ?>" data-proses="<?= Html::encode($namaProsesRow) ?>">
                            <td class="col-cek" style="text-align: center; vertical-align: middle;">
                                <input type="checkbox" class="admin-penanda" data-id="<?= $chkId ?>" data-panjang="<?= $panjangValRow ?>">
                            </td>
                            <td class="col-shift" style="font-weight:bold; color: <?= $shiftInfo['color'] ?>;" data-label="<?= Html::encode($shiftInfo['label']) ?>" data-color="<?= Html::encode($shiftInfo['color']) ?>">
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
                            <td class="col-keterangan">
                                <?= Html::encode($row['keterangan']) ?>
                                <button type="button" class="btn btn-xs btn-default pull-right btn-edit-row" 
                                    data-tipe="<?= Html::encode($row['tipe'] ?? 'Order') ?>"
                                    data-shift="<?= Html::encode($row['shift_group']) ?>"
                                    data-wo="<?= Html::encode($row['wo_no']) ?>"
                                    data-nk="<?= Html::encode($row['nk']) ?>"
                                    data-proses="<?= Html::encode($row['proses']) ?>"
                                    data-input-id="<?= Html::encode($row['input_id'] ?? '') ?>"
                                    title="Edit via Tambahan Input"><i class="fa fa-edit"></i> Edit</button>
                                
                                <button type="button" class="btn btn-xs btn-danger pull-right btn-hapus-rekap" style="margin-right: 5px;"
                                    data-tipe="<?= Html::encode($row['tipe'] ?? 'Order') ?>"
                                    data-nk="<?= Html::encode($row['nk']) ?>"
                                    data-proses="<?= Html::encode($row['proses']) ?>"
                                    data-wo="<?= Html::encode($row['wo_no']) ?>"
                                    data-input-id="<?= Html::encode($row['input_id'] ?? '') ?>"
                                    title="Hapus Data (Tanggal, Mesin, Temp, Panjang Jadi)"><i class="fa fa-trash"></i> Hapus</button>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
        <div class="box-footer" style="background-color: #f9f9f9; border-top: 1px solid #ddd;">
            <div id="total-hasil-select-container" style="margin-bottom: 10px; font-size: 1.1em; padding: 5px; background: #e9ecef; border-radius: 4px; display: none;">
                <strong>TOTAL HASIL SELECT: </strong> <span id="total-hasil-select-value">0</span> (<span id="total-hasil-select-count">0</span>x)
            </div>
            <div id="dynamic-shift-totals-container"></div>
        </div>
    </div>

    <!-- Rangkuman Proses -->
    <?php if (!empty($rangkumanProses['Order']) || !empty($rangkumanProses['Percobaan'])): ?>
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-bar-chart"></i> Rangkuman Proses</h3>
        </div>
        <div class="box-body">
            <?php 
            $totalAll = 0;
            $totalCount = 0;
            ?>

            <?php if (!empty($rangkumanProses['Order'])): ?>
                <h5 style="font-weight: bold; margin-top: 0;">Order</h5>
                <div style="margin-bottom: 10px;">
                    <?php foreach ($rangkumanProses['Order'] as $prosesName => $data): 
                        $totalAll += $data['total_panjang'];
                        $totalCount += $data['count'];
                    ?>
                        <span class="label label-primary" style="font-size: 13px; margin-right: 10px; padding: 5px 10px; display: inline-block; margin-bottom: 5px;">
                            <?= Html::encode($prosesName) ?> <?= number_format($data['total_panjang'], 0) ?> (<?= $data['count'] ?>x)
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($rangkumanProses['Percobaan'])): ?>
                <h5 style="font-weight: bold; margin-top: 10px;">Percobaan</h5>
                <div style="margin-bottom: 10px;">
                    <?php foreach ($rangkumanProses['Percobaan'] as $prosesName => $data): 
                        $totalAll += $data['total_panjang'];
                        $totalCount += $data['count'];
                    ?>
                        <span class="label label-info" style="font-size: 13px; margin-right: 10px; padding: 5px 10px; display: inline-block; margin-bottom: 5px;">
                            <?= Html::encode($prosesName) ?> <?= number_format($data['total_panjang'], 0) ?> (<?= $data['count'] ?>x)
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

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

    <!-- Tambahan Input Section -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">TAMBAHAN INPUT</h3>
        </div>
        <div class="box-body">
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-4"><strong>Jenis Mesin:</strong> <?= Html::encode($selectedModel) ?></div>
                <div class="col-md-4"><strong>Nomor Mesin:</strong> <?= Html::encode($mesin->nama_mesin) ?></div>
                <div class="col-md-4"><strong>Tanggal:</strong> <?= Html::encode($tanggal) ?></div>
            </div>

            <form id="tambahan-input-form" action="<?= Url::to(['tambah-input-proses']) ?>" method="post">
    <!-- Required CSRF Token -->
    <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                <input type="hidden" name="mesin_id" value="<?= $mesin->id ?>">
                <input type="hidden" name="model_mesin" value="<?= Html::encode($selectedModel) ?>">
                <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
                <input type="hidden" name="shift_pagi" value="<?= Html::encode(Yii::$app->request->get('shift_pagi')) ?>">
                <input type="hidden" name="shift_siang" value="<?= Html::encode(Yii::$app->request->get('shift_siang')) ?>">
                <input type="hidden" name="shift_malam" value="<?= Html::encode(Yii::$app->request->get('shift_malam')) ?>">
                
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed" id="table-tambahan-input">
                        <thead>
                            <tr style="background-color: #f5f5f5;">
                                <th style="width: 120px;">PERC. / ORDER</th>
                                <th style="width: 80px;">SHIFT</th>
                                <th>WO (jika ada)</th>
                                <th>NK</th>
                                <th>Proses</th>
                                <th style="width: 80px;">Temp</th>
                                <th style="width: 150px;">Panjang Greige</th>
                                <th style="width: 150px;">Panjang Jadi</th>
                                <th>KETERANGAN</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Rows will be added here via JS -->
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-success btn-sm" id="btn-tambah-input"><i class="fa fa-plus"></i> Tambah Set Inputan</button>
                <hr>
                <button type="submit" class="btn btn-primary" id="btn-simpan-input">Simpan Data Input</button>
            </form>
        </div>
    </div>
    
    <?php endif; ?>
</div>

<?php
$prosesListNames = [];
if ($mesin) {
    // 1. Get valid Dyeing Process IDs
    $validProcessIds = \common\models\ar\MstMesinProses::find()
        ->alias('mmp')
        ->select('mpdm.mst_process_dyeing_id')
        ->innerJoin('mst_process_dyeing_mesin mpdm', 'mmp.id = mpdm.mst_mesin_proses_id')
        ->where(['mmp.model_mesin' => $mesin->model_mesin])
        ->column();
    
    if (!empty($validProcessIds)) {
        $dyeingProses = \common\models\ar\MstProcessDyeing::find()
            ->select('nama_proses')
            ->where(['id' => $validProcessIds])
            ->column();
        $prosesListNames = array_merge($prosesListNames, $dyeingProses);
    }

    // 2. Get valid PFP Process IDs
    $validProcessIdsPfp = \common\models\ar\MstMesinProses::find()
        ->alias('mmp')
        ->select('mpdm.mst_process_pfp_id')
        ->innerJoin('mst_process_pfp_mesin mpdm', 'mmp.id = mpdm.mst_mesin_proses_id')
        ->where(['mmp.model_mesin' => $mesin->model_mesin])
        ->column();
        
    if (!empty($validProcessIdsPfp)) {
        $pfpProses = \common\models\ar\MstProcessPfp::find()
            ->select('nama_proses')
            ->where(['id' => $validProcessIdsPfp])
            ->column();
        $prosesListNames = array_merge($prosesListNames, $pfpProses);
    }
    
    // 3. Get valid Printing Process IDs (if any)
    try {
        $validProcessIdsPrinting = \common\models\ar\MstMesinProses::find()
            ->alias('mmp')
            ->select('mpdm.mst_process_printing_id')
            ->innerJoin('mst_process_printing_mesin mpdm', 'mmp.id = mpdm.mst_mesin_proses_id')
            ->where(['mmp.model_mesin' => $mesin->model_mesin])
            ->column();
            
        if (!empty($validProcessIdsPrinting)) {
            $printingProses = \common\models\ar\MstProcessPrinting::find()
                ->select('nama_proses')
                ->where(['id' => $validProcessIdsPrinting])
                ->column();
            $prosesListNames = array_merge($prosesListNames, $printingProses);
        }
    } catch (\Exception $e) {
        // Table might not exist yet, safe to ignore
    }
    
    $prosesListNames = array_unique($prosesListNames);
    sort($prosesListNames);
} else {
    // Fallback if no machine selected: Load all dyeing processes
    $prosesListNames = \common\models\ar\MstProcessDyeing::find()->select('nama_proses')->orderBy('nama_proses')->column();
}

$prosesOptions = '<option value="">- Pilih Proses -</option>';
foreach ($prosesListNames as $namaProses) {
    $prosesOptions .= '<option value="' . Html::encode($namaProses) . '">' . Html::encode($namaProses) . '</option>';
}
$prosesOptionsJson = json_encode($prosesOptions);
$urlLookupWo = \yii\helpers\Url::to(['/ajax/lookup-wo-all']);
$urlLookupNk = \yii\helpers\Url::to(['/ajax/lookup-nk-all']);
$urlGetExistingProsesData = \yii\helpers\Url::to(['/ajax/get-existing-proses-data']);
$urlHapusRekapData = \yii\helpers\Url::to(['/processing-dyeing/hapus-rekap-data']);
$mesinIdStr = $mesin ? $mesin->id : '';

$js = <<<JS
// Admin checkbox persistence and highlighting
function updateTotalSelect() {
    var totalPanjang = 0;
    var count = 0;
    $('.admin-penanda:checked').each(function() {
        var p = parseFloat($(this).data('panjang')) || 0;
        totalPanjang += p;
        count++;
    });
    
    if (count > 0) {
        $('#total-hasil-select-value').text(totalPanjang.toLocaleString('en-US'));
        $('#total-hasil-select-count').text(count);
        $('#total-hasil-select-container').show();
    } else {
        $('#total-hasil-select-container').hide();
    }
}

$('.admin-penanda').each(function() {
    var id = $(this).data('id');
    if (localStorage.getItem(id) === 'true') {
        $(this).prop('checked', true);
        $(this).closest('tr').css('background-color', '#d4edda');
    }
});

updateTotalSelect();

$('.admin-penanda').on('change', function() {
    var id = $(this).data('id');
    var isChecked = $(this).is(':checked');
    
    if (isChecked) {
        localStorage.setItem(id, 'true');
        $(this).closest('tr').css('background-color', '#d4edda');
    } else {
        localStorage.removeItem(id);
        $(this).closest('tr').css('background-color', '');
    }
    
    updateTotalSelect();
});

// Text selection to check functionality
$(document).on('mouseup', function() {
    var sel = window.getSelection();
    if (!sel || sel.isCollapsed) return; // No text selected
    
    // Check which rows are partially or fully in the selection
    $('.data-row').each(function() {
        var tr = this;
        // containsNode checks if the node is part of the selection
        if (sel.containsNode && sel.containsNode(tr, true)) {
            var cb = $(this).find('.admin-penanda');
            if (!cb.prop('checked')) {
                cb.prop('checked', true).trigger('change');
            }
        }
    });
});

// Tambahan Input Dynamic Rows
var rowCount = 0;
var prosesOptions = {$prosesOptionsJson};
$('#btn-tambah-input').click(function() {
    rowCount++;
    var html = '<tr id="row-input-' + rowCount + '">';
    html += '<td><select name="InputProses[' + rowCount + '][tipe]" class="form-control input-sm input-tipe"><option value="Order">Order</option><option value="Percobaan">Percobaan</option></select></td>';
    html += '<td><select name="InputProses[' + rowCount + '][shift]" class="form-control input-sm"><option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option></select></td>';
    
    // WO Select2
    html += '<td><select name="InputProses[' + rowCount + '][wo]" class="form-control input-sm input-wo"></select></td>';
    
    // NK Select2
    html += '<td><select name="InputProses[' + rowCount + '][nk]" class="form-control input-sm input-nk"></select></td>';
    
    // Proses Select2
    html += '<td><select name="InputProses[' + rowCount + '][proses]" class="form-control input-sm input-proses">' + prosesOptions + '</select></td>';
    
    // Temp
    html += '<td><input type="text" name="InputProses[' + rowCount + '][temp]" class="form-control input-sm input-temp"></td>';
    
    // Panjang Greige
    html += '<td><div class="input-group"><input type="text" name="InputProses[' + rowCount + '][panjang_greige]" class="form-control input-sm" placeholder="Ketik..."></div></td>';
    
    // Panjang Jadi
    html += '<td><div class="input-group"><input type="text" name="InputProses[' + rowCount + '][panjang_jadi]" class="form-control input-sm input-panjang" placeholder="Ketik..."><span class="input-group-btn"><button type="button" class="btn btn-success btn-sm">Set</button></span></div></td>';
    
    // Keterangan
    html += '<td><input type="text" name="InputProses[' + rowCount + '][keterangan]" class="form-control input-sm input-keterangan"></td>';
    
    // Delete
    html += '<td><button type="button" class="btn btn-danger btn-sm btn-hapus-baris"><i class="fa fa-trash"></i></button></td>';
    
    html += '</tr>';
    $('#table-tambahan-input tbody').append(html);
    
    let currentRowId = 'row-input-' + rowCount;
    
    // Initialize Select2
    $('#' + currentRowId + ' .input-wo').select2({
        ajax: {
            url: '{$urlLookupWo}',
            dataType: 'json',
            delay: 250,
            data: function (params) { return { q: params.term }; },
            processResults: function (data) {
                return {
                    results: $.map(data.results, function (item) {
                        return { text: item.text, id: item.text }
                    })
                };
            }
        },
        placeholder: 'Search WO...',
        minimumInputLength: 3,
        allowClear: true,
        width: '100%'
    });
    
    $('#' + currentRowId + ' .input-nk').select2({
        ajax: {
            url: '{$urlLookupNk}',
            dataType: 'json',
            delay: 250,
            data: function (params) { 
                return { 
                    q: params.term,
                    wo_no: $('#' + currentRowId + ' .input-wo').val()
                }; 
            },
            processResults: function (data) {
                return {
                    results: $.map(data.results, function (item) {
                        return { text: item.text, id: item.text }
                    })
                };
            }
        },
        placeholder: 'Search NK...',
        allowClear: true,
        width: '100%'
    });
    
    $('#row-input-' + rowCount + ' .input-proses').select2({
        placeholder: 'Pilih Proses...',
        allowClear: true,
        width: '100%'
    });
    
    // Auto-fetch data when NK or Proses changes
    function fetchExistingData(rowId) {
        var row = $('#' + rowId);
        var nk = row.find('.input-nk').val();
        var proses = row.find('.input-proses').val();
        var tipe = row.find('.input-tipe').val();
        
        if (proses && (nk || tipe === 'Percobaan')) {
            $.ajax({
                url: '{$urlGetExistingProsesData}',
                data: {
                    nk: nk,
                    prosesName: proses,
                    mesinId: '{$mesinIdStr}',
                    tanggal: '{$tanggal}',
                    wo_no: row.find('.input-wo').val(),
                    tipe: tipe
                },
                success: function(res) {
                    if (res && res.found) {
                        if (res.data.temp) row.find('.input-temp').val(res.data.temp);
                        if (res.data.panjang_greige) row.find('input[name*="[panjang_greige]"]').val(res.data.panjang_greige);
                        if (res.data.panjang_jadi) row.find('.input-panjang').val(res.data.panjang_jadi);
                        
                        // Flash row color to indicate fetch success
                        row.css('background-color', '#d4edda');
                        setTimeout(function(){ row.css('background-color', ''); }, 1500);
                    }
                }
            });
        }
    }

    $('#' + currentRowId + ' .input-nk, #' + currentRowId + ' .input-proses').on('change', function() {
        fetchExistingData(currentRowId);
    });
});

// Auto add first row on load
$('#btn-tambah-input').click();

// Remove row
$(document).on('click', '.btn-hapus-baris', function(e) {
    e.preventDefault();
    if (!confirm('Yakin ingin menghapus baris input ini?')) {
        return;
    }
    if (!confirm('Anda benar-benar yakin? Data yang belum disimpan akan hilang!')) {
        return;
    }
    $(this).closest('tr').remove();
});

// Validation on submit
$('#tambahan-input-form').submit(function(e) {
    var isValid = true;
    var hasRows = false;
    $('#table-tambahan-input tbody tr').each(function() {
        hasRows = true;
        var tipe = $(this).find('.input-tipe').val();
        var wo = $(this).find('.input-wo').val() || '';
        var nk = $(this).find('.input-nk').val() || '';
        var proses = $(this).find('.input-proses').val() || '';
        var panjang = $(this).find('.input-panjang').val() || '';
        var panjangGreige = $(this).find('input[name*="[panjang_greige]"]').val() || '';
        
        wo = wo.trim();
        nk = nk.trim();
        proses = proses.trim();
        panjang = panjang.trim();
        panjangGreige = panjangGreige.trim();
        
        if (tipe === 'Order') {
            if (!wo || !nk || !proses) {
                isValid = false;
                alert('Untuk Tipe Order, field WO, NK, dan Proses wajib diisi!');
                $(this).css('background-color', '#f8d7da');
                return false; // break loop
            }
        } else if (tipe === 'Percobaan') {
            if (!proses || (!panjang && !panjangGreige)) {
                isValid = false;
                alert('Untuk Tipe Percobaan, field Proses dan Panjang (Jadi atau Greige) wajib diisi!');
                $(this).css('background-color', '#f8d7da');
                return false; // break loop
            }
        }
        $(this).css('background-color', '');
    });
    
    if (!hasRows) {
        alert('Silakan tambah minimal 1 baris input!');
        e.preventDefault();
        return;
    }
    
    if (!isValid) {
        e.preventDefault();
    } else {
        var confirmed = confirm("Sudah ada data proses ini di kartu, apakah tetap mau menimpa data?");
        if (!confirmed) {
            e.preventDefault();
        }
    }
});

// Column search functionality
$('.column-search').on('keyup', function() {
    var searches = {};
    $('.column-search').each(function() {
        var val = $(this).val().toLowerCase();
        if (val) {
            searches[$(this).data('col')] = val;
        }
    });

    $('#tabel-rekap tbody tr:not(.search-row)').each(function() {
        // Skip shift dividers or empty rows
        if ($(this).find('td').length <= 1) return;
        
        var showRow = true;
        for (var colClass in searches) {
            var cellText = $(this).find('.' + colClass).text().toLowerCase();
            if (cellText.indexOf(searches[colClass]) === -1) {
                showRow = false;
                break;
            }
        }
        $(this).toggle(showRow);
    });
    
    updateDynamicShiftTotals();
});

function updateDynamicShiftTotals() {
    var totals = {};
    var order = [];
    $('#tabel-rekap tbody tr.data-row:visible').each(function() {
        var shift = $(this).data('shift');
        var panjang = parseFloat($(this).data('panjang')) || 0;
        var proses = $(this).data('proses');
        
        var colShift = $(this).find('.col-shift');
        var color = colShift.data('color') || '#333';
        var label = colShift.data('label') || shift;
        
        if (!totals[shift]) {
            totals[shift] = {
                length: 0,
                count: 0,
                color: color,
                label: label,
                breakdown: {}
            };
            order.push(shift);
        }
        
        totals[shift].length += panjang;
        totals[shift].count += 1;
        
        if (!totals[shift].breakdown[proses]) {
            totals[shift].breakdown[proses] = { length: 0, count: 0 };
        }
        totals[shift].breakdown[proses].length += panjang;
        totals[shift].breakdown[proses].count += 1;
    });
    
    var html = '';
    for (var i = 0; i < order.length; i++) {
        var shift = order[i];
        var data = totals[shift];
        var bdStrs = [];
        for (var p in data.breakdown) {
            bdStrs.push(p + ': ' + data.breakdown[p].length.toLocaleString('en-US') + ' (' + data.breakdown[p].count + 'x)');
        }
        
        html += '<div style="margin-bottom: 5px;">' +
                '<strong style="margin-right: 10px; color: ' + data.color + ';">' +
                    'TOTAL SHIFT ' + data.label + ' (' + shift + '): ' + data.length.toLocaleString('en-US') + ' (' + data.count + 'x)' +
                '</strong>' +
                '<span style="font-size: 0.9em; font-weight: normal; color: #555;">' +
                    '[ ' + bdStrs.join('; ') + ' ]' +
                '</span>' +
            '</div>';
    }
    
    $('#dynamic-shift-totals-container').html(html);
}

// Initial calculation
updateDynamicShiftTotals();

// Edit Button functionality
$(document).on('click', '.btn-edit-row', function() {
    var tipe = $(this).data('tipe');
    var shift = $(this).data('shift');
    var wo = $(this).data('wo');
    var nk = $(this).data('nk');
    var proses = $(this).data('proses');
    var inputId = $(this).data('input-id') || '';
    
    // Click add input to generate a new row
    $('#btn-tambah-input').click();
    
    var lastRowId = 'row-input-' + rowCount;
    var row = $('#' + lastRowId);
    
    // Append hidden input_id if it exists (for editing Percobaan)
    if (inputId !== '') {
        row.append('<input type="hidden" name="InputProses[' + rowCount + '][input_id]" value="' + inputId + '">');
    }
    
    // Populate fields
    if (tipe === 'Percobaan' || tipe === 'Order') {
        row.find('.input-tipe').val(tipe);
    }
    
    if (['A','B','C','D'].includes(shift)) {
        row.find('select[name*="[shift]"]').val(shift);
    }
    
    if (wo && wo !== '-' && wo !== '') {
        var woOption = new Option(wo, wo, true, true);
        row.find('.input-wo').append(woOption);
    }
    
    if (nk && nk !== '-' && nk !== '') {
        var nkOption = new Option(nk, nk, true, true);
        row.find('.input-nk').append(nkOption);
    }
    
    if (proses && proses !== '') {
        row.find('.input-proses').val(proses);
    }
    
    // Trigger change only at the end to fire fetchExistingData once
    row.find('.input-proses').trigger('change');
    
    // Scroll to the new row
    $('html, body').animate({
        scrollTop: row.offset().top - 150
    }, 500);
    
    // Highlight the row temporarily
    row.css('background-color', '#fff3cd');
    setTimeout(function() {
        row.css('background-color', '');
    }, 2000);
});

// Hapus Button functionality
$(document).on('click', '.btn-hapus-rekap', function(e) {
    e.preventDefault();
    if (!confirm('Yakin ingin menghapus data (tanggal, mesin, temp, dll) dari rekap ini?')) {
        return;
    }
    if (!confirm('Anda benar-benar yakin? Data akan hilang dari rekap mesin ini!')) {
        return;
    }
    
    var btn = $(this);
    var tipe = btn.data('tipe');
    var nk = btn.data('nk');
    var proses = btn.data('proses');
    var wo = btn.data('wo');
    var inputId = btn.data('input-id');
    
    var originalText = btn.html();
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
    
    $.ajax({
        url: '{$urlHapusRekapData}',
        type: 'POST',
        data: {
            tipe: tipe,
            nk: nk,
            proses: proses,
            wo: wo,
            input_id: inputId,
            YII_CSRF_TOKEN: yii.getCsrfToken()
        },
        success: function(res) {
            if (res.success) {
                btn.closest('tr').fadeOut(function() { 
                    $(this).remove(); 
                    updateDynamicShiftTotals(); 
                });
            } else {
                alert('Gagal menghapus: ' + (res.message || 'Unknown error'));
                btn.prop('disabled', false).html(originalText);
            }
        },
        error: function() {
            alert('Terjadi kesalahan koneksi saat menghapus data.');
            btn.prop('disabled', false).html(originalText);
        }
    });
});

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
    
    // Readjust sticky headers after column toggle as height might change
    setTimeout(adjustStickyHeaders, 50);
    return false;
});

// Fix sticky header overlap by calculating exact height
function adjustStickyHeaders() {
    var firstRowHeight = $('.table-sticky-container thead tr:first-child').outerHeight();
    if (firstRowHeight > 0) {
        $('.table-sticky-container thead tr:nth-child(2) th').css('top', firstRowHeight + 'px');
    }
}
$(window).on('resize', adjustStickyHeaders);
setTimeout(adjustStickyHeaders, 100);
setTimeout(adjustStickyHeaders, 500); // Failsafe for slow rendering

JS;
$this->registerJs($js);
?>
