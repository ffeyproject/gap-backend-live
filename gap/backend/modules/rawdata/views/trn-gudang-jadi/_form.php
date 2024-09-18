<?php

use common\models\ar\MstGreigeGroup;
use kartik\widgets\Select2;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnGudangJadi */
/* @var $form ActiveForm */
?>

<div class="trn-gudang-jadi-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
            <div class="box">
                <div class="box-body">
                    <?= $form->field($model, 'wo_id')->textInput() ?>

                    <?= $form->field($model, 'source_ref')->textInput(['maxlength' => true]) ?>

                    <?=$form->field($model, 'status')->widget(Select2::classname(), [
                        'data' => $model::statusOptions(),
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])?>

                    <?= $form->field($model, 'no_memo_repair')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'no_memo_ganti_greige')->textInput(['maxlength' => true]) ?>


                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6"><?= $form->field($model, 'qty')->textInput() ?></div>

                        <div class="col-md-6">
                            <?=$form->field($model, 'unit')->widget(Select2::classname(), [
                                'data' => MstGreigeGroup::unitOptions(),
                                'options' => ['placeholder' => 'Pilih ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])?>
                        </div>
                    </div>

                    <?=$form->field($model, 'jenis_gudang')->widget(Select2::classname(), [
                        'data' => $model::jenisGudangOptions(),
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])?>

                    <?=$form->field($model, 'source')->widget(Select2::classname(), [
                        'data' => $model::sourceOptions(),
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])?>

                    <?= $form->field($model, 'date')->textInput() ?>

                    <?= $form->field($model, 'color')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box">
                <div class="box-body">
                    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
