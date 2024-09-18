<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnKartuProsesPfpSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-kartu-proses-pfp-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'greige_group_id') ?>

    <?= $form->field($model, 'greige_id') ?>

    <?= $form->field($model, 'order_pfp_id') ?>

    <?= $form->field($model, 'no_urut') ?>

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

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'delivered_at') ?>

    <?php // echo $form->field($model, 'delivered_by') ?>

    <?php // echo $form->field($model, 'reject_notes') ?>

    <?php // echo $form->field($model, 'berat') ?>

    <?php // echo $form->field($model, 'lebar') ?>

    <?php // echo $form->field($model, 'k_density_lusi') ?>

    <?php // echo $form->field($model, 'k_density_pakan') ?>

    <?php // echo $form->field($model, 'gramasi') ?>

    <?php // echo $form->field($model, 'lebar_preset') ?>

    <?php // echo $form->field($model, 'lebar_finish') ?>

    <?php // echo $form->field($model, 'berat_finish') ?>

    <?php // echo $form->field($model, 't_density_lusi') ?>

    <?php // echo $form->field($model, 't_density_pakan') ?>

    <?php // echo $form->field($model, 'handling') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
