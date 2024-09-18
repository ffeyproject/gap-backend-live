<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="kartu-proses-dyeing-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'wo_id') ?>

    <?= $form->field($model, 'no_proses') ?>

    <?= $form->field($model, 'no_kartu') ?>

    <?= $form->field($model, 'asal_greige') ?>

    <?php // echo $form->field($model, 'dikerjakan_oleh') ?>

    <?php // echo $form->field($model, 'lusi') ?>

    <?php // echo $form->field($model, 'pakan') ?>

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
