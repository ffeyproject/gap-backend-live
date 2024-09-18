<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\InspectingPrintingReject */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inspecting-printing-reject-form">
    <?php $form = ActiveForm::begin(['id'=>'InspectingPrintingRejectForm']); ?>

    <div class="row">
        <div class="col-md-8"><?= $form->field($model, 'untuk_bagian')->textInput(['maxlength' => true]) ?></div>

        <div class="col-md-4"><?= $form->field($model, 'pcs')->textInput(['maxlength' => true]) ?></div>
    </div>

    <div class="row">
        <div class="col-md-4"><?= $form->field($model, 'penerima')->textInput(['maxlength' => true]) ?></div>

        <div class="col-md-4"><?= $form->field($model, 'mengetahui')->textInput(['maxlength' => true]) ?></div>

        <div class="col-md-4"><?= $form->field($model, 'pengirim')->textInput(['maxlength' => true]) ?></div>
    </div>

    <?= $form->field($model, 'keterangan')->textarea(['rows' => 3]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs($this->renderFile(__DIR__.'/js/form.js'), \yii\web\View::POS_END, 'inspectingPringtingRejectFormJs');
