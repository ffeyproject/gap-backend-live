<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\MstGreige */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mst-greige-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'group_id')->textInput() ?>

    <?= $form->field($model, 'nama_kain')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'no_dok_referensi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'gap')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'aktif')->checkbox() ?>

    <?= $form->field($model, 'stock')->textInput() ?>

    <?= $form->field($model, 'booked')->textInput() ?>

    <?= $form->field($model, 'stock_pfp')->textInput() ?>

    <?= $form->field($model, 'booked_pfp')->textInput() ?>

    <?= $form->field($model, 'stock_wip')->textInput() ?>

    <?= $form->field($model, 'booked_wip')->textInput() ?>

    <?= $form->field($model, 'stock_ef')->textInput() ?>

    <?= $form->field($model, 'booked_ef')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
