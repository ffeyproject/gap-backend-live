<?php
use common\models\ar\TrnKartuProsesPfp;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesPfpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $mesinOptions array */
/* @var $mcFilter array */
/* @var $shiftPagiFilter string */
/* @var $shiftSiangFilter string */
/* @var $tanggalFilter string */
/* @var $processId int */

$this->title = 'Laporan Persiapan PFP';
$this->params['breadcrumbs'][] = $this->title;

$shiftOptions = [
    'A' => 'A',
    'B' => 'B',
    'C' => 'C',
    'D' => 'D'
];

$urlLookupOrderPfp = \yii\helpers\Url::to(['/ajax/lookup-order-pfp']);
$urlGetInfoOrderPfp = \yii\helpers\Url::to(['/laporan/get-info-by-order-pfp']);

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
window.addEditRow = function(id, tanggal, orderNo, motif, noKartu, pbg, shift, mc, ket) {
    var isManual = !id;
    var hasDataCls = (tanggal || shift || mc || ket) ? ' has-existing-data' : '';
    
    var orderHtml = isManual ? '<select name="Inputan['+rowIndex+'][order_pfp]" class="form-control input-sm select2-order" style="width: 100%;"></select>' : '<input type="text" class="form-control input-sm" value="'+orderNo+'" readonly>';
    var motifHtml = '<input type="text" class="form-control input-sm motif-input" value="'+motif+'" readonly>';
    var noKartuHtml = isManual ? '<select name="Inputan['+rowIndex+'][nomor_kartu]" class="form-control input-sm select-nk"><option value="">-</option></select>' : '<input type="text" name="Inputan['+rowIndex+'][nomor_kartu]" class="form-control input-sm" value="'+noKartu+'" readonly>';
    var pbgHtml = '<input type="text" class="form-control input-sm pbg-input" value="'+pbg+'" readonly>';
    
    var shiftHtml = '<select name="Inputan['+rowIndex+'][shift_operator]" class="form-control input-sm">' +
        '<option value="">-</option>' +
        '<option value="A" '+(shift==='A'?'selected':'')+'>A</option>' +
        '<option value="B" '+(shift==='B'?'selected':'')+'>B</option>' +
        '<option value="C" '+(shift==='C'?'selected':'')+'>C</option>' +
        '<option value="D" '+(shift==='D'?'selected':'')+'>D</option>' +
    '</select>';
    
    var mesinOptions = {$mesinOptionsJson};
    var mcOptionsHtml = '<option value="">-</option>';
    $.each(mesinOptions, function(k, v) {
        var sel = (v === mc) ? ' selected' : '';
        mcOptionsHtml += '<option value="'+v+'"'+sel+'>'+v+'</option>';
    });
    var mcHtml = '<select name="Inputan['+rowIndex+'][no_mesin]" class="form-control input-sm select2-mc">'+mcOptionsHtml+'</select>';
    
    var rowHtml = '<tr id="row-'+rowIndex+'" class="'+hasDataCls+'">' +
        '<td><input type="date" name="Inputan['+rowIndex+'][tanggal]" class="form-control input-sm" value="'+tanggal+'"></td>' +
        '<td>' + orderHtml + '</td>' +
        '<td>' + motifHtml + '</td>' +
        '<td>' + noKartuHtml + '<input type="hidden" name="Inputan['+rowIndex+'][kartu_process_id]" class="kartu-process-id" value="'+id+'"></td>' +
        '<td>' + pbgHtml + '</td>' +
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
    
    if (isManual) {
        rowEl.find('.select2-order').select2({
            ajax: {
                url: '{$urlLookupOrderPfp}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return { results: data.results };
                },
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
                        
                        // Store NK data on the row for later
                        currentRowEl.data('nks', res.nks);
                        
                        var nkOpt = '<option value="">Pilih NK</option>';
                        res.nks.forEach(function(nk) { nkOpt += '<option value="'+nk.nomor_kartu+'">'+nk.nomor_kartu+'</option>'; });
                        currentRowEl.find('.select-nk').html(nkOpt);
                    }
                });
            }
        });
        
        rowEl.find('.select-nk').on('change', function() {
            var noKartu = $(this).val();
            var currentRowEl = $(this).closest('tr');
            var nks = currentRowEl.data('nks') || [];
            
            var selectedNk = nks.find(function(n) { return n.nomor_kartu === noKartu; });
            if (selectedNk) {
                currentRowEl.find('.pbg-input').val(selectedNk.pbg);
                currentRowEl.find('.kartu-process-id').val(selectedNk.id);
                if (selectedNk.has_data) {
                    currentRowEl.addClass('has-existing-data');
                } else {
                    currentRowEl.removeClass('has-existing-data');
                }
            } else {
                currentRowEl.find('.pbg-input').val('');
                currentRowEl.find('.kartu-process-id').val('');
                currentRowEl.removeClass('has-existing-data');
            }
        });
    }
    
    rowIndex++;
    
    $('html, body').animate({
        scrollTop: $("#form-tambahan-input").offset().top
    }, 500);
};

$(document).on('submit', '#form-inputan-bawah', function(e) {
    var form = $(this);
    if (form.data('confirmed')) {
        return true;
    }
    
    var hasExisting = form.find('.has-existing-data').length > 0;
    if (hasExisting) {
        e.preventDefault();
        krajeeDialog.confirm('Beberapa data yang diinput sudah memiliki isi sebelumnya (tanggal/shift/mc/keterangan). Apakah Anda yakin ingin menimpanya?', function(result) {
            if (result) {
                form.data('confirmed', true);
                form.submit();
            }
        });
    }
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

$(document).on('click', '#btn-tambah-set-inputan', function(e) {
    e.preventDefault();
    addEditRow('', '', '', '', '', '', '', '', '');
});

$(document).on('click', '.edit-row-btn', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    var tanggal = $(this).data('tanggal');
    var orderNo = $(this).data('orderno');
    var motif = $(this).data('motif');
    var noKartu = $(this).data('nokartu');
    var pbg = $(this).data('pbg');
    var shift = $(this).data('shift');
    var mc = $(this).data('mc');
    var ket = $(this).data('ket');
    
    addEditRow(id, tanggal, orderNo, motif, noKartu, pbg, shift, mc, ket);
});
JS;
$this->registerJs($js);
?>
<div class="kartu-proses-pfp-index">
    
    <div class="panel panel-default">
        <div class="panel-body">
            <?= Html::beginForm(['persiapan-pfp'], 'get', ['class' => 'form-inline']) ?>
            
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-3">
                    <label style="display:block;">Pilih Mesin:</label>
                    <?= Select2::widget([
                        'name' => 'mcFilter',
                        'value' => isset($mcFilter) ? $mcFilter : [],
                        'data' => isset($mesinOptions) ? $mesinOptions : [],
                        'options' => [
                            'placeholder' => 'Pilih Mesin...',
                            'multiple' => true,
                        ],
                    ]) ?>
                </div>
                <div class="col-md-3">
                    <label style="display:block;">SHIFT:</label>
                    <div class="input-group" style="width:100%; margin-bottom: 5px;">
                        <span class="input-group-addon" style="background-color: #4CAF50; color: white; font-weight: bold; width: 80px;">PAGI</span>
                        <?= Html::dropDownList('shiftPagiFilter', isset($shiftPagiFilter) ? $shiftPagiFilter : null, $shiftOptions, ['class' => 'form-control', 'prompt' => 'Pilih Shift']) ?>
                    </div>
                    <div class="input-group" style="width:100%;">
                        <span class="input-group-addon" style="background-color: #F44336; color: white; font-weight: bold; width: 80px;">SIANG</span>
                        <?= Html::dropDownList('shiftSiangFilter', isset($shiftSiangFilter) ? $shiftSiangFilter : null, $shiftOptions, ['class' => 'form-control', 'prompt' => 'Pilih Shift']) ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <label style="display:block;">Tanggal:</label>
                    <?= \kartik\daterange\DateRangePicker::widget([
                        'name' => 'tanggalFilter',
                        'value' => isset($tanggalFilter) ? $tanggalFilter : '',
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'locale' => [
                                'format' => 'Y-m-d',
                                'separator' => ' to ',
                            ],
                            'maxSpan' => [
                                'days' => 3
                            ]
                        ],
                        'options' => ['placeholder' => 'Pilih Rentang Tanggal...', 'class' => 'form-control'],
                    ]) ?>
                </div>
                <div class="col-md-3" style="padding-top: 25px;">
                    <?= Html::submitButton('<i class="glyphicon glyphicon-search"></i> Tampilkan', ['class' => 'btn btn-primary']) ?>
                    <?= Html::button('<i class="glyphicon glyphicon-print"></i>', [
                        'class' => 'btn btn-default', 
                        'onclick' => "window.open('/laporan/print-persiapan-pfp?' + $(this).closest('form').serialize(), '_blank');"
                    ]) ?>
                </div>
            </div>
            
            <?= Html::endForm() ?>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'showPageSummary'=>true,
        'panel' => [
            'type' => 'default',
            'before'=>Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['persiapan-pfp'], ['class' => 'btn btn-default']).
                Html::button('<i class="glyphicon glyphicon-eye-open"></i> Tampilkan Baris Tersembunyi', ['class' => 'btn btn-info', 'id' => 'btn-unhide-all']),
                ['class'=>'btn-group', 'role'=>'group']
            ),
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'attribute' => 'shift',
                'label' => 'SHIFT',
                'value' => function ($data) use ($processId) {
                    $model = $data->getKartuProcessPfpProcesses()
                        ->where(['process_id' => $processId])
                        ->one();

                    if ($model !== null) {
                        try {
                            $json = \yii\helpers\Json::decode($model->value);
                            return $json['shift_operator'] ?? '-';
                        } catch (\Throwable $t) {
                            return '-';
                        }
                    }
                    return '-';
                }
            ],
            [
                'attribute' => 'openDateRange',
                'label' => 'Tanggal',
                'value' => function($data) use ($processId) {
                    /* @var $data TrnKartuProsesPfp*/
                    try {
                        $model = $data->getKartuProcessPfpProcesses()->where(['process_id'=>$processId])->one();
                        if($model !== null){
                            $json = \yii\helpers\Json::decode($model->value);
                            if(isset($json['tanggal']) && !empty($json['tanggal'])){
                                return date('j M Y', strtotime($json['tanggal']));
                            }
                        }
                        return $data->tanggalKartuProcessPfpProcess ? date('j M Y', strtotime($data->tanggalKartuProcessPfpProcess)) : '-';
                    }catch (\Throwable $t){
                        return null;
                    }
                },
                'format' => 'raw',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        'locale'=>[
                            'format'=>'Y-m-d',
                            'separator'=>' to ',
                        ]
                    ]
                ],
            ],
            [
                'attribute'=>'orderPfpNo',
                'label'=>'No Order PFP',
                'value'=>'orderPfp.no'
            ],
            [
                'label' => 'Motif',
                'value' => function ($data) {
                    $lusi  = $data->lusi ?? '';
                    $motif = $data->greige->nama_kain ?? '';
                    $pakan = $data->pakan ?? '';
                    return trim($lusi . ' ' . $motif . ' ' . $pakan);
                }
            ],
            [
                'attribute' => 'nomor_kartu',
                'format' => 'raw',
                'value' => function($data) {
                    return Html::a($data->nomor_kartu, ['/trn-kartu-proses-pfp/view', 'id' => $data->id], ['target' => '_blank', 'title' => 'Lihat Detail NK', 'data-pjax' => 0]);
                }
            ],
            [
                'label' => 'Panjang',
                'value' => function($data){
                    $panjangTotal = $data->getTrnKartuProsesPfpItems()->sum('panjang_m');
                    return $panjangTotal === null ? 0 : (float) $panjangTotal;
                },
                'format' => ['decimal', 0],
                'pageSummary' => true
            ],
            [
                'label' => 'Berat',
                'value' => function($data){
                    return (float) $data->berat;
                },
                'format' => ['decimal', 1],
                'pageSummary' => true
            ],
            [
                'label' => 'Gul',
                'value' => function($data){
                    $jumlahRoll = $data->getTrnKartuProsesPfpItems()->count('id');
                    return $jumlahRoll === null ? 0 : $jumlahRoll;
                },
                'format' => ['decimal', 0],
                'pageSummary' => true
            ],
            [
                'label' => 'Shift',
                'value' => function($data) use ($processId) {
                    $model = $data->getKartuProcessPfpProcesses()->where(['process_id'=>$processId])->one();
                    if($model !== null){
                        try {
                            $json = \yii\helpers\Json::decode($model['value']);
                            return isset($json['shift_operator']) ? $json['shift_operator'] : '-';
                        }catch (\Throwable $t){
                            return '-';
                        }
                    }
                    return '-';
                }
            ],
            [
                'label' => 'MC',
                'value' => function($data) use ($processId) {
                    $model = $data->getKartuProcessPfpProcesses()->where(['process_id'=>$processId])->one();
                    if($model !== null){
                        try {
                            $json = \yii\helpers\Json::decode($model['value']);
                            return isset($json['no_mesin']) ? $json['no_mesin'] : '-';
                        }catch (\Throwable $t){
                            return '-';
                        }
                    }
                    return '-';
                }
            ],
            [
                'label' => 'Ket.',
                'value' => function($data) use ($processId) {
                    $model = $data->getKartuProcessPfpProcesses()->where(['process_id'=>$processId])->one();
                    if($model !== null){
                        try {
                            $json = \yii\helpers\Json::decode($model['value']);
                            $keterangan = isset($json['keterangan']) ? trim($json['keterangan']) : '';
                            $gangguan = isset($json['gangguan_produksi']) ? trim($json['gangguan_produksi']) : '';
                            
                            $res = [];
                            if (!empty($keterangan)) $res[] = $keterangan;
                            if (!empty($gangguan)) $res[] = $gangguan;
                            
                            return !empty($res) ? implode(', ', $res) : '-';
                        }catch (\Throwable $t){
                            return '-';
                        }
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
                    'edit' => function ($url, $model, $key) use ($processId) {
                        $tanggal = '-';
                        $shift = '';
                        $mc = '';
                        $ket = '';
                        
                        $kpdp = $model->getKartuProcessPfpProcesses()->where(['process_id' => $processId])->one();
                        if ($kpdp) {
                            try {
                                $json = \yii\helpers\Json::decode($kpdp->value);
                                if (!empty($json['tanggal'])) $tanggal = $json['tanggal'];
                                if (!empty($json['shift_operator'])) $shift = $json['shift_operator'];
                                if (!empty($json['no_mesin'])) $mc = $json['no_mesin'];
                                if (!empty($json['keterangan'])) $ket = $json['keterangan'];
                            } catch (\Throwable $t) {}
                        }
                        if ($tanggal === '-' && $model->tanggalKartuProcessPfpProcess) {
                            $tanggal = $model->tanggalKartuProcessPfpProcess;
                        }
                        if ($tanggal === '-') $tanggal = '';
                        
                        $lusi  = $model->lusi ?? '';
                        $motifName = $model->greige->nama_kain ?? '';
                        $pakan = $model->pakan ?? '';
                        $motif = trim($lusi . ' ' . $motifName . ' ' . $pakan);
                        
                        $panjangTotal = $model->getTrnKartuProsesPfpItems()->sum('panjang_m') ?: 0;
                        $panjangTotal = number_format((float)$panjangTotal, 0);
                        $berat = number_format((float)$model->berat, 1);
                        $jumlahRoll = $model->getTrnKartuProsesPfpItems()->count('id') ?: 0;
                        $pbg = $panjangTotal . ' / ' . $berat . ' / ' . $jumlahRoll;

                        $orderNo = $model->orderPfp ? $model->orderPfp->no : '';

                        return Html::a('<i class="glyphicon glyphicon-edit"></i> Edit', '#', [
                            'class' => 'btn btn-default btn-xs edit-row-btn',
                            'title' => 'Edit',
                            'data-id' => $model->id,
                            'data-tanggal' => $tanggal,
                            'data-orderno' => $orderNo,
                            'data-motif' => $motif,
                            'data-nokartu' => $model->nomor_kartu,
                            'data-pbg' => $pbg,
                            'data-shift' => $shift,
                            'data-mc' => $mc,
                            'data-ket' => $ket,
                        ]);
                    },
                    'hide' => function ($url, $model, $key) {
                        return Html::a('<i class="glyphicon glyphicon-eye-close"></i>', '#', [
                            'class' => 'btn btn-default btn-xs toggle-hide-btn',
                            'data-id' => $model->id,
                            'title' => 'Hide Row'
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>
    
    <div class="panel panel-default hidden-print" id="form-tambahan-input" style="margin-top: 20px;">
        <div class="panel-heading">
            <h3 class="panel-title" style="font-weight: bold; color: #666;">TAMBAHAN INPUT</h3>
        </div>
        <div class="panel-body">
            <?= Html::beginForm(['update-persiapan-pfp'], 'post', ['id' => 'form-inputan-bawah']) ?>
            <div class="table-responsive">
                <table class="table table-bordered table-condensed" id="inputan-table">
                    <thead>
                        <tr style="color: #337ab7;">
                            <th>TANGGAL</th>
                            <th>Nomor Order PFP</th>
                            <th>Motif</th>
                            <th>Nomor Kartu</th>
                            <th>Panjang / Berat / Gul</th>
                            <th width="10%">Shift</th>
                            <th width="10%">MC</th>
                            <th width="15%">Ket</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            
            <div class="form-group">
                <button type="button" class="btn btn-success" id="btn-tambah-set-inputan"><i class="glyphicon glyphicon-plus"></i> Tambah Set Inputan</button>
            </div>
            
            <div class="form-group" style="margin-top: 15px;">
                <?= Html::submitButton('Simpan Data Input', ['class' => 'btn btn-primary']) ?>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>