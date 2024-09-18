<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnScAgen */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-sc-agen-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sc_id')->textInput() ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'nama_agen')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'attention')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'no_urut')->textInput() ?>

    <?= $form->field($model, 'no')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
