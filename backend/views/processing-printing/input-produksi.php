<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

\kartik\select2\Select2Asset::register($this);

/* @var $this yii\web\View */
/* @var $jenisMesins array */
/* @var $machines array */
/* @var $mesin \common\models\ar\MstMesinProses|null */
/* @var $jenis_mesin string */
/* @var $mesin_id int */
/* @var $tanggal string */
/* @var $shift string */
/* @var $pembagian_hari string */
/* @var $existingData array */
/* @var $hambatanList array */

$this->title = 'Inputan Produksi Printing';
$this->params['breadcrumbs'][] = ['label' => 'Processing', 'url' => ['/processing-printing/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="input-produksi-printing">
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Filter Section -->
    <div class="box box-primary">
        <div class="box-body">
            <form method="get" action="<?= Url::to(['input-produksi']) ?>" id="filter-form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Pilih Jenis Mesin:</label>
                            <select name="jenis_mesin" id="jenis-mesin-select" class="form-control" style="width: 100%;">
                                <option value="">-- Pilih Jenis Mesin --</option>
                                <?php foreach ($jenisMesins as $key => $val): ?>
                                    <option value="<?= Html::encode($key) ?>" <?= $jenis_mesin === $key ? 'selected' : '' ?>><?= Html::encode($val) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Pilih No Mesin:</label>
                            <input type="hidden" name="mesin_id" id="mesin-id-input" value="<?= Html::encode($mesin_id) ?>">
                            <select id="mesin-select" class="form-control" style="width: 100%;">
                                <option value="">-- Pilih No Mesin --</option>
                                <?php foreach ($machines as $mc): ?>
                                    <option value="<?= $mc->id ?>" <?= $mesin_id == $mc->id ? 'selected' : '' ?>><?= Html::encode($mc->nama_mesin) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Shift:</label>
                            <select name="shift" class="form-control">
                                <option value="A" <?= $shift === 'A' ? 'selected' : '' ?>>A</option>
                                <option value="B" <?= $shift === 'B' ? 'selected' : '' ?>>B</option>
                                <option value="C" <?= $shift === 'C' ? 'selected' : '' ?>>C</option>
                                <option value="D" <?= $shift === 'D' ? 'selected' : '' ?>>D</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Pembagian Hari:</label>
                            <select name="pembagian_hari" class="form-control">
                                <option value="Pagi" <?= $pembagian_hari === 'Pagi' ? 'selected' : '' ?>>Pagi</option>
                                <option value="Siang" <?= $pembagian_hari === 'Siang' ? 'selected' : '' ?>>Siang</option>
                                <option value="Malam" <?= $pembagian_hari === 'Malam' ? 'selected' : '' ?>>Malam</option>
                                <option value="Non-shift" <?= $pembagian_hari === 'Non-shift' ? 'selected' : '' ?>>Non-shift</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Pilih Tanggal:</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= Html::encode($tanggal) ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Tampilkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($mesin && $tanggal && $shift && $pembagian_hari): ?>
        <!-- Info Bar -->
        <div class="alert alert-info" style="font-size: 16px; font-weight: bold; background-color: #00c0ef !important; border-color: #00acd6 !important; color: #fff !important;">
            Jenis Mesin: <?= Html::encode($jenis_mesin) ?> | No Mesin: <?= Html::encode($mesin->nama_mesin) ?> | Tanggal: <?= Html::encode($tanggal) ?> | Shift: <?= Html::encode($shift) ?> (<?= Html::encode($pembagian_hari) ?>)
        </div>

        <!-- Existing Data Table -->
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-list"></i> Data Proses Printing (Existing)</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-striped table-bordered table-hover" id="existing-data-table">
                    <thead>
                        <tr class="info">
                            <th>Jenis Input</th>
                            <th>Start</th>
                            <th>Stop</th>
                            <th>No Mesin</th>
                            <th>No WO</th>
                            <th>NK</th>
                            <th>Design</th>
                            <th>Motif</th>
                            <th>Warna</th>
                            <th>Jumlah Pesanan</th>
                            <th>Realisasi</th>
                            <th>Kurang</th>
                            <th>con. inspect</th>
                            <th>con. printing</th>
                            <th>Keterangan</th>
                            <th>Jenis Hambatan</th>
                            <th style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($existingData)): ?>
                            <tr>
                                <td colspan="17" class="text-center text-muted">Belum ada data untuk kombinasi filter ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($existingData as $row): ?>
                                <tr data-id="<?= Html::encode($row['id']) ?>" 
                                    data-jenis-input="<?= Html::encode($row['jenis_input']) ?>"
                                    data-start="<?= Html::encode($row['start']) ?>"
                                    data-stop="<?= Html::encode($row['stop']) ?>"
                                    data-wo="<?= Html::encode($row['no_wo']) ?>"
                                    data-nk="<?= Html::encode($row['nk']) ?>"
                                    data-design="<?= Html::encode($row['design']) ?>"
                                    data-motif="<?= Html::encode($row['motif']) ?>"
                                    data-warna="<?= Html::encode($row['warna']) ?>"
                                    data-jumlah-pesanan="<?= Html::encode($row['jumlah_pesanan']) ?>"
                                    data-realisasi="<?= Html::encode($row['realisasi']) ?>"
                                    data-kurang="<?= Html::encode($row['kurang']) ?>"
                                    data-panjang-greige="<?= Html::encode($row['panjang_greige']) ?>"
                                    data-panjang-jadi="<?= Html::encode($row['panjang_jadi']) ?>"
                                    data-keterangan="<?= Html::encode($row['keterangan']) ?>"
                                    data-hambatan="<?= Html::encode($row['tipe_record'] === 'rekap' ? $row['raw_id'] : '') ?>"
                                    data-hambatan-id="<?= Html::encode($row['tipe_record'] === 'rekap' && isset($row['mst_jenis_hambatan_id']) ? $row['mst_jenis_hambatan_id'] : '') ?>"
                                >
                                    <td><span class="label label-default"><?= Html::encode($row['jenis_input']) ?></span></td>
                                    <td><?= Html::encode($row['start']) ?></td>
                                    <td><?= Html::encode($row['stop']) ?></td>
                                    <td><?= Html::encode($row['no_mc']) ?></td>
                                    <td><?= Html::encode($row['no_wo']) ?></td>
                                    <td>
                                        <?php if (!empty($row['kartu_proses_id'])): ?>
                                            <?= Html::a(Html::encode($row['nk']), ['/processing-printing/view', 'id' => $row['kartu_proses_id']], ['target' => '_blank']) ?>
                                        <?php else: ?>
                                            <?= Html::encode($row['nk']) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= Html::encode($row['design']) ?></td>
                                    <td><?= Html::encode($row['motif']) ?></td>
                                    <td><?= Html::encode($row['warna']) ?></td>
                                    <td><?= Html::encode($row['jumlah_pesanan']) ?></td>
                                    <td><?= Html::encode($row['realisasi']) ?></td>
                                    <td><?= Html::encode($row['kurang']) ?></td>
                                    <td><?= Html::encode($row['panjang_greige']) ?></td>
                                    <td><?= Html::encode($row['panjang_jadi']) ?></td>
                                    <td><?= Html::encode($row['keterangan']) ?></td>
                                    <td><?= Html::encode($row['jenis_hambatan']) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-xs btn-edit" onclick="editRow('<?= Html::encode($row['id']) ?>')"><i class="fa fa-pencil"></i></button>
                                        <?= Html::a('<i class="fa fa-trash"></i>', ['hapus-input-produksi', 'id' => $row['raw_id'], 'tipe' => $row['tipe_record'], 'jenis_mesin' => $jenis_mesin, 'mesin_id' => $mesin_id, 'tanggal' => $tanggal, 'shift' => $shift, 'pembagian_hari' => $pembagian_hari], [
                                            'class' => 'btn btn-danger btn-xs',
                                            'data' => [
                                                'confirm' => 'Apakah Anda yakin ingin menghapus data ini?',
                                                'method' => 'post',
                                            ],
                                        ]) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tambahan Input Form -->
        <div class="box box-warning" id="input-form-box">
            <div class="box-header with-border">
                <h3 class="box-title" id="form-title"><i class="fa fa-plus"></i> TAMBAHAN INPUT - Input Produksi Printing</h3>
            </div>
            <div class="box-body">
                <form method="post" action="<?= Url::to(['tambah-input-produksi']) ?>" id="input-form">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                    <input type="hidden" name="mesin_id" value="<?= Html::encode($mesin_id) ?>">
                    <input type="hidden" name="jenis_mesin" value="<?= Html::encode($jenis_mesin) ?>">
                    <input type="hidden" name="tanggal" value="<?= Html::encode($tanggal) ?>">
                    <input type="hidden" name="shift" value="<?= Html::encode($shift) ?>">
                    <input type="hidden" name="pembagian_hari" value="<?= Html::encode($pembagian_hari) ?>">
                    
                    <!-- Edit Mode Hidden Fields -->
                    <input type="hidden" name="record_id" id="form-record-id" value="">
                    <input type="hidden" name="tipe_record" id="form-tipe-record" value="">

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Jenis Input <span class="text-danger">*</span></label>
                                <select name="jenis_input" id="form-jenis-input" class="form-control" required>
                                    <option value="Produksi">Produksi</option>
                                    <option value="Percobaan">Percobaan</option>
                                    <option value="Strike-Off (S/O)">Strike-Off (S/O)</option>
                                    <option value="Bukan Produksi">Bukan Produksi</option>
                                    <option value="Hambatan">Hambatan</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Start</label>
                                <input type="time" name="start" id="form-start" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Stop</label>
                                <input type="time" name="stop" id="form-stop" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>No Mesin</label>
                                <input type="text" class="form-control" value="<?= Html::encode($mesin->nama_mesin) ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3" id="container-nk">
                            <div class="form-group">
                                <label>NK (No Kartu)</label>
                                <div id="container-nk-select">
                                    <select name="nk_no" id="form-nk" class="form-control" style="width: 100%;">
                                        <option value="">-- Pilih NK --</option>
                                    </select>
                                </div>
                                <div id="container-nk-text" style="display: none;">
                                    <input type="text" id="form-nk-text" class="form-control" placeholder="Input NK manual...">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3" id="container-wo">
                            <div class="form-group">
                                <label>No WO</label>
                                <select name="wo_no" id="form-wo" class="form-control" style="width: 100%;">
                                    <option value="">-- Pilih WO --</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Design</label>
                                <input type="text" name="design" id="form-design" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Motif</label>
                                <input type="text" name="motif" id="form-motif" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Warna</label>
                                <div id="container-warna-select" style="display: none;">
                                    <select name="warna_select" id="form-warna-select" class="form-control">
                                        <option value="">-- Pilih Warna --</option>
                                    </select>
                                </div>
                                <input type="text" name="warna" id="form-warna" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Jumlah Pesanan</label>
                                <input type="text" name="jumlah_pesanan" id="form-jumlah-pesanan" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Realisasi</label>
                                <input type="text" name="realisasi" id="form-realisasi" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Kurang</label>
                                <input type="text" name="kurang" id="form-kurang" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>con. inspect (pjg greige)</label>
                                <input type="text" name="panjang_greige" id="form-panjang-greige" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>con. printing (pjg jadi)</label>
                                <input type="text" name="panjang_jadi" id="form-panjang-jadi" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Jenis Hambatan</label>
                                <select name="mst_jenis_hambatan_id" id="form-hambatan" class="form-control" disabled>
                                    <option value="">-- Pilih Hambatan --</option>
                                    <?php foreach ($hambatanList as $h): ?>
                                        <option value="<?= $h['id'] ?>"><?= Html::encode($h['nama']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea name="keterangan" id="form-keterangan" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan Data Input</button>
                            <button type="button" class="btn btn-default" id="btn-cancel-edit" style="display: none;" onclick="resetForm()"><i class="fa fa-times"></i> Batal Edit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$getNkDetailsUrl = Url::to(['get-nk-details']);
$getNksUrl = Url::to(['get-nks']);
$getWosUrl = Url::to(['get-wos']);
$getWoDetailsUrl = Url::to(['get-wo-details']);

$js = <<<JS
// Initialize Select2 for NK lookup
$('#form-nk').select2({
    ajax: {
        url: '{$getNksUrl}',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                q: params.term,
                wo_no: $('#form-wo').val()
            };
        },
        processResults: function (data) {
            return {
                results: data.results
            };
        },
        cache: true
    },
    placeholder: 'Cari No Kartu...',
    allowClear: true
}).on('change', function() {
    var nk = $(this).val();
    if (!nk) return;
    
    $.ajax({
        url: '{$getNkDetailsUrl}',
        data: { nk: nk },
        dataType: 'json',
        success: function(data) {
            console.log("NK Details Response:", data);
            if (data.success) {
                $('#form-design').val(data.design);
                $('#form-motif').val(data.motif);
                $('#form-warna').val(data.warna);
                
                // Select default color from NK in the dropdown
                if ($('#form-warna-select option[value="' + data.warna + '"]').length === 0 && data.warna) {
                    $('#form-warna-select').append('<option value="' + data.warna + '">' + data.warna + '</option>');
                }
                $('#form-warna-select').val(data.warna);

                $('#form-jumlah-pesanan').val(data.jumlah_pesanan);
                $('#form-realisasi').val(data.realisasi);
                $('#form-kurang').val(data.kurang);
                $('#form-panjang-greige').val(data.panjang_greige);
                
                // Also set the WO Select2 value if not already set
                if (!$('#form-wo').val()) {
                    var newOption = new Option(data.wo_no, data.wo_no, true, true);
                    $('#form-wo').append(newOption).trigger('change');
                }
            } else {
                console.warn("NK Details returned success: false", data.message);
                alert("Gagal memproses NK: " + (data.message || "NK tidak ditemukan."));
            }
        },
        error: function(xhr, status, error) {
            console.error("NK Details AJAX Error:", status, error, xhr.responseText);
            alert("Gagal mengambil data NK: " + error);
        }
    });
});

// Initialize Select2 for WO lookup
$('#form-wo').select2({
    ajax: {
        url: '{$getWosUrl}',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                q: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data.results
            };
        },
        cache: true
    },
    placeholder: 'Cari No WO...',
    allowClear: true
});

// Switch behavior based on Jenis Input
function handleJenisInput() {
    var jenis = $('#form-jenis-input').val();
    
    // Reset disabled and blocked states
    $('#form-start, #form-stop, #form-nk, #form-nk-text, #form-wo, #form-design, #form-motif, #form-warna, #form-jumlah-pesanan, #form-realisasi, #form-kurang, #form-panjang-greige, #form-panjang-jadi, #form-hambatan, #form-keterangan').prop('disabled', false).prop('readonly', false);
    
    // Default: show select2 NK, hide text NK
    $('#container-nk-select').show();
    $('#form-nk').prop('disabled', false).attr('name', 'nk_no');
    $('#container-nk-text').hide();
    $('#form-nk-text').prop('disabled', true).removeAttr('name');

    // Default: enable WO select
    $('#form-wo').prop('disabled', false);

    if (jenis === 'Produksi') {
        $('#form-design, #form-motif, #form-warna, #form-jumlah-pesanan, #form-realisasi, #form-kurang').prop('readonly', true);
        $('#form-hambatan').prop('disabled', true).val('');
        
        // Show Warna dropdown and hide text input
        $('#container-warna-select').show();
        $('#form-warna').hide();
        
        // If WO is not selected yet, disable NK
        var wo = $('#form-wo').val();
        if (!wo) {
            $('#form-nk').val(null).trigger('change').prop('disabled', true);
        }
    } else if (jenis === 'Percobaan') {
        $('#form-design, #form-motif, #form-warna, #form-jumlah-pesanan, #form-realisasi, #form-kurang').prop('readonly', true);
        $('#form-hambatan').prop('disabled', true).val('');
        $('#form-jumlah-pesanan, #form-realisasi, #form-kurang, #form-panjang-greige').val('').prop('disabled', true);
        
        // Show Warna dropdown and hide text input
        $('#container-warna-select').show();
        $('#form-warna').hide();

        // NK is a normal text input and can be empty
        $('#container-nk-select').hide();
        $('#form-nk').prop('disabled', true).removeAttr('name');
        $('#container-nk-text').show();
        $('#form-nk-text').prop('disabled', false).attr('name', 'nk_no').val('');
    } else if (jenis === 'Strike-Off (S/O)' || jenis === 'Bukan Produksi' || jenis === 'Hambatan') {
        // NK is text input
        $('#container-nk-select').hide();
        $('#form-nk').prop('disabled', true).removeAttr('name');
        $('#container-nk-text').show();
        $('#form-nk-text').prop('disabled', false).attr('name', 'nk_no');

        // Warna is text input
        $('#container-warna-select').hide();
        $('#form-warna').show().prop('disabled', false).prop('readonly', false);

        // Enable all other fields
        $('#form-design, #form-motif, #form-jumlah-pesanan, #form-realisasi, #form-kurang, #form-panjang-greige, #form-panjang-jadi, #form-keterangan').prop('disabled', false).prop('readonly', false);

        if (jenis === 'Hambatan') {
            $('#form-hambatan').prop('disabled', false);
        } else {
            $('#form-hambatan').prop('disabled', true).val('');
        }
    }
}

$('#form-jenis-input').on('change', function() {
    handleJenisInput();
});

// Fetch WO Details / Handle Dependent NK
$('#form-wo').on('change', function() {
    var wo_no = $(this).val();
    var jenis = $('#form-jenis-input').val();
    
    if (jenis === 'Produksi') {
        if (wo_no) {
            $('#form-nk').prop('disabled', false);
        } else {
            $('#form-nk').val(null).trigger('change').prop('disabled', true);
            $('#form-design').val('');
            $('#form-motif').val('');
            $('#form-warna').val('');
            $('#form-warna-select').empty().append('<option value="">-- Pilih Warna --</option>');
            return;
        }
    }
    
    if (!wo_no) {
        $('#form-design').val('');
        $('#form-motif').val('');
        $('#form-warna').val('');
        $('#form-warna-select').empty().append('<option value="">-- Pilih Warna --</option>');
        return;
    }
    
    $.ajax({
        url: '{$getWoDetailsUrl}',
        data: { wo_no: wo_no },
        dataType: 'json',
        success: function(data) {
            console.log("WO Details Response:", data);
            if (data.success) {
                $('#form-design').val(data.design);
                $('#form-motif').val(data.motif);
                
                // Populate warna select
                var currentWarna = $('#form-warna').val();
                $('#form-warna-select').empty().append('<option value="">-- Pilih Warna --</option>');
                $.each(data.colors, function(i, item) {
                    $('#form-warna-select').append('<option value="'+item.color+'" data-qty="'+item.qty_finish_yard+'" data-realisasi="'+item.realisasi+'">'+item.color+'</option>');
                });
                
                if (currentWarna) {
                    if ($('#form-warna-select option[value="' + currentWarna + '"]').length === 0) {
                        $('#form-warna-select').append('<option value="' + currentWarna + '">' + currentWarna + '</option>');
                    }
                    $('#form-warna-select').val(currentWarna).trigger('change');
                } else if (data.colors.length > 0) {
                    $('#form-warna-select').val(data.colors[0].color).trigger('change');
                }
                
                // For manual warna input types, also populate the first color if empty
                if (jenis === 'Strike-Off (S/O)' || jenis === 'Bukan Produksi' || jenis === 'Hambatan') {
                    if (data.colors.length > 0 && !$('#form-warna').val()) {
                        $('#form-warna').val(data.colors[0].color);
                        
                        // Also populate the corresponding quantity and realisasi if empty
                        if (!$('#form-jumlah-pesanan').val()) {
                            $('#form-jumlah-pesanan').val(data.colors[0].qty_finish_yard);
                        }
                        if (!$('#form-realisasi').val()) {
                            $('#form-realisasi').val(data.colors[0].realisasi);
                        }
                        if (!$('#form-kurang').val()) {
                            var q = parseFloat(data.colors[0].qty_finish_yard) || 0;
                            var r = parseFloat(data.colors[0].realisasi) || 0;
                            $('#form-kurang').val((q - r).toFixed(2));
                        }
                    }
                }
            } else {
                console.warn("WO Details returned success: false");
            }
        },
        error: function(xhr, status, error) {
            console.error("WO Details AJAX Error:", status, error, xhr.responseText);
        }
    });
});

// Handle warna select change
$('#form-warna-select').on('change', function() {
    var val = $(this).val();
    $('#form-warna').val(val);
    
    var selectedOption = $(this).find('option:selected');
    var qty = selectedOption.data('qty');
    var realisasi = selectedOption.data('realisasi');
    var nk = $('#form-nk').val();
    if (!nk) {
        if (qty !== undefined && qty !== null && qty !== '') {
            $('#form-jumlah-pesanan').val(qty);
        }
        if (realisasi !== undefined && realisasi !== null && realisasi !== '') {
            $('#form-realisasi').val(realisasi);
            
            // Calculate Kurang: jumlah_pesanan - realisasi
            var q = parseFloat(qty) || 0;
            var r = parseFloat(realisasi) || 0;
            $('#form-kurang').val((q - r).toFixed(2));
        }
    }
});

// Edit row
window.editRow = function(id) {
    var row = $('tr[data-id="' + id + '"]');
    var jenis = row.data('jenis-input');
    
    $('#form-record-id').val(row.data('id').split('_')[1]);
    $('#form-tipe-record').val(row.data('id').split('_')[0]);
    
    $('#form-jenis-input').val(jenis).trigger('change');
    $('#form-start').val(row.data('start'));
    $('#form-stop').val(row.data('stop'));
    
    if (row.data('nk')) {
        if (jenis === 'Produksi') {
            var newOptionNk = new Option(row.data('nk'), row.data('nk'), true, true);
            $('#form-nk').append(newOptionNk).trigger('change');
        } else {
            $('#form-nk-text').val(row.data('nk'));
        }
    } else {
        $('#form-nk').val(null).trigger('change');
        $('#form-nk-text').val('');
    }
    
    if (row.data('wo')) {
        var newOptionWo = new Option(row.data('wo'), row.data('wo'), true, true);
        $('#form-wo').append(newOptionWo).trigger('change');
    } else {
        $('#form-wo').val(null).trigger('change');
    }
    
    $('#form-design').val(row.data('design'));
    $('#form-motif').val(row.data('motif'));
    $('#form-warna').val(row.data('warna'));
    
    // Set warna dropdown value
    var editWarna = row.data('warna');
    if (editWarna) {
        if ($('#form-warna-select option[value="' + editWarna + '"]').length === 0) {
            $('#form-warna-select').append('<option value="' + editWarna + '">' + editWarna + '</option>');
        }
        $('#form-warna-select').val(editWarna);
    }
    $('#form-jumlah-pesanan').val(row.data('jumlah-pesanan'));
    $('#form-realisasi').val(row.data('realisasi'));
    $('#form-kurang').val(row.data('kurang'));
    $('#form-panjang-greige').val(row.data('panjang-greige'));
    $('#form-panjang-jadi').val(row.data('panjang-jadi'));
    $('#form-keterangan').val(row.data('keterangan'));
    
    if (row.data('hambatan-id')) {
        $('#form-hambatan').val(row.data('hambatan-id'));
    }
    
    $('#form-title').html('<i class="fa fa-pencil"></i> EDIT INPUT - ' + jenis);
    $('#btn-cancel-edit').show();
    
    $('html, body').animate({
        scrollTop: $("#input-form-box").offset().top
    }, 500);
};

window.resetForm = function() {
    $('#form-record-id').val('');
    $('#form-tipe-record').val('');
    $('#input-form')[0].reset();
    $('#form-nk').val(null).trigger('change');
    $('#form-nk-text').val('');
    $('#form-wo').val(null).trigger('change');
    $('#form-jenis-input').val('Produksi').trigger('change');
    $('#form-title').html('<i class="fa fa-plus"></i> TAMBAHAN INPUT - Input Produksi Printing');
    $('#btn-cancel-edit').hide();
};

// Initialize Select2 for filters
$('#jenis-mesin-select').select2({
    placeholder: '-- Pilih Jenis Mesin --',
    allowClear: true
}).on('change', function() {
    $('#mesin-id-input').val('');
    $(this).closest('form').submit();
});

$('#mesin-select').select2({
    placeholder: '-- Pilih No Mesin --',
    allowClear: true
}).on('change', function() {
    $('#mesin-id-input').val($(this).val());
    $(this).closest('form').submit();
});

// Initial run
handleJenisInput();
JS;

$this->registerJs($js);
?>
