<?php

use common\models\ar\TrnScGreige;
use dosamigos\tinymce\TinyMce;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnScGreige */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-sc-greige-form">
    <?php $form = ActiveForm::begin(['id'=>'scGreigeForm']); ?>

    <div class="row">
        <div class="col-md-4">
            <?php
            $ajaxUrl = Url::to(['ajax/lookup-greige-group']);
            $greige = empty($model->greige_group_id) ? '' : $model->greigeGroup->nama_kain;
            echo $form->field($model, 'greige_group_id')->widget(Select2::class, [
                'initValueText' => $greige, // set the initial display text
                'options' => ['placeholder' => 'Cari greige group...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'language' => [
                        'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                    ],
                    'ajax' => [
                        'url' => $ajaxUrl,
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(greige) { return greige.text; }'),
                    'templateSelection' => new JsExpression('function (greige) { return greige.text; }'),
                ]
            ]);?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'process')->widget(\kartik\select2\Select2::className(), [
                'options' => ['placeholder' => 'Pilih ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
                'data' => [TrnScGreige::PROCESS_DYEING => 'Dyeing', TrnScGreige::PROCESS_PRINTING => 'Printing'],
            ]) ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($model, 'lebar_kain')->widget(\kartik\select2\Select2::className(), [
                'options' => ['placeholder' => 'Pilih ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
                'data' => $model::lebarKainOptions(),
            ]) ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($model, 'piece_length')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'merek')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'artikel_sc')->textInput(['maxlength' => true]) ?>

            <div id="printing-finish-calculator" style="display: none; background: #f0f7fd; border: 1px solid #d0e5f5; border-radius: 4px; padding: 10px 15px; margin-bottom: 15px;">
                <label style="font-weight: bold; color: #20638f; margin-bottom: 6px; display: block;">
                    <i class="fa fa-calculator"></i> Kalkulator Qty Finish (Printing):
                </label>
                <div class="row">
                    <div class="col-md-6">
                        <label class="control-label" style="font-size: 12px; margin-bottom: 3px;">Pilihan Qty Finish:</label>
                        <div style="margin-top: 3px;">
                            <label class="radio-inline" style="font-size: 13px; font-weight: normal; cursor: pointer;">
                                <input type="radio" name="qty_finish_type" value="meter" checked> Qty Finish Meter
                            </label>
                            <label class="radio-inline" style="font-size: 13px; font-weight: normal; cursor: pointer;">
                                <input type="radio" name="qty_finish_type" value="yard"> Qty Finish Yard
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label" id="qty_finish_label" style="font-size: 12px; margin-bottom: 3px;">Jumlah Qty Finish (Meter):</label>
                        <input type="number" step="any" min="0" class="form-control input-sm" id="qty_finish_val" placeholder="Masukkan jumlah qty finish...">
                    </div>
                </div>
                <div id="greige-group-info" style="margin-top: 8px; font-size: 12px; color: #666;">
                    <span class="text-muted"><i class="fa fa-info-circle"></i> Pilih Greige Group terlebih dahulu untuk kalkulasi otomatis.</span>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6"><?= $form->field($model, 'qty')->textInput(['maxlength' => true]) ?></div>

                <div class="col-md-6">
                    <?= $form->field($model, 'grade')->widget(\kartik\select2\Select2::className(), [
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'data' => $model::gradeOptions(),
                    ]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6"><?= $form->field($model, 'unit_price')->textInput(['maxlength' => true]) ?></div>

                <div class="col-md-6">
                    <?= $form->field($model, 'price_param')->widget(\kartik\select2\Select2::className(), [
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'data' => $model::priceParamOptions(),
                    ]) ?>
                </div>
            </div>

            <?=$form->field($model, 'woven_selvedge')->textInput()?>
        </div>

        <div class="col-md-6">
            <?=$form->field($model, 'note')->textArea(['rows' => 5])?>
            <?=$form->field($model, 'order_greige_note')->textArea(['rows' => 5])?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$ajaxGetGreigeGroupUrl = Url::to(['/ajax/get-greige-group']);
$processPrintingVal = TrnScGreige::PROCESS_PRINTING;
$this->registerJs("var ajaxGetGreigeGroupUrl = '{$ajaxGetGreigeGroupUrl}'; var processPrintingVal = {$processPrintingVal};", \yii\web\View::POS_HEAD);
$this->registerJs($this->renderFile(__DIR__.'/js/form.js'), \yii\web\View::POS_END, 'scGreigeFormJs');