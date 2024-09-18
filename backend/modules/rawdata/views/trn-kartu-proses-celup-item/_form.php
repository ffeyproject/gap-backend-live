<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnKartuProsesCelupItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-kartu-proses-celup-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'greige_group_id')->textInput() ?>

    <?= $form->field($model, 'greige_id')->textInput() ?>

    <?= $form->field($model, 'order_celup_id')->textInput() ?>

    <?= $form->field($model, 'kartu_process_id')->textInput() ?>

    <?= $form->field($model, 'stock_id')->textInput() ?>

    <?= $form->field($model, 'panjang_m')->textInput() ?>

    <?= $form->field($model, 'mesin')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tube')->textInput() ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
