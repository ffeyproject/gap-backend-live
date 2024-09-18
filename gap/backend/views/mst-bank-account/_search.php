<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstBankAccountSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mst-bank-account-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'bank_name') ?>

    <?= $form->field($model, 'acct_no') ?>

    <?= $form->field($model, 'acct_name') ?>

    <?= $form->field($model, 'swift_code') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'correspondence') ?>

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
