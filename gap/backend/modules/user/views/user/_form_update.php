<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\user\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form box box-primary">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body table-responsive">
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
