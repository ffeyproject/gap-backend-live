<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstLocationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mst-location-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php //$form->field($model, 'id') ?>

    <?php //$form->field($model, 'group_id') ?>

    <?php //$form->field($model, 'nama_kain') ?>

    <?php //$form->field($model, 'alias') ?>

    <?php //$form->field($model, 'no_dok_referensi') ?>

    <?php // echo $form->field($model, 'gap') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'aktif')->checkbox() ?>

    <div class="form-group">
        <?php //Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php //Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
