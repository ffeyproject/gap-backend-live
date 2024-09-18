<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnStockGreigeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-stock-greige-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'greige_group_id') ?>

    <?= $form->field($model, 'greige_id') ?>

    <?= $form->field($model, 'no_lapak') ?>

    <?= $form->field($model, 'grade') ?>

    <?php // echo $form->field($model, 'lot_lusi') ?>

    <?php // echo $form->field($model, 'lot_pakan') ?>

    <?php // echo $form->field($model, 'no_set_lusi') ?>

    <?php // echo $form->field($model, 'panjang_m') ?>

    <?php // echo $form->field($model, 'status_tsd') ?>

    <?php // echo $form->field($model, 'no_document') ?>

    <?php // echo $form->field($model, 'pengirim') ?>

    <?php // echo $form->field($model, 'mengetahui') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'date') ?>

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
