<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\MstGreigeGroup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mst-greige-group-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'jenis_kain')->textInput() ?>

    <?= $form->field($model, 'nama_kain')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qty_per_batch')->textInput() ?>

    <?= $form->field($model, 'unit')->textInput() ?>

    <?= $form->field($model, 'nilai_penyusutan')->textInput() ?>

    <?= $form->field($model, 'gramasi_kain')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sulam_pinggir')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'aktif')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
