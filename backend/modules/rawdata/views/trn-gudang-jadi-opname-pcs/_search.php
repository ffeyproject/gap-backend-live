<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnGudangJadiOpnamePcsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-gudang-jadi-opname-pcs-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'opname_code') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'qr_code') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'locs_code') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'woNo') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Cari', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
