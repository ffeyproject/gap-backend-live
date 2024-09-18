<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \backend\models\form\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Reset password';
$this->params['breadcrumbs'][] = $this->title;

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>

<div class="login-box">
    <div class="login-logo">
        <a href="<?= Url::to(['site/index'])?>" style="font-size: large"><b>GAP V2</b> APP</a>
    </div>

    <!-- /.login-logo -->
    <div class="login-box-body">
        <h4 class="text-center"><?= Html::encode($this->title) ?></h4>
        <p>Please choose your new password:</p>

        <?php $form = ActiveForm::begin(['id' => 'reset-password-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

        <?= $form
            ->field($model, 'password_repeat', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password_repeat')]) ?>

        <div class="form-group">
            <?= Html::submitButton('Reset Password', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->
