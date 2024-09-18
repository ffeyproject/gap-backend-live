<?php

use common\models\ar\TrnScGreige;
use common\models\ar\TrnStockGreige;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesDyeingItem */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchHint string*/
?>

<div class="kartu-proses-dyeing-item-form">

    <?php $form = ActiveForm::begin(['id'=>'KartuProsesItemForm']); ?>

    <?php
    echo $form->field($model, 'stock_id')->widget(Select2::classname(), [
        'options' => ['placeholder' => 'Format pencarian: {no_document}*{qty}*{grade}*{no_lot_lusi}*{no_lot_pakan}*{ket_weaving}'],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 1,
            'ajax' => [
                'url' => \yii\helpers\Url::to(['/ajax/lookup-kartu-proses-stock']),
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {processId:'.TrnScGreige::PROCESS_DYEING.', kartuProsesId:'.$model->kartu_process_id.', greigeId:'.$model->wo->greige_id.', asalGreige:'.$model->kartuProcess->asal_greige.', jenisGudang:'.$model->wo->mo->jenis_gudang.', q:params.term}; }'),
                //'delay' => 250,
                //'processResults' => new JsExpression($resultsJs),
                //'cache' => true
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('function(response) { return response.text; }'),
            'templateSelection' => new JsExpression('function (response) { return response.text; }'),
        ],
        'pluginEvents' => [
            'select2:select' => 'function(e) {stockOnSelect(e);}',
        ]
    ])->hint($searchHint);
    ?>

    <div class="row">
        <div class="col-md-4">
            <?=$form->field($model, 'tube')->widget(Select2::classname(), [
                'data' => $model::tubeOptions(),
                'options' => ['placeholder' => 'Pilih ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ])?>
        </div>

        <div class="col-md-4"><?php echo $form->field($model, 'mesin')->textInput(['maxlength' => true]); ?></div>

        <div class="col-md-4">
            <?php echo $form->field($model, 'date')->textInput(); ?>
        </div>
    </div>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs($this->renderFile(__DIR__.'/js/form.js'), \yii\web\View::POS_END, 'kartuProsesItemFormJs');