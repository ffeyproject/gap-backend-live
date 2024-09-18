<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnScKomisi */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-sc-komisi-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sc_id')->textInput() ?>

    <?= $form->field($model, 'sc_agen_id')->textInput() ?>

    <?= $form->field($model, 'sc_greige_id')->textInput() ?>

    <?= $form->field($model, 'tipe_komisi')->textInput() ?>

    <?= $form->field($model, 'komisi_amount')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
