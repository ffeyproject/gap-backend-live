<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\JualExFinish */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="jual-ex-finish-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'jenis_gudang')->textInput() ?>

    <?= $form->field($model, 'customer_id')->textInput() ?>

    <?= $form->field($model, 'grade')->textInput() ?>

    <?= $form->field($model, 'harga')->textInput() ?>

    <?= $form->field($model, 'no_po')->textInput() ?>

    <?= $form->field($model, 'ongkir')->textInput() ?>

    <?= $form->field($model, 'pembayaran')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tanggal_pengiriman')->textInput() ?>

    <?= $form->field($model, 'komisi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'jenis_order')->textInput() ?>

    <?= $form->field($model, 'keterangan')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
