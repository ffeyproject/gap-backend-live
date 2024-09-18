<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnScAgen */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-sc-agen-form">

    <?php $form = ActiveForm::begin(['id'=>'TrnScAgenForm']); ?>

    <div class="trn-sc-agen-form">
        <?= $form->field($model, 'nama_agen')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'attention')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', [
                'class' => 'btn btn-success',
                'id'=>'BtnSubmit'
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs($this->renderFile(__DIR__.'/js/form.js'), \yii\web\View::POS_END, 'scAgenFormJs');
