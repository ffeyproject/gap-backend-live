<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;

$this->title = 'Data Duplikasi Stock Greige Opname';
$this->params['breadcrumbs'][] = 'Gudang Stock Opname > ' . $this->title;

?>
<div class="trn-stock-greige-opname-duplicate-index">

    <?php
    $form = ActiveForm::begin([
        'id' => 'bulk-duplicate-form',
        'action' => ['duplicate-bulk'],
        'method' => 'post',
    ]);
    ?>

    <?= Html::hiddenInput('ids', '', ['id' => 'bulk-duplicate-ids']) ?>

    <div class="alert alert-info">
        <h4><i class="glyphicon glyphicon-info-sign"></i> Panduan Penggunaan Migrasi Stock</h4>
        <ol>
            <li>Check terlebih dahulu stock greige dan available pada master greige yang akan di migrasi. (Cek di halaman <?= Html::a('Master Greige', ['/mst-greige/index'], ['target' => '_blank', 'class' => 'alert-link']) ?>)</li>
            <li>Klik button Migrasi sesuai dengan asal greigenya.</li>
            <li>Pilih greige yang akan di migrasi sesuai dengan nomer 1.</li>
            <li>Klik button "Proses Migrasi" pada pop-up. Setelah proses selesai, refresh halaman Master Greige (pada point nomer 1) maka data Stock dan Available akan berubah menyesuaikan.</li>
            <li>Check history migrasi untuk memastikan greige yang sudah di migrasi.</li>
        </ol>
    </div>

    <div style="margin-bottom: 15px;">
        <!-- <?= Html::button('<i class="glyphicon glyphicon-plus"></i> Migrasi Ke Stock', [
            'class' => 'btn btn-info',
            'id' => 'btn-duplicate-bulk'
        ]) ?> -->

        <?= Html::button('<i class="glyphicon glyphicon-log-out"></i> Keluar Stock Opname', [
            'class' => 'btn btn-danger',
            'id' => 'btn-keluar-bulk',
            'style' => 'margin-right: 5px;'
        ]) ?>

        <div class="btn-group" role="group">
            <?= Html::button('<i class="glyphicon glyphicon-random"></i> Migrasi Stock Water Jet Loom', [
                'class' => 'btn btn-warning',
                'id' => 'btn-migrasi-wjl',
                'data-toggle' => 'modal',
                'data-target' => '#modal-migrasi-wjl',
            ]) ?>

            <?= Html::button('<i class="glyphicon glyphicon-time"></i> History Migrasi Wjl', [
                'class' => 'btn btn-default',
                'id' => 'btn-history-migrasi',
                'data-toggle' => 'modal',
                'data-target' => '#modal-history-migrasi',
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <?php
    Modal::begin([
        'id' => 'modal-migrasi-wjl',
        'header' => '<h4>Migrasi Stock Water Jet Loom</h4>',
        'options' => [
            'tabindex' => false, // important for Select2 to work inside modal
        ],
    ]);
    
    $formMigrasi = ActiveForm::begin([
        'id' => 'form-migrasi-wjl',
        'action' => ['migrasi-wjl'],
        'method' => 'post',
    ]);
    
    echo '<div class="form-group">';
    echo '<label>Nama Motif (Greige) Water Jet Loom</label>';
    echo Select2::widget([
        'name' => 'greige_id',
        'options' => ['placeholder' => 'Cari Motif (Greige)...', 'id' => 'migrasi-greige-id'],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 3,
            'language' => [
                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
            ],
            'ajax' => [
                'url' => Url::to(['lookup-greige-wjl']),
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }')
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('function(greige) { return greige.text; }'),
            'templateSelection' => new JsExpression('function (greige) { return greige.text; }'),
        ],
    ]);
    echo '</div>';
    
    echo '<div id="migrasi-wjl-preview" style="display:none; margin-bottom:15px; padding: 10px; border: 1px solid #faebcc; border-radius: 4px; background-color: #fcf8e3; color: #8a6d3b;">
        <strong><i class="glyphicon glyphicon-eye-open"></i> Jejak Perhitungan (Preview):</strong><br/>
        Stock Opname saat ini (Duplikat): <b id="preview-opname-count">0</b> roll (<b id="preview-opname-qty">0</b> M).<br/>
        Akan memigrasi/mengeluarkan <b id="preview-out-count">0</b> roll (<b id="preview-out-qty">0</b> M) stock lama.<br/>
        <table class="table table-condensed" style="margin-top:10px; margin-bottom:0; background:transparent;">
            <tr>
                <th style="border-top:none;">Stock:</th>
                <td style="border-top:none;"><s id="preview-old-stock" class="text-danger">0</s> &nbsp;&rarr;&nbsp; <b id="preview-new-stock" class="text-success">0</b></td>
            </tr>
            <tr>
                <th style="border-top:none;">Available:</th>
                <td style="border-top:none;"><s id="preview-old-avail" class="text-danger">0</s> &nbsp;&rarr;&nbsp; <b id="preview-new-avail" class="text-success">0</b></td>
            </tr>
        </table>
    </div>';

    echo '<div class="form-group">';
    echo Html::submitButton('Proses Migrasi', ['class' => 'btn btn-success', 'data-confirm' => 'Yakin ingin memproses migrasi untuk motif ini?', 'id' => 'btn-proses-migrasi-wjl']);
    echo '</div>';
    
    ActiveForm::end();
    Modal::end();
    ?>

    <?php
    Modal::begin([
        'id' => 'modal-history-migrasi',
        'header' => '<h4>History Migrasi Stock Water Jet Loom</h4>',
        'size' => Modal::SIZE_LARGE,
    ]);
    echo '<div id="history-migrasi-content"><div class="text-center"><i class="glyphicon glyphicon-refresh glyphicon-spin"></i> Loading...</div></div>';
    Modal::end();
    ?>

    <?php Pjax::begin(['id' => 'StockGreigeOpnameGrid-pjax']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'StockGreigeOpnameGrid',
        'responsiveWrap' => false,
        'pjax' => true,
        'panel' => [
            'type' => 'primary',
            'before'=>Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index-duplicate'], ['class' => 'btn btn-default']),
                ['class'=>'btn-group', 'role'=>'group']
            ),
            'after'=>false,
        ],
        'showPageSummary'=>true,
        'rowOptions' => function ($model, $key, $index, $grid) {
            static $migratedGreigeIds = null;
            if ($migratedGreigeIds === null) {
                $migratedGreigeIds = \common\models\ar\HistoryMigrasiWjl::find()
                    ->select('greige_id')
                    ->column();
            }
            
            if (in_array($model->greige_id, $migratedGreigeIds)) {
                return ['class' => 'info']; 
            }
            return [];
        },
        'columns' => [
            [
                'class' => 'kartik\grid\CheckboxColumn',
                'checkboxOptions' => function ($model) {
                    return [
                        'value'    => $model->id,
                        'disabled' => $model->status != \common\models\ar\TrnStockGreige::STATUS_VALID, // disable kalau bukan VALID
                    ];
                }
            ],
            'id',
            'stock_greige_id',
            [
                'attribute' => 'dateRange',
                'label' => 'Tanggal',
                'value' => 'date',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        'locale'=>[
                            'format'=>'Y-m-d',
                            'separator'=>' to ',
                        ]
                    ]
                ],
            ],
            'no_document',
            'no_lapak',
            [
                'label'=>'Nama Kain',
                'attribute'=>'greigeNamaKain',
                'value'=>function($data){
                    return $data->greigeNamaKain;
                }
            ],
            [
                'attribute'=>'grade',
                'value'=>function($data){
                    return $data::gradeOptions()[$data->grade] ?? $data->grade;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\TrnStockGreige::gradeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            'lot_lusi',
            'lot_pakan',
            'no_set_lusi',
            [
                'attribute'=>'panjang_m',
                'format'=>'decimal',
                'pageSummary'=>true
            ],
            [
                'attribute'=>'asal_greige',
                'value'=>function($data){
                    return $data::asalGreigeOptions()[$data->asal_greige] ?? $data->asal_greige;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\TrnStockGreige::asalGreigeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            [
                'attribute'=>'status_tsd',
                'value'=>function($data){
                    /* @var $data TrnGudangInspect*/
                    return $data::tsdOptions()[$data->status_tsd];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\TrnGudangStockOpname::tsdOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'attribute'=>'status',
                'value'=>function($data){
                    return $data::statusOptions()[$data->status] ?? $data->status;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\TrnStockGreige::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            'note:ntext',
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<?php
// $js = <<<JS
// $('#btn-duplicate-bulk').on('click', function() {
//     var keys = $('#StockGreigeOpnameGrid').yiiGridView('getSelectedRows');
//     if (keys.length === 0) {
//         alert('Pilih minimal 1 data untuk diduplikasi.');
//         return;
//     }
//     if (confirm('Yakin ingin menduplikasi ' + keys.length + ' data ke TrnStockGreige?')) {
//         $('#bulk-duplicate-ids').val(keys.join(',')); 
//         $('#bulk-duplicate-form').submit(); 
//     }
// });
// JS;
// $this->registerJs($js);

$historyUrl = \yii\helpers\Url::to(['history-migrasi-wjl']);
$predictUrl = \yii\helpers\Url::to(['predict-migrasi-wjl']);
$js = <<<JS
$('#btn-duplicate-bulk').on('click', function() {
    var keys = $('#StockGreigeOpnameGrid').yiiGridView('getSelectedRows');
    if (keys.length === 0) {
        alert('Pilih minimal 1 data untuk diduplikasi.');
        return;
    }
    if (confirm('Yakin ingin menduplikasi ' + keys.length + ' data ke TrnStockGreige?')) {
        $('#bulk-duplicate-ids').val(keys.join(',')); 
        $('#bulk-duplicate-form').attr('action', 'duplicate-bulk').submit(); 
    }
});

$('#btn-keluar-bulk').on('click', function() {
    var keys = $('#StockGreigeOpnameGrid').yiiGridView('getSelectedRows');
    if (keys.length === 0) {
        alert('Pilih minimal 1 data untuk keluar stock opname.');
        return;
    }
    if (confirm('Yakin ingin mengeluarkan ' + keys.length + ' data dari stock opname?')) {
        $('#bulk-duplicate-ids').val(keys.join(','));
        $('#bulk-duplicate-form').attr('action', 'keluar-bulk').submit();
    }
});

$('#modal-history-migrasi').on('show.bs.modal', function (e) {
    $('#history-migrasi-content').load('{$historyUrl}');
});

var predictUrl = '{$predictUrl}';
$('#migrasi-greige-id').on('change', function() {
    var gid = $(this).val();
    if (!gid) {
        $('#migrasi-wjl-preview').slideUp();
        $('#btn-proses-migrasi-wjl').prop('disabled', true);
        return;
    }
    
    // disable while loading
    $('#btn-proses-migrasi-wjl').prop('disabled', true);
    
    $.ajax({
        url: predictUrl,
        type: 'GET',
        data: {greige_id: gid},
        dataType: 'json',
        success: function(res) {
            if (res.error) {
                alert(res.error);
                $('#migrasi-wjl-preview').slideUp();
            } else {
                $('#preview-opname-count').text(res.opname_count);
                $('#preview-opname-qty').text(res.opname_qty);
                $('#preview-out-count').text(res.out_count);
                $('#preview-out-qty').text(res.out_qty);
                $('#preview-old-stock').text(res.old_stock);
                $('#preview-new-stock').text(res.new_stock);
                $('#preview-old-avail').text(res.old_available);
                $('#preview-new-avail').text(res.new_available);
                
                $('#migrasi-wjl-preview').slideDown();
                
                $('#btn-proses-migrasi-wjl').prop('disabled', false);
            }
        },
        error: function() {
            alert('Gagal mengambil preview data.');
            $('#btn-proses-migrasi-wjl').prop('disabled', false);
        }
    });
});

JS;
$this->registerJs($js);
?>