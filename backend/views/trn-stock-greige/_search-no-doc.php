<?php
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnStockGreigeSearch */
/* @var $form kartik\form\ActiveForm */
?>

<div class="trn-stock-greige-search">

    <?php $form = ActiveForm::begin([
        'action' => ['process'],
        'method' => 'get',
        'type' => ActiveForm::TYPE_INLINE,
        'fieldConfig' => ['options' => ['class' => 'form-group mr-2']] // spacing form field groups
    ]); ?>

    <?php echo $form->field($model, 'no_document') ?>

    <?php //echo Html::submitButton('Search', ['class' => 'btn btn-primary mr-1']); ?>

    <?php ActiveForm::end(); ?>

</div>
