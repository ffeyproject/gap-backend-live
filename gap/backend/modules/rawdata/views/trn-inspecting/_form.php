<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnInspecting */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-inspecting-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sc_id')->textInput() ?>

    <?= $form->field($model, 'sc_greige_id')->textInput() ?>

    <?= $form->field($model, 'mo_id')->textInput() ?>

    <?= $form->field($model, 'wo_id')->textInput() ?>

    <?= $form->field($model, 'kartu_process_dyeing_id')->textInput() ?>

    <?= $form->field($model, 'jenis_process')->textInput() ?>

    <?= $form->field($model, 'no_urut')->textInput() ?>

    <?= $form->field($model, 'no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'tanggal_inspeksi')->textInput() ?>

    <?= $form->field($model, 'no_lot')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'kombinasi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'unit')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'approved_at')->textInput() ?>

    <?= $form->field($model, 'approved_by')->textInput() ?>

    <?= $form->field($model, 'approval_reject_note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'delivered_at')->textInput() ?>

    <?= $form->field($model, 'delivered_by')->textInput() ?>

    <?= $form->field($model, 'delivery_reject_note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'kartu_process_printing_id')->textInput() ?>

    <?= $form->field($model, 'memo_repair_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
