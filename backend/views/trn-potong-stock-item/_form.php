<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnPotongStockItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-potong-stock-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'potong_stock_id')->textInput() ?>

    <?= $form->field($model, 'qty')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
