<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnKartuProsesDyeing */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-kartu-proses-dyeing-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sc_id')->textInput() ?>

    <?= $form->field($model, 'sc_greige_id')->textInput() ?>

    <?= $form->field($model, 'mo_id')->textInput() ?>

    <?= $form->field($model, 'wo_id')->textInput() ?>

    <?= $form->field($model, 'kartu_proses_id')->textInput() ?>

    <?= $form->field($model, 'no_urut')->textInput() ?>

    <?= $form->field($model, 'no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nomor_kartu')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'asal_greige')->textInput() ?>

    <?= $form->field($model, 'dikerjakan_oleh')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lusi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pakan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'posted_at')->textInput() ?>

    <?= $form->field($model, 'approved_at')->textInput() ?>

    <?= $form->field($model, 'approved_by')->textInput() ?>

    <?= $form->field($model, 'delivered_at')->textInput() ?>

    <?= $form->field($model, 'delivered_by')->textInput() ?>

    <?= $form->field($model, 'reject_notes')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'memo_pg')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'memo_pg_at')->textInput() ?>

    <?= $form->field($model, 'memo_pg_by')->textInput() ?>

    <?= $form->field($model, 'memo_pg_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'berat')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lebar')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'k_density_lusi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'k_density_pakan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lebar_preset')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lebar_finish')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'berat_finish')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 't_density_lusi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 't_density_pakan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'handling')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hasil_tes_gosok')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'wo_color_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>