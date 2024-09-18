<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMoColor */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-mo-color-form">

    <?php $form = ActiveForm::begin(['id'=>'TrnMoColorForm']); ?>

    <div class="row">
        <div class="col-md-6"><?= $form->field($model, 'color')->textInput(['maxlength' => true]) ?></div>

        <div class="col-md-6"><?= $form->field($model, 'qty')->textInput(['maxlength' => true]) ?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs($this->renderFile(__DIR__.'/js/form.js'), \yii\web\View::POS_END, 'moColorFormJs');