<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\InspectingMklBjSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inspecting-mkl-bj-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'wo_id') ?>

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

    <?php // echo $form->field($model, 'no_urut') ?>

    <?php // echo $form->field($model, 'no') ?>

    <?php // echo $form->field($model, 'delivered_at') ?>

    <?php // echo $form->field($model, 'delivered_by') ?>

    <?php // echo $form->field($model, 'delivery_reject_note') ?>

    <?php // echo $form->field($model, 'k3l_code') ?>

    <?php // echo $form->field($model, 'defect') ?>

    <?php // echo $form->field($model, 'inspection_table') ?>

    <?php // echo $form->field($model, 'jenis_inspek') ?>

    <?php // echo $form->field($model, 'no_memo') ?>

    <?php // echo $form->field($model, 'note') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
