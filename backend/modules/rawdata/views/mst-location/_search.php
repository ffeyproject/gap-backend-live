<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\MstGreigeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mst-greige-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'group_id') ?>

    <?= $form->field($model, 'nama_kain') ?>

    <?= $form->field($model, 'alias') ?>

    <?= $form->field($model, 'no_dok_referensi') ?>

    <?php // echo $form->field($model, 'gap') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'aktif')->checkbox() ?>

    <?php // echo $form->field($model, 'stock') ?>

    <?php // echo $form->field($model, 'booked') ?>

    <?php // echo $form->field($model, 'stock_pfp') ?>

    <?php // echo $form->field($model, 'booked_pfp') ?>

    <?php // echo $form->field($model, 'stock_wip') ?>

    <?php // echo $form->field($model, 'booked_wip') ?>

    <?php // echo $form->field($model, 'stock_ef') ?>

    <?php // echo $form->field($model, 'booked_ef') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
