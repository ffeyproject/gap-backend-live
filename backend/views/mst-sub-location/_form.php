<?php

use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use common\models\ar\MstLocation;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-body">
                <?=$form->field($model, 'locs_loc_id')->widget(Select2::class, [
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'data' => MstLocation::optionList(),
                    ])
                ?>

                <?= $form->field($model, 'locs_code')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'locs_description')->textInput(['maxlength' => true]) ?>

                <?=$form->field($model, 'locs_active')->widget(Select2::class, [
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'data' => ['Y' => 'Y', 'N' => 'N'],
                    ])
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box">
            <div class="box-body">

                <?= $form->field($model, 'locs_floor_code')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'locs_line_code')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'locs_column_code')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'locs_rack_code')->textInput(['maxlength' => true]) ?>

            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-block']) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>