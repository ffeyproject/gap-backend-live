<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\MutasiExFinishItemSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mutasi-ex-finish-item-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'mutasi_id') ?>

    <?= $form->field($model, 'panjang_m') ?>

    <?= $form->field($model, 'note') ?>

    <?= $form->field($model, 'greige_id') ?>

    <?php // echo $form->field($model, 'greige_group_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
