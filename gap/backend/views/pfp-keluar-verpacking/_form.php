<?php

use common\widgets\dynamicform\DynamicFormWidget;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\PfpKeluarVerpacking */
/* @var $form yii\widgets\ActiveForm */
/* @var $modelsItem common\models\ar\PfpKeluarVerpackingItem[] */

$pfpKeluarOnSelectJs = <<<JS
let params = e.params;
let data = params.data;
console.log(data);
$('#pfpkeluarverpacking-jenis').val(data.jenis).trigger('change');
$('#NomorReferensi').html(data.no_referensi);
JS;

$vendorOnSelectJs = <<<JS
let params = e.params;
let data = params.data;
console.log(data);
$('#pfpkeluarverpacking-vendor_address').val(data.address).trigger('change');
JS;

$jsDynamicForm = <<<JS
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-items").each(function(index) {
        jQuery(this).html((index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-items").each(function(index) {
        jQuery(this).html((index + 1))
    });
});
JS;
$this->registerJs($jsDynamicForm)
?>

<div class="pfp-keluar-verpacking-form">

    <?php $form = ActiveForm::begin(['id'=>'dynamic-form']); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <?=$form->field($model, 'tgl_kirim')->widget(\kartik\widgets\DatePicker::classname(), [
                        'options' => ['placeholder' => 'Pilih Tanggal ...'],
                        'readonly' => true,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,
                            'todayBtn' => true,
                        ]
                    ])?>

                    <?php
                    $kp = $model->pfp_keluar_id === null ? '' : $model->pfpKeluar->no;
                    echo $form->field($model, 'pfp_keluar_id')->widget(Select2::class, [
                        'initValueText' => $kp, // set the initial display text
                        'options' => ['placeholder' => 'Cari ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                            ],
                            'ajax' => [
                                'url' => Url::to(['ajax/lookup-pfp-keluar']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(wo) { return wo.text; }'),
                            'templateSelection' => new JsExpression('function (wo) { return wo.text; }'),
                        ],
                        'pluginEvents' => [
                            'select2:select' => 'function(e){'.$pfpKeluarOnSelectJs.'}',
                            'select2:unselect' => 'function(e){$("#pfpkeluarverpacking-jenis").val(null).trigger("change"); $("#NomorReferensi").html("");}'
                        ]
                    ])->label('No. PFP Keluar');?>

                    <?php
                    $wo = $model->wo_id === null ? '' : $model->wo->no;
                    echo $form->field($model, 'wo_id')->widget(Select2::class, [
                        'initValueText' => $wo, // set the initial display text
                        'options' => ['placeholder' => 'Cari ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                            ],
                            'ajax' => [
                                'url' => Url::to(['ajax/lookup-wo']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(wo) { return wo.text; }'),
                            'templateSelection' => new JsExpression('function (wo) { return wo.text; }'),
                        ],
                    ])->label('No. WO');?>

                    <?= $form->field($model, 'jenis')->widget(Select2::classname(), [
                        'data' => \common\models\ar\TrnPfpKeluar::jenisOptions(),
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]) ?>

                    <?php
                    $motif = $model->greige_id === null ? '' : $model->greigeNamaKain;
                    echo $form->field($model, 'greige_id')->widget(Select2::class, [
                        'initValueText' => $motif, // set the initial display text
                        'options' => ['placeholder' => 'Cari ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                            ],
                            'ajax' => [
                                'url' => Url::to(['ajax/lookup-greige']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(wo) { return wo.text; }'),
                            'templateSelection' => new JsExpression('function (wo) { return wo.text; }'),
                        ],
                    ])->label('Motif');?>

                    <p><strong>No. Referensi: </strong> <span id="NomorReferensi"></span></p>

                    <?= $form->field($model, 'satuan')->widget(Select2::classname(), [
                        'data' => \common\models\ar\MstGreigeGroup::unitOptions(),
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <?=$form->field($model, 'tgl_inspect')->widget(\kartik\widgets\DatePicker::classname(), [
                        'options' => ['placeholder' => 'Pilih Tanggal ...'],
                        'readonly' => true,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,
                            'todayBtn' => true,
                        ]
                    ])?>

                    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>
                </div>
            </div>

            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Pengiriman Keluar</h3>
                </div>
                <div class="box-body">
                    <?php
                    $vend = $model->vendor_id === null ? '' : $model->vendor->name;
                    echo $form->field($model, 'vendor_id')->widget(Select2::class, [
                        'initValueText' => $vend, // set the initial display text
                        'options' => ['placeholder' => 'Cari ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                            ],
                            'ajax' => [
                                'url' => Url::to(['ajax/lookup-vendor']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(wo) { return wo.text; }'),
                            'templateSelection' => new JsExpression('function (wo) { return wo.text; }'),
                        ],
                        'pluginEvents' => [
                            'select2:select' => 'function(e){'.$vendorOnSelectJs.'}',
                            'select2:unselect' => 'function(e){$("#pfpkeluarverpacking-vendor_address").val(null).trigger("change");}'
                        ]
                    ])->label('Vendor');?>

                    <?= $form->field($model, 'vendor_address')->textarea(['rows' => 6]) ?>
                </div>

                <div class="box-footer">
                    Jika vendor diisi, maka tidak dimasukan ke gudang jadi.
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Items</h3>
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
                    'pfp_keluar_verpacking_id',
                    'ukuran',
                    'join_piece',
                    'keterangan',
                ],
            ]);
            ?>

            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Ukuran</th>
                    <th>Join Piece</th>
                    <th>Keterangan</th>
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
                        <td><span class="panel-title-items"><?= ($index + 1) ?></span></td>
                        <td><?= $form->field($modelItem, "[{$index}]ukuran")->textInput()->label(false) ?></td>
                        <td><?= $form->field($modelItem, "[{$index}]join_piece")->textInput()->label(false) ?></td>
                        <td><?= $form->field($modelItem, "[{$index}]keterangan")->textInput()->label(false) ?></td>
                        <td><button type="button" class="pull-right remove-item btn btn-danger btn-xs"><i class="fa fa-minus"></i></button></td>
                    </tr>
                <?php endforeach;?>
                </tbody>
                <thead>
                <tr>
                    <th>No</th>
                    <th>Ukuran</th>
                    <th>Join Piece</th>
                    <th>Keterangan</th>
                    <th><button type="button" class="pull-right add-item btn btn-success btn-xs"><i class="fa fa-plus"></i></button></th>
                </tr>
                </thead>
            </table>

            <?php DynamicFormWidget::end();?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
