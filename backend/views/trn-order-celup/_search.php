<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnOrderCelupSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-order-celup-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sc_id') ?>

    <?= $form->field($model, 'greige_group_id') ?>

    <?= $form->field($model, 'greige_id') ?>

    <?= $form->field($model, 'no_urut') ?>

    <?php // echo $form->field($model, 'no') ?>

    <?php // echo $form->field($model, 'qty') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'date') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
