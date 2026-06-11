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

$js = <<<JS
var mesinData = $mesinDataJson;
var initialNoMesin = $initialNoMesin;

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
            if (woSelect.find("option[value='" + (woId || woVal) + "']").length === 0) {
                woSelect.append(new Option(woVal, woId || woVal, true, true));
            }
            woSelect.val(woId || woVal).trigger('change');
        } else {
            woSelect.val(null).trigger('change');
        }
    } else {
        row.find('td:eq(0) input').val(woVal);
    }
    
    var nkSelect = row.find('td:eq(1) select');
    if (nkSelect.length) {
        if (nkVal) {
            if (nkSelect.find("option[value='" + (nkId || nkVal) + "']").length === 0) {
                nkSelect.append(new Option(nkVal, nkId || nkVal, true, true));
            }
            nkSelect.val(nkId || nkVal).trigger('change');
        } else {
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
    prosesSelect.val(prosesVal);
    
    row.find('td:eq(5) input').val($(this).data('start'));
    row.find('td:eq(6) input').val($(this).data('stop'));
    row.find('td:eq(7) input').val($(this).data('nomesin'));
    row.find('td:eq(8) input').val($(this).data('temp'));
    row.find('td:eq(9) input').val($(this).data('speed'));
    row.find('td:eq(10) input').val($(this).data('gramasi'));
    row.find('td:eq(11) input').val($(this).data('program'));
    row.find('td:eq(12) input').val($(this).data('density'));
    
    $('html, body').animate({
        scrollTop: tbody.closest('.box').offset().top - 50
    }, 500);
    
    row.css('background-color', '#fff3cd');
    setTimeout(function() {
        row.css('background-color', '');
    }, 2000);
});
JS;
$this->registerJs($js);
?>

<?php if ($jenis_mesin && $no_mesin && $shift && $tanggal): ?>
<?php 
$noMesinStr = is_array($no_mesin) ? implode(', ', $no_mesin) : $no_mesin;
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
                            <td><?= Html::encode($record->kartuProcess->no) ?></td>
                            <td><?= $mo ? Html::encode($mo->design) : '-' ?></td>
                            <td><?= $woColor && $woColor->moColor ? Html::encode($woColor->moColor->color) : '-' ?></td>
                            <td><?= Html::encode($record->process->nama_proses) ?></td>
                            <td><?= Html::encode($val['start'] ?? '-') ?></td>
                            <td><?= Html::encode($val['stop'] ?? '-') ?></td>
                            <td><?= Html::encode($val['no_mesin'] ?? '-') ?></td>
                            <td><?= Html::encode($val['temp'] ?? '-') ?></td>
                            <td><?= Html::encode($val['speed'] ?? '-') ?></td>
                            <td><?= Html::encode($val['gramasi'] ?? '-') ?></td>
                            <td><?= Html::encode($val['program_number'] ?? '-') ?></td>
                            <td><?= Html::encode($val['density'] ?? '-') ?></td>
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
                                    data-start="<?= Html::encode($val['start'] ?? '') ?>"
                                    data-stop="<?= Html::encode($val['stop'] ?? '') ?>"
                                    data-nomesin="<?= Html::encode($val['no_mesin'] ?? '') ?>"
                                    data-temp="<?= Html::encode($val['temp'] ?? '') ?>"
                                    data-speed="<?= Html::encode($val['speed'] ?? '') ?>"
                                    data-gramasi="<?= Html::encode($val['gramasi'] ?? '') ?>"
                                    data-program="<?= Html::encode($val['program_number'] ?? '') ?>"
                                    data-density="<?= Html::encode($val['density'] ?? '') ?>"
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
                    <th>Start</th>
                    <th>Stop</th>
                    <th>No Mesin</th>
                    <th>Temp</th>
                    <th>Speed</th>
                    <th>Gramasi</th>
                    <th>Program Number</th>
                    <th>Density</th>
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
                            $wo = $record->kartuProcess->wo;
                            $mo = $record->kartuProcess->mo;
                            $woColor = $record->kartuProcess->woColor;
                        ?>
                        <tr>
                            <td><?= $wo ? Html::encode($wo->no) : '-' ?></td>
                            <td><?= Html::encode($record->kartuProcess->no) ?></td>
                            <td><?= $mo ? Html::encode($mo->design) : '-' ?></td>
                            <td><?= $woColor && $woColor->moColor ? Html::encode($woColor->moColor->color) : '-' ?></td>
                            <td><?= Html::encode($record->process->nama_proses) ?></td>
                            <td><?= Html::encode($val['start'] ?? '-') ?></td>
                            <td><?= Html::encode($val['stop'] ?? '-') ?></td>
                            <td><?= Html::encode($val['no_mesin'] ?? '-') ?></td>
                            <td><?= Html::encode($val['temp'] ?? '-') ?></td>
                            <td><?= Html::encode($val['speed'] ?? '-') ?></td>
                            <td><?= Html::encode($val['gramasi'] ?? '-') ?></td>
                            <td><?= Html::encode($val['program_number'] ?? '-') ?></td>
                            <td><?= Html::encode($val['density'] ?? '-') ?></td>
                            <td>
                                <button type="button" class="btn btn-default btn-xs btn-edit-row"
                                    data-target="pfp"
                                    data-wo="<?= $wo ? Html::encode($record->kartuProcess->orderPfp->no ?? $wo->no) : '' ?>"
                                    data-wo-id="<?= $wo ? Html::encode($record->kartuProcess->orderPfp->id ?? $wo->id) : '' ?>"
                                    data-nk="<?= Html::encode($record->kartuProcess->no) ?>"
                                    data-nk-id="<?= Html::encode($record->kartuProcess->id) ?>"
                                    data-motif="<?= $mo ? Html::encode($mo->design) : '' ?>"
                                    data-warna="<?= $woColor && $woColor->moColor ? Html::encode($woColor->moColor->color) : '' ?>"
                                    data-proses="<?= Html::encode($record->process->nama_proses) ?>"
                                    data-start="<?= Html::encode($val['start'] ?? '') ?>"
                                    data-stop="<?= Html::encode($val['stop'] ?? '') ?>"
                                    data-nomesin="<?= Html::encode($val['no_mesin'] ?? '') ?>"
                                    data-temp="<?= Html::encode($val['temp'] ?? '') ?>"
                                    data-speed="<?= Html::encode($val['speed'] ?? '') ?>"
                                    data-gramasi="<?= Html::encode($val['gramasi'] ?? '') ?>"
                                    data-program="<?= Html::encode($val['program_number'] ?? '') ?>"
                                    data-density="<?= Html::encode($val['density'] ?? '') ?>"
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
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tbody-input-dyeing">
                <tr>
                    <td style="min-width: 150px;">
                        <?= Select2::widget([
                            'name' => 'InputDyeing[wo]',
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
                            'name' => 'InputDyeing[nk]',
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
                    <td><input type="text" class="form-control" readonly></td>
                    <td><input type="text" class="form-control" readonly></td>
                    <td style="min-width: 150px;">
                        <?= Select2::widget([
                            'name' => 'InputDyeing[proses]',
                            'data' => $prosesDyeing,
                            'options' => ['placeholder' => 'Pilih Proses...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]) ?>
                    </td>
                    <td><input type="time" class="form-control"></td>
                    <td><input type="time" class="form-control"></td>
                    <td><input type="text" class="form-control" value="<?= Html::encode($noMesinStr) ?>"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><button class="btn btn-danger btn-sm"><i class="glyphicon glyphicon-trash"></i></button></td>
                </tr>
            </tbody>
        </table>
        </div>
        <button class="btn btn-success btn-sm"><i class="glyphicon glyphicon-plus"></i> Tambah Set Inputan</button>

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
                    <th>Density</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tbody-input-pfp">
                <tr>
                    <td style="min-width: 150px;">
                        <?= Select2::widget([
                            'name' => 'InputPfp[wo]',
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
                            'name' => 'InputPfp[nk]',
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
                    <td><input type="text" class="form-control" readonly></td>
                    <td><input type="text" class="form-control" readonly></td>
                    <td style="min-width: 150px;">
                        <?= Select2::widget([
                            'name' => 'InputPfp[proses]',
                            'data' => $prosesPfp,
                            'options' => ['placeholder' => 'Pilih Proses...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]) ?>
                    </td>
                    <td><input type="time" class="form-control"></td>
                    <td><input type="time" class="form-control"></td>
                    <td><input type="text" class="form-control" value="<?= Html::encode($noMesinStr) ?>"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><button class="btn btn-danger btn-sm"><i class="glyphicon glyphicon-trash"></i></button></td>
                </tr>
            </tbody>
        </table>
        </div>
        <button class="btn btn-success btn-sm"><i class="glyphicon glyphicon-plus"></i> Tambah Set Inputan</button>

    </div>
    <div class="box-footer">
        <button class="btn btn-primary"><i class="glyphicon glyphicon-save"></i> Simpan Data Input</button>
    </div>
</div>
<?php endif; ?>
