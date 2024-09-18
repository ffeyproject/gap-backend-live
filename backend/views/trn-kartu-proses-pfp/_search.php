<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesPfpSearch */
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

    <?php // echo $form->field($model, 'pc_bukaan') ?>

    <?php // echo $form->field($model, 'pc_scouring') ?>

    <?php // echo $form->field($model, 'pc_relaxing') ?>

    <?php // echo $form->field($model, 'pc_scutcher') ?>

    <?php // echo $form->field($model, 'pc_preset') ?>

    <?php // echo $form->field($model, 'pc_weight_reducetion') ?>

    <?php // echo $form->field($model, 'pc_washing_off') ?>

    <?php // echo $form->field($model, 'pc_heat_sett') ?>

    <?php // echo $form->field($model, 'pc_padding') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
