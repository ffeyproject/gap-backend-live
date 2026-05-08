<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstMesinProcessing */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<div class="box">
    <div class="box-body">
        <?= $form->field($model, 'nama_mesin')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'jenis_mesin')->dropDownList([
            'Relax' => 'Relax',
            'Celup' => 'Celup',
        ], ['prompt' => 'Select ...']) ?>

        <?= $form->field($model, 'jenis_nozzle')->dropDownList([
            'Relax' => 'Relax',
            'Celup' => 'Celup',
        ], ['prompt' => 'Select ...']) ?>

        <?= $form->field($model, 'ukuran_nozzle')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
