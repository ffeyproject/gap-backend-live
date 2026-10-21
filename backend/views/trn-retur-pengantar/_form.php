<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnReturPengantar;
use common\models\ar\TrnStockGreige;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnReturPengantar */
/* @var $selectedIds array trn_gudang_jadi.id yang dipilih */
/* @var $form ActiveForm */

$stocks = TrnReturPengantar::availableStockQuery($model->id)
    ->with(['wo', 'returFrom'])
    ->orderBy(['trn_gudang_jadi.source_ref' => SORT_DESC, 'trn_gudang_jadi.id' => SORT_ASC])
    ->all();
$units = MstGreigeGroup::unitOptions();
$grades = TrnStockGreige::gradeOptions();
?>

<div class="trn-retur-pengantar-form">

    <?php $form = ActiveForm::begin(['id' => 'retur-pengantar-form']); ?>

    <?= $form->errorSummary($model) ?>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'date')->widget(\kartik\widgets\DatePicker::classname(), [
                        'options' => ['placeholder' => 'Pilih Tanggal ...'],
                        'readonly' => true,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,
                        ]
                    ]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'bagian')->dropDownList(TrnReturPengantar::bagianOptions(), ['prompt' => '-- Pilih Bagian --'])
                        ->hint('Processing: stok masuk Gudang Mutasi dan bisa dipakai di Kartu Proses Mutasi.') ?>
                </div>
                <div class="col-md-3 field-bagian-lain" style="<?= $model->bagian == TrnReturPengantar::BAGIAN_LAIN ? '' : 'display: none' ?>">
                    <?= $form->field($model, 'bagian_lain')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'penerima')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <?= $form->field($model, 'note')->textarea(['rows' => 2]) ?>
        </div>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Pilih Stok Retur</h3>
            <div class="box-tools pull-right" style="width: 300px">
                <input type="text" class="form-control input-sm retur-pengantar-filter" placeholder="Cari WO / no retur / warna ...">
            </div>
        </div>
        <div class="box-body table-responsive">
            <?php if (empty($stocks)): ?>
                <p class="text-muted">Tidak ada stok retur di gudang jadi. Stok retur terbentuk saat Retur Buyer diposting.</p>
            <?php else: ?>
            <table class="table table-bordered table-condensed table-hover">
                <thead>
                <tr>
                    <th style="width: 30px"><input type="checkbox" class="retur-pengantar-all"></th>
                    <th>ID Stok</th>
                    <th>No Retur</th>
                    <th>WO</th>
                    <th>Warna</th>
                    <th class="text-right">Qty</th>
                    <th>Unit</th>
                    <th>Grade</th>
                    <th>Roll Asal</th>
                    <th>Tgl Masuk</th>
                </tr>
                <?php // filter per kolom, index data-col = urutan kolom (kolom checkbox = 0) ?>
                <tr class="retur-pengantar-col-filters">
                    <th></th>
                    <?php foreach (['ID', 'No Retur', 'WO', 'Warna', 'Qty', 'Unit', 'Grade', 'Roll Asal', 'Tgl Masuk'] as $i => $label): ?>
                        <th><input type="text" class="form-control input-sm retur-pengantar-col-filter" data-col="<?= $i + 1 ?>" placeholder="<?= $label ?>" autocomplete="off"></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($stocks as $stock): ?>
                    <tr class="retur-pengantar-row">
                        <td><?= Html::checkbox('stock_ids[]', in_array($stock->id, $selectedIds), ['value' => $stock->id, 'class' => 'retur-pengantar-check']) ?></td>
                        <td><?= Html::a('#'.$stock->id, ['/trn-gudang-jadi/view', 'id' => $stock->id], ['target' => '_blank']) ?></td>
                        <td><?= Html::encode($stock->source_ref) ?></td>
                        <td><?= Html::encode($stock->wo->no) ?></td>
                        <td><?= Html::encode($stock->getColorResolved()) ?></td>
                        <td class="text-right retur-pengantar-qty" data-qty="<?= $stock->qty ?>"><?= Yii::$app->formatter->asDecimal($stock->qty) ?></td>
                        <td><?= $units[$stock->unit] ?? '-' ?></td>
                        <td><?= $grades[$stock->grade] ?? '-' ?></td>
                        <td><?= $stock->returFrom !== null ? '#'.$stock->retur_from_id.($stock->returFrom->qr_code ? ' | '.Html::encode($stock->returFrom->qr_code) : '') : '<span class="text-muted">Tidak diketahui</span>' ?></td>
                        <td><?= Yii::$app->formatter->asDate($stock->date) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="5">Dipilih: <span class="retur-pengantar-count">0</span> roll</th>
                    <th class="text-right retur-pengantar-total">0</th>
                    <th colspan="4"></th>
                </tr>
                </tfoot>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$bagianLain = TrnReturPengantar::BAGIAN_LAIN;
$js = <<<JS
function returPengantarSummary() {
    var count = 0, total = 0;
    $('.retur-pengantar-check:checked').each(function () {
        count++;
        total += parseFloat($(this).closest('tr').find('.retur-pengantar-qty').data('qty')) || 0;
    });
    $('.retur-pengantar-count').text(count);
    $('.retur-pengantar-total').text(total.toLocaleString('id-ID'));
}

$('#trnreturpengantar-bagian').on('change', function () {
    $('.field-bagian-lain').toggle($(this).val() == '{$bagianLain}');
});

$(document).on('change', '.retur-pengantar-check', returPengantarSummary);

// centang semua hanya baris yang sedang tampil (hasil filter)
$('.retur-pengantar-all').on('change', function () {
    $('.retur-pengantar-row:visible .retur-pengantar-check').prop('checked', $(this).prop('checked'));
    returPengantarSummary();
});

// pencarian umum (semua kolom) + pencarian per kolom, baris tampil jika cocok dengan semuanya
function returPengantarFilter() {
    var q = $.trim($('.retur-pengantar-filter').val()).toLowerCase();
    var colFilters = [];
    $('.retur-pengantar-col-filter').each(function () {
        var val = $.trim($(this).val()).toLowerCase();
        if (val !== '') {
            colFilters.push({col: $(this).data('col'), val: val});
        }
    });

    $('.retur-pengantar-row').each(function () {
        var cells = $(this).children('td');
        var show = q === '' || $(this).text().toLowerCase().indexOf(q) !== -1;
        for (var i = 0; show && i < colFilters.length; i++) {
            show = cells.eq(colFilters[i].col).text().toLowerCase().indexOf(colFilters[i].val) !== -1;
        }
        $(this).toggle(show);
    });
}

$('.retur-pengantar-filter, .retur-pengantar-col-filter').on('input', returPengantarFilter);

// Enter di kolom pencarian jangan submit form
$('.retur-pengantar-filter, .retur-pengantar-col-filter').on('keydown', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        e.preventDefault();
    }
});

returPengantarSummary();
JS;
$this->registerJs($js, View::POS_READY);
?>
