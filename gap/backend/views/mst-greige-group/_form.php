<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstGreigeGroup */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-body">
                <?=$form->field($model, 'jenis_kain')->widget(Select2::class, [
                    'data' => $model::jenisKainOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])?>

                <?=$form->field($model, 'lebar_kain')->widget(Select2::class, [
                    'data' => $model::lebarKainOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])?>

                <?= $form->field($model, 'nama_kain')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'qty_per_batch')->textInput() ?>

                <?=$form->field($model, 'unit')->widget(Select2::class, [
                    'data' => $model::unitOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box">
            <div class="box-body">
                <?= $form->field($model, 'nilai_penyusutan')->textInput() ?>

                <?= $form->field($model, 'gramasi_kain')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'sulam_pinggir')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'aktif')->checkbox() ?>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
