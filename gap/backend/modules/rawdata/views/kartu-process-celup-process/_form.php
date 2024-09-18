<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\KartuProcessCelupProcess */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="kartu-process-celup-process-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'kartu_process_id')->textInput() ?>

    <?= $form->field($model, 'process_id')->textInput() ?>

    <?= $form->field($model, 'value')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
