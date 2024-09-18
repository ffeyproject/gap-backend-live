<?php

use backend\models\form\PenerimaanPackingForm;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnInspecting;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrnInspecting */
/* @var $modelPenerimaan PenerimaanPackingForm */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(['id'=>'penerimaanPackingForm']); ?>

<?= $form->field($modelPenerimaan, 'jenis_gudang')->widget(\kartik\select2\Select2::className(), [
    'options' => ['placeholder' => 'Pilih ...'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
    'data' => TrnGudangJadi::jenisGudangOptions(),
]) ?>

<div class="form-group">
    <?= Html::submitButton('Terima', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php
$this->registerJs($this->renderFile(__DIR__ . '/js/form.js'), \yii\web\View::POS_END, 'penerimaanPackingFormJs');