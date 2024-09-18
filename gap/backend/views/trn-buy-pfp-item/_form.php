<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnBuyPfpItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-buy-pfp-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'greige_group_id')->textInput() ?>

    <?= $form->field($model, 'greige_id')->textInput() ?>

    <?= $form->field($model, 'buy_pfp_id')->textInput() ?>

    <?= $form->field($model, 'panjang_m')->textInput() ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
