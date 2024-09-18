<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnBuyGreigeItemSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-buy-greige-item-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'greige_group_id') ?>

    <?= $form->field($model, 'greige_id') ?>

    <?= $form->field($model, 'buy_greige_id') ?>

    <?= $form->field($model, 'qty') ?>

    <?php // echo $form->field($model, 'note') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
