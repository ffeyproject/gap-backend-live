<?php

use common\models\ar\MstK3l;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstK3lSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mst-k3l-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'k3l_code') ?>

    <?= $form->field($model, 'k3l_desc') ?>

    <?= $form->field($model, 'k3l_active')->dropDownList(MstK3l::statusOptions(), ['prompt' => 'Semua']) ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
