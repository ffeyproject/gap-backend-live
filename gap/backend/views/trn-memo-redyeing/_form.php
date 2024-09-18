<?php

use dosamigos\tinymce\TinyMce;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMemoRedyeing */
/* @var $form ActiveForm */
?>

<div class="trn-memo-redyeing-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <?php
                    $ajaxUrl = Url::to(['ajax/lookup-retur-buyer-redyeing']);
                    $wo = empty($model->wo_id) ? '' : $model->wo->no;
                    echo $form->field($model, 'retur_buyer_id')->widget(Select2::class, [
                        'initValueText' => $wo, // set the initial display text
                        'options' => ['placeholder' => 'Cari...'],
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
                            'templateResult' => new JsExpression('function(wo) { return wo.text; }'),
                            'templateSelection' => new JsExpression('function (wo) { return wo.text; }'),
                        ],
                    ])->label('Nomor Retur Buyer');
                    ?>
                </div>

                <div class="col-md-6">
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
            </div>

            <?= $form->field($model, 'note')->widget(TinyMce::class, [
                'options' => ['rows' => 6],
                'language' => 'id',
                'clientOptions' => [
                    'menubar' => false,
                    'plugins' => [
                        "lists",
                    ],
                    'toolbar' => " bold italic | alignleft aligncenter alignright alignjustify | bullist numlist"
                ]
            ])?>

            <?= $form->field($model, 'instruksi')->widget(TinyMce::class, [
                'options' => ['rows' => 6],
                'language' => 'id',
                'clientOptions' => [
                    'menubar' => false,
                    'plugins' => [
                        "lists",
                    ],
                    'toolbar' => " bold italic | alignleft aligncenter alignright alignjustify | bullist numlist"
                ]
            ])?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
