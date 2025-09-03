<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model common\models\ar\TrnKartuProsesDyeingItem */
?>

<div class="trn-kartu-proses-dyeing-item-form">
    <?php $form = ActiveForm::begin([
        'id' => 'form-edit-mesin',
        'enableAjaxValidation' => true,
    ]); ?>

    <?= $form->field($model, 'mesin')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>