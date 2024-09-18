<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnPotongGreigeItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-potong-greige-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'potong_greige_id')->textInput() ?>

    <?= $form->field($model, 'stock_greige_id')->textInput() ?>

    <?= $form->field($model, 'panjang_m')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
