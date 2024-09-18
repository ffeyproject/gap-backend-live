<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MutasiExFinishAlt */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mutasi-ex-finish-alt-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'no_referensi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pemohon')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'no_urut')->textInput() ?>

    <?= $form->field($model, 'no')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
