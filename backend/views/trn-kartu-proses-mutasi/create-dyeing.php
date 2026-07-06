<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesDyeing */
/* @var $selectedStocks common\models\ar\TrnStockGreige[] */
/* @var $selectedTubes array */

$selectedStocks = $selectedStocks ?? [];
$selectedTubes = $selectedTubes ?? [];

$this->title = 'Buat Kartu Stock Mutasi Dyeing';
$this->params['breadcrumbs'][] = ['label' => 'Pembuatan Kartu Stock Mutasi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-mutasi-create-dyeing">
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Form Kartu Stock Mutasi Dyeing</h3>
        </div>
        <div class="box-body">
            <?php $form = ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-md-6">
                    <?php
                    $woText = $model->wo_id === null ? '' : ($model->wo ? $model->wo->no : '');
                    echo $form->field($model, 'wo_id')->widget(Select2::class, [
                        'initValueText' => $woText,
                        'options' => [
                            'placeholder' => 'Cari Working Order...',
                            'id' => 'wo-id-select'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                            ],
                            'ajax' => [
                                'url' => Url::to(['/ajax/lookup-wo-dyeing']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(wo) { return wo.text; }'),
                            'templateSelection' => new JsExpression('function(wo) { return wo.text; }'),
                        ],
                    ])->label('Nomor Working Order');
                    ?>

                    <div class="form-group">
                        <label class="control-label">Pilih Stock Asal Mutasi</label>
                        <?= Select2::widget([
                            'name' => 'stock_ids',
                            'id' => 'stock-ids-select',
                            'value' => \yii\helpers\ArrayHelper::getColumn($selectedStocks, 'id'),
                            'data' => \yii\helpers\ArrayHelper::map($selectedStocks, 'id', function($item) {
                                return "WO: {$item->nomor_wo} | Motif: {$item->greige->nama_kain} | Warna: {$item->color} | Qty: " . Yii::$app->formatter->asDecimal($item->panjang_m) . " M | Doc: {$item->no_document}";
                            }),
                            'options' => [
                                'placeholder' => 'Cari stock dari Gudang Mutasi...',
                                'multiple' => true,
                                'options' => \yii\helpers\ArrayHelper::map($selectedStocks, 'id', function($item) {
                                    return [
                                        'data-qty' => $item->panjang_m,
                                        'data-wo' => $item->nomor_wo,
                                        'data-motif' => $item->greige->nama_kain,
                                        'data-color' => $item->color,
                                    ];
                                }),
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 0,
                                'ajax' => [
                                    'url' => Url::to(['/ajax/lookup-stock-mutasi']),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                                ],
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression('function(res) { return res.text; }'),
                                'templateSelection' => new JsExpression('function(res) { return res.text; }'),
                            ],
                        ]) ?>
                    </div>

                    <div id="stock-info" style="display:none; margin-bottom: 15px; padding: 10px; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
                        <strong>Informasi Stock Terpilih:</strong>
                        <table class="table table-condensed table-striped" style="margin-bottom:0; margin-top:5px;">
                            <thead>
                                <tr>
                                    <th>WO</th>
                                    <th>Motif</th>
                                    <th>Warna</th>
                                    <th class="text-right">Qty (M)</th>
                                    <th style="width: 150px;">Tube</th>
                                </tr>
                            </thead>
                            <tbody id="selected-stock-list">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Total Qty:</th>
                                    <th class="text-right"><span id="total-selected-qty">0.00</span> M</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <?= $form->field($model, 'nomor_kartu')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'note')->textarea(['rows' => 4]) ?>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$selectedTubesJson = json_encode($selectedTubes);
$this->registerJs("var initialTubes = {$selectedTubesJson};", \yii\web\View::POS_HEAD);

$this->registerJs("
    function updateSelectedStockInfo() {
        var data = $('#stock-ids-select').select2('data');
        var tbody = $('#selected-stock-list');
        tbody.empty();
        var totalQty = 0;
        
        if (data && data.length > 0) {
            $('#stock-info').show();
            data.forEach(function(item, index) {
                var qty = parseFloat(item.qty || $(item.element).data('qty')) || 0;
                totalQty += qty;
                
                var wo = item.wo || $(item.element).data('wo') || '';
                var motif = item.motif || $(item.element).data('motif') || '';
                var color = item.color || $(item.element).data('color') || '';
                var id = item.id;
                
                var initialTubeVal = (initialTubes && initialTubes[id]) ? initialTubes[id].toString() : '';
                var defaultTube = initialTubeVal || ((index % 2 === 0) ? '1' : '2');
                
                var row = $('<tr>');
                row.append($('<td>').text(wo));
                row.append($('<td>').text(motif));
                row.append($('<td>').text(color));
                row.append($('<td>').addClass('text-right').text(qty.toFixed(2)));
                
                var selectTube = $('<select>')
                    .addClass('form-control input-sm')
                    .attr('name', 'tubes[' + id + ']')
                    .append($('<option>').attr('value', '1').text('Tube Kiri').prop('selected', defaultTube === '1'))
                    .append($('<option>').attr('value', '2').text('Tube Kanan').prop('selected', defaultTube === '2'));
                
                row.append($('<td>').append(selectTube));
                tbody.append(row);
            });
            $('#total-selected-qty').text(totalQty.toFixed(2));
        } else {
            $('#stock-info').hide();
            $('#total-selected-qty').text('0.00');
        }
    }
    
    $('#stock-ids-select').on('change', function() {
        updateSelectedStockInfo();
    });
    
    // Trigger on page load to render previously selected items
    updateSelectedStockInfo();
", \yii\web\View::POS_READY);
?>
