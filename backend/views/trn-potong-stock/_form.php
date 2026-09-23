<?php

use common\widgets\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnPotongStock */
/* @var $modelsItem common\models\ar\TrnPotongStockItem[] */
/* @var $form yii\widgets\ActiveForm */

$getStockUrl = Url::to(['/trn-potong-stock/get-stock']);

$js = <<<JS
var currentStockQty = 0;
var currentStockUnit = '';

function calculateSummary() {
    var totalCut = 0;
    $(".container-items .item").each(function() {
        var inputVal = $(this).find('input[name*="[qty]"]').val();
        var val = parseFloat(inputVal);
        if (!isNaN(val)) {
            totalCut += val;
        }
    });

    totalCut = Math.round(totalCut * 100) / 100;
    $("#summary-total-cut").text(totalCut.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

    if (currentStockQty > 0) {
        var remaining = Math.round((currentStockQty - totalCut) * 100) / 100;
        $("#summary-remaining-qty").text(remaining.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

        if (totalCut > currentStockQty) {
            $("#summary-remaining-box").removeClass("alert-info alert-success").addClass("alert-danger");
            $("#summary-remaining-status").html('<span class="label label-danger"><i class="fa fa-times-circle"></i> Melebihi Stock Awal!</span>');
            $("#cut-warning-msg").show().html('<i class="fa fa-exclamation-triangle"></i> Total pemotongan (' + totalCut + ' ' + currentStockUnit + ') melebihi stock awal (' + currentStockQty + ' ' + currentStockUnit + '). Mohon sesuaikan nilai pemotongan.');
            $("#btn-save-potong").prop("disabled", true);
        } else if (totalCut === currentStockQty) {
            $("#summary-remaining-box").removeClass("alert-danger alert-info").addClass("alert-success");
            $("#summary-remaining-status").html('<span class="label label-success"><i class="fa fa-check"></i> Habis Terpotong (Tanpa Sisa)</span>');
            $("#cut-warning-msg").hide();
            $("#btn-save-potong").prop("disabled", false);
        } else {
            $("#summary-remaining-box").removeClass("alert-danger alert-success").addClass("alert-info");
            $("#summary-remaining-status").html('<span class="label label-primary"><i class="fa fa-info-circle"></i> Sisa ' + remaining + ' ' + currentStockUnit + ' tetap tersimpan di ID Stock ini</span>');
            $("#cut-warning-msg").hide();
            $("#btn-save-potong").prop("disabled", false);
        }
    } else {
        $("#summary-remaining-qty").text("0.00");
        $("#summary-remaining-status").html('<span class="text-muted">Menunggu input Stock ID</span>');
        $("#summary-remaining-box").removeClass("alert-danger alert-success").addClass("alert-info");
        $("#cut-warning-msg").hide();
        $("#btn-save-potong").prop("disabled", false);
    }
}

jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .colNomor").each(function(index) {
        jQuery(this).html((index + 1));
    });
    calculateSummary();
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .colNomor").each(function(index) {
        jQuery(this).html((index + 1));
    });
    calculateSummary();
});

$(document).on("input change", '.container-items input[name*="[qty]"]', function() {
    calculateSummary();
});

var getStockUrl = "{$getStockUrl}";

function loadStockDetail(stockId) {
    if (!stockId) {
        $("#stock-detail-container").html('');
        currentStockQty = 0;
        currentStockUnit = '';
        $("#summary-stock-qty").text("0.00");
        $(".summary-stock-unit").text("-");
        calculateSummary();
        return;
    }

    $("#stock-detail-container").html('<div class="text-center text-muted" style="padding: 10px;"><i class="fa fa-spinner fa-spin"></i> Memuat detail barang...</div>');

    $.ajax({
        url: getStockUrl,
        type: "GET",
        data: { id: stockId },
        dataType: "json",
        success: function(response) {
            if (response.success) {
                var d = response.data;
                currentStockQty = d.qty_raw;
                currentStockUnit = d.unit;

                $("#summary-stock-qty").text(d.qty);
                $(".summary-stock-unit").text(d.unit);
                $("#th-qty-label").text('Qty Potongan (' + d.unit + ')');

                var statusBadgeClass = d.status_id == 1 ? "label label-success" : "label label-danger";
                var statusHtml = '<span class="' + statusBadgeClass + '">' + d.status + '</span>';
                if (d.dipotong) {
                    statusHtml += ' <span class="label label-warning">Sudah Dipotong</span>';
                }

                var html = '<div class="panel panel-info" style="margin-top: 10px; margin-bottom: 15px;">' +
                    '<div class="panel-heading" style="padding: 6px 12px;">' +
                        '<h4 class="panel-title" style="font-size: 13px; font-weight: bold;">' +
                            '<i class="fa fa-cube"></i> Detail Barang (Stock ID: ' + d.id + ')' +
                        '</h4>' +
                    '</div>' +
                    '<div class="panel-body" style="padding: 0;">' +
                        '<table class="table table-bordered table-striped table-condensed" style="margin-bottom: 0; font-size: 12px;">' +
                            '<tbody>' +
                                '<tr><th style="width: 35%;">Nomor Roll / Stock</th><td><strong>' + d.no + '</strong></td></tr>' +
                                '<tr><th>Nomor WO</th><td>' + d.wo_no + '</td></tr>' +
                                '<tr><th>Motif / Kain</th><td>' + d.nama_kain + '</td></tr>' +
                                '<tr><th>Artikel</th><td>' + d.article + '</td></tr>' +
                                '<tr><th>Design</th><td>' + d.design + '</td></tr>' +
                                '<tr><th>Warna</th><td>' + d.color + '</td></tr>' +
                                '<tr><th>Qty Stock</th><td style="font-weight: bold; color: #0073b7; font-size: 13px;">' + d.qty + ' ' + d.unit + '</td></tr>' +
                                '<tr><th>Grade</th><td>' + d.grade + '</td></tr>' +
                                '<tr><th>Jenis Gudang</th><td>' + d.jenis_gudang + '</td></tr>' +
                                '<tr><th>No. Lot</th><td>' + d.no_lot + '</td></tr>' +
                                '<tr><th>Lokasi</th><td>' + d.locs_code + '</td></tr>' +
                                '<tr><th>Status</th><td>' + statusHtml + '</td></tr>' +
                            '</tbody>' +
                        '</table>' +
                    '</div>' +
                '</div>';

                if (response.warnings && response.warnings.length > 0) {
                    html += '<div class="alert alert-warning" style="padding: 8px 12px; margin-bottom: 15px; font-size: 12px;"><i class="fa fa-exclamation-triangle"></i> ' + response.warnings.join('<br>') + '</div>';
                }

                $("#stock-detail-container").html(html);
                calculateSummary();
            } else {
                currentStockQty = 0;
                currentStockUnit = '';
                $("#summary-stock-qty").text("0.00");
                $(".summary-stock-unit").text("-");
                $("#stock-detail-container").html('<div class="alert alert-danger" style="padding: 8px 12px; margin-top: 10px; margin-bottom: 15px; font-size: 12px;"><i class="fa fa-exclamation-triangle"></i> ' + (response.message || 'Stock ID tidak ditemukan.') + '</div>');
                calculateSummary();
            }
        },
        error: function() {
            currentStockQty = 0;
            currentStockUnit = '';
            $("#summary-stock-qty").text("0.00");
            $(".summary-stock-unit").text("-");
            $("#stock-detail-container").html('<div class="alert alert-danger" style="padding: 8px 12px; margin-top: 10px; margin-bottom: 15px; font-size: 12px;"><i class="fa fa-exclamation-triangle"></i> Terjadi kesalahan saat memuat data stock.</div>');
            calculateSummary();
        }
    });
}

var lookupTimeout = null;
$("#trnpotongstock-stock_id").on("input change", function() {
    clearTimeout(lookupTimeout);
    var val = $.trim($(this).val());
    lookupTimeout = setTimeout(function() {
        loadStockDetail(val);
    }, 400);
});

$("#trnpotongstock-stock_id").on("blur", function() {
    clearTimeout(lookupTimeout);
    var val = $.trim($(this).val());
    loadStockDetail(val);
});

// Auto load jika stock_id sudah ada nilainya (misal saat edit / validasi error)
var initialStockId = $.trim($("#trnpotongstock-stock_id").val());
if (initialStockId) {
    loadStockDetail(initialStockId);
}
JS;
$this->registerJs($js);
?>

<div class="trn-potong-stock-form">

    <?php $form = ActiveForm::begin(['id'=>'dynamic-form']); ?>

    <?=$form->errorSummary($modelsItem)?>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <?= $form->field($model, 'stock_id')->textInput(['placeholder' => 'Masukkan ID Stock Gudang Jadi...']) ?>

                    <div id="stock-detail-container"></div>

                    <?= $form->field($model, 'diperintahkan_oleh')->textInput(['placeholder' => 'Nama pemberi perintah...']) ?>

                    <?= $form->field($model, 'note')->textarea(['rows' => 4, 'placeholder' => 'Catatan pemotongan...']) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><strong>Items Potongan (Hasil Roll Baru)</strong></h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body">
                    <p class="text-muted" style="font-size: 12px; margin-bottom: 10px;">
                        <i class="fa fa-info-circle"></i> Masukkan ukuran/qty tiap roll hasil pemotongan. Sisa stock akan tetap tersimpan di ID Stock yang dipilih.
                    </p>

                    <?php
                    DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper',
                        'widgetBody' => '.container-items',
                        'widgetItem' => '.item',
                        'insertButton' => '.add-item',
                        'deleteButton' => '.remove-item',
                        'model' => $modelsItem[0],
                        'formId' => 'dynamic-form',
                        'formFields' => [
                            'qty',
                        ],
                    ]);
                    ?>
                    <table class="table table-bordered table-hover">
                        <thead style="background-color: #f9f9f9;">
                        <tr>
                            <th style="width: 40px; text-align: center;">#</th>
                            <th id="th-qty-label">Qty Potongan</th>
                            <th style="width: 50px; text-align: center;">
                                <button type="button" class="add-item btn btn-success btn-xs" title="Tambah Baris Potongan"><i class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="container-items">
                        <?php foreach ($modelsItem as $index => $modelItem): ?>
                            <tr class="item">
                                <?php
                                if (!$modelItem->isNewRecord) {
                                    echo Html::activeHiddenInput($modelItem, "[{$index}]id");
                                }
                                ?>
                                <td class="colNomor" style="text-align: center; vertical-align: middle; font-weight: bold;"><?=$index+1?></td>
                                <td>
                                    <?= $form->field($modelItem, "[{$index}]qty", ['options' => ['style' => 'margin-bottom: 0;']])
                                        ->textInput(['placeholder' => 'Qty potongan...', 'type' => 'number', 'step' => '0.01', 'min' => '0.01'])
                                        ->label(false) ?>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <button type="button" class="remove-item btn btn-danger btn-xs" title="Hapus Baris"><i class="fa fa-minus"></i></button>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                    <?php DynamicFormWidget::end();?>

                    <!-- Kalkulasi Ringkasan Pemotongan -->
                    <div id="summary-remaining-box" class="alert alert-info" style="margin-top: 15px; margin-bottom: 0; padding: 10px 15px;">
                        <table style="width: 100%; font-size: 13px;">
                            <tr>
                                <td style="width: 50%;">Qty Stock Awal:</td>
                                <td style="text-align: right;"><strong><span id="summary-stock-qty">0.00</span> <span class="summary-stock-unit">-</span></strong></td>
                            </tr>
                            <tr>
                                <td>Total Qty Dipotong:</td>
                                <td style="text-align: right; color: #3c8dbc;"><strong><span id="summary-total-cut">0.00</span> <span class="summary-stock-unit">-</span></strong></td>
                            </tr>
                            <tr style="border-top: 1px solid rgba(0,0,0,0.1);">
                                <td style="padding-top: 5px;"><strong>Sisa Qty di ID Stock Ini:</strong></td>
                                <td style="padding-top: 5px; text-align: right; font-size: 14px;"><strong><span id="summary-remaining-qty">0.00</span> <span class="summary-stock-unit">-</span></strong></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top: 5px; text-align: right;" id="summary-remaining-status">
                                    <span class="text-muted">Menunggu input Stock ID</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div id="cut-warning-msg" class="alert alert-danger" style="display: none; margin-top: 10px; margin-bottom: 0; padding: 8px 12px; font-size: 12px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success btn-lg', 'id' => 'btn-save-potong']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
