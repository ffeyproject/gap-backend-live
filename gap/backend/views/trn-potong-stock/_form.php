<?php

use common\widgets\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnPotongStock */
/* @var $modelsItem common\models\ar\TrnPotongStockItem[] */
/* @var $form yii\widgets\ActiveForm */

$js = '
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    //console.log(item);
    jQuery(".dynamicform_wrapper .colNomor").each(function(index) {
        jQuery(this).html((index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .colNomor").each(function(index) {
        jQuery(this).html((index + 1))
    });
});
';
$this->registerJs($js);
?>

<div class="trn-potong-stock-form">

    <?php $form = ActiveForm::begin(['id'=>'dynamic-form']); ?>

    <?=$form->errorSummary($modelsItem)?>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <?= $form->field($model, 'stock_id')->textInput() ?>

                    <?= $form->field($model, 'diperintahkan_oleh')->textInput() ?>

                    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Items</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body">
                    <?php
                    DynamicFormWidget::begin([
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
                            'panjang_m',
                        ],

                    ]);
                    ?>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Qty</th>
                            <th><button type="button" class="pull-right add-item btn btn-success btn-xs"><i class="fa fa-plus"></i></button></th>
                        </tr>
                        </thead>
                        <tbody class="container-items">
                        <?php foreach ($modelsItem as $index => $modelItem): ?>
                            <tr class="item">
                                <?php
                                // necessary for update action.
                                if (!$modelItem->isNewRecord) {
                                    echo Html::activeHiddenInput($modelItem, "[{$index}]id");
                                }
                                ?>
                                <td class="colNomor"><?=$index+1?></td>
                                <td><?= $form->field($modelItem, "[{$index}]qty")->textInput()->label(false) ?></td>
                                <td><button type="button" class="pull-right remove-item btn btn-danger btn-xs"><i class="fa fa-minus"></i></button></td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                    <?php DynamicFormWidget::end();?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
