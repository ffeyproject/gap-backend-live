<?php

use dosamigos\tinymce\TinyMce;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMemoPerubahanData */
/* @var $form ActiveForm */
?>

<div class="trn-memo-perubahan-data-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box">
        <div class="box-body">
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

            <?= $form->field($model, 'description')->widget(TinyMce::class, [
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

        <div class="box-footer">
            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
