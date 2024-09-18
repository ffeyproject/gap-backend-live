<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-kartu-proses-dyeing-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sc_id') ?>

    <?= $form->field($model, 'sc_greige_id') ?>

    <?= $form->field($model, 'mo_id') ?>

    <?= $form->field($model, 'wo_id') ?>

    <?php // echo $form->field($model, 'no_urut') ?>

    <?php // echo $form->field($model, 'no') ?>

    <?php // echo $form->field($model, 'no_proses') ?>

    <?php // echo $form->field($model, 'asal_greige') ?>

    <?php // echo $form->field($model, 'dikerjakan_oleh') ?>

    <?php // echo $form->field($model, 'lusi') ?>

    <?php // echo $form->field($model, 'pakan') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'date') ?>

    <?php // echo $form->field($model, 'posted_at') ?>

    <?php // echo $form->field($model, 'approved_at') ?>

    <?php // echo $form->field($model, 'approved_by') ?>

    <?php // echo $form->field($model, 'delivered_at') ?>

    <?php // echo $form->field($model, 'delivered_by') ?>

    <?php // echo $form->field($model, 'reject_notes') ?>

    <?php // echo $form->field($model, 'buka_greige') ?>

    <?php // echo $form->field($model, 'washing') ?>

    <?php // echo $form->field($model, 'relaxing') ?>

    <?php // echo $form->field($model, 'scutcher_relaxing') ?>

    <?php // echo $form->field($model, 'pre_set') ?>

    <?php // echo $form->field($model, 'weight_reduce') ?>

    <?php // echo $form->field($model, 'cuci_wr') ?>

    <?php // echo $form->field($model, 'dyeing') ?>

    <?php // echo $form->field($model, 'scutcher_dyeing') ?>

    <?php // echo $form->field($model, 'setting') ?>

    <?php // echo $form->field($model, 'resin_finish') ?>

    <?php // echo $form->field($model, 'heat_cut') ?>

    <?php // echo $form->field($model, 'status') ?>

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
