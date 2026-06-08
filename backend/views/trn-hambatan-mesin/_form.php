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

// Get distinct machine models
$modelsList = MstMesinProses::find()
    ->select(['model_mesin'])
    ->distinct()
    ->asArray()
    ->all();
$modelsMap = [];
foreach ($modelsList as $m) {
    $val = $m['model_mesin'] ? $m['model_mesin'] : '_empty_';
    $label = $m['model_mesin'] ? $m['model_mesin'] : 'Tanpa Model';
    $modelsMap[$val] = $label;
}

// Initial machine list if updating
$machinesMap = [];
if (!$model->isNewRecord) {
    $selectedMachine = MstMesinProses::findOne($model->mst_mesin_proses_id);
    if ($selectedMachine) {
        $model_mesin_val = $selectedMachine->model_mesin ? $selectedMachine->model_mesin : '_empty_';
        $machinesList = MstMesinProses::find()
            ->where($selectedMachine->model_mesin ? ['model_mesin' => $selectedMachine->model_mesin] : ['or', ['model_mesin' => null], ['model_mesin' => '']])
            ->asArray()
            ->all();
        $machinesMap = ArrayHelper::map($machinesList, 'id', 'nama_mesin');
    }
}
?>

<div class="trn-hambatan-mesin-form">
    <?php $form = ActiveForm::begin(['id' => 'hambatan-mesin-form']); ?>

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Informasi Mesin & Tanggal</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Mesin</label>
                        <?= Select2::widget([
                            'name' => 'model_mesin',
                            'value' => isset($model_mesin_val) ? $model_mesin_val : null,
                            'data' => $modelsMap,
                            'disabled' => !$model->isNewRecord,
                            'options' => [
                                'id' => 'model-mesin-select',
                                'placeholder' => 'Pilih Model Mesin ...',
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]) ?>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <?= $form->field($model, 'mst_mesin_proses_id')->widget(Select2::classname(), [
                        'data' => $machinesMap,
                        'disabled' => !$model->isNewRecord,
                        'options' => [
                            'id' => 'machine-select',
                            'placeholder' => 'Pilih Nomor Mesin ...',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label('Nomor Mesin') ?>
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
        <div class="box-body no-padding">
            <table class="table table-bordered table-striped" id="hambatan-table" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 100px;">Start</th>
                        <th style="width: 100px;">Stop</th>
                        <th style="width: 100px;">Shift</th>
                        <th>Keterangan</th>
                        <th style="width: 200px;">WO (jika ada)</th>
                        <th style="width: 200px;">NK (jika ada)</th>
                        <th style="width: 250px;">Jenis Hambatan (Bisa Pilih Banyak)</th>
                        <th style="width: 50px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="hambatan-tbody">
                    <?php foreach ($dyeingItems as $index => $item): ?>
                        <tr class="hambatan-row" data-index="<?= $index ?>">
                            <td>
                                <?= Html::textInput("Items[{$index}][start_time]", $item->start_time, [
                                    'class' => 'form-control start-time-input',
                                    'placeholder' => 'HH:MM',
                                    'required' => true
                                ]) ?>
                            </td>
                            <td>
                                <?= Html::textInput("Items[{$index}][stop_time]", $item->stop_time, [
                                    'class' => 'form-control stop-time-input',
                                    'placeholder' => 'HH:MM',
                                    'required' => true
                                ]) ?>
                            </td>
                            <td>
                                <?= Html::dropDownList("Items[{$index}][shift]", $item->shift, ['A'=>'A', 'B'=>'B', 'C'=>'C', 'D'=>'D'], [
                                    'class' => 'form-control',
                                    'prompt' => 'Pilih...'
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
                                <?= Html::listBox("Items[{$index}][jenis_hambatan_ids]", $item->jenis_hambatan_ids, 
                                    !$model->isNewRecord && $model->mstMesinProses ? ArrayHelper::map($model->mstMesinProses->mstJenisHambatans, 'id', 'nama') : [], [
                                    'class' => 'form-control jenis-hambatan-select2',
                                    'multiple' => true
                                ]) ?>
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
        <div class="box-body no-padding">
            <table class="table table-bordered table-striped" id="hambatan-table-pfp" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 100px;">Start</th>
                        <th style="width: 100px;">Stop</th>
                        <th style="width: 100px;">Shift</th>
                        <th>Keterangan</th>
                        <th style="width: 200px;">Order PFP (jika ada)</th>
                        <th style="width: 200px;">NK PFP (jika ada)</th>
                        <th style="width: 250px;">Jenis Hambatan (Bisa Pilih Banyak)</th>
                        <th style="width: 50px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="hambatan-tbody-pfp">
                    <?php 
                    // Start offset for PFP items
                    $pfpOffset = max(100, count($items) + 100);
                    foreach ($pfpItems as $index => $item): 
                        $idx = $pfpOffset + $index;
                    ?>
                        <tr class="hambatan-row" data-index="<?= $idx ?>">
                            <td>
                                <?= Html::textInput("Items[{$idx}][start_time]", $item->start_time, [
                                    'class' => 'form-control start-time-input',
                                    'placeholder' => 'HH:MM',
                                    'required' => true
                                ]) ?>
                            </td>
                            <td>
                                <?= Html::textInput("Items[{$idx}][stop_time]", $item->stop_time, [
                                    'class' => 'form-control stop-time-input',
                                    'placeholder' => 'HH:MM',
                                    'required' => true
                                ]) ?>
                            </td>
                            <td>
                                <?= Html::dropDownList("Items[{$idx}][shift]", $item->shift, ['A'=>'A', 'B'=>'B', 'C'=>'C', 'D'=>'D'], [
                                    'class' => 'form-control',
                                    'prompt' => 'Pilih...'
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
                                <?= Html::listBox("Items[{$idx}][jenis_hambatan_ids]", $item->jenis_hambatan_ids, 
                                    !$model->isNewRecord && $model->mstMesinProses ? ArrayHelper::map($model->mstMesinProses->mstJenisHambatans, 'id', 'nama') : [], [
                                    'class' => 'form-control jenis-hambatan-select2',
                                    'multiple' => true
                                ]) ?>
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

$js = <<<JS
var rowIndex = 1000;
var pfpRowIndex = 2000;

// Preload current machine's hambatans if updating
var currentMachineHambatans = [];
function fetchHambatans(machineId, callback) {
    if (!machineId) {
        currentMachineHambatans = [];
        $('.jenis-hambatan-select2').each(function() {
            $(this).empty().trigger('change');
        });
        if (callback) callback();
        return;
    }
    $.ajax({
        url: '{$hambatansUrl}',
        data: { machine_id: machineId },
        dataType: 'json',
        success: function(data) {
            currentMachineHambatans = data;
            // Update all existing rows dropdown
            $('.jenis-hambatan-select2').each(function() {
                var val = $(this).val() || [];
                var dropdown = $(this);
                dropdown.empty();
                $.each(data, function(i, item) {
                    dropdown.append($('<option>', {
                        value: item.id,
                        text: item.nama
                    }));
                });
                dropdown.val(val).trigger('change');
            });
            if (callback) callback();
        }
    });
}

// When machine model changes, load its machines
$('#model-mesin-select').on('change', function() {
    var modelVal = $(this).val();
    $('#machine-select').empty().append('<option value="">Pilih Nomor Mesin ...</option>');
    if (!modelVal) return;
    
    $.ajax({
        url: '{$machinesUrl}',
        data: { model_mesin: modelVal },
        dataType: 'json',
        success: function(data) {
            $.each(data, function(i, item) {
                $('#machine-select').append($('<option>', {
                    value: item.id,
                    text: item.nama_mesin
                }));
            });
        }
    });
});

// When machine select changes, update all jenis hambatan dropdowns
$('#machine-select').on('change', function() {
    var machineId = $(this).val();
    fetchHambatans(machineId);
});

// Initialize NK select2
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

// Initialize for existing rows
$('.nk-select2').each(function() {
    initSelect2(this);
});

$('.wo-select2').each(function() {
    initWoSelect2(this);
});

$('.order-pfp-select2').each(function() {
    initOrderPfpSelect2(this);
});

$('.jenis-hambatan-select2').each(function() {
    initJenisHambatanSelect2(this);
});

$('.start-time-input, .stop-time-input').each(function() {
    initTimeMask(this);
});

// Clear NK when WO/Order PFP changes
$(document).on('change', '.wo-select2, .order-pfp-select2', function() {
    $(this).closest('tr').find('.nk-select2').val(null).trigger('change');
});

// If updating, load hambatans initially
var initialMachineId = $('#machine-select').val();
if (initialMachineId) {
    fetchHambatans(initialMachineId);
}

// Add new row set for Dyeing
$('#add-row-btn').on('click', function() {
    var newRow = $('<tr class="hambatan-row" data-index="' + rowIndex + '">');
    
    newRow.append('<td><input type="text" name="Items[' + rowIndex + '][start_time]" class="form-control start-time-input" placeholder="HH:MM" required></td>');
    newRow.append('<td><input type="text" name="Items[' + rowIndex + '][stop_time]" class="form-control stop-time-input" placeholder="HH:MM" required></td>');
    newRow.append('<td><select name="Items[' + rowIndex + '][shift]" class="form-control"><option value="">Pilih...</option><option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option></select></td>');
    newRow.append('<td><input type="text" name="Items[' + rowIndex + '][keterangan]" class="form-control keterangan-input" placeholder="Ketik Keterangan..."></td>');
    newRow.append('<td><select name="Items[' + rowIndex + '][no_wo]" class="form-control wo-select2"></select></td>');
    newRow.append('<td><select name="Items[' + rowIndex + '][no_kartu]" class="form-control nk-select2"></select></td>');
    
    var dropdownHtml = '<td><select name="Items[' + rowIndex + '][jenis_hambatan_ids][]" class="form-control jenis-hambatan-select2" multiple="multiple">';
    $.each(currentMachineHambatans, function(i, item) {
        dropdownHtml += '<option value="' + item.id + '">' + item.nama + '</option>';
    });
    dropdownHtml += '</select></td>';
    newRow.append(dropdownHtml);
    newRow.append('<td style="text-align: center; vertical-align: middle;"><button type="button" class="btn btn-sm btn-danger remove-row-btn"><i class="glyphicon glyphicon-trash"></i></button></td>');
    
    $('#hambatan-tbody').append(newRow);
    
    // Init select2 on new selects
    initSelect2(newRow.find('.nk-select2'));
    initWoSelect2(newRow.find('.wo-select2'));
    initJenisHambatanSelect2(newRow.find('.jenis-hambatan-select2'));
    initTimeMask(newRow.find('.start-time-input'));
    initTimeMask(newRow.find('.stop-time-input'));
    
    rowIndex++;
});

// Add new row set for PFP
$('#add-row-btn-pfp').on('click', function() {
    var newRow = $('<tr class="hambatan-row" data-index="' + pfpRowIndex + '">');
    
    newRow.append('<td><input type="text" name="Items[' + pfpRowIndex + '][start_time]" class="form-control start-time-input" placeholder="HH:MM" required></td>');
    newRow.append('<td><input type="text" name="Items[' + pfpRowIndex + '][stop_time]" class="form-control stop-time-input" placeholder="HH:MM" required></td>');
    newRow.append('<td><select name="Items[' + pfpRowIndex + '][shift]" class="form-control"><option value="">Pilih...</option><option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option></select></td>');
    newRow.append('<td><input type="text" name="Items[' + pfpRowIndex + '][keterangan]" class="form-control keterangan-input" placeholder="Ketik Keterangan..."></td>');
    newRow.append('<td><select name="Items[' + pfpRowIndex + '][no_wo]" class="form-control order-pfp-select2"></select></td>');
    newRow.append('<td><select name="Items[' + pfpRowIndex + '][no_kartu]" class="form-control nk-select2"></select></td>');
    
    var dropdownHtml = '<td><select name="Items[' + pfpRowIndex + '][jenis_hambatan_ids][]" class="form-control jenis-hambatan-select2" multiple="multiple">';
    $.each(currentMachineHambatans, function(i, item) {
        dropdownHtml += '<option value="' + item.id + '">' + item.nama + '</option>';
    });
    dropdownHtml += '</select></td>';
    newRow.append(dropdownHtml);
    newRow.append('<td style="text-align: center; vertical-align: middle;"><button type="button" class="btn btn-sm btn-danger remove-row-btn-pfp"><i class="glyphicon glyphicon-trash"></i></button></td>');
    
    $('#hambatan-tbody-pfp').append(newRow);
    
    // Init select2 on new selects
    initSelect2(newRow.find('.nk-select2'));
    initOrderPfpSelect2(newRow.find('.order-pfp-select2'));
    initJenisHambatanSelect2(newRow.find('.jenis-hambatan-select2'));
    initTimeMask(newRow.find('.start-time-input'));
    initTimeMask(newRow.find('.stop-time-input'));
    
    pfpRowIndex++;
});

// Remove row
$(document).on('click', '.remove-row-btn', function() {
    if ($('#hambatan-tbody .hambatan-row').length > 1) {
        $(this).closest('tr').remove();
    } else {
        alert('Minimal harus ada satu set hambatan Dyeing.');
    }
});

$(document).on('click', '.remove-row-btn-pfp', function() {
    if ($('#hambatan-tbody-pfp .hambatan-row').length > 1) {
        $(this).closest('tr').remove();
    } else {
        alert('Minimal harus ada satu set hambatan PFP.');
    }
});
JS;

$this->registerJs($js);
?>
