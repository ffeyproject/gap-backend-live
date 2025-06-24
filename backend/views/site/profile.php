<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\modules\user\models\User */

$this->title = 'Edit Profile';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
    .form-group label {
        font-weight: 600;
        margin-bottom: 5px;
        display: block;
    }
    .form-group input, 
    .form-group textarea {
        width: 50%;
        max-width: 400px;
    }
    .form-actions {
        margin-top: 20px;
    }
    .profile-image {
        margin-top: 10px;
        margin-bottom: 10px;
    }
");
?>

<div class="user-edit">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{hint}\n{error}",
            'labelOptions' => ['class' => 'control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'username')->textInput(['readonly' => true]) ?>

    <?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'readonly' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput() ?>
    <?= $form->field($model, 'password_repeat')->passwordInput() ?>

    <?= $form->field($model, 'foto')->fileInput() ?>

    <div class="profile-image">
        <img src="<?= $model->foto ? $model->getAvatarUrl() : Url::to('@web/images/icons/awo.png') ?>"
            class="img-thumbnail" style="width: 150px; height: 150px;">
    </div>

    <div class="form-actions">
        <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>