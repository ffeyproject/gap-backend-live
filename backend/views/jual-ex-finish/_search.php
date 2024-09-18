<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\JualExFinishSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="jual-ex-finish-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'jenis_gudang') ?>

    <?= $form->field($model, 'customer_id') ?>

    <?= $form->field($model, 'grade') ?>

    <?= $form->field($model, 'harga') ?>

    <?php // echo $form->field($model, 'ongkir') ?>

    <?php // echo $form->field($model, 'pembayaran') ?>

    <?php // echo $form->field($model, 'tanggal_pengiriman') ?>

    <?php // echo $form->field($model, 'komisi') ?>

    <?php // echo $form->field($model, 'jenis_order') ?>

    <?php // echo $form->field($model, 'keterangan') ?>

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
