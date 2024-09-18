<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\GudangJadiMutasiSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gudang-jadi-mutasi-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'no_urut') ?>

    <?= $form->field($model, 'nomor') ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'pengirim') ?>

    <?php // echo $form->field($model, 'penerima') ?>

    <?php // echo $form->field($model, 'kepala_gudang') ?>

    <?php // echo $form->field($model, 'dept_tujuan') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
