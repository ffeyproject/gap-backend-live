<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnWo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-wo-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sc_id')->textInput() ?>

    <?= $form->field($model, 'sc_greige_id')->textInput() ?>

    <?= $form->field($model, 'mo_id')->textInput() ?>

    <?= $form->field($model, 'jenis_order')->textInput() ?>

    <?= $form->field($model, 'greige_id')->textInput() ?>

    <?= $form->field($model, 'mengetahui_id')->textInput() ?>

    <?= $form->field($model, 'apv_mengetahui_at')->textInput() ?>

    <?= $form->field($model, 'reject_note_mengetahui')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'no_urut')->textInput() ?>

    <?= $form->field($model, 'no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'plastic_size')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_mark')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'note_two')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'marketing_id')->textInput() ?>

    <?= $form->field($model, 'apv_marketing_at')->textInput() ?>

    <?= $form->field($model, 'reject_note_marketing')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'posted_at')->textInput() ?>

    <?= $form->field($model, 'closed_at')->textInput() ?>

    <?= $form->field($model, 'closed_by')->textInput() ?>

    <?= $form->field($model, 'closed_note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'batal_at')->textInput() ?>

    <?= $form->field($model, 'batal_by')->textInput() ?>

    <?= $form->field($model, 'batal_note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'handling_id')->textInput() ?>

    <?= $form->field($model, 'papper_tube_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
