<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\modules\rawdata\models\TrnKirimBuyerBal $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="trn-kirim-buyer-bal-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'trn_kirim_buyer_id')->textInput() ?>

    <?= $form->field($model, 'no_bal')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'header_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Simpan' : 'Update', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Kembali', ['index'], ['class'=>'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>