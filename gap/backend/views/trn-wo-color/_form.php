<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnWoColor */
/* @var $colors array */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-wo-color-form">

    <?php $form = ActiveForm::begin(['id'=>'TrnWoColorForm']); ?>

    <?php
    //MoColor hanya bisa dipilih saat insert
    if($model->isNewRecord){
        echo $form->field($model, 'mo_color_id')->widget(Select2::class, [
            'data' => $colors,
            'options' => ['placeholder' => 'Select a color ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label('Color');
    }
    ?>

    <?= $form->field($model, 'qty')->textInput(['maxlength' => true])->label('Qty (Batch)') ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6])?>
    <?php
    /*echo $form->field($model, 'note')->widget(TinyMce::className(), [
        'options' => ['rows' => 6],
        'language' => 'id',
        'clientOptions' => [
            'menubar' => false,
            'plugins' => [
                "lists",
            ],
            'toolbar' => " bold italic | alignleft aligncenter alignright alignjustify | bullist numlist"
        ]
    ]);*/
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php
$this->registerJs($this->renderFile(__DIR__.'/js/form.js'), \yii\web\View::POS_END, 'woColorFormJs');
