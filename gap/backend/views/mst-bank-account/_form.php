<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstBankAccount */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<div class="box">
    <div class="box-body">
        <?= $form->field($model, 'bank_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'acct_no')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'acct_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'swift_code')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'correspondence')->textarea(['rows' => 6]) ?>
    </div>
</div>

<div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>