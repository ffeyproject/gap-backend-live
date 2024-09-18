<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnScGreige */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-sc-greige-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sc_id')->textInput() ?>

    <?= $form->field($model, 'greige_group_id')->textInput() ?>

    <?= $form->field($model, 'process')->textInput() ?>

    <?= $form->field($model, 'lebar_kain')->textInput() ?>

    <?= $form->field($model, 'merek')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'grade')->textInput() ?>

    <?= $form->field($model, 'piece_length')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'unit_price')->textInput() ?>

    <?= $form->field($model, 'price_param')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qty')->textInput() ?>

    <?= $form->field($model, 'woven_selvedge')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'closed')->checkbox() ?>

    <?= $form->field($model, 'closing_note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'no_order_greige')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'no_urut_order_greige')->textInput() ?>

    <?= $form->field($model, 'order_greige_note')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
