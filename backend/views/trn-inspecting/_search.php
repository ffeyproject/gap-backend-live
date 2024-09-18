<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnInspectingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-inspecting-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sc_id') ?>

    <?= $form->field($model, 'sc_greige_id') ?>

    <?= $form->field($model, 'mo_id') ?>

    <?= $form->field($model, 'wo_id') ?>

    <?php // echo $form->field($model, 'kartu_process_dyeing_id') ?>

    <?php // echo $form->field($model, 'kartu_process_printing_id') ?>

    <?php // echo $form->field($model, 'no_urut') ?>

    <?php // echo $form->field($model, 'no') ?>

    <?php // echo $form->field($model, 'date') ?>

    <?php // echo $form->field($model, 'tanggal_inspeksi') ?>

    <?php // echo $form->field($model, 'no_lot') ?>

    <?php // echo $form->field($model, 'kombinasi') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'grade_a_pcs') ?>

    <?php // echo $form->field($model, 'grade_b_pcs') ?>

    <?php // echo $form->field($model, 'grade_c_pcs') ?>

    <?php // echo $form->field($model, 'piece_kecil_pcs') ?>

    <?php // echo $form->field($model, 'sample_pcs') ?>

    <?php // echo $form->field($model, 'grade_a_roll') ?>

    <?php // echo $form->field($model, 'grade_b_roll') ?>

    <?php // echo $form->field($model, 'grade_c_roll') ?>

    <?php // echo $form->field($model, 'piece_kecil_roll') ?>

    <?php // echo $form->field($model, 'sample_roll') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'approved_at') ?>

    <?php // echo $form->field($model, 'approved_by') ?>

    <?php // echo $form->field($model, 'delivered_at') ?>

    <?php // echo $form->field($model, 'delivered_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
