<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnKartuProsesMaklonItemSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-kartu-proses-maklon-item-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sc_id') ?>

    <?= $form->field($model, 'sc_greige_id') ?>

    <?= $form->field($model, 'mo_id') ?>

    <?= $form->field($model, 'wo_id') ?>

    <?php // echo $form->field($model, 'kartu_process_id') ?>

    <?php // echo $form->field($model, 'stock_id') ?>

    <?php // echo $form->field($model, 'panjang_m') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'date') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
