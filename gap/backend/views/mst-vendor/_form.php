<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstVendor */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mst-vendor-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'telp')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'fax')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'cp_name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'aktif')->checkbox() ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
