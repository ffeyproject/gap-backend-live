<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMixedGreigeItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-mixed-greige-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'mix_id')->textInput() ?>

    <?= $form->field($model, 'stock_greige_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
