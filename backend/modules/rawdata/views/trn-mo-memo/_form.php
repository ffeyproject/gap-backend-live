<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnMoMemo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-mo-memo-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'mo_id')->textInput() ?>

    <?= $form->field($model, 'memo')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
