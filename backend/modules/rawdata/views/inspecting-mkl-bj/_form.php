<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\InspectingMklBj */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inspecting-mkl-bj-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'wo_id')->textInput() ?>

    <?= $form->field($model, 'wo_color_id')->textInput() ?>

    <?= $form->field($model, 'tgl_inspeksi')->textInput() ?>

    <?= $form->field($model, 'tgl_kirim')->textInput() ?>

    <?= $form->field($model, 'no_lot')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'jenis')->textInput() ?>

    <?= $form->field($model, 'satuan')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList(\common\models\ar\InspectingMklBj::statusOptions()) ?>

    <?= $form->field($model, 'no_urut')->textInput() ?>

    <?= $form->field($model, 'delivered_at')->textInput() ?>

    <?= $form->field($model, 'delivered_by')->textInput() ?>

    <?= $form->field($model, 'delivery_reject_note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'k3l_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'defect')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'jenis_inspek')->textInput() ?>

    <?= $form->field($model, 'no_memo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
