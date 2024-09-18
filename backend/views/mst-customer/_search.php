<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstCustomerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mst-customer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cust_no') ?>

    <?= $form->field($model, 'telp') ?>

    <?= $form->field($model, 'fax') ?>

    <?= $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'cp_name') ?>

    <?php // echo $form->field($model, 'cp_phone') ?>

    <?php // echo $form->field($model, 'cp_email') ?>

    <?php // echo $form->field($model, 'npwp') ?>

    <?php // echo $form->field($model, 'aktif')->checkbox() ?>

    <?php // echo $form->field($model, 'name') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
