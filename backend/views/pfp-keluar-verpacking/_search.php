<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\PfpKeluarVerpackingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pfp-keluar-verpacking-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'pfp_keluar_id') ?>

    <?= $form->field($model, 'no_urut') ?>

    <?= $form->field($model, 'no') ?>

    <?= $form->field($model, 'jenis') ?>

    <?php // echo $form->field($model, 'satuan') ?>

    <?php // echo $form->field($model, 'tgl_kirim') ?>

    <?php // echo $form->field($model, 'tgl_inspect') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'send_to_vendor')->checkbox() ?>

    <?php // echo $form->field($model, 'vendor_id') ?>

    <?php // echo $form->field($model, 'wo_id') ?>

    <?php // echo $form->field($model, 'vendor_address') ?>

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
