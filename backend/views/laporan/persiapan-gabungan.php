<?php

use kartik\daterange\DateRangePicker;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ArrayDataProvider */
/* @var $mesinOptions array */
/* @var $mcFilter array */
/* @var $shiftPagiFilter string */
/* @var $shiftSiangFilter string */
/* @var $tanggalFilter string */

$this->title = 'Laporan Persiapan (Gabungan)';
$this->params['breadcrumbs'][] = $this->title;

$shiftGroupOptions = [
    'A' => 'A',
    'B' => 'B',
    'C' => 'C',
    'D' => 'D'
];

$urlLookupWo = \yii\helpers\Url::to(['/ajax/lookup-wo-dyeing']);
$urlGetInfoWo = \yii\helpers\Url::to(['/laporan/get-info-by-wo']);

$urlLookupOrderPfp = \yii\helpers\Url::to(['/ajax/lookup-order-pfp']);
$urlGetInfoOrderPfp = \yii\helpers\Url::to(['/laporan/get-info-by-order-pfp']);
$urlUpdateNamaWarnaTrn = \yii\helpers\Url::to(['/processing-dyeing/update-nama-warna-trn']);


$mesinOptionsJson = json_encode($mesinInputOptions);

$js = <<<JS
$('.toggle-hide-btn').click(function(e) {
    e.preventDefault();
    $(this).closest('tr').addClass('hidden-print').fadeOut();
});

$(document).on('click', '#btn-unhide-all', function(e) {
    e.preventDefault();
    $('tr.hidden-print').removeClass('hidden-print').fadeIn();
});

var rowIndex = 0;
$(document).on('click', '.edit-row-btn', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    var tipe = $(this).data('tipe');
    var tanggal = $(this).data('tanggal');
    var woNo = $(this).data('wono');
    var motif = $(this).data('motif');
    var warna = $(this).data('warna');
    var noKartu = $(this).data('nokartu');
    var pbg = $(this).data('pbg');
    var shift = $(this).data('shift');
    var mc = $(this).data('mc');
    var ket = $(this).data('ket');
    var hasDataCls = (tanggal || shift || mc || ket) ? ' has-existing-data' : '';
    
    var mesinOptions = {$mesinOptionsJson};
    var mcOptionsHtml = '<option value="">-</option>';
    $.each(mesinOptions, function(k, v) {
        var sel = (v === mc) ? ' selected' : '';
        mcOptionsHtml += '<option value="'+v+'"'+sel+'>'+v+'</option>';
    });
    var mcHtml = '<select name="Inputan['+rowIndex+'][no_mesin]" class="form-control input-sm select2-mc">'+mcOptionsHtml+'</select>';
    
    var shiftHtml = '<select name="Inputan['+rowIndex+'][shift_operator]" class="form-control input-sm">' +
        '<option value="">-</option>' +
        '<option value="A" '+(shift==='A'?'selected':'')+'>A</option>' +
        '<option value="B" '+(shift==='B'?'selected':'')+'>B</option>' +
        '<option value="C" '+(shift==='C'?'selected':'')+'>C</option>' +
        '<option value="D" '+(shift==='D'?'selected':'')+'>D</option>' +
    '</select>';
    
    var rowHtml = '<tr id="row-'+rowIndex+'" class="'+hasDataCls+'">' +
        '<td><input type="date" name="Inputan['+rowIndex+'][tanggal]" class="form-control input-sm" value="'+tanggal+'"></td>' +
        '<td><input type="text" class="form-control input-sm" value="'+woNo+'" readonly></td>' +
        '<td><input type="text" class="form-control input-sm motif-input" value="'+motif+'" readonly></td>' +
        '<td><input type="text" class="form-control input-sm" value="'+noKartu+'" readonly><input type="hidden" name="Inputan['+rowIndex+'][kartu_process_id]" class="kartu-process-id" value="'+id+'"><input type="hidden" name="Inputan['+rowIndex+'][tipe]" value="'+tipe+'"></td>' +
        '<td><input type="text" class="form-control input-sm" value="'+warna+'" readonly></td>' +
        '<td><input type="text" class="form-control input-sm pbg-input" value="'+pbg+'" readonly></td>' +
        '<td>' + shiftHtml + '</td>' +
        '<td>' + mcHtml + '</td>' +
        '<td><input type="text" name="Inputan['+rowIndex+'][keterangan]" class="form-control input-sm" value="'+ket+'"></td>' +
        '<td><button type="button" class="btn btn-danger btn-sm delete-row-btn"><i class="glyphicon glyphicon-trash"></i></button></td>' +
    '</tr>';
    
    var rowEl = $(rowHtml);
    $('#inputan-table tbody').append(rowEl);
    
    rowEl.find('.select2-mc').select2({
        placeholder: 'Pilih MC',
        allowClear: true,
        width: '100%'
    });
    rowIndex++;
    
    $('html, body').animate({
        scrollTop: $("#form-tambahan-input").offset().top
    }, 500);
});

window.addManualRow = function(tipeLaporan) {
    var woHtml = '';
    var warnaHtml = '';
    var noKartuHtml = '<select name="Inputan['+rowIndex+'][nomor_kartu]" class="form-control input-sm select-nk"><option value="">-</option></select>';
    var ajaxUrl = '';
    var ajaxDataFn = null;
    
    if (tipeLaporan === 'Dyeing') {
        woHtml = '<select name="Inputan['+rowIndex+'][wo]" class="form-control input-sm select2-wo" style="width: 100%;"></select>';
        warnaHtml = '<select name="Inputan['+rowIndex+'][warna]" class="form-control input-sm select-warna"><option value="">-</option></select>';
        ajaxUrl = '{$urlLookupWo}';
    } else {
        woHtml = '<select name="Inputan['+rowIndex+'][wo]" class="form-control input-sm select2-order" style="width: 100%;"></select>';
        warnaHtml = '<input type="text" class="form-control input-sm" value="-" readonly>';
        ajaxUrl = '{$urlLookupOrderPfp}';
    }
    
    var mesinOptions = {$mesinOptionsJson};
    var mcOptionsHtml = '<option value="">-</option>';
    $.each(mesinOptions, function(k, v) {
        mcOptionsHtml += '<option value="'+v+'">'+v+'</option>';
    });
    var mcHtml = '<select name="Inputan['+rowIndex+'][no_mesin]" class="form-control input-sm select2-mc">'+mcOptionsHtml+'</select>';
    
    var shiftHtml = '<select name="Inputan['+rowIndex+'][shift_operator]" class="form-control input-sm">' +
        '<option value="">-</option>' +
        '<option value="A">A</option>' +
        '<option value="B">B</option>' +
        '<option value="C">C</option>' +
        '<option value="D">D</option>' +
    '</select>';
    
    var rowHtml = '<tr id="row-'+rowIndex+'">' +
        '<td><input type="date" name="Inputan['+rowIndex+'][tanggal]" class="form-control input-sm" value=""></td>' +
        '<td>' + woHtml + '</td>' +
        '<td><input type="text" class="form-control input-sm motif-input" value="" readonly></td>' +
        '<td>' + noKartuHtml + '<input type="hidden" name="Inputan['+rowIndex+'][kartu_process_id]" class="kartu-process-id" value=""><input type="hidden" name="Inputan['+rowIndex+'][tipe]" value="'+tipeLaporan+'"></td>' +
        '<td>' + warnaHtml + '</td>' +
        '<td><input type="text" class="form-control input-sm pbg-input" value="" readonly></td>' +
        '<td>' + shiftHtml + '</td>' +
        '<td>' + mcHtml + '</td>' +
        '<td><input type="text" name="Inputan['+rowIndex+'][keterangan]" class="form-control input-sm" value=""></td>' +
        '<td><button type="button" class="btn btn-danger btn-sm delete-row-btn"><i class="glyphicon glyphicon-trash"></i></button></td>' +
    '</tr>';
    
    var rowEl = $(rowHtml);
    $('#inputan-table tbody').append(rowEl);
    
    rowEl.find('.select2-mc').select2({
        placeholder: 'Pilih MC',
        allowClear: true,
        width: '100%'
    });
    
    if (tipeLaporan === 'Dyeing') {
        rowEl.find('.select2-wo').select2({
            ajax: {
                url: ajaxUrl,
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            },
            placeholder: 'Pilih WO Dyeing',
            allowClear: true
        }).on('change', function() {
            var wo_id = $(this).val();
            var currentRowEl = $(this).closest('tr');
            if (wo_id) {
                $.get('{$urlGetInfoWo}', {wo_id: wo_id}, function(res) {
                    if (res.success) {
                        currentRowEl.find('.motif-input').val(res.motif);
                        var warnaOpt = '<option value="">Pilih Warna</option>';
                        res.colors.forEach(function(c) { warnaOpt += '<option value="'+c+'">'+c+'</option>'; });
                        currentRowEl.find('.select-warna').html(warnaOpt);
                        currentRowEl.data('nks', res.nks);
                        var nkOpt = '<option value="">Pilih NK</option>';
                        res.nks.forEach(function(nk) { nkOpt += '<option value="'+nk.nomor_kartu+'">'+nk.nomor_kartu+'</option>'; });
                        currentRowEl.find('.select-nk').html(nkOpt);
                    }
                });
            }
        });
        
        rowEl.find('.select-warna').on('change', function() {
            // Removed filtering of NK based on Warna as requested
        });
        
    } else {
        rowEl.find('.select2-order').select2({
            ajax: {
                url: ajaxUrl,
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            },
            placeholder: 'Pilih Order PFP',
            allowClear: true
        }).on('change', function() {
            var order_id = $(this).val();
            var currentRowEl = $(this).closest('tr');
            if (order_id) {
                $.get('{$urlGetInfoOrderPfp}', {order_pfp_id: order_id}, function(res) {
                    if (res.success) {
                        currentRowEl.find('.motif-input').val(res.motif);
                        currentRowEl.data('nks', res.nks);
                        var nkOpt = '<option value="">Pilih NK</option>';
                        res.nks.forEach(function(nk) { nkOpt += '<option value="'+nk.nomor_kartu+'">'+nk.nomor_kartu+'</option>'; });
                        currentRowEl.find('.select-nk').html(nkOpt);
                    }
                });
            }
        });
    }
    
    rowEl.find('.select-nk').on('change', function() {
        var noKartu = $(this).val();
        var currentRowEl = $(this).closest('tr');
        var nks = currentRowEl.data('nks') || [];
        var selectedNk = nks.find(function(n) { return n.nomor_kartu === noKartu; });
        if (selectedNk) {
            currentRowEl.find('.pbg-input').val(selectedNk.pbg);
            currentRowEl.find('.kartu-process-id').val(selectedNk.id);
            if (currentRowEl.find('.select-warna').length) {
                currentRowEl.find('.select-warna').val(selectedNk.warna);
            }
            if (selectedNk.has_data) {
                currentRowEl.addClass('has-existing-data');
            } else {
                currentRowEl.removeClass('has-existing-data');
            }
        } else {
            currentRowEl.find('.pbg-input').val('');
            currentRowEl.find('.kartu-process-id').val('');
            if (currentRowEl.find('.select-warna').length) {
                currentRowEl.find('.select-warna').val('');
            }
            currentRowEl.removeClass('has-existing-data');
        }
    });
    
    rowIndex++;
    $('html, body').animate({
        scrollTop: $("#form-tambahan-input").offset().top
    }, 500);
};

$(document).on('click', '#btn-tambah-dyeing', function(e) {
    e.preventDefault();
    addManualRow('Dyeing');
});

$(document).on('click', '#btn-tambah-pfp', function(e) {
    e.preventDefault();
    addManualRow('PFP');
});

$(document).on('click', '.delete-row-btn', function() {
    $(this).closest('tr').remove();
});

$(document).on('click', '.toggle-hide-btn', function(e) {
    e.preventDefault();
    $(this).closest('tr').addClass('hidden-row').hide();
});

$(document).on('click', '#btn-unhide-all', function(e) {
    e.preventDefault();
    $('.hidden-row').removeClass('hidden-row').show();
});

$(document).on('submit', '#form-inputan-bawah', function(e) {
    var form = $(this);
    if (form.data('confirmed')) {
        return true;
    }
    
    var hasExisting = form.find('.has-existing-data').length > 0;
    if (hasExisting) {
        e.preventDefault();
        krajeeDialog.confirm('Beberapa data yang diinput sudah memiliki isi sebelumnya (tanggal/mc/keterangan). Apakah Anda yakin ingin menimpanya?', function(result) {
            if (result) {
                form.data('confirmed', true);
                form.submit();
            }
        });
    }
});
// Modal Nama Warna Trn Logic
var currentTargetNamaWarnaTrn = null;
$(document).on('click', '.btn-edit-nama-warna-trn', function(e) {
    e.preventDefault();
    currentTargetNamaWarnaTrn = $(this);
    
    var id = $(this).data('id');
    var namaWarna = $(this).data('val');
    
    $('#nwt-id').val(id);
    $('#nwt-nama-warna').val(namaWarna);
    
    $('#modal-nama-warna-trn').modal('show');
    
    setTimeout(function() {
        $('#nwt-nama-warna').focus();
    }, 500);
});

$('#form-nama-warna-trn').on('submit', function(e) {
    e.preventDefault();
    var btn = $('#btn-save-nama-warna-trn');
    var originalText = btn.text();
    btn.text('Menyimpan...').prop('disabled', true);
    
    $.ajax({
        url: '{$urlUpdateNamaWarnaTrn}',
        type: 'POST',
        data: $(this).serialize(),
        success: function(res) {
            btn.text(originalText).prop('disabled', false);
            if (res.success) {
                $('#modal-nama-warna-trn').modal('hide');
                if (currentTargetNamaWarnaTrn) {
                    currentTargetNamaWarnaTrn.data('val', res.nama_warna);
                    if (res.nama_warna && res.nama_warna.trim() !== '') {
                        currentTargetNamaWarnaTrn.text(res.nama_warna);
                    } else {
                        currentTargetNamaWarnaTrn.html('<i class="glyphicon glyphicon-pencil text-muted"></i> Isi Warna');
                    }
                }
            } else {
                alert(res.message || 'Gagal menyimpan data.');
            }
        },
        error: function() {
            btn.text(originalText).prop('disabled', false);
            alert('Terjadi kesalahan pada server.');
        }
    });
});
JS;
$this->registerJs($js);

$css = <<<CSS
@media print {
    .hidden-print {
        display: none !important;
    }
}
CSS;
$this->registerCss($css);
?>
<div class="laporan-persiapan-gabungan">
    
    <div class="box box-primary hidden-print">
        <div class="box-header with-border">
            <h3 class="box-title">Filter Pencarian</h3>
        </div>
        <div class="box-body">
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['persiapan-gabungan'],
            ]); ?>
            
            <div class="row">
                <div class="col-md-3">
                    <label>Filter MC</label>
                    <?= Select2::widget([
                        'name' => 'mcFilter',
                        'value' => $mcFilter,
                        'data' => $mesinOptions,
                        'options' => ['placeholder' => 'Pilih MC ...', 'multiple' => true],
                        'pluginOptions' => ['allowClear' => true],
                    ]) ?>
                </div>
                <div class="col-md-2">
                    <label>Filter Shift Pagi</label>
                    <?= Html::dropDownList('shiftPagiFilter', $shiftPagiFilter, $shiftGroupOptions, ['class' => 'form-control', 'prompt' => 'Pilih Shift...']) ?>
                </div>
                <div class="col-md-2">
                    <label>Filter Shift Siang</label>
                    <?= Html::dropDownList('shiftSiangFilter', $shiftSiangFilter, $shiftGroupOptions, ['class' => 'form-control', 'prompt' => 'Pilih Shift...']) ?>
                </div>
                <div class="col-md-3">
                    <label>Tanggal Proses</label>
                    <?= DateRangePicker::widget([
                        'name' => 'tanggalFilter',
                        'value' => $tanggalFilter,
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'locale' => ['format' => 'Y-m-d'],
                            'allowClear' => true
                        ],
                        'options' => ['class' => 'form-control', 'placeholder' => 'Pilih rentang tanggal...']
                    ]) ?>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <?= Html::submitButton('Tampilkan', ['class' => 'btn btn-primary btn-block']) ?>
                </div>
            </div>
            
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    
    <div class="box">
        <div class="box-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $filterModel,
                'id' => 'gabungan-grid',
                'showPageSummary' => true,
                'panel' => [
                    'type' => GridView::TYPE_DEFAULT,
                    'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Data Persiapan Gabungan</h3>',
                ],
                'toolbar' => [
                    [
                        'content' => Html::button('<i class="glyphicon glyphicon-eye-open"></i> Tampilkan Baris Tersembunyi', ['class' => 'btn btn-info', 'id' => 'btn-unhide-all']) . ' ' . 
                        Html::a('<i class="glyphicon glyphicon-print"></i> Print', ['print-persiapan-gabungan', 'mcFilter' => $mcFilter, 'shiftPagiFilter' => $shiftPagiFilter, 'shiftSiangFilter' => $shiftSiangFilter, 'tanggalFilter' => $tanggalFilter], [
                            'class' => 'btn btn-default',
                            'title' => 'Print Report',
                            'target' => '_blank'
                        ]),
                        'options' => ['class' => 'btn-group mr-2']
                    ],
                ],
                'columns' => [
                    ['class' => 'kartik\grid\SerialColumn'],
                    [
                        'attribute' => 'gabungan_shift',
                        'label' => 'SHIFT',
                        'value' => function($data) {
                            $processId = $data->tipe_laporan === 'Dyeing' ? 1 : (\common\models\ar\MstProcessPfp::find()->select('id')->where(['order'=>1])->scalar() ?: 1);
                            $kpdp = $data->tipe_laporan === 'Dyeing' ? $data->getKartuProcessDyeingProcesses()->where(['process_id'=>$processId])->one() : $data->getKartuProcessPfpProcesses()->where(['process_id'=>$processId])->one();
                            if($kpdp){
                                try{
                                    $json = \yii\helpers\Json::decode($kpdp->value);
                                    if($data->tipe_laporan === 'Dyeing' && !empty($json['shift_group'])) return $json['shift_group'];
                                    if($data->tipe_laporan === 'PFP' && !empty($json['shift_operator'])) return $json['shift_operator'];
                                }catch(\Throwable $t){}
                            }
                            return '-';
                        }
                    ],
                    [
                        'attribute' => 'gabungan_tanggal',
                        'label' => 'Tanggal',
                        'value' => function($data) {
                            $processId = $data->tipe_laporan === 'Dyeing' ? 1 : (\common\models\ar\MstProcessPfp::find()->select('id')->where(['order'=>1])->scalar() ?: 1);
                            $kpdp = $data->tipe_laporan === 'Dyeing' ? $data->getKartuProcessDyeingProcesses()->where(['process_id'=>$processId])->one() : $data->getKartuProcessPfpProcesses()->where(['process_id'=>$processId])->one();
                            if($kpdp){
                                try{
                                    $json = \yii\helpers\Json::decode($kpdp->value);
                                    if(!empty($json['tanggal'])) return date('j M Y', strtotime($json['tanggal']));
                                }catch(\Throwable $t){}
                            }
                            $fallbackDate = $data->tipe_laporan === 'Dyeing' ? $data->tanggalKartuProcessDyeingProcess : $data->tanggalKartuProcessPfpProcess;
                            return $fallbackDate ? date('j M Y', strtotime($fallbackDate)) : '-';
                        }
                    ],
                    [
                        'attribute' => 'gabungan_nomor_wo',
                        'label' => 'Nomor WO',
                        'value' => function($data) {
                            if($data->tipe_laporan === 'Dyeing'){
                                return $data->wo ? $data->wo->no : '-';
                            }else{
                                return $data->orderPfp ? $data->orderPfp->no : '-';
                            }
                        }
                    ],
                    [
                        'label' => 'Motif',
                        'value' => function($data) {
                            $lusi = $data->lusi ?? '';
                            $pakan = $data->pakan ?? '';
                            if($data->tipe_laporan === 'Dyeing'){
                                $motifName = $data->wo->greigeNamaKain ?? '';
                            }else{
                                $motifName = $data->greige->nama_kain ?? '';
                            }
                            return trim($lusi . ' ' . $motifName . ' ' . $pakan);
                        }
                    ],
                    [
                        'label' => 'Warna',
                        'value' => function($data) {
                            if($data->tipe_laporan === 'Dyeing'){
                                return $data->woColor->moColor->color ?? '-';
                            }
                            return '-';
                        }
                    ],
                    [
                        'attribute' => 'nama_warna',
                        'label' => 'Nama Warna',
                        'value' => function($data) {
                            if($data->tipe_laporan === 'Dyeing'){
                                $val = !empty($data->nama_warna) ? $data->nama_warna : '';
                                $display = $val !== '' ? $val : '<i class="glyphicon glyphicon-pencil text-muted"></i> Isi Warna';
                                return \yii\helpers\Html::a($display, 'javascript:void(0)', [
                                    'class' => 'btn-edit-nama-warna-trn',
                                    'data-id' => $data->id,
                                    'data-val' => $val,
                                    'title' => 'Klik untuk edit Nama Warna'
                                ]);
                            }
                            return '-';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Nomor Kartu',
                        'attribute' => 'nomor_kartu',
                        'format' => 'raw',
                        'value' => function($data) {
                            if($data->tipe_laporan === 'Dyeing'){
                                return Html::a($data->nomor_kartu, ['/trn-kartu-proses-dyeing/view', 'id' => $data->id], ['target' => '_blank', 'title' => 'Lihat Detail NK', 'data-pjax' => 0]);
                            }else{
                                return Html::a($data->nomor_kartu, ['/trn-kartu-proses-pfp/view', 'id' => $data->id], ['target' => '_blank', 'title' => 'Lihat Detail NK', 'data-pjax' => 0]);
                            }
                        }
                    ],
                    [
                        'label' => 'Pjg',
                        'format' => ['decimal', 0],
                        'pageSummary' => true,
                        'value' => function($data) {
                            if($data->tipe_laporan === 'Dyeing'){
                                $panjangTotal = $data->getTrnKartuProsesDyeingItems()->sum('panjang_m') ?: 0;
                            }else{
                                $panjangTotal = $data->getTrnKartuProsesPfpItems()->sum('panjang_m') ?: 0;
                            }
                            return (float)$panjangTotal;
                        }
                    ],
                    [
                        'label' => 'Berat',
                        'format' => ['decimal', 1],
                        'pageSummary' => true,
                        'value' => function($data) {
                            return (float)($data->berat ?: 0);
                        }
                    ],
                    [
                        'label' => 'Gul',
                        'pageSummary' => true,
                        'value' => function($data) {
                            if($data->tipe_laporan === 'Dyeing'){
                                return $data->getTrnKartuProsesDyeingItems()->count('id') ?: 0;
                            }else{
                                return $data->getTrnKartuProsesPfpItems()->count('id') ?: 0;
                            }
                        }
                    ],
                    [
                        'label' => 'Shift',
                        'value' => function($data) {
                            $processId = $data->tipe_laporan === 'Dyeing' ? 1 : (\common\models\ar\MstProcessPfp::find()->select('id')->where(['order'=>1])->scalar() ?: 1);
                            $kpdp = $data->tipe_laporan === 'Dyeing' ? $data->getKartuProcessDyeingProcesses()->where(['process_id'=>$processId])->one() : $data->getKartuProcessPfpProcesses()->where(['process_id'=>$processId])->one();
                            if($kpdp){
                                try{
                                    $json = \yii\helpers\Json::decode($kpdp->value);
                                    if(!empty($json['shift_operator'])) return $json['shift_operator'];
                                }catch(\Throwable $t){}
                            }
                            return '-';
                        }
                    ],
                    [
                        'label' => 'MC',
                        'value' => function($data) {
                            $processId = $data->tipe_laporan === 'Dyeing' ? 1 : (\common\models\ar\MstProcessPfp::find()->select('id')->where(['order'=>1])->scalar() ?: 1);
                            $kpdp = $data->tipe_laporan === 'Dyeing' ? $data->getKartuProcessDyeingProcesses()->where(['process_id'=>$processId])->one() : $data->getKartuProcessPfpProcesses()->where(['process_id'=>$processId])->one();
                            if($kpdp){
                                try{
                                    $json = \yii\helpers\Json::decode($kpdp->value);
                                    if(!empty($json['no_mesin'])) return $json['no_mesin'];
                                }catch(\Throwable $t){}
                            }
                            return '-';
                        }
                    ],
                    [
                        'label' => 'Ket.',
                        'value' => function($data) {
                            $processId = $data->tipe_laporan === 'Dyeing' ? 1 : (\common\models\ar\MstProcessPfp::find()->select('id')->where(['order'=>1])->scalar() ?: 1);
                            $kpdp = $data->tipe_laporan === 'Dyeing' ? $data->getKartuProcessDyeingProcesses()->where(['process_id'=>$processId])->one() : $data->getKartuProcessPfpProcesses()->where(['process_id'=>$processId])->one();
                            if($kpdp){
                                try{
                                    $json = \yii\helpers\Json::decode($kpdp->value);
                                    if(!empty($json['keterangan'])) return $json['keterangan'];
                                }catch(\Throwable $t){}
                            }
                            return '-';
                        }
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{edit} {hide}',
                        'headerOptions' => ['class' => 'hidden-print'],
                        'contentOptions' => ['class' => 'hidden-print'],
                        'buttons' => [
                            'edit' => function ($url, $model, $key) {
                                $isDyeing = $model->tipe_laporan === 'Dyeing';
                                
                                $processId = $isDyeing ? 1 : (\common\models\ar\MstProcessPfp::find()->select('id')->where(['order'=>1])->scalar() ?: 1);
                                $kpdp = $isDyeing ? $model->getKartuProcessDyeingProcesses()->where(['process_id'=>$processId])->one() : $model->getKartuProcessPfpProcesses()->where(['process_id'=>$processId])->one();
                                
                                $tanggal = '-';
                                $shift = '';
                                $mc = '';
                                $ket = '';
                                if($kpdp){
                                    try{
                                        $json = \yii\helpers\Json::decode($kpdp->value);
                                        if(!empty($json['tanggal'])) $tanggal = $json['tanggal'];
                                        if(!empty($json['shift_operator'])) $shift = $json['shift_operator'];
                                        if(!empty($json['no_mesin'])) $mc = $json['no_mesin'];
                                        if(!empty($json['keterangan'])) $ket = $json['keterangan'];
                                    }catch(\Throwable $t){}
                                }
                                if($tanggal === '-'){
                                    $fallbackDate = $isDyeing ? $model->tanggalKartuProcessDyeingProcess : $model->tanggalKartuProcessPfpProcess;
                                    if($fallbackDate) $tanggal = $fallbackDate;
                                }

                                $woNo = $isDyeing ? ($model->wo ? $model->wo->no : '-') : ($model->orderPfp ? $model->orderPfp->no : '-');
                                
                                $lusi = $model->lusi ?? '';
                                $pakan = $model->pakan ?? '';
                                $motifName = $isDyeing ? ($model->wo->greigeNamaKain ?? '') : ($model->greige->nama_kain ?? '');
                                $motif = trim($lusi . ' ' . $motifName . ' ' . $pakan);
                                
                                $warna = $isDyeing ? ($model->woColor->moColor->color ?? '-') : '-';
                                
                                if($isDyeing){
                                    $panjangTotal = $model->getTrnKartuProsesDyeingItems()->sum('panjang_m') ?: 0;
                                    $jumlahRoll = $model->getTrnKartuProsesDyeingItems()->count('id') ?: 0;
                                }else{
                                    $panjangTotal = $model->getTrnKartuProsesPfpItems()->sum('panjang_m') ?: 0;
                                    $jumlahRoll = $model->getTrnKartuProsesPfpItems()->count('id') ?: 0;
                                }
                                $panjangTotal = number_format((float)$panjangTotal, 0);
                                $berat = number_format((float)($model->berat ?: 0), 1);
                                $pbg = $panjangTotal . ' / ' . $berat . ' / ' . $jumlahRoll;

                                return Html::a('<i class="glyphicon glyphicon-edit"></i> Edit', '#', [
                                    'class' => 'btn btn-default btn-xs edit-row-btn',
                                    'title' => 'Edit',
                                    'data-id' => $model->id,
                                    'data-tipe' => $model->tipe_laporan,
                                    'data-tanggal' => $tanggal,
                                    'data-wono' => $woNo,
                                    'data-motif' => $motif,
                                    'data-warna' => $warna,
                                    'data-nokartu' => $model->nomor_kartu,
                                    'data-pbg' => $pbg,
                                    'data-shift' => $shift,
                                    'data-mc' => $mc,
                                    'data-ket' => $ket,
                                ]);
                            },
                            'hide' => function ($url, $model, $key) {
                                return Html::a('<i class="glyphicon glyphicon-eye-close"></i>', '#', [
                                    'class' => 'btn btn-warning btn-xs toggle-hide-btn',
                                    'title' => 'Sembunyikan',
                                ]);
                            },
                        ],
                    ],
                ]
            ]); ?>
        </div>
    
    <div class="panel panel-default hidden-print" id="form-tambahan-input" style="margin-top: 20px;">
        <div class="panel-heading">
            <h3 class="panel-title" style="font-weight: bold; color: #666;">TAMBAHAN INPUT (GABUNGAN)</h3>
        </div>
        <div class="panel-body">
            <?= Html::beginForm(['update-persiapan-gabungan'], 'post', ['id' => 'form-inputan-bawah']) ?>
            <div class="table-responsive">
                <table class="table table-bordered table-condensed" id="inputan-table">
                    <thead>
                        <tr style="color: #337ab7;">
                            <th>TANGGAL</th>
                            <th>Nomor WO</th>
                            <th>Motif</th>
                            <th>Nomor Kartu</th>
                            <th>Warna</th>
                            <th>Pjg / Berat / Gul</th>
                            <th>Shift</th>
                            <th>Mesin</th>
                            <th>Keterangan</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            
            <div class="form-group">
                <button type="button" class="btn btn-success" id="btn-tambah-dyeing"><i class="glyphicon glyphicon-plus"></i> Tambah Input Dyeing</button>
                <button type="button" class="btn btn-info" id="btn-tambah-pfp"><i class="glyphicon glyphicon-plus"></i> Tambah Input PFP</button>
            </div>
            
            <div class="form-group" style="margin-top: 15px;">
                <?= Html::submitButton('Simpan Data Input', ['class' => 'btn btn-primary']) ?>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>

<!-- Modal Edit Nama Warna Trn -->
<div class="modal fade" id="modal-nama-warna-trn" tabindex="-1" role="dialog" aria-labelledby="modalNamaWarnaTrnLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <?php yii\widgets\ActiveForm::begin(['id' => 'form-nama-warna-trn']); ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalNamaWarnaTrnLabel">Edit Nama Warna</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="nwt-id">
        <div class="form-group">
            <label>Nama Warna</label>
            <input type="text" name="nama_warna" id="nwt-nama-warna" class="form-control" autocomplete="off">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" id="btn-save-nama-warna-trn">Simpan</button>
      </div>
      <?php yii\widgets\ActiveForm::end(); ?>
    </div>
  </div>
</div>