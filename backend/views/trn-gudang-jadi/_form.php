<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnGudangJadi */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-gudang-jadi-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'jenis_gudang')->textInput() ?>

    <?= $form->field($model, 'wo_id')->textInput() ?>

    <?= $form->field($model, 'source')->textInput() ?>

    <?= $form->field($model, 'source_ref')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'unit')->textInput() ?>

    <?= $form->field($model, 'qty')->textInput() ?>

    <?= $form->field($model, 'no_urut')->textInput() ?>

    <?= $form->field($model, 'no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'approved_at')->textInput() ?>

    <?= $form->field($model, 'approved_by')->textInput() ?>

    <?= $form->field($model, 'approval_reject_note')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
