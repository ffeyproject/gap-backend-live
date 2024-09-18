<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnInspectingItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-inspecting-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'qty')->textInput() ?>

    <?= $form->field($model, 'trn_inspecting_roll_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
