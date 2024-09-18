<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnStockGreige */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-stock-greige-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'greige_group_id')->textInput() ?>

    <?= $form->field($model, 'greige_id')->textInput() ?>

    <?= $form->field($model, 'asal_greige')->textInput() ?>

    <?= $form->field($model, 'no_lapak')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'grade')->textInput() ?>

    <?= $form->field($model, 'lot_lusi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lot_pakan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'no_set_lusi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'panjang_m')->textInput() ?>

    <?= $form->field($model, 'status_tsd')->textInput() ?>

    <?= $form->field($model, 'no_document')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pengirim')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mengetahui')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'jenis_gudang')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'nomor_wo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'keputusan_qc')->textInput() ?>

    <?= $form->field($model, 'color')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
