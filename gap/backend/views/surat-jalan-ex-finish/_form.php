<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\SuratJalanExFinish */
/* @var $form ActiveForm */
?>

<div class="surat-jalan-ex-finish-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box">
        <div class="box-body">
            <?php
            $ajaxUrl = Url::to(['ajax/jual-ex-finish-search']);
            $noMemoLbl = empty($model->memo_id) ? '' : $model->memo->no;
            echo $form->field($model, 'memo_id')->widget(Select2::class, [
                'initValueText' => $noMemoLbl, // set the initial display text
                'options' => ['placeholder' => 'Cari Memo...'],
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
            ])->label('Pilih Memo Penjualan');
            ?>

            <?= $form->field($model, 'pengirim')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'penerima')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'kepala_gudang')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'plat_nomor')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>
        </div>
    </div>

    <div class="box-footer">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
