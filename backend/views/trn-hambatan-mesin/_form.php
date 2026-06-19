<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use common\models\ar\MstMesinProses;
use common\models\ar\MstJenisHambatan;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnHambatanMesin */
/* @var $items common\models\ar\TrnHambatanMesinItem[] */
/* @var $form kartik\widgets\ActiveForm */

\yii\widgets\MaskedInputAsset::register($this);

// Get all models
$modelsList = MstMesinProses::find()->select(['model_mesin'])->distinct()->asArray()->all();
$modelsMap = [];
foreach ($modelsList as $m) {
    $val = $m['model_mesin'] ? $m['model_mesin'] : '_empty_';
    $label = $m['model_mesin'] ? $m['model_mesin'] : 'Tanpa Model';
    $modelsMap[$val] = $label;
}

// Get all machines for initially populated dropdowns
$machinesList = MstMesinProses::find()->orderBy(['nama_mesin' => SORT_ASC])->asArray()->all();
$machinesMap = ArrayHelper::map($machinesList, 'id', 'nama_mesin');
?>

<style>
/* Make select2 multi-select look normal in height */
.select2-container--default .select2-selection--multiple {
    min-height: 34px;
}
.select2-container .select2-selection--multiple .select2-selection__rendered {
    display: flex;
    flex-wrap: wrap;
    padding: 0 4px;
}
</style>

<div class="trn-hambatan-mesin-form">
    <?php $form = ActiveForm::begin(['id' => 'hambatan-mesin-form']); ?>

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Informasi Shift & Tanggal</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'shift')->widget(Select2::classname(), [
                        'data' => ['A'=>'A', 'B'=>'B', 'C'=>'C', 'D'=>'D'],
                        'options' => ['placeholder' => 'Pilih Shift...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]) ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'model_mesin')->widget(Select2::classname(), [
                        'data' => $modelsMap,
                        'options' => ['placeholder' => 'Pilih Model Mesin...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]) ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'tanggal')->widget(DatePicker::classname(), [
                        'disabled' => !$model->isNewRecord,
                        'options' => ['placeholder' => 'Pilih Tanggal ...'],
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true
                        ]
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($dataProviderItems)): ?>
    <?php \yii\widgets\Pjax::begin(['id' => 'grid-hambatan-pjax', 'timeout' => false]); ?>
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Data Hambatan yang Sudah Diinput (Hari Ini)</h3>
        </div>
        <div class="box-body no-padding" style="overflow-x: auto;">
            <?= \kartik\grid\GridView::widget([
                'dataProvider' => $dataProviderItems,
                'responsiveWrap' => false,
                'layout' => "{items}\n{pager}",
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => 'Shift',
                        'value' => 'trnHambatanMesin.shift',
                    ],
                    'start_time',
                    'stop_time',
                    [
                        'label' => 'Model Mesin',
                        'value' => function ($data) {
                            return $data->mstMesinProses && $data->mstMesinProses->model_mesin ? $data->mstMesinProses->model_mesin : ($data->trnHambatanMesin->model_mesin ? $data->trnHambatanMesin->model_mesin : '-');
                        },
                    ],
                    [
                        'label' => 'Mesin',
                        'value' => function ($data) {
                            return $data->mstMesinProses ? $data->mstMesinProses->nama_mesin : '-';
                        },
                    ],
                    'keterangan',
                    'no_wo',
                    'no_kartu',
                    [
                        'label' => 'Jenis Hambatan',
                        'value' => function ($data) {
                            $names = [];
                            foreach ($data->mstJenisHambatans as $jh) {
                                $names[] = $jh->nama;
                            }
                            return implode(', ', $names);
                        },
                    ],
                    [
                        'label' => 'Aksi',
                        'format' => 'raw',
                        'value' => function ($data) {
                            $editBtn = Html::a('<i class="glyphicon glyphicon-pencil"></i> Edit Set', 'javascript:void(0)', [
                                'class' => 'btn btn-xs btn-primary btn-edit-set',
                                'data-id' => $data->trn_hambatan_mesin_id
                            ]);
                            $delBtn = Html::a('<i class="glyphicon glyphicon-trash"></i> Hapus Set', ['delete', 'id' => $data->trn_hambatan_mesin_id], [
                                'class' => 'btn btn-xs btn-danger',
                                'data' => [
                                    'confirm' => 'Apakah Anda yakin ingin menghapus seluruh set data hambatan ini?',
                                    'method' => 'post',
                                ],
                            ]);
                            return $editBtn . ' ' . $delBtn;
                        },
                    ],
                ],
            ]); ?>
        </div>
    </div>
    <?php \yii\widgets\Pjax::end(); ?>
    <?php endif; ?>

    <?php
    $dyeingItems = [];
    $pfpItems = [];
    foreach ($items as $item) {
        // Assume PFP if no_wo has OPFP or no_kartu has PFP
        if (($item->no_wo && strpos($item->no_wo, 'OPFP') !== false) || ($item->no_kartu && strpos($item->no_kartu, 'PFP') !== false)) {
            $pfpItems[] = $item;
        } else {
            $dyeingItems[] = $item;
        }
    }
    if (empty($dyeingItems)) {
        $dyeingItems[] = new \common\models\ar\TrnHambatanMesinItem();
    }
    if (empty($pfpItems)) {
        $pfpItems[] = new \common\models\ar\TrnHambatanMesinItem();
    }
    ?>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Daftar Hambatan (Dyeing / Printing)</h3>
        </div>
        <div class="box-body no-padding" style="overflow-x: auto;">
            <table class="table table-bordered table-striped" id="hambatan-table" style="margin-bottom: 0; min-width: 1600px;">
                <thead>
                    <tr>
                        <th style="width: 100px;">Start</th>
                        <th style="width: 100px;">Stop</th>
                        <th style="width: 150px;">Model Mesin</th>
                        <th style="width: 200px;">Mesin</th>
                        <th style="width: 350px;">Keterangan</th>
                        <th style="width: 200px;">WO (jika ada)</th>
                        <th style="width: 200px;">NK (jika ada)</th>
                        <th style="width: 250px;">Jenis Hambatan (Bisa Pilih Banyak)</th>
                        <th style="width: 50px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="hambatan-tbody">
                    <?php foreach ($dyeingItems as $index => $item): 
                        $curMachinesMap = $machinesMap;
                        $curModel = null;
                        if ($item->mstMesinProses) {
                            $curModel = $item->mstMesinProses->model_mesin ?: '_empty_';
                        }
                    ?>
                        <tr class="hambatan-row" data-index="<?= $index ?>">
                            <td>
                                <?= Html::textInput("Items[{$index}][start_time]", $item->start_time, [
                                    'class' => 'form-control start-time-input',
                                    'placeholder' => 'HH:MM'
                                ]) ?>
                            </td>
                            <td>
                                <?= Html::textInput("Items[{$index}][stop_time]", $item->stop_time, [
                                    'class' => 'form-control stop-time-input',
                                    'placeholder' => 'HH:MM'
                                ]) ?>
                            </td>
                            <td>
                                <?= Html::dropDownList("Items[{$index}][model_mesin_dummy]", $curModel, $modelsMap, [
                                    'class' => 'form-control model-mesin-select2',
                                    'prompt' => 'Pilih Model...'
                                ]) ?>
                            </td>
                            <td>
                                <?= Html::dropDownList("Items[{$index}][mst_mesin_proses_id]", $item->mst_mesin_proses_id, $curMachinesMap, [
                                    'class' => 'form-control mesin-select2',
                                    'prompt' => 'Pilih Mesin...'
                                ]) ?>
                            </td>
                            <td>
                                <?= Html::textInput("Items[{$index}][keterangan]", $item->keterangan, [
                                    'class' => 'form-control keterangan-input',
                                    'placeholder' => 'Ketik Keterangan...'
                                ]) ?>
                            </td>
                            <td>
                                <select name="Items[<?= $index ?>][no_wo]" class="form-control wo-select2">
                                    <?php if ($item->no_wo): ?>
                                        <option value="<?= Html::encode($item->no_wo) ?>" selected><?= Html::encode($item->no_wo) ?></option>
                                    <?php endif; ?>
                                </select>
                            </td>
                            <td>
                                <select name="Items[<?= $index ?>][no_kartu]" class="form-control nk-select2">
                                    <?php if ($item->no_kartu): ?>
                                        <option value="<?= Html::encode($item->no_kartu) ?>" selected><?= Html::encode($item->no_kartu) ?></option>
                                    <?php endif; ?>
                                </select>
                            </td>
                            <td>
                                <select name="Items[<?= $index ?>][jenis_hambatan_ids][]" class="form-control jenis-hambatan-select2" multiple="multiple">
                                    <?php 
                                    if ($item->mst_mesin_proses_id && $item->mstMesinProses && !empty($item->mstMesinProses->mstJenisHambatans)) {
                                        $hambatanList = ArrayHelper::map($item->mstMesinProses->mstJenisHambatans, 'id', 'nama');
                                    } else {
                                        $hambatanList = ArrayHelper::map(MstJenisHambatan::find()->all(), 'id', 'nama');
                                    }
                                    foreach ($hambatanList as $id => $nama): 
                                        $selected = in_array($id, (array)$item->jenis_hambatan_ids) ? 'selected' : '';
                                    ?>
                                        <option value="<?= $id ?>" <?= $selected ?>><?= Html::encode($nama) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <button type="button" class="btn btn-sm btn-danger remove-row-btn">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="padding: 10px;">
                <button type="button" class="btn btn-success" id="add-row-btn">
                    <i class="glyphicon glyphicon-plus"></i> Tambah Set Hambatan
                </button>
            </div>
        </div>
    </div>

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Daftar Hambatan (PFP)</h3>
        </div>
        <div class="box-body no-padding" style="overflow-x: auto;">
            <table class="table table-bordered table-striped" id="hambatan-table-pfp" style="margin-bottom: 0; min-width: 1600px;">
                <thead>
                    <tr>
                        <th style="width: 100px;">Start</th>
                        <th style="width: 100px;">Stop</th>
                        <th style="width: 150px;">Model Mesin</th>
                        <th style="width: 200px;">Mesin</th>
                        <th style="width: 350px;">Keterangan</th>
                        <th style="width: 200px;">Order PFP (jika ada)</th>
                        <th style="width: 200px;">NK PFP (jika ada)</th>
                        <th style="width: 250px;">Jenis Hambatan (Bisa Pilih Banyak)</th>
                        <th style="width: 50px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="hambatan-tbody-pfp">
                    <?php 
                    $pfpOffset = max(100, count($items) + 100);
                    foreach ($pfpItems as $index => $item): 
                        $idx = $pfpOffset + $index;
                        $curModel = null;
                        if ($item->mstMesinProses) {
                            $curModel = $item->mstMesinProses->model_mesin ?: '_empty_';
                        }
                    ?>
                        <tr class="hambatan-row" data-index="<?= $idx ?>">
                            <td>
                                <?= Html::textInput("Items[{$idx}][start_time]", $item->start_time, [
                                    'class' => 'form-control start-time-input',
                                    'placeholder' => 'HH:MM'
                                ]) ?>
                            </td>
                            <td>
                                <?= Html::textInput("Items[{$idx}][stop_time]", $item->stop_time, [
                                    'class' => 'form-control stop-time-input',
                                    'placeholder' => 'HH:MM'
                                ]) ?>
                            </td>
                            <td>
                                <?= Html::dropDownList("Items[{$idx}][model_mesin_dummy]", $curModel, $modelsMap, [
                                    'class' => 'form-control model-mesin-select2',
                                    'prompt' => 'Pilih Model...'
                                ]) ?>
                            </td>
                            <td>
                                <?= Html::dropDownList("Items[{$idx}][mst_mesin_proses_id]", $item->mst_mesin_proses_id, $machinesMap, [
                                    'class' => 'form-control mesin-select2',
                                    'prompt' => 'Pilih Mesin...'
                                ]) ?>
                            </td>
                            <td>
                                <?= Html::textInput("Items[{$idx}][keterangan]", $item->keterangan, [
                                    'class' => 'form-control keterangan-input',
                                    'placeholder' => 'Ketik Keterangan...'
                                ]) ?>
                            </td>
                            <td>
                                <select name="Items[<?= $idx ?>][no_wo]" class="form-control order-pfp-select2">
                                    <?php if ($item->no_wo): ?>
                                        <option value="<?= Html::encode($item->no_wo) ?>" selected><?= Html::encode($item->no_wo) ?></option>
                                    <?php endif; ?>
                                </select>
                            </td>
                            <td>
                                <select name="Items[<?= $idx ?>][no_kartu]" class="form-control nk-select2">
                                    <?php if ($item->no_kartu): ?>
                                        <option value="<?= Html::encode($item->no_kartu) ?>" selected><?= Html::encode($item->no_kartu) ?></option>
                                    <?php endif; ?>
                                </select>
                            </td>
                            <td>
                                <select name="Items[<?= $idx ?>][jenis_hambatan_ids][]" class="form-control jenis-hambatan-select2" multiple="multiple">
                                    <?php 
                                    if ($item->mst_mesin_proses_id && $item->mstMesinProses && !empty($item->mstMesinProses->mstJenisHambatans)) {
                                        $hambatanList = ArrayHelper::map($item->mstMesinProses->mstJenisHambatans, 'id', 'nama');
                                    } else {
                                        $hambatanList = ArrayHelper::map(MstJenisHambatan::find()->all(), 'id', 'nama');
                                    }
                                    foreach ($hambatanList as $id => $nama): 
                                        $selected = in_array($id, (array)$item->jenis_hambatan_ids) ? 'selected' : '';
                                    ?>
                                        <option value="<?= $id ?>" <?= $selected ?>><?= Html::encode($nama) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <button type="button" class="btn btn-sm btn-danger remove-row-btn-pfp">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="padding: 10px;">
                <button type="button" class="btn btn-warning" id="add-row-btn-pfp">
                    <i class="glyphicon glyphicon-plus"></i> Tambah Set Hambatan PFP
                </button>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Simpan Data Hambatan', ['class' => 'btn btn-primary btn-lg']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$hambatansUrl = \yii\helpers\Url::to(['get-hambatans-by-machine']);
$machinesUrl = \yii\helpers\Url::to(['get-machines-by-model']);
$searchKpUrl = \yii\helpers\Url::to(['search-kartu-proses']);
$searchWoUrl = \yii\helpers\Url::to(['search-wo']);
$searchOrderPfpUrl = \yii\helpers\Url::to(['search-order-pfp']);

$modelsJson = json_encode($modelsMap);

$js = <<<JS
var rowIndex = 1000;
var pfpRowIndex = 2000;
var modelsList = {$modelsJson};

function initModelSelect2(element) {
    $(element).select2({
        placeholder: 'Pilih Model...',
        width: '100%',
        allowClear: true
    });
}

function initMesinSelect2(element) {
    $(element).select2({
        placeholder: 'Pilih Mesin...',
        width: '100%',
        allowClear: true
    });
}

$(document).on('change', '.model-mesin-select2', function() {
    var modelVal = $(this).val();
    var row = $(this).closest('.hambatan-row');
    var mesinSelect = row.find('.mesin-select2');
    var hambatanSelect = row.find('.jenis-hambatan-select2');
    
    mesinSelect.empty().append('<option value="">Pilih Mesin...</option>');
    hambatanSelect.empty().trigger('change');
    
    if (!modelVal) {
        mesinSelect.trigger('change');
        return;
    }
    
    $.ajax({
        url: '{$machinesUrl}',
        data: { model_mesin: modelVal },
        dataType: 'json',
        success: function(data) {
            $.each(data, function(i, item) {
                mesinSelect.append($('<option>', {
                    value: item.id,
                    text: item.nama_mesin
                }));
            });
            
            var preset = mesinSelect.data('preset-value');
            if (preset) {
                mesinSelect.val(preset);
                mesinSelect.data('preset-value', null); // clear after use
            }
            mesinSelect.trigger('change');
        }
    });
});

function fetchHambatans(machineId, dropdown, selectedVals) {
    $.ajax({
        url: '{$hambatansUrl}',
        data: { machine_id: machineId || '' },
        dataType: 'json',
        success: function(data) {
            dropdown.empty();
            $.each(data, function(i, item) {
                dropdown.append($('<option>', {
                    value: item.id,
                    text: item.nama
                }));
            });
            
            var preset = dropdown.data('preset-value');
            if (preset) {
                dropdown.val(preset);
                dropdown.data('preset-value', null); // clear after use
            } else if (selectedVals) {
                dropdown.val(selectedVals);
            }
            dropdown.trigger('change');
        }
    });
}

$(document).on('change', '.mesin-select2', function() {
    var machineId = $(this).val();
    var row = $(this).closest('.hambatan-row');
    var hambatanSelect = row.find('.jenis-hambatan-select2');
    
    // Simpan selected value saat ini
    var currentVal = hambatanSelect.val();
    
    fetchHambatans(machineId, hambatanSelect, currentVal);
});

function initSelect2(element) {
    $(element).select2({
        ajax: {
            url: '{$searchKpUrl}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var row = $(element).closest('.hambatan-row');
                var selectedWo = row.find('.wo-select2').length ? row.find('.wo-select2').val() : row.find('.order-pfp-select2').val();
                return {
                    q: params.term,
                    wo: selectedWo
                };
            },
            processResults: function (data) {
                return {
                    results: data.results
                };
            },
            cache: true
        },
        placeholder: 'Cari No. Kartu...',
        width: '100%',
        allowClear: true
    });
}

function initWoSelect2(element) {
    $(element).select2({
        ajax: {
            url: '{$searchWoUrl}',
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
        placeholder: 'Cari No. WO...',
        minimumInputLength: 2,
        width: '100%',
        allowClear: true
    });
}

function initOrderPfpSelect2(element) {
    $(element).select2({
        ajax: {
            url: '{$searchOrderPfpUrl}',
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
        placeholder: 'Cari No. Order PFP...',
        minimumInputLength: 2,
        width: '100%',
        allowClear: true
    });
}

function initJenisHambatanSelect2(element) {
    $(element).select2({
        placeholder: 'Pilih Hambatan...',
        width: '100%',
        allowClear: true
    });
}

function initTimeMask(element) {
    $(element).inputmask('hh:mm', {
        placeholder: 'HH:MM',
        insertMode: false,
        showMaskOnHover: false
    });
}

// Initialize existing elements
$('.model-mesin-select2').each(function() { initModelSelect2(this); });
$('.mesin-select2').each(function() { initMesinSelect2(this); });
$('.nk-select2').each(function() { initSelect2(this); });
$('.wo-select2').each(function() { initWoSelect2(this); });
$('.order-pfp-select2').each(function() { initOrderPfpSelect2(this); });
$('.jenis-hambatan-select2').each(function() { initJenisHambatanSelect2(this); });
$('.start-time-input, .stop-time-input').each(function() { initTimeMask(this); });

$(document).on('change', '.wo-select2, .order-pfp-select2', function() {
    $(this).closest('tr').find('.nk-select2').val(null).trigger('change');
});

function buildModelOptions() {
    var opts = '<option value="">Pilih Model...</option>';
    $.each(modelsList, function(val, label) {
        opts += '<option value="'+val+'">'+label+'</option>';
    });
    return opts;
}

$('#add-row-btn').on('click', function() {
    var newRow = $('<tr class="hambatan-row" data-index="' + rowIndex + '">');
    
    var defaultModel = $('#trnhambatanmesin-model_mesin').val() || '';
    
    newRow.append('<td><input type="text" name="Items[' + rowIndex + '][start_time]" class="form-control start-time-input" placeholder="HH:MM"></td>');
    newRow.append('<td><input type="text" name="Items[' + rowIndex + '][stop_time]" class="form-control stop-time-input" placeholder="HH:MM"></td>');
    newRow.append('<td><select name="Items[' + rowIndex + '][model_mesin_dummy]" class="form-control model-mesin-select2">' + buildModelOptions() + '</select></td>');
    newRow.append('<td><select name="Items[' + rowIndex + '][mst_mesin_proses_id]" class="form-control mesin-select2"><option value="">Pilih Mesin...</option></select></td>');
    newRow.append('<td><input type="text" name="Items[' + rowIndex + '][keterangan]" class="form-control keterangan-input" placeholder="Ketik Keterangan..."></td>');
    newRow.append('<td><select name="Items[' + rowIndex + '][no_wo]" class="form-control wo-select2"></select></td>');
    newRow.append('<td><select name="Items[' + rowIndex + '][no_kartu]" class="form-control nk-select2"></select></td>');
    newRow.append('<td><select name="Items[' + rowIndex + '][jenis_hambatan_ids][]" class="form-control jenis-hambatan-select2" multiple="multiple"></select></td>');
    newRow.append('<td style="text-align: center; vertical-align: middle;"><button type="button" class="btn btn-sm btn-danger remove-row-btn"><i class="glyphicon glyphicon-trash"></i></button></td>');
    
    $('#hambatan-tbody').append(newRow);
    
    
    initModelSelect2(newRow.find('.model-mesin-select2'));
    if (defaultModel) {
        newRow.find('.model-mesin-select2').val(defaultModel).trigger('change');
    }
    initMesinSelect2(newRow.find('.mesin-select2'));
    initSelect2(newRow.find('.nk-select2'));
    initWoSelect2(newRow.find('.wo-select2'));
    initJenisHambatanSelect2(newRow.find('.jenis-hambatan-select2'));
    initTimeMask(newRow.find('.start-time-input'));
    initTimeMask(newRow.find('.stop-time-input'));
    
    // Fetch initial hambatans for new row
    fetchHambatans('', newRow.find('.jenis-hambatan-select2'), null);
    
    rowIndex++;
});

$('#add-row-btn-pfp').on('click', function() {
    var newRow = $('<tr class="hambatan-row" data-index="' + pfpRowIndex + '">');
    
    var defaultModel = $('#trnhambatanmesin-model_mesin').val() || '';

    newRow.append('<td><input type="text" name="Items[' + pfpRowIndex + '][start_time]" class="form-control start-time-input" placeholder="HH:MM"></td>');
    newRow.append('<td><input type="text" name="Items[' + pfpRowIndex + '][stop_time]" class="form-control stop-time-input" placeholder="HH:MM"></td>');
    newRow.append('<td><select name="Items[' + pfpRowIndex + '][model_mesin_dummy]" class="form-control model-mesin-select2">' + buildModelOptions() + '</select></td>');
    newRow.append('<td><select name="Items[' + pfpRowIndex + '][mst_mesin_proses_id]" class="form-control mesin-select2"><option value="">Pilih Mesin...</option></select></td>');
    newRow.append('<td><input type="text" name="Items[' + pfpRowIndex + '][keterangan]" class="form-control keterangan-input" placeholder="Ketik Keterangan..."></td>');
    newRow.append('<td><select name="Items[' + pfpRowIndex + '][no_wo]" class="form-control order-pfp-select2"></select></td>');
    newRow.append('<td><select name="Items[' + pfpRowIndex + '][no_kartu]" class="form-control nk-select2"></select></td>');
    newRow.append('<td><select name="Items[' + pfpRowIndex + '][jenis_hambatan_ids][]" class="form-control jenis-hambatan-select2" multiple="multiple"></select></td>');
    newRow.append('<td style="text-align: center; vertical-align: middle;"><button type="button" class="btn btn-sm btn-danger remove-row-btn-pfp"><i class="glyphicon glyphicon-trash"></i></button></td>');
    
    $('#hambatan-tbody-pfp').append(newRow);
    
    
    initModelSelect2(newRow.find('.model-mesin-select2'));
    if (defaultModel) {
        newRow.find('.model-mesin-select2').val(defaultModel).trigger('change');
    }
    initMesinSelect2(newRow.find('.mesin-select2'));
    initSelect2(newRow.find('.nk-select2'));
    initOrderPfpSelect2(newRow.find('.order-pfp-select2'));
    initJenisHambatanSelect2(newRow.find('.jenis-hambatan-select2'));
    initTimeMask(newRow.find('.start-time-input'));
    initTimeMask(newRow.find('.stop-time-input'));
    
    // Fetch initial hambatans for new row
    fetchHambatans('', newRow.find('.jenis-hambatan-select2'), null);
    
    pfpRowIndex++;
});

$(document).on('click', '.remove-row-btn, .remove-row-btn-pfp', function() {
    $(this).closest('tr').remove();
});

$(document).on('click', '.btn-edit-set', function() {
    var id = $(this).data('id');
    $.ajax({
        url: '/trn-hambatan-mesin/get-set-data',
        data: {id: id},
        dataType: 'json',
        success: function(data) {
            // Populate basic form
            $('#trnhambatanmesin-shift').val(data.shift).trigger('change');
            if (data.model_mesin) {
                $('#trnhambatanmesin-model_mesin').val(data.model_mesin).trigger('change');
            } else {
                $('#trnhambatanmesin-model_mesin').val(null).trigger('change');
            }
            $('#trnhambatanmesin-tanggal').val(data.tanggal);
            
            // Set hidden ID
            if ($('#form-hambatan-id').length === 0) {
                $('#hambatan-mesin-form').append('<input type="hidden" name="id" id="form-hambatan-id">');
            }
            $('#form-hambatan-id').val(data.id);
            
            // Empty tables
            $('#hambatan-tbody').empty();
            $('#hambatan-tbody-pfp').empty();
            
            // Avoid Pjax reload when pre-filling from Edit Set
            window.isEditingSet = true;
            
            // Re-populate rows
            $.each(data.items, function(i, item) {
                var isPfp = (item.no_wo && item.no_wo.indexOf('OPFP') !== -1) || (item.no_kartu && item.no_kartu.indexOf('PFP') !== -1);
                
                if (isPfp) {
                    $('#add-row-btn-pfp').click();
                    var newRow = $('#hambatan-tbody-pfp tr.hambatan-row').last();
                } else {
                    $('#add-row-btn').click();
                    var newRow = $('#hambatan-tbody tr.hambatan-row').last();
                }
                
                newRow.find('.start-time-input').val(item.start_time);
                newRow.find('.stop-time-input').val(item.stop_time);
                newRow.find('.keterangan-input').val(item.keterangan);
                
                // For model mesin, when we trigger change, it will load machines. We pass preset-value so it selects the correct machine when done loading.
                if (item.mst_mesin_proses_id) {
                    newRow.find('.mesin-select2').data('preset-value', item.mst_mesin_proses_id);
                }
                
                // Set model_mesin and trigger change to fetch machines
                newRow.find('.model-mesin-select2').val(item.model_mesin).trigger('change');
                
                if (item.no_wo) {
                    newRow.find(isPfp ? '.order-pfp-select2' : '.wo-select2').append(new Option(item.no_wo, item.no_wo, true, true)).val(item.no_wo);
                }
                
                if (item.no_kartu) {
                    newRow.find('.nk-select2').append(new Option(item.no_kartu, item.no_kartu, true, true)).val(item.no_kartu);
                }
                
                if (item.jenis_hambatan_ids && item.jenis_hambatan_ids.length > 0) {
                    newRow.find('.jenis-hambatan-select2').data('preset-value', item.jenis_hambatan_ids);
                }
            });
            
            // Scroll to form
            $('html, body').animate({
                scrollTop: $("#hambatan-mesin-form").offset().top - 50
            }, 500);
            
            setTimeout(function() {
                window.isEditingSet = false;
            }, 500);
        }
    });
});

$(document).on('change', '#trnhambatanmesin-model_mesin', function() {
    if (window.isEditingSet) return; // Prevent reload if being populated by Edit Set
    var val = $(this).val() || '';
    if ($('#grid-hambatan-pjax').length > 0) {
        var url = new URL(window.location.href);
        url.searchParams.set('model_mesin', val);
        $.pjax.reload({container: '#grid-hambatan-pjax', url: url.toString(), replace: false, timeout: false});
    }
});
JS;

$this->registerJs($js);
?>