<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;

$this->title = 'Input Data Produksi Mesin';
$this->params['breadcrumbs'][] = $this->title;

$mesinData = [];
foreach ($mesins as $m) {
    if (!isset($mesinData[$m->model_mesin])) {
        $mesinData[$m->model_mesin] = [];
    }
    $mesinData[$m->model_mesin][] = [
        'id' => $m->nama_mesin,
        'text' => $m->nama_mesin
    ];
}

$jenisMesinOptions = array_combine($jenisMesins, $jenisMesins);
?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Filter Pencarian</h3>
    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => ['index'],
            'options' => ['class' => 'form-horizontal']
        ]); ?>

        <div class="form-group">
            <label class="col-sm-2 control-label">Pilih Jenis Mesin</label>
            <div class="col-sm-4">
                <?= Select2::widget([
                    'name' => 'jenis_mesin',
                    'value' => $jenis_mesin,
                    'data' => $jenisMesinOptions,
                    'options' => [
                        'placeholder' => 'Pilih Jenis Mesin...',
                        'id' => 'jenis-mesin-select',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Pilih No. Mesin</label>
            <div class="col-sm-4">
                <?= Select2::widget([
                    'name' => 'no_mesin',
                    'value' => $no_mesin,
                    'data' => [], // Akan diisi via JS
                    'options' => [
                        'placeholder' => 'Bisa lebih dari 1...',
                        'multiple' => true,
                        'id' => 'no-mesin-select',
                    ],
                ]) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Shift</label>
            <div class="col-sm-4">
                <?= Select2::widget([
                    'name' => 'shift',
                    'value' => $shift,
                    'data' => ['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D'],
                    'options' => ['placeholder' => 'Pilih Shift...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Pilih Tanggal</label>
            <div class="col-sm-4">
                <?= DatePicker::widget([
                    'name' => 'tanggal',
                    'value' => $tanggal ? $tanggal : date('Y-m-d'),
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'autoclose' => true,
                        'todayHighlight' => true
                    ]
                ]) ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <?= Html::submitButton('<i class="glyphicon glyphicon-search"></i> Tampilkan', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$mesinDataJson = json_encode($mesinData);
$initialNoMesin = $no_mesin ? json_encode((array)$no_mesin) : '[]';
$urlLookupKpById = \yii\helpers\Url::to(['/ajax/lookup-kp-by-id']);
$urlCheckExistingData = \yii\helpers\Url::to(['/produksi-mesin/check-existing-data']);
$urlLookupWoAll = \yii\helpers\Url::to(['/ajax/lookup-wo-all']);
$urlLookupOrderPfp = \yii\helpers\Url::to(['/ajax/lookup-order-pfp']);
$urlLookupNkAll = \yii\helpers\Url::to(['/ajax/lookup-nk-all']);
$dyeingConfigJson = json_encode(isset($prosesDyeingConfig) ? $prosesDyeingConfig : []);
$pfpConfigJson = json_encode(isset($prosesPfpConfig) ? $prosesPfpConfig : []);

$js = <<<JS
var urlLookupWoAll = '{$urlLookupWoAll}';
var urlLookupOrderPfp = '{$urlLookupOrderPfp}';
var urlLookupNkAll = '{$urlLookupNkAll}';
var mesinData = $mesinDataJson;
var initialNoMesin = $initialNoMesin;
var dyeingConfig = $dyeingConfigJson;
var pfpConfig = $pfpConfigJson;

$('#jenis-mesin-select').on('change', function() {
    var jenis = $(this).val();
    var select = $('#no-mesin-select');
    var currentValues = select.val() || initialNoMesin; // keep selected values if possible
    
    select.empty();
    
    if (jenis && mesinData[jenis]) {
        var options = mesinData[jenis];
        $.each(options, function(index, item) {
            var selected = currentValues.indexOf(item.id) !== -1;
            var newOption = new Option(item.text, item.id, selected, selected);
            select.append(newOption);
        });
    }
    select.trigger('change');
});

// Trigger change on load to populate the initial no_mesin options
if ($('#jenis-mesin-select').val()) {
    $('#jenis-mesin-select').trigger('change');
}

// Edit via Tambahan Input logic
$(document).on('click', '.btn-edit-row', function() {
    var target = $(this).data('target'); // 'dyeing' or 'pfp'
    var tbody = $('#tbody-input-' + target);
    
    // We populate the first row of the corresponding input table
    var row = tbody.find('tr').first();
    
    var woVal = $(this).data('wo');
    var woId = $(this).data('wo-id');
    var nkVal = $(this).data('nk');
    var nkId = $(this).data('nk-id');
    
    var woSelect = row.find('td:eq(0) select');
    if (woSelect.length) {
        if (woVal) {
            woSelect.empty().append(new Option('', '')).append(new Option(woVal, woId || woVal, true, true));
            woSelect.val(woId || woVal).trigger('change');
        } else {
            woSelect.empty().append(new Option('', ''));
            woSelect.val(null).trigger('change');
        }
    } else {
        row.find('td:eq(0) input').val(woVal);
    }
    
    var nkSelect = row.find('td:eq(1) select');
    if (nkSelect.length) {
        if (nkVal) {
            nkSelect.empty().append(new Option('', '')).append(new Option(nkVal, nkId || nkVal, true, true));
            nkSelect.val(nkId || nkVal).trigger('change');
        } else {
            nkSelect.empty().append(new Option('', ''));
            nkSelect.val(null).trigger('change');
        }
    } else {
        row.find('td:eq(1) input').val(nkVal);
    }
    
    row.find('td:eq(2) input').val($(this).data('motif'));
    row.find('td:eq(3) input').val($(this).data('warna'));
    
    var prosesSelect = row.find('td:eq(4) select');
    var prosesVal = $(this).data('proses');
    if (prosesSelect.find("option[value='" + prosesVal + "']").length === 0) {
        prosesSelect.append(new Option(prosesVal, prosesVal));
    }
    prosesSelect.val(prosesVal).trigger('change');
    
    row.find('td:eq(5) input').val($(this).data('start'));
    row.find('td:eq(6) input').val($(this).data('stop'));
    var nmSelect = row.find('td:eq(7) select');
    if (nmSelect.length) {
        nmSelect.val($(this).data('nomesin'));
    } else {
        row.find('td:eq(7) input').val($(this).data('nomesin'));
    }
    if (target === 'dyeing') {
        row.find('td:eq(8) input').val($(this).data('temp'));
        row.find('td:eq(9) input').val($(this).data('speed'));
        row.find('td:eq(10) input').val($(this).data('gramasi'));
        row.find('td:eq(11) input').val($(this).data('program'));
        row.find('td:eq(12) input').val($(this).data('density'));
        row.find('td:eq(13) input').val($(this).data('overfeed'));
        row.find('td:eq(14) input').val($(this).data('lebarjadi'));
        row.find('td:eq(15) input').val($(this).data('panjangjadi'));
        row.find('td:eq(16) input').val($(this).data('infokualitas'));
        row.find('td:eq(17) input').val($(this).data('keterangan'));
    } else {
        row.find('td:eq(8) input').val($(this).data('temp'));
        row.find('td:eq(9) input').val($(this).data('speed'));
        row.find('td:eq(10) input').val($(this).data('gramasi'));
        row.find('td:eq(11) input').val($(this).data('program'));
        row.find('td:eq(12) input').val($(this).data('exrelax'));
        row.find('td:eq(13) input').val($(this).data('exwroligomer'));
        row.find('td:eq(14) input').val($(this).data('exdyeing'));
        row.find('td:eq(15) input').val($(this).data('wrpcnt'));
        row.find('td:eq(16) input').val($(this).data('rpm'));
        row.find('td:eq(17) input').val($(this).data('density'));
        row.find('td:eq(18) input').val($(this).data('jamur'));
        row.find('td:eq(19) input').val($(this).data('karat'));
        row.find('td:eq(20) input').val($(this).data('overfeed'));
        row.find('td:eq(21) input').val($(this).data('lebarjadi'));
        row.find('td:eq(22) input').val($(this).data('panjangjadi'));
        row.find('td:eq(23) input').val($(this).data('infokualitas'));
        row.find('td:eq(24) input').val($(this).data('keterangan'));
    }
    
    $('html, body').animate({
        scrollTop: tbody.closest('.box').offset().top - 50
    }, 500);
    
    row.css('background-color', '#fff3cd');
    setTimeout(function() {
        row.css('background-color', '');
    }, 2000);
});

$('#dyeing-nk-input').on('change', function() {
    var id = $(this).val();
    if(id) {
        $.ajax({
            url: '{$urlLookupKpById}',
            type: 'GET',
            data: {q: 'dyeing', id: id},
            success: function(data) {
                if (data) {
                    $('#dyeing-motif-input').val(data.wo && data.wo.greige ? data.wo.greige.nama_kain : '');
                    $('#dyeing-warna-input').val(data.woColor && data.woColor.moColor ? data.woColor.moColor.color : '');
                }
            }
        });
    } else {
        $('#dyeing-motif-input').val('');
        $('#dyeing-warna-input').val('');
    }
});

$('#pfp-nk-input').on('change', function() {
    var id = $(this).val();
    if(id) {
        $.ajax({
            url: '{$urlLookupKpById}',
            type: 'GET',
            data: {q: 'pfp', id: id},
            success: function(data) {
                if (data) {
                    $('#pfp-motif-input').val(data.greige ? data.greige.nama_kain : (data.orderPfp && data.orderPfp.greige ? data.orderPfp.greige.nama_kain : ''));
                    $('#pfp-warna-input').val(data.orderPfp && data.orderPfp.dasar_warna ? data.orderPfp.dasar_warna : '');
                }
            }
        });
    } else {
        $('#pfp-motif-input').val('');
        $('#pfp-warna-input').val('');
    }
});

function applyDyeingProcessConfig(prosesName, targetRow) {
    var config = dyeingConfig[prosesName] || {};
    var row = targetRow || $('#tbody-input-dyeing tr:first');
    row.find('input[name$="[temp]"]').prop('disabled', !config.temp);
    row.find('input[name$="[speed]"]').prop('disabled', !config.speed);
    row.find('input[name$="[gramasi]"]').prop('disabled', !config.gramasi);
    row.find('input[name$="[program_number]"]').prop('disabled', !config.program_number);
    row.find('input[name$="[density]"]').prop('disabled', !config.density);
    row.find('input[name$="[over_feed]"]').prop('disabled', !config.over_feed);
    row.find('input[name$="[lebar_jadi]"]').prop('disabled', !config.lebar_jadi);
    row.find('input[name$="[panjang_jadi]"]').prop('disabled', !config.panjang_jadi);
    row.find('input[name$="[info_kualitas]"]').prop('disabled', !config.info_kualitas);
    row.find('input[name$="[keterangan]"]').prop('disabled', !config.keterangan);
}

$(document).on('change', '.input-proses-dyeing', function() {
    applyDyeingProcessConfig($(this).val(), $(this).closest('tr'));
});

function applyPfpProcessConfig(prosesName, targetRow) {
    var config = pfpConfig[prosesName] || {};
    var row = targetRow || $('#tbody-input-pfp tr:first');
    row.find('input[name$="[temp]"]').prop('disabled', !config.temp);
    row.find('input[name$="[speed]"]').prop('disabled', !config.speed);
    row.find('input[name$="[waktu]"]').prop('disabled', !config.waktu);
    row.find('input[name$="[program_number]"]').prop('disabled', !config.program_number);
    row.find('input[name$="[ex_relax]"]').prop('disabled', !config.ex_relax);
    row.find('input[name$="[ex_wr_oligomer]"]').prop('disabled', !config.ex_wr_oligomer);
    row.find('input[name$="[ex_dyeing]"]').prop('disabled', !config.ex_dyeing);
    row.find('input[name$="[wr_pcnt]"]').prop('disabled', !config.wr_pcnt);
    row.find('input[name$="[rpm]"]').prop('disabled', !config.rpm);
    row.find('input[name$="[density]"]').prop('disabled', !config.density);
    row.find('input[name$="[jamur]"]').prop('disabled', !config.jamur);
    row.find('input[name$="[karat]"]').prop('disabled', !config.karat);
    row.find('input[name$="[over_feed]"]').prop('disabled', !config.over_feed);
    row.find('input[name$="[counter]"]').prop('disabled', !config.counter);
    row.find('input[name$="[lebar_jadi]"]').prop('disabled', !config.lebar_jadi);
    row.find('input[name$="[info_kualitas]"]').prop('disabled', !config.info_kualitas);
    row.find('input[name$="[gangguan_produksi]"]').prop('disabled', !config.gangguan_produksi);
    row.find('input[name$="[gramasi]"]').prop('disabled', !config.gramasi);
    row.find('input[name$="[panjang_jadi]"]').prop('disabled', !config.panjang_jadi);
    row.find('input[name$="[keterangan]"]').prop('disabled', !config.keterangan);
}

$(document).on('change', '.input-proses-pfp', function() {
    applyPfpProcessConfig($(this).val(), $(this).closest('tr'));
});

$(document).on('click', '.btn-tambah-row', function(e) {
    e.preventDefault();
    var target = $(this).data('target');
    var tbody = $('#tbody-input-' + target);
    var firstRow = tbody.find('tr').first();
    
    var newRow = firstRow.clone();
    newRow.find('input').val('');
    
    newRow.find('.select2-container').remove();
    newRow.find('select').removeClass('select2-hidden-accessible').removeAttr('data-select2-id tabindex aria-hidden data-krajee-select2 data-s2-options').empty();
    
    var newIndex = new Date().getTime();
    
    newRow.find('input, select').each(function() {
        var name = $(this).attr('name');
        if (name) {
            $(this).attr('name', name.replace(/\[\d+\]/, '[' + newIndex + ']'));
        }
        var id = $(this).attr('id');
        if (id) {
            $(this).attr('id', id + '-' + newIndex);
        }
    });
    
    tbody.append(newRow);
    
    newRow.find('.input-wo-' + target).select2({
        allowClear: true,
        placeholder: target === 'dyeing' ? 'Cari WO...' : 'Cari Order PFP...',
        minimumInputLength: 3,
        ajax: {
            url: target === 'dyeing' ? urlLookupWoAll : urlLookupOrderPfp,
            dataType: 'json',
            delay: 250,
            data: function(params) { return {q: params.term}; },
            processResults: function(data) { return {results: data.results || data}; }
        }
    });
    
    newRow.find('.input-nk-' + target).select2({
        allowClear: true,
        placeholder: 'Cari NK...',
        minimumInputLength: 0,
        ajax: {
            url: urlLookupNkAll,
            dataType: 'json',
            delay: 250,
            data: function(params) {
                var wo_no = newRow.find('.input-wo-' + target + ' option:selected').text();
                return {q: params.term, wo_no: wo_no};
            },
            processResults: function(data) { return {results: data.results || data}; }
        }
    }).on('change', function() {
        var id = $(this).val();
        if(id) {
            $.ajax({
                url: '{$urlLookupKpById}',
                type: 'GET',
                data: {q: target, id: id},
                success: function(data) {
                    if (data) {
                        if (target === 'dyeing') {
                            newRow.find('input[id^="dyeing-motif-input"]').val(data.wo && data.wo.greige ? data.wo.greige.nama_kain : '');
                            newRow.find('input[id^="dyeing-warna-input"]').val(data.woColor && data.woColor.moColor ? data.woColor.moColor.color : '');
                        } else {
                            newRow.find('input[id^="pfp-motif-input"]').val(data.greige ? data.greige.nama_kain : (data.orderPfp && data.orderPfp.greige ? data.orderPfp.greige.nama_kain : ''));
                            newRow.find('input[id^="pfp-warna-input"]').val(data.orderPfp && data.orderPfp.dasar_warna ? data.orderPfp.dasar_warna : '');
                        }
                    }
                }
            });
        } else {
            if (target === 'dyeing') {
                newRow.find('input[id^="dyeing-motif-input"]').val('');
                newRow.find('input[id^="dyeing-warna-input"]').val('');
            } else {
                newRow.find('input[id^="pfp-motif-input"]').val('');
                newRow.find('input[id^="pfp-warna-input"]').val('');
            }
        }
    });
    
    var prosesSelect = firstRow.find('.input-proses-' + target);
    var clonedProsesSelect = newRow.find('.input-proses-' + target);
    prosesSelect.find('option').each(function() {
        clonedProsesSelect.append(new Option($(this).text(), $(this).val()));
    });
    clonedProsesSelect.select2({
        allowClear: true,
        placeholder: 'Pilih Proses...'
    });
    
    var mesinSelect = firstRow.find('select[name$="[no_mesin]"]');
    var clonedMesinSelect = newRow.find('select[name$="[no_mesin]"]');
    mesinSelect.find('option').each(function() {
        clonedMesinSelect.append(new Option($(this).text(), $(this).val()));
    });
});

$(document).on('click', '.btn-hapus-row', function(e) {
    e.preventDefault();
    var tbody = $(this).closest('tbody');
    if (tbody.find('tr').length > 1) {
        $(this).closest('tr').remove();
    } else {
        $(this).closest('tr').find('input').val('');
        $(this).closest('tr').find('select').val(null).trigger('change');
    }
});

$('#form-tambahan-input').on('submit', function(e) {
    e.preventDefault();
    var form = $(this);
    
    var valid = true;
    var errMsg = "";

    function validateRows(tbodyId, label) {
        $(tbodyId + ' tr').each(function(index) {
            var tr = $(this);
            var wo = tr.find('.input-wo-' + label.toLowerCase()).val() || tr.find('input[name$="[wo]"]').val() || tr.find('select[name$="[wo]"]').val();
            var nk = tr.find('.input-nk-' + label.toLowerCase()).val() || tr.find('input[name$="[nk]"]').val() || tr.find('select[name$="[nk]"]').val();
            var proses = tr.find('.input-proses-' + label.toLowerCase()).val() || tr.find('select[name$="[proses]"]').val();
            var mesin = tr.find('select[name$="[no_mesin]"]').val();
            var ket = tr.find('input[name$="[keterangan]"]').val();
            
            var hasData = false;
            tr.find('input[type!="hidden"], select').each(function() {
                var val = $(this).val();
                if (val && val !== '') {
                    hasData = true;
                }
            });
            
            if (hasData) {
                if (!wo || !nk || !proses || !mesin) {
                    valid = false;
                    errMsg = "Pada baris " + label + " ke-" + (index + 1) + ": WO, NK, Proses, dan No Mesin HARUS diisi! (Atau hapus baris jika tidak digunakan)";
                    return false;
                }
                if ((proses === 'Cuci' || proses === 'Cuci 2-5') && (!ket || ket.trim() === '')) {
                    valid = false;
                    errMsg = "Pada baris " + label + " ke-" + (index + 1) + ": Proses '" + proses + "' mewajibkan kolom Keterangan diisi!";
                    return false;
                }
            }
        });
    }

    validateRows('#tbody-input-dyeing', 'Dyeing');
    if (!valid) {
        alert(errMsg);
        return;
    }
    
    validateRows('#tbody-input-pfp', 'PFP');
    if (!valid) {
        alert(errMsg);
        return;
    }
    
    // Disable submit button to prevent double click
    var submitBtn = form.find('button[type="submit"]');
    var originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Checking...');

    isSubmitting = true;

    $.ajax({
        url: '{$urlCheckExistingData}',
        type: 'POST',
        data: form.serialize(),
        success: function(res) {
            if (res.exists) {
                if (confirm('Data sudah ada, apakah mau menimpa data?')) {
                    form.off('submit').submit();
                } else {
                    submitBtn.prop('disabled', false).html(originalText);
                    isSubmitting = false;
                }
            } else {
                form.off('submit').submit();
            }
        },
        error: function() {
            alert('Terjadi kesalahan saat memeriksa data existing.');
            submitBtn.prop('disabled', false).html(originalText);
            isSubmitting = false;
        }
    });
});

var isDirty = false;
var isSubmitting = false;

$('input, select, textarea').on('change input', function() {
    isDirty = true;
});

$('form').not('#form-tambahan-input').on('submit', function() {
    isSubmitting = true;
});

window.addEventListener("beforeunload", function (e) {
    if (isDirty && !isSubmitting) {
        var confirmationMessage = 'Ada perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
        (e || window.event).returnValue = confirmationMessage;
        return confirmationMessage;
    }
});

document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'hidden') {
        if (isDirty && !isSubmitting) {
            alert('PERHATIAN: Ada perubahan yang belum disimpan di halaman Produksi Mesin ini!');
        }
    }
});

$('a').on('click', function(e) {
    if (isDirty && !isSubmitting) {
        var href = $(this).attr('href');
        // Jangan intercept jika itu link buntu atau javascript
        if (href && href !== '#' && href.indexOf('javascript:') !== 0 && !$(this).attr('target')) {
            if (!confirm('Ada perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?')) {
                e.preventDefault();
            }
        }
    }
});

JS;
$this->registerJs($js);
?>

<?php if ($jenis_mesin && $no_mesin && $shift && $tanggal): ?>
<?php 
$noMesinStr = is_array($no_mesin) ? implode(', ', $no_mesin) : $no_mesin;
$noMesinList = [];
if (is_array($no_mesin)) {
    foreach ($no_mesin as $nm) {
        $noMesinList[$nm] = $nm;
    }
}
?>
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <strong>Jenis Mesin:</strong> <?= Html::encode($jenis_mesin) ?> | 
            <strong>No Mesin:</strong> <?= Html::encode($noMesinStr) ?> | 
            <strong>Tanggal:</strong> <?= Html::encode($tanggal) ?> | 
            <strong>Shift:</strong> <?= Html::encode($shift) ?>
        </div>
    </div>
</div>

<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Data Proses Dyeing (Existing)</h3>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>WO</th>
                    <th>NK</th>
                    <th>Motif</th>
                    <th>Warna</th>
                    <th>Nama Proses</th>
                    <th>Start</th>
                    <th>Stop</th>
                    <th>No Mesin</th>
                    <th>Temp</th>
                    <th>Speed</th>
                    <th>Gramasi</th>
                    <th>Program Number</th>
                    <th>Density</th>
                    <th>Over Feed</th>
                    <th>Lebar Jadi</th>
                    <th>Panjang Jadi</th>
                    <th>Info Kualitas</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($dyeingRecords)): ?>
                <tr>
                    <td colspan="14" class="text-center"><em>Tidak ada data existing.</em></td>
                </tr>
                <?php else: ?>
                    <?php foreach ($dyeingRecords as $record): ?>
                        <?php 
                            $val = json_decode($record->value, true); 
                            $wo = $record->kartuProcess->wo;
                            $mo = $record->kartuProcess->mo;
                            $woColor = $record->kartuProcess->woColor;
                        ?>
                        <tr>
                            <td><?= $wo ? Html::encode($wo->no) : '-' ?></td>
                            <td><?= Html::a(Html::encode($record->kartuProcess->no), ['/trn-kartu-proses-dyeing/view', 'id' => $record->kartuProcess->id], ['target' => '_blank', 'title' => 'Lihat Kartu Proses']) ?></td>
                            <td><?= $mo ? Html::encode($mo->design) : '-' ?></td>
                            <td><?= $woColor && $woColor->moColor ? Html::encode($woColor->moColor->color) : '-' ?></td>
                            <td><?= Html::encode($record->process->nama_proses) ?></td>
                            <td><?= Html::encode(isset($val['start']) ? $val['start'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['stop']) ? $val['stop'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['no_mesin']) ? $val['no_mesin'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['temp']) ? $val['temp'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['speed']) ? $val['speed'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['gramasi']) ? $val['gramasi'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['program_number']) ? $val['program_number'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['density']) ? $val['density'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['over_feed']) ? $val['over_feed'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['lebar_jadi']) ? $val['lebar_jadi'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['panjang_jadi']) ? $val['panjang_jadi'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['info_kualitas']) ? $val['info_kualitas'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['keterangan']) ? $val['keterangan'] : '-') ?></td>
                            <td>
                                <button type="button" class="btn btn-default btn-xs btn-edit-row"
                                    data-target="dyeing"
                                    data-wo="<?= $wo ? Html::encode($wo->no) : '' ?>"
                                    data-wo-id="<?= $wo ? Html::encode($wo->id) : '' ?>"
                                    data-nk="<?= Html::encode($record->kartuProcess->no) ?>"
                                    data-nk-id="<?= Html::encode($record->kartuProcess->id) ?>"
                                    data-motif="<?= $mo ? Html::encode($mo->design) : '' ?>"
                                    data-warna="<?= $woColor && $woColor->moColor ? Html::encode($woColor->moColor->color) : '' ?>"
                                    data-proses="<?= Html::encode($record->process->nama_proses) ?>"
                                    data-start="<?= Html::encode(isset($val['start']) ? $val['start'] : '') ?>"
                                    data-stop="<?= Html::encode(isset($val['stop']) ? $val['stop'] : '') ?>"
                                    data-nomesin="<?= Html::encode(isset($val['no_mesin']) ? $val['no_mesin'] : '') ?>"
                                    data-temp="<?= Html::encode(isset($val['temp']) ? $val['temp'] : '') ?>"
                                    data-speed="<?= Html::encode(isset($val['speed']) ? $val['speed'] : '') ?>"
                                    data-gramasi="<?= Html::encode(isset($val['gramasi']) ? $val['gramasi'] : '') ?>"
                                    data-program="<?= Html::encode(isset($val['program_number']) ? $val['program_number'] : '') ?>"
                                    data-density="<?= Html::encode(isset($val['density']) ? $val['density'] : '') ?>"
                                    data-overfeed="<?= Html::encode(isset($val['over_feed']) ? $val['over_feed'] : '') ?>"
                                    data-lebarjadi="<?= Html::encode(isset($val['lebar_jadi']) ? $val['lebar_jadi'] : '') ?>"
                                    data-panjangjadi="<?= Html::encode(isset($val['panjang_jadi']) ? $val['panjang_jadi'] : '') ?>"
                                    data-infokualitas="<?= Html::encode(isset($val['info_kualitas']) ? $val['info_kualitas'] : '') ?>"
                                    data-keterangan="<?= Html::encode(isset($val['keterangan']) ? $val['keterangan'] : '') ?>"
                                    title="Edit via Tambahan Input"
                                ><i class="glyphicon glyphicon-pencil"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="box box-warning">
    <div class="box-header with-border">
        <h3 class="box-title">Data Proses PFP (Existing)</h3>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>WO</th>
                    <th>NK</th>
                    <th>Motif</th>
                    <th>Warna</th>
                    <th>Nama Proses</th>
                    <th>Tanggal</th>
                    <th>Start</th>
                    <th>Stop</th>
                    <th>No Mesin</th>
                    <th>Shift</th>
                    <th>Temp</th>
                    <th>Speed</th>
                    <th>Program Number</th>
                    <th>Ex Relax</th>
                    <th>Ex Wr Oligomer</th>
                    <th>Ex Dyeing</th>
                    <th>WR %</th>
                    <th>Rpm</th>
                    <th>Density</th>
                    <th>Jamur</th>
                    <th>Karat</th>
                    <th>Over Feed</th>
                    <th>Lebar Jadi</th>
                    <th>Info Kualitas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pfpRecords)): ?>
                <tr>
                    <td colspan="14" class="text-center"><em>Tidak ada data existing.</em></td>
                </tr>
                <?php else: ?>
                    <?php foreach ($pfpRecords as $record): ?>
                        <?php 
                            $val = json_decode($record->value, true); 
                            $orderPfp = $record->kartuProcess->orderPfp;
                            $greige = $record->kartuProcess->greige;
                        ?>
                        <tr>
                            <td><?= $orderPfp ? Html::encode($orderPfp->no) : '-' ?></td>
                            <td><?= Html::a(Html::encode($record->kartuProcess->no), ['/trn-kartu-proses-pfp/view', 'id' => $record->kartuProcess->id], ['target' => '_blank', 'title' => 'Lihat Kartu Proses']) ?></td>
                            <td><?= $greige ? Html::encode($greige->nama_kain) : '-' ?></td>
                            <td><?= $orderPfp ? Html::encode($orderPfp->dasar_warna) : '-' ?></td>
                            <td><?= Html::encode($record->process->nama_proses) ?></td>
                            <td><?= Html::encode(isset($val['tanggal']) ? $val['tanggal'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['start']) ? $val['start'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['stop']) ? $val['stop'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['no_mesin']) ? $val['no_mesin'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['shift_group']) ? $val['shift_group'] : (isset($val['shift_operator']) ? $val['shift_operator'] : '-')) ?></td>
                            <td><?= Html::encode(isset($val['temp']) ? $val['temp'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['speed']) ? $val['speed'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['program_number']) ? $val['program_number'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['ex_relax']) ? $val['ex_relax'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['ex_wr_oligomer']) ? $val['ex_wr_oligomer'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['ex_dyeing']) ? $val['ex_dyeing'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['wr_pcnt']) ? $val['wr_pcnt'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['rpm']) ? $val['rpm'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['density']) ? $val['density'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['jamur']) ? $val['jamur'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['karat']) ? $val['karat'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['over_feed']) ? $val['over_feed'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['lebar_jadi']) ? $val['lebar_jadi'] : '-') ?></td>
                            <td><?= Html::encode(isset($val['info_kualitas']) ? $val['info_kualitas'] : '-') ?></td>
                            <td>
                                <button type="button" class="btn btn-default btn-xs btn-edit-row"
                                    data-target="pfp"
                                    data-wo="<?= $orderPfp ? Html::encode($orderPfp->no) : '' ?>"
                                    data-wo-id="<?= $orderPfp ? Html::encode($orderPfp->id) : '' ?>"
                                    data-nk="<?= Html::encode($record->kartuProcess->no) ?>"
                                    data-nk-id="<?= Html::encode($record->kartuProcess->id) ?>"
                                    data-motif="<?= $greige ? Html::encode($greige->nama_kain) : '' ?>"
                                    data-warna="<?= $orderPfp ? Html::encode($orderPfp->dasar_warna) : '' ?>"
                                    data-proses="<?= Html::encode($record->process->nama_proses) ?>"
                                    data-start="<?= Html::encode(isset($val['start']) ? $val['start'] : '') ?>"
                                    data-stop="<?= Html::encode(isset($val['stop']) ? $val['stop'] : '') ?>"
                                    data-nomesin="<?= Html::encode(isset($val['no_mesin']) ? $val['no_mesin'] : '') ?>"
                                    data-temp="<?= Html::encode(isset($val['temp']) ? $val['temp'] : '') ?>"
                                    data-speed="<?= Html::encode(isset($val['speed']) ? $val['speed'] : '') ?>"
                                    data-waktu=""
                                    data-program="<?= Html::encode(isset($val['program_number']) ? $val['program_number'] : '') ?>"
                                    data-exrelax="<?= Html::encode(isset($val['ex_relax']) ? $val['ex_relax'] : '') ?>"
                                    data-exwroligomer="<?= Html::encode(isset($val['ex_wr_oligomer']) ? $val['ex_wr_oligomer'] : '') ?>"
                                    data-exdyeing="<?= Html::encode(isset($val['ex_dyeing']) ? $val['ex_dyeing'] : '') ?>"
                                    data-wrpcnt="<?= Html::encode(isset($val['wr_pcnt']) ? $val['wr_pcnt'] : '') ?>"
                                    data-rpm="<?= Html::encode(isset($val['rpm']) ? $val['rpm'] : '') ?>"
                                    data-density="<?= Html::encode(isset($val['density']) ? $val['density'] : '') ?>"
                                    data-jamur="<?= Html::encode(isset($val['jamur']) ? $val['jamur'] : '') ?>"
                                    data-karat="<?= Html::encode(isset($val['karat']) ? $val['karat'] : '') ?>"
                                    data-overfeed="<?= Html::encode(isset($val['over_feed']) ? $val['over_feed'] : '') ?>"
                                    data-counter=""
                                    data-lebarjadi="<?= Html::encode(isset($val['lebar_jadi']) ? $val['lebar_jadi'] : '') ?>"
                                    data-infokualitas="<?= Html::encode(isset($val['info_kualitas']) ? $val['info_kualitas'] : '') ?>"
                                    data-gangguanproduksi=""
                                    data-gramasi=""
                                    data-panjangjadi=""
                                    data-keterangan=""
                                    title="Edit via Tambahan Input"
                                ><i class="glyphicon glyphicon-pencil"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= Html::beginForm(['save-input'], 'post', ['id' => 'form-tambahan-input']) ?>
<?= Html::hiddenInput('jenis_mesin', $jenis_mesin) ?>
<?= Html::hiddenInput('tanggal', $tanggal) ?>
<?= Html::hiddenInput('shift', $shift) ?>
<?php 
if (is_array($no_mesin)) {
    foreach ($no_mesin as $nm) {
        echo Html::hiddenInput('no_mesin[]', $nm);
    }
}
?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">TAMBAHAN INPUT</h3>
    </div>
    <div class="box-body">
        
        <h4>INPUT DYEING</h4>
        <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>WO</th>
                    <th>NK</th>
                    <th>Motif</th>
                    <th>Warna</th>
                    <th>Proses</th>
                    <th>Start</th>
                    <th>Stop</th>
                    <th>No Mesin</th>
                    <th>Temp</th>
                    <th>Speed</th>
                    <th>Gramasi</th>
                    <th>Program Number</th>
                    <th>Density</th>
                    <th>Over Feed</th>
                    <th>Lebar Jadi</th>
                    <th>Panjang Jadi</th>
                    <th>Info Kualitas</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tbody-input-dyeing">
                <tr>
                    <td style="min-width: 150px;">
                        <?= Select2::widget([
                            'name' => 'InputDyeing[0][wo]',
                            'options' => ['id' => 'dyeing-wo-input', 'class' => 'input-wo-dyeing', 'placeholder' => 'Cari WO...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 3,
                                'ajax' => [
                                    'url' => \yii\helpers\Url::to(['/ajax/lookup-wo-all']),
                                    'dataType' => 'json',
                                    'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                                ],
                            ],
                        ]) ?>
                    </td>
                    <td style="min-width: 150px;">
                        <?= Select2::widget([
                            'name' => 'InputDyeing[0][nk]',
                            'options' => ['id' => 'dyeing-nk-input', 'class' => 'input-nk-dyeing', 'placeholder' => 'Cari NK...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 0,
                                'ajax' => [
                                    'url' => \yii\helpers\Url::to(['/ajax/lookup-nk-all']),
                                    'dataType' => 'json',
                                    'data' => new \yii\web\JsExpression('function(params) { 
                                        var wo_no = $("#dyeing-wo-input option:selected").text();
                                        return {q:params.term, wo_no: wo_no}; 
                                    }')
                                ],
                            ],
                        ]) ?>
                    </td>
                    <td style="min-width: 110px; width: 110px;"><input type="text" id="dyeing-motif-input" class="form-control" readonly></td>
                    <td style="min-width: 110px; width: 110px;"><input type="text" id="dyeing-warna-input" class="form-control" readonly></td>
                    <td style="min-width: 150px;">
                        <?= Select2::widget([
                            'name' => 'InputDyeing[0][proses]',
                            'data' => $prosesDyeing,
                            'options' => ['id' => 'dyeing-proses-input', 'class' => 'input-proses-dyeing', 'placeholder' => 'Pilih Proses...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]) ?>
                    </td>
                    <td><input type="time" name="InputDyeing[0][start]" class="form-control"></td>
                    <td><input type="time" name="InputDyeing[0][stop]" class="form-control"></td>
                    <td style="min-width: 80px; width: 80px;">
                        <?= Html::dropDownList('InputDyeing[0][no_mesin]', null, $noMesinList, ['class' => 'form-control', 'prompt' => 'Pilih...']) ?>
                    </td>
                    <td style="min-width: 80px; width: 80px;"><input type="text" name="InputDyeing[0][temp]" class="form-control"></td>
                    <td style="min-width: 80px; width: 80px;"><input type="text" name="InputDyeing[0][speed]" class="form-control"></td>
                    <td><input type="text" name="InputDyeing[0][gramasi]" class="form-control"></td>
                    <td><input type="text" name="InputDyeing[0][program_number]" class="form-control"></td>
                    <td style="min-width: 80px; width: 80px;"><input type="text" name="InputDyeing[0][density]" class="form-control"></td>
                    <td style="min-width: 80px; width: 80px;"><input type="text" name="InputDyeing[0][over_feed]" class="form-control"></td>
                    <td><input type="text" name="InputDyeing[0][lebar_jadi]" class="form-control"></td>
                    <td style="min-width: 80px; width: 80px;"><input type="text" name="InputDyeing[0][panjang_jadi]" class="form-control"></td>
                    <td><input type="text" name="InputDyeing[0][info_kualitas]" class="form-control"></td>
                    <td><input type="text" name="InputDyeing[0][keterangan]" class="form-control"></td>
                    <td><button type="button" class="btn btn-danger btn-sm btn-hapus-row"><i class="glyphicon glyphicon-trash"></i></button></td>
                </tr>
            </tbody>
        </table>
        </div>
        <button type="button" class="btn btn-success btn-sm btn-tambah-row" data-target="dyeing"><i class="glyphicon glyphicon-plus"></i> Tambah Set Inputan</button>

        <hr>

        <h4>INPUT PFP</h4>
        <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>WO</th>
                    <th>NK</th>
                    <th>Motif</th>
                    <th>Warna</th>
                    <th>Proses</th>
                    <th>Start</th>
                    <th>Stop</th>
                    <th>No Mesin</th>
                    <th>Temp</th>
                    <th>Speed</th>
                    <th>Gramasi</th>
                    <th>Program Number</th>
                    <th>Ex Relax</th>
                    <th>Ex Wr Oligomer</th>
                    <th>Ex Dyeing</th>
                    <th>WR %</th>
                    <th>Rpm</th>
                    <th>Density</th>
                    <th>Jamur</th>
                    <th>Karat</th>
                    <th>Over Feed</th>
                    <th>Lebar Jadi</th>
                    <th>Panjang Jadi</th>
                    <th>Info Kualitas</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tbody-input-pfp">
                <tr>
                    <td style="min-width: 150px;">
                        <?= Select2::widget([
                            'name' => 'InputPfp[0][wo]',
                            'options' => ['id' => 'pfp-wo-input', 'class' => 'input-wo-pfp', 'placeholder' => 'Cari Order PFP...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 3,
                                'ajax' => [
                                    'url' => \yii\helpers\Url::to(['/ajax/lookup-order-pfp']),
                                    'dataType' => 'json',
                                    'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                                ],
                            ],
                        ]) ?>
                    </td>
                    <td style="min-width: 150px;">
                        <?= Select2::widget([
                            'name' => 'InputPfp[0][nk]',
                            'options' => ['id' => 'pfp-nk-input', 'class' => 'input-nk-pfp', 'placeholder' => 'Cari NK...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 0,
                                'ajax' => [
                                    'url' => \yii\helpers\Url::to(['/ajax/lookup-nk-all']),
                                    'dataType' => 'json',
                                    'data' => new \yii\web\JsExpression('function(params) { 
                                        var wo_no = $("#pfp-wo-input option:selected").text();
                                        return {q:params.term, wo_no: wo_no}; 
                                    }')
                                ],
                            ],
                        ]) ?>
                    </td>
                    <td style="min-width: 110px; width: 110px;"><input type="text" id="pfp-motif-input" class="form-control" readonly></td>
                    <td style="min-width: 110px; width: 110px;"><input type="text" id="pfp-warna-input" class="form-control" readonly></td>
                    <td style="min-width: 150px;">
                        <?= Select2::widget([
                            'name' => 'InputPfp[0][proses]',
                            'data' => $prosesPfp,
                            'options' => ['id' => 'pfp-proses-input', 'class' => 'input-proses-pfp', 'placeholder' => 'Pilih Proses...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]) ?>
                    </td>
                    <td><input type="time" name="InputPfp[0][start]" class="form-control"></td>
                    <td><input type="time" name="InputPfp[0][stop]" class="form-control"></td>
                    <td style="min-width: 80px; width: 80px;">
                        <?= Html::dropDownList('InputPfp[0][no_mesin]', null, $noMesinList, ['class' => 'form-control', 'prompt' => 'Pilih...']) ?>
                    </td>
                    <td style="min-width: 80px; width: 80px;"><input type="text" name="InputPfp[0][temp]" class="form-control"></td>
                    <td style="min-width: 80px; width: 80px;"><input type="text" name="InputPfp[0][speed]" class="form-control"></td>
                    <td><input type="text" name="InputPfp[0][gramasi]" class="form-control"></td>
                    <td><input type="text" name="InputPfp[0][program_number]" class="form-control"></td>
                    <td style="min-width: 80px; width: 80px;"><input type="text" name="InputPfp[0][ex_relax]" class="form-control"></td>
                    <td><input type="text" name="InputPfp[0][ex_wr_oligomer]" class="form-control"></td>
                    <td style="min-width: 80px; width: 80px;"><input type="text" name="InputPfp[0][ex_dyeing]" class="form-control"></td>
                    <td style="min-width: 80px; width: 80px;"><input type="text" name="InputPfp[0][wr_pcnt]" class="form-control"></td>
                    <td style="min-width: 80px; width: 80px;"><input type="text" name="InputPfp[0][rpm]" class="form-control"></td>
                    <td style="min-width: 80px; width: 80px;"><input type="text" name="InputPfp[0][density]" class="form-control"></td>
                    <td style="min-width: 80px; width: 80px;"><input type="text" name="InputPfp[0][jamur]" class="form-control"></td>
                    <td style="min-width: 80px; width: 80px;"><input type="text" name="InputPfp[0][karat]" class="form-control"></td>
                    <td style="min-width: 80px; width: 80px;"><input type="text" name="InputPfp[0][over_feed]" class="form-control"></td>
                    <td><input type="text" name="InputPfp[0][lebar_jadi]" class="form-control"></td>
                    <td style="min-width: 80px; width: 80px;"><input type="text" name="InputPfp[0][panjang_jadi]" class="form-control"></td>
                    <td><input type="text" name="InputPfp[0][info_kualitas]" class="form-control"></td>
                    <td><input type="text" name="InputPfp[0][keterangan]" class="form-control"></td>
                    <td><button type="button" class="btn btn-danger btn-sm btn-hapus-row"><i class="glyphicon glyphicon-trash"></i></button></td>
                </tr>
            </tbody>
        </table>
        </div>
        <button type="button" class="btn btn-success btn-sm btn-tambah-row" data-target="pfp"><i class="glyphicon glyphicon-plus"></i> Tambah Set Inputan</button>

    </div>
    <div class="box-footer">
        <button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-save"></i> Simpan Data Input</button>
    </div>
</div>
<?= Html::endForm() ?>
<?php endif; ?>
