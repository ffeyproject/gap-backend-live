<?php

use dosamigos\tinymce\TinyMce;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnScMemo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-sc-memo-form">

    <?php $form = ActiveForm::begin(['id'=>'TrnScMemoForm']); ?>

    <div class="trn-wo-memo-form">
        <?=$form->field($model, 'memo')->widget(TinyMce::class, [
            'options' => ['rows' => 6],
            'language' => 'id',
            'clientOptions' => [
                'menubar' => false,
                'plugins' => [
                    "lists",
                ],
                'toolbar' => " bold italic | alignleft aligncenter alignright alignjustify | bullist numlist"
            ]
        ])?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs($this->renderFile(__DIR__.'/js/form.js'), \yii\web\View::POS_END, 'scMemoFormJs');
