<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesDyeing */
?>

<?php $form = ActiveForm::begin([
    'id' => 'form-edit-nomor-kartu',
    'action' => ['edit-nomor-kartu', 'id' => $model->id],
]); ?>

<?= $form->field($model, 'nomor_kartu')->textInput(['maxlength' => true]) ?>

<div class="form-group">
    <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
    <?= Html::button('Close', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
</div>

<?php ActiveForm::end(); ?>