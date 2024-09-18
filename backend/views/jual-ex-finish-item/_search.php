<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\JualExFinishItemSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="jual-ex-finish-item-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'jual_id') ?>

    <?= $form->field($model, 'greige_id') ?>

    <?= $form->field($model, 'grade') ?>

    <?= $form->field($model, 'qty') ?>

    <?php // echo $form->field($model, 'unit') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
