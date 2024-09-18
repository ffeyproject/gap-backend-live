<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\MstGreigeGroupSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mst-greige-group-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'jenis_kain') ?>

    <?= $form->field($model, 'nama_kain') ?>

    <?= $form->field($model, 'qty_per_batch') ?>

    <?= $form->field($model, 'unit') ?>

    <?php // echo $form->field($model, 'nilai_penyusutan') ?>

    <?php // echo $form->field($model, 'gramasi_kain') ?>

    <?php // echo $form->field($model, 'sulam_pinggir') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'aktif')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
