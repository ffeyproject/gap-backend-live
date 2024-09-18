<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\PfpKeluarVerpackingItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pfp-keluar-verpacking-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pfp_keluar_verpacking_id')->textInput() ?>

    <?= $form->field($model, 'ukuran')->textInput() ?>

    <?= $form->field($model, 'join_piece')->textInput() ?>

    <?= $form->field($model, 'keterangan')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
