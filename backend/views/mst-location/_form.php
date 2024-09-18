<?php

use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstGreige */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-body">
                <?= $form->field($model, 'loc_id')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'loc_name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'loc_description')->textInput(['maxlength' => true]) ?>

                <?=$form->field($model, 'loc_active')->widget(Select2::class, [
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'data' => ['Y' => 'Y', 'N' => 'N'],
                    ])
                ?>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-block']) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>