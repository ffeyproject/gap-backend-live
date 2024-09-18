<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnScGreigeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-sc-greige-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sc_id') ?>

    <?= $form->field($model, 'greige_group_id') ?>

    <?= $form->field($model, 'process') ?>

    <?= $form->field($model, 'lebar_kain') ?>

    <?php // echo $form->field($model, 'merek') ?>

    <?php // echo $form->field($model, 'grade') ?>

    <?php // echo $form->field($model, 'piece_length') ?>

    <?php // echo $form->field($model, 'unit_price') ?>

    <?php // echo $form->field($model, 'price_param') ?>

    <?php // echo $form->field($model, 'qty') ?>

    <?php // echo $form->field($model, 'woven_selvedge') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'closed')->checkbox() ?>

    <?php // echo $form->field($model, 'closing_note') ?>

    <?php // echo $form->field($model, 'no_order_greige') ?>

    <?php // echo $form->field($model, 'no_urut_order_greige') ?>

    <?php // echo $form->field($model, 'order_greige_note') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
