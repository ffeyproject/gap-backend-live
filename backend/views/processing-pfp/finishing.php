<?php

use backend\models\form\FinishingProcessingPfpForm;
use common\models\ar\TrnStockGreige;
use common\widgets\dynamicform\DynamicFormWidget;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesPfp */
/* @var $modelsItem FinishingProcessingPfpForm[] */
/* @var $form ActiveForm */

$js = '
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .item-no").each(function(index) {
        jQuery(this).html((index + 1));
    });

    var $prevItem = jQuery(item).prev(".item");
    if ($prevItem.length > 0) {
        var prevVal = $prevItem.find(".jenis_printing_select").val();
        if (prevVal) {
            jQuery(item).find(".jenis_printing_select").val(prevVal);
        }
    }
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .item-no").each(function(index) {
        jQuery(this).html((index + 1));
    });
});

jQuery(document).off("change", ".jenis_printing_select").on("change", ".jenis_printing_select", function() {
    var currentVal = jQuery(this).val();
    if (!currentVal) return;

    var $currentItem = jQuery(this).closest(".item");
    $currentItem.nextAll(".item").each(function() {
        var $selectBelow = jQuery(this).find(".jenis_printing_select");
        if (!$selectBelow.val()) {
            $selectBelow.val(currentVal);
        }
    });
});

function hitungTotal(){
    let totalPanjang = 0;
    jQuery(".dynamicform_wrapper .panjang_unit").each(function(index) {
        var input = jQuery(this);
        totalPanjang += Number(input.val());
    });
    $("#TotalLength").html(totalPanjang);
}
';

$this->registerJs($js, $this::POS_END);
?>

<?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-items', // required: css class selector
        'widgetItem' => '.item', // required: css class
        //'limit' => 4, // the maximum times, an element can be cloned (default 999)
        //'min' => 0, // 0 or 1 (default 1)
        'insertButton' => '.add-item', // css class
        'deleteButton' => '.remove-item', // css class
        'model' => $modelsItem[0],
        'formId' => 'dynamic-form',
        'formFields' => [
            'qty',
            'jenis_printing',
        ],
    ]);?>

        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Jumlah</th>
                <th>Jenis Printing</th>
                <th class="text-center" style="width: 90px;">
                    <button type="button" class="add-item btn btn-success btn-xs"><span class="fa fa-plus"></span></button>
                </th>
            </tr>
            </thead>
            <tbody class="container-items">
            <?php foreach ($modelsItem as $index => $modelItem): ?>
                <tr class="item">
                    <td class="item-no">1</td>
                    <td>
                        <?= $form->field($modelItem, "[{$index}]qty")->textInput(['class'=>'form-control panjang_unit', 'onchange'=>'hitungTotal()'])->label(false) ?>
                    </td>
                    <td>
                        <?= $form->field($modelItem, "[{$index}]jenis_printing")->dropDownList(TrnStockGreige::jenisPrintingOptions(), ['class' => 'form-control jenis_printing_select', 'prompt' => 'Pilih Jenis Printing...'])->label(false) ?>
                    </td>
                    <td class="text-center vcenter" style="width: 90px;">
                        <button type="button" class="remove-item btn btn-danger btn-xs"><span class="fa fa-minus"></span></button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th>#</th>
                <th>Jumlah</th>
                <th>Jenis Printing</th>
                <th class="text-center" style="width: 90px;">
                    <button type="button" class="add-item btn btn-success btn-xs"><span class="fa fa-plus"></span></button>
                </th>
            </tr>
            </tfoot>
        </table>

    <?php DynamicFormWidget::end();?>

    <p><strong>TOTAL: <span id="TotalLength">0</span></strong></p>

    <div class="form-group">
        <?= Html::submitButton('Proses', ['class' => 'btn btn-primary']) ?>
    </div>

<?php
ActiveForm::end();
$this->registerJs($this->renderFile(__DIR__.'/js/form.js'), $this::POS_END, 'processingPfpFormJs');
