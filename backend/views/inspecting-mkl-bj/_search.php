<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\InspectingMklBjSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inspecting-mkl-bj-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'wo_no') ?>

    <?= $form->field($model, 'wo_color_id') ?>

    <?= $form->field($model, 'tgl_inspeksi') ?>

    <?= $form->field($model, 'tgl_kirim') ?>

    <?php // echo $form->field($model, 'no_lot') ?>

    <?php // echo $form->field($model, 'jenis') ?>

    <?php // echo $form->field($model, 'satuan') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
