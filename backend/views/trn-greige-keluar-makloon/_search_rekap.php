<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnGreigeKeluarSearch */
/* @var $form ActiveForm */
?>

<div class="trn-greige-keluar-search">

    <?php $form = ActiveForm::begin([
        'action' => ['rekap'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>



    <?= $form->field($model, 'no') ?>

    <?= $form->field($model, 'dateRange') ?>

    <?= $form->field($model, 'no_urut') ?>

    <?= $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'posted_at') ?>

    <?php // echo $form->field($model, 'approved_at') ?>

    <?php // echo $form->field($model, 'approved_by') ?>

    <?php // echo $form->field($model, 'status') ?>

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
