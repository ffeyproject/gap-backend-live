<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesPfpItem */
?>

<div class="edit-mesin-form">
    <?php $form = ActiveForm::begin([
        'id' => 'edit-mesin-form',
        'action' => ['trn-kartu-proses-pfp-item/edit-mesin', 'id' => $model->id],
        'method' => 'post',
    ]); ?>

    <?= $form->field($model, 'mesin')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
        <?= Html::button('Batal', [
            'class' => 'btn btn-secondary',
            'data-bs-dismiss' => 'modal'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>