<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MutasiExFinishAltItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mutasi-ex-finish-alt-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'mutasi_id')->textInput() ?>

    <?= $form->field($model, 'gudang_jadi_id')->textInput() ?>

    <?= $form->field($model, 'grade')->textInput() ?>

    <?= $form->field($model, 'qty')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
