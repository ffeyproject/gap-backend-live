<?php
use backend\assets\JqueryUiAsset;
use common\models\ar\TrnKartuProsesPrintingItem;
use common\models\ar\TrnScGreige;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model TrnKartuProsesPrintingItem */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchHint string*/

JqueryUiAsset::register($this);
?>

<div class="kartu-proses-printing-item-form">

    <?php $form = ActiveForm::begin(['id'=>'KartuProsesItemForm']); ?>

    <?php
    echo $form->field($model, 'stock_id')->widget(Select2::classname(), [
        'options' => ['placeholder' => 'Format pencarian: {no_document}*{qty}*{grade}*{no_lot_lusi}*{no_lot_pakan}'],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 1,
            'ajax' => [
                'url' => \yii\helpers\Url::to(['/ajax/lookup-kartu-proses-stock']),
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {processId:'.TrnScGreige::PROCESS_PRINTING.', kartuProsesId:'.$model->kartu_process_id.', greigeId:'.$model->wo->greige_id.', asalGreige:'.$model->kartuProcess->asal_greige.', jenisGudang:'.$model->wo->mo->jenis_gudang.', q:params.term}; }'),
                //'delay' => 250,
                //'processResults' => new JsExpression($resultsJs),
                //'cache' => true
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('function(response) { return response.text; }'),
            'templateSelection' => new JsExpression('function (response) { return response.text; }'),
        ],
    ])->hint($searchHint);
    ?>

    <div class="row">
        <div class="col-md-6"><?php /*echo $form->field($model, 'mesin')->textInput(['maxlength' => true]);*/ ?></div>

        <div class="col-md-6">
            <?php echo $form->field($model, 'date')->textInput(['id'=>'datepicker', 'readonly'=>'readonly']); ?>
            <?php
            /*echo $form->field($model, 'date')->widget(\kartik\widgets\DatePicker::classname(), [
                'options' => ['placeholder' => 'Pilih Tanggal ...'],
                'readonly' => true,
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    //'todayBtn' => true,
                ]
            ]);*/
            ?>
        </div>
    </div>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs($this->renderFile(__DIR__.'/js/form.js'), View::POS_END, 'kartuProsesItemFormJs');