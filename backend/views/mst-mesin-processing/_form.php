<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use common\models\ar\MstGreige;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstMesinProcessing */
/* @var $form yii\widgets\ActiveForm */

$availableGreiges = MstGreige::find()->where(['aktif' => true])->orderBy('nama_kain')->all();
$greigesData = ArrayHelper::map($availableGreiges, 'nama_kain', 'nama_kain');
?>

<?php $form = ActiveForm::begin(); ?>

<div class="box">
    <div class="box-body">
        <?= $form->field($model, 'nama_mesin')->widget(Select2::classname(), [
            'data' => $greigesData,
            'options' => [
                'placeholder' => 'Pilih Motif...',
                'multiple' => true,
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'tags' => true, // Allows user to enter custom names if it's not in the list
            ],
        ])->label('Nama Motif') ?>

        <fieldset style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px;">
            <legend style="width: auto; padding: 0 10px; font-weight: bold; border-bottom: none; font-size: 16px;">RELAX</legend>
            
            <?= $form->field($model, 'relax_mesin')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'relax_jenis_nozzle')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'relax_ukuran_nozzle')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'relax_catatan')->textarea(['rows' => 2]) ?>
        </fieldset>

        <fieldset style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px;">
            <legend style="width: auto; padding: 0 10px; font-weight: bold; border-bottom: none; font-size: 16px;">CELUP</legend>
            
            <?= $form->field($model, 'celup_mesin')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'celup_jenis_nozzle')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'celup_ukuran_nozzle')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'celup_catatan')->textarea(['rows' => 2]) ?>
        </fieldset>
    </div>
</div>

<div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
