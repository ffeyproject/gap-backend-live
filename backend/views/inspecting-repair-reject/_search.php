<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\InspectingRepairRejectSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inspecting-repair-reject-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'memo_repair_id') ?>

    <?= $form->field($model, 'no_urut') ?>

    <?= $form->field($model, 'no') ?>

    <?= $form->field($model, 'date') ?>

    <?php // echo $form->field($model, 'untuk_bagian') ?>

    <?php // echo $form->field($model, 'pcs') ?>

    <?php // echo $form->field($model, 'keterangan') ?>

    <?php // echo $form->field($model, 'penerima') ?>

    <?php // echo $form->field($model, 'mengetahui') ?>

    <?php // echo $form->field($model, 'pengirim') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
