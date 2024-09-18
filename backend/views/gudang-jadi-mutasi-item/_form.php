<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\GudangJadiMutasiItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gudang-jadi-mutasi-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'mutasi_id')->textInput() ?>

    <?= $form->field($model, 'stock_id')->textInput() ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
