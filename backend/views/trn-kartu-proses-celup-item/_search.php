<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesCelupItemSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-kartu-proses-celup-item-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'greige_group_id') ?>

    <?= $form->field($model, 'greige_id') ?>

    <?= $form->field($model, 'order_celup_id') ?>

    <?= $form->field($model, 'kartu_process_id') ?>

    <?php // echo $form->field($model, 'stock_id') ?>

    <?php // echo $form->field($model, 'panjang_m') ?>

    <?php // echo $form->field($model, 'mesin') ?>

    <?php // echo $form->field($model, 'tube') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
