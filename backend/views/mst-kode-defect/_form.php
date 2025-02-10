<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstKodeDefect */
/* @var $form ActiveForm */

?>

<div class="mst-kode-defect-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'no_urut')->textInput(['value' => $model->no_urut, 'readonly' => true]) ?>

                    <?= $form->field($model, 'kode')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'nama_defect')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'asal_defect')->textInput(['maxlength' => true]) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>