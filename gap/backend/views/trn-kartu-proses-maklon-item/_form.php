<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesMaklonItem */
/* @var $stockOptionListMap array */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchHint string*/
?>

<div class="trn-kartu-proses-maklon-item-form">

    <?php $form = ActiveForm::begin(['id'=>'KartuProsesItemForm']); ?>

    <?= $form->field($model, 'stock_id')->widget(Select2::classname(), [
        'data' => $stockOptionListMap,
        'options' => ['placeholder' => 'Pilih stock ...'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])->hint($searchHint)?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs($this->renderFile(__DIR__.'/js/form.js'), \yii\web\View::POS_END, 'kartuProsesItemFormJs');
