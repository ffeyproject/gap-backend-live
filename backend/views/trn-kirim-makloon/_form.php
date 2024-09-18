<?php

use common\models\ar\MstGreigeGroup;
use kartik\widgets\Select2;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimMakloon */
/* @var $form ActiveForm */
?>

<div class="trn-kirim-makloon-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <?php
                    if($model->isNewRecord){
                        $wo = $model->wo_id === null ? '' : $model->wo->no;
                        echo $form->field($model, 'wo_id')->widget(Select2::class, [
                            'initValueText' => $wo, // set the initial display text
                            'options' => ['placeholder' => 'Cari WO...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 3,
                                'language' => [
                                    'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                                ],
                                'ajax' => [
                                    'url' => Url::to(['ajax/lookup-wo-all']),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                ],
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression('function(wo) { return wo.text; }'),
                                'templateSelection' => new JsExpression('function (wo) { return wo.text; }'),
                            ],
                            /*'pluginEvents' => [
                                'select2:select' => 'function(e){let lookupWoColorUrl = "'.$lookupWoColorUrl.'"; '.$this->renderFile(Yii::$app->controller->viewPath.'/js/wo-on-select.js').'}',
                                'select2:unselect' => 'function(e){$("#trnkartuprosesdyeing-wo_color_id").val(null).trigger("change"); $("#trnkartuprosesdyeing-wo_color_id").empty();}'
                            ]*/
                        ])->label('Nomor Working Order');
                    }else{
                        echo '<div class="form-group"><label>Nomor WO</label><input type="text" class="form-control" value="'.$model->wo->no.'" disabled></div>';
                    }
                    ?>
                </div>

                <div class="col-md-4">
                    <?=$form->field($model, 'date')->widget(\kartik\widgets\DatePicker::classname(), [
                        'options' => ['placeholder' => 'Pilih Tanggal ...'],
                        'readonly' => true,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,
                            //'todayBtn' => true,
                        ]
                    ])?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'vendor_id')->widget(Select2::classname(), [
                        'data' => \common\models\ar\MstVendor::optionList(),
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]) ?></div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'unit')->widget(Select2::classname(), [
                        'data' => MstGreigeGroup::unitOptions(),
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]) ?>
                </div>

                <div class="col-md-6"><?= $form->field($model, 'penerima')->textInput(['maxlength' => true]) ?></div>
            </div>

            <?= $form->field($model, 'note')->textarea(['rows' => 3]) ?>
        </div>

        <div class="box-footer">
            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
