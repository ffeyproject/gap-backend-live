<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \backend\models\form\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Request password reset';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="login-box">
    <div class="login-logo">
        <a href="<?= \yii\helpers\Url::to(['site/index'])?>" style="font-size: large"><b>GAP V2</b> APP</a>
    </div>

    <!-- /.login-logo -->
    <div class="login-box-body">
        <h4 class="text-center"><?= Html::encode($this->title) ?></h4>
        <p>Please fill out your email. A link to reset password will be sent there.</p>

        <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form', 'enableClientValidation' => false]); ?>

        <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
        </div>


        <?php ActiveForm::end(); ?>
    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->
