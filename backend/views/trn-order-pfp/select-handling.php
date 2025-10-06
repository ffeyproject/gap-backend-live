<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use common\models\ar\MstHandling;

/* @var $model common\models\ar\TrnOrderPfp */

$form = ActiveForm::begin([
    'id' => 'select-handling-form',
]);
?>

<?= $form->field($model, 'handling_id')->widget(Select2::class, [
    'data' => MstHandling::find()
        ->where(['greige_id' => $model->greige_id])
        ->select(['name', 'id'])
        ->indexBy('id')
        ->column(),
    'options' => [
        'placeholder' => 'Pilih Handling...',
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]) ?>

<div class="form-group">
    <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>