<?php

use backend\components\ajax_modal\AjaxModal;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnStockGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Packing List Greige';
$this->params['breadcrumbs'][] = $this->title;

\backend\assets\DataTablesAsset::register($this);

echo AjaxModal::widget([
    'id' => 'allStockModal',
    'size' => 'modal-md',
    'header' => '<h4 class="modal-title">...</h4>',
]);
echo AjaxModal::widget([
    'id' => 'editQtyModal',
    'size' => 'modal-md',
    'header' => '<h4 class="modal-title">Edit Qty Stock & Opname</h4>',
]);
?>
<div class="trn-stock-greige-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Html::a('Lihat Stock Keseluruhan', ['seluruh-stock'], [
            'class' => 'btn btn-success',
            'title' => 'Lihat Stock Keseluruhan',
            'data-toggle'=>"modal",
            'data-target'=>"#allStockModal",
            'data-title' => 'Lihat Stock Keseluruhan'
        ])?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'StockGreigeGrid',
        'rowOptions' => function($model) {
        /* @var $model \common\models\ar\TrnStockGreige */
        return $model->isDuplicated 
            ? ['style' => 'background-color:#fff3cd;']
            : [];
        },
        'responsiveWrap' => false,
        'pjax' => true,
        'panel' => [
        'type' => 'default',
        'before'=>Html::tag(
        'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default', 'style' => 'margin-right:5px;']).
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success', 'style' => 'margin-right:5px;']).
                Html::a('<i class="glyphicon glyphicon-plus"></i> Bulk', ['create-dua'], ['class' => 'btn btn-info', 'style' => 'margin-right:5px;']).
                Html::a('Change Notes', ['change-notes'], [
                    'class' => 'btn btn-warning',
                    'style' => 'margin-right:5px;',
                    'onclick' => 'changeNotes(event);',
                    'title' => 'Change Notes Selected Items'
                ]).
                Html::a('Ganti Ket. Weaving', ['change-ket-weaving'], [
                    'class' => 'btn btn-default',
                    'style' => 'margin-right:5px;',
                    'onclick' => 'changeKetWeaving(event);',
                    'title' => 'Change Ket. Weaving Selected Items'
                ]).
                Html::a('<i class="glyphicon glyphicon-duplicate"></i> Duplikat Stock ke Stock Opname', ['duplicate-bulk'], [
                    'class' => 'btn btn-primary',
                    'style' => 'margin-right:5px;',
                    'onclick' => 'duplicateStock(event);',
                    'title' => 'Duplikat Stock ke Stock Opname'
                ]).
                Html::a('<i class="glyphicon glyphicon-edit"></i> Edit Qty Stock & Opname', ['edit-qty'], [
                    'class' => 'btn btn-warning',
                    'style' => 'margin-right:5px;',
                    'onclick' => 'editQtyStock(event);',
                    'title' => 'Edit Qty Stock dan Opname'
                ]),
                ['class'=>'btn-group', 'role'=>'group']
            ),
            'after'=>false,
        ],
        'showPageSummary'=>true,
        'columns' => [
            //['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\CheckboxColumn',
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    //@var $model TrnStockGreige
                    /*if($model->status != $model::STATUS_VALID){
                        return ['value' => '', 'disabled'=>'disabled'];
                    }*/
                    return ['value' => $model->id];
                }
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template'=>'{view} {add-mix}',
                'buttons'=>[
                    'add-mix' => function($url, $model, $key){
                        /* @var $model TrnStockGreige*/

                        if($model->status != $model::STATUS_VALID){
                            return '';
                        }

                        $data = [
                            'id' => $model->id,
                            'tanggal' => Yii::$app->formatter->asDate($model->date),
                            'no_document' => $model->no_document,
                            'no_lapak' => $model->no_lapak,
                            'nama_greige' => $model->greige->nama_kain,
                            'grade' => $model->grade,
                            'grade_name' => $model::gradeOptions()[$model->grade],
                            'qty' => $model->panjang_m,
                            'qty_fmt' => Yii::$app->formatter->asDecimal($model->panjang_m),
                            'lot_lusi' => $model->lot_lusi,
                            'lot_pakan' => $model->lot_pakan,
                            'asal_greige' => $model->asal_greige,
                            'asal_greige_name' => $model::asalGreigeOptions()[$model->asal_greige],
                            'no_mc_weaving' => $model->no_set_lusi
                        ];
                        $dataStr = \yii\helpers\Json::encode($data);
                        return Html::a('<span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>', '#', [
                            'onclick' => "addItem(event, {$dataStr})"
                        ]);
                    }
                ]
            ],

            'id',
            [
                'attribute' => 'dateRange',
                'label' => 'TANGGAL',
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
                'attribute'=>'status_tsd',
                'value'=>function($data){
                    /* @var $data TrnStockGreige*/
                    return $data::tsdOptions()[$data->status_tsd];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\TrnStockGreige::tsdOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'label'=>'Greige',
                'attribute'=>'greigeNamaKain',
                'value'=>'greige.nama_kain'
            ],
            [
                'attribute'=>'grade',
                'value'=>function($data){
                    /* @var $data TrnStockGreige*/
                    return $data::gradeOptions()[$data->grade];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::gradeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
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
            [
                'attribute'=>'asal_greige',
                'value'=>function($data){
                    /* @var $data TrnStockGreige*/
                    return $data::asalGreigeOptions()[$data->asal_greige];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::asalGreigeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            /*[
                'attribute'=>'jenis_gudang',
                'value'=>'jenisGudangName',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::jenisGudangOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],*/
            'is_pemotongan:boolean',
            'is_hasil_mix:boolean',
            //'pengirim',
            //'mengetahui',
            //'note:ntext',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
        ],
    ]); ?>

    <?=$this->render('_mix-quality')?>

    <!-- Modal bawaan edit qty -->
    <div id="modal-edit-qty" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-body text-center p-3">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p>Loading...</p>
                </div>
            </div>
        </div>
    </div>

</div>
<?php
$editQtyUrl = \yii\helpers\Url::to(['edit-qty']);
$jsEditQty = <<<JS
// Saat tombol Edit Qty diklik
$(document).on('click', '.btn-edit-qty', function(e) {
    e.preventDefault();
    var keys = $('#StockGreigeGrid').yiiGridView('getSelectedRows');
    if (keys.length === 0) {
        alert('Pilih minimal satu item untuk edit Qty.');
        return false;
    }
    var url = '{$editQtyUrl}?ids=' + keys.join(',');
    $('#modal-edit-qty').modal('show')
        .find('.modal-content')
        .load(url);
});
JS;
$this->registerJs($jsEditQty, View::POS_END);
?>

<?php
$this->registerJsVar('mixItems', []);

$actionUrl = \yii\helpers\Url::to(['mix-quality']);
$js = <<<JS
var actionUrl = "{$actionUrl}";
var greigeId = undefined;
var greigeGrade = undefined;
JS;
//$this->registerJsVar('actionUrl', $actionUrl);
$this->registerJs($js.$this->render('js/index.js'), View::POS_END);
$jsFixSelectAll = <<<JS
function rebindGridCheckboxes() {
    var grid = $('#StockGreigeGrid');
    var selectAll = grid.find('.select-on-check-all');
    var checkboxes = grid.find('input[name="selection[]"]');

    // Rebind "select all" behaviour
    selectAll.off('change').on('change', function() {
        var checked = $(this).is(':checked');
        checkboxes.prop('checked', checked).trigger('change');
    });

    // Rebind child checkbox behaviour
    checkboxes.off('change').on('change', function() {
        var total = checkboxes.length;
        var checked = checkboxes.filter(':checked').length;
        selectAll.prop('checked', total === checked);
    });
}

// Initial load
$(function() {
    rebindGridCheckboxes();
});

// Rebind after PJAX reload
$(document).on('pjax:end', function() {
    rebindGridCheckboxes();
});
JS;

$this->registerJs($jsFixSelectAll, View::POS_END);