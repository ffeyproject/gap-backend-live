<?php
use backend\assets\JqueryUiAsset;
use common\models\ar\TrnKartuProsesPrinting;
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
$kartuProses = $model->kartuProcess;
$isDigital = ($kartuProses !== null && $kartuProses->jenis_printing == TrnKartuProsesPrinting::JENIS_PRINTING_DIGITAL);
?>

<div class="kartu-proses-printing-item-form">

    <?php $form = ActiveForm::begin(['id'=>'KartuProsesItemForm']); ?>

    <?php
    echo $form->field($model, 'stock_ids')->widget(Select2::classname(), [
        'options' => ['placeholder' => 'Format pencarian: {no_document}*{qty}*{grade}*{no_lot_lusi}*{no_lot_pakan}', 'multiple' => true],
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
    ])->hint($searchHint)->label('Stock Items');
    ?>

    <?php if ($isDigital): ?>
        <div id="digital-cut-wrapper" style="margin-top: 15px; margin-bottom: 15px;">
            <div class="panel panel-info">
                <div class="panel-heading" style="padding: 8px 15px;">
                    <strong class="panel-title" style="font-size: 13px;"><i class="fa fa-cut"></i> Penyesuaian Qty Pemotongan (Digital Printing)</strong>
                    <span class="text-muted pull-right small">Jika qty dipakai kurang dari asal, sisanya otomatis menjadi stok baru di Gudang 2.</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-hover" id="tbl-cut-items" style="margin-bottom: 0;">
                        <thead>
                            <tr class="active">
                                <th style="width: 35%;">Informasi Stock</th>
                                <th style="width: 15%; text-align: right;">Qty Asal (M)</th>
                                <th style="width: 20%; text-align: center;">Qty Dipakai (M)</th>
                                <th style="width: 15%; text-align: right;">Sisa (M)</th>
                                <th style="width: 15%; text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tbl-cut-items-body">
                            <tr id="row-empty-placeholder">
                                <td colspan="5" class="text-center text-muted" style="padding: 12px;">Belum ada stock yang dipilih. Silakan cari dan pilih stock di atas.</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="info" style="font-weight: bold;">
                                <td colspan="2" class="text-right">Total Qty Dipakai:</td>
                                <td class="text-center"><span id="sum-qty-dipakai">0.00</span> M</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6"><?php /*echo $form->field($model, 'mesin')->textInput(['maxlength' => true]);*/ ?></div>

        <div class="col-md-6">
            <?php echo $form->field($model, 'date')->textInput(['id'=>'datepicker', 'readonly'=>'readonly']); ?>
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
?>