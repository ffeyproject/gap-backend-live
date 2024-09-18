<?php

use common\models\ar\TrnStockGreige;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesPfp */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-kartu-proses-pfp-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            $ajaxUrl = Url::to(['ajax/lookup-order-pfp']);
                            $orderPfp = empty($model->order_pfp_id) ? '' : $model->orderPfp->no;
                            echo $form->field($model, 'order_pfp_id')->widget(Select2::class, [
                                'initValueText' => $orderPfp, // set the initial display text
                                'options' => ['placeholder' => 'Cari Order PFP...'],
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
                                ]
                            ])->label('Nomor Order PFP');
                            ?>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($model, 'asal_greige')->widget(Select2::classname(), [
                                'data' => TrnStockGreige::asalGreigeOptions(),
                                'options' => ['placeholder' => 'Pilih ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6"><?= $form->field($model, 'dikerjakan_oleh')->textInput(['maxlength' => true]) ?></div>

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

                    <?= $form->field($model, 'berat')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'nomor_kartu')->textInput() ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">KONSTRUKSI GREIGE</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'lebar')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'gramasi')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'k_density_lusi')->textInput(['maxlength' => true])->label('Density Lusi') ?>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($model, 'lusi')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'pakan')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'k_density_pakan')->textInput(['maxlength' => true])->label('Density Pakan') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
