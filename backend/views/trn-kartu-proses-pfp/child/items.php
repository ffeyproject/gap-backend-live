<?php

use common\models\ar\TrnKartuProsesPfp;
use common\models\ar\TrnKartuProsesPfpItem;
use common\models\ar\TrnStockGreige;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrnKartuProsesPfp */

$orderPfp = $model->orderPfp;
$greige = $orderPfp->greige;
$greigeGroup = $orderPfp->greigeGroup;

$canCreateItem = false;

if($model->status === $model::STATUS_DRAFT){
    $canCreateItem = true;
}

$dataProviderTubeKiri = new ActiveDataProvider([
    'query' => $model->getTrnKartuProsesPfpItemsTubeKiri(),
    'pagination' => false,
    'sort' => false
]);
$dataProviderTubeKanan = new ActiveDataProvider([
    'query' => $model->getTrnKartuProsesPfpItemsTubeKanan(),
    'pagination' => false,
    'sort' => false
]);
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">ITEMS</h3>
        <div class="box-tools pull-right">
            <?=$canCreateItem ? Html::a('<i class="glyphicon glyphicon-plus"></i>', ['/trn-kartu-proses-pfp-item/create', 'processId' => $model->id], [
                'class' => 'btn btn-xs btn-success',
                'title' => 'Add Items',
                'data-toggle'=>"modal",
                'data-target'=>"#kartuProsesPfpModal",
                'data-title' => 'Add Items'
            ]) : ''?>
        </div>
    </div>
    <div class="box-body">
        <p><?='<strong>Greige: '.$greige->nama_kain.' - Per Batch: '.Yii::$app->formatter->asDecimal($greigeGroup->qty_per_batch).' '.$greigeGroup::unitOptions()[$greigeGroup->unit].'</strong>'?>
        </p>
        <div class="row">
            <div class="col-md-6">
                <?=GridView::widget([
                    'dataProvider' => $dataProviderTubeKiri,
                    'id' => 'KartuProsesPfpItemsGridTubeKiri',
                    'rowOptions' => function ($model) {
                        $exists = \common\models\ar\TrnStockGreigeOpname::find()
                            ->where(['stock_greige_id' => $model->stock_id])
                            ->exists();

                        if ($exists) {
                            return ['style' => 'background-color: #ffff66']; // kuning cerah
                        }
                        return [];
                    },
                    'pjax' => true,
                    'responsiveWrap' => false,
                    'resizableColumns' => false,
                    'showPageSummary' => true,
                    'toolbar' => false,
                    'panel' => [
                        'heading' => 'Tube Kiri',
                        'type' => GridView::TYPE_DEFAULT,
                        'before' => false,
                        'after' => false,
                        'footer' => false
                    ],
                    'columns' => [
                        ['class' => 'kartik\grid\SerialColumn'],

                        //'id',
                        //'process_id',
                        'date:date',
                        [
                            'label' => 'No Document',
                            'value' => function($data){
                                return $data->stock->no_document ?? '-';
                            }
                        ],
                        //'mesin',
                        [
                            'attribute'=>'panjang_m',
                            'label'=>'Qty',
                            'format'=>'decimal',
                            'pageSummary' => true,
                            'hAlign' => 'right'
                        ],
                        [
                            'attribute'=>'mesin'
                        ],
                        [
                            'label'=>'Unit',
                            'value'=>function($data) use($greigeGroup){
                                return $greigeGroup->unitName;
                            },
                        ],
                        [
                            'label'=>'Grade',
                            'value'=>function($data){
                                /* @var $data TrnKartuProsesPfpItem*/
                                return TrnStockGreige::gradeOptions()[$data->stock->grade];
                            },
                        ],
                        [
                            'attribute'=>'tube',
                            'value'=>function($data){
                                /* @var $data TrnKartuProsesPfpItem*/
                                return $data::tubeOptions()[$data->tube];
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filterWidgetOptions' => [
                                'data' => TrnKartuProsesPfpItem::tubeOptions(),
                                'options' => ['placeholder' => '...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ],
                        ],
                        //'note:ntext',
                        //'status',
                        //'created_at',
                        //'created_by',
                        //'updated_at',
                        //'updated_by',

                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'controller' => 'trn-kartu-proses-pfp-item',
                            'template' => '{delete} {print}',
                            'buttons' => [
                                'delete' => function($url, $model, $key) use($canCreateItem) {
                                    /* @var $model TrnKartuProsesPfpItem*/
                                    if($canCreateItem){
                                        return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, [
                                            'class' => 'btn btn-xs btn-danger',
                                            'title' => 'Delete: '.$model->id,
                                            'data' => [
                                                'confirm' => 'Are you sure you want to delete this item?',
                                                'method' => 'post',
                                            ],
                                        ]);
                                    }

                                    return '';
                                },
                            ]
                        ],
                    ],
                ])?>
            </div>

            <div class="col-md-6">
                <?=GridView::widget([
                    'dataProvider' => $dataProviderTubeKanan,
                    'rowOptions' => function ($model) {
                        $exists = \common\models\ar\TrnStockGreigeOpname::find()
                            ->where(['stock_greige_id' => $model->stock_id])
                            ->exists();

                        if ($exists) {
                            return ['style' => 'background-color: #ffff66']; // kuning cerah
                        }
                        return [];
                    },
                    'id' => 'KartuProsesPfpItemsGridTubeKanan',
                    'pjax' => true,
                    'responsiveWrap' => false,
                    'resizableColumns' => false,
                    'showPageSummary' => true,
                    'toolbar' => false,
                    'panel' => [
                        'heading' => 'Tube Kanan',
                        'type' => GridView::TYPE_DEFAULT,
                        'before' => false,
                        'after' => false,
                        'footer' => false
                    ],
                    'columns' => [
                        ['class' => 'kartik\grid\SerialColumn'],

                        //'id',
                        //'process_id',
                        'date:date',
                        //'mesin',
                        [
                            'label' => 'No Document',
                            'value' => function($data){
                                return $data->stock->no_document ?? '-';
                            }
                        ],
                        [
                            'attribute'=>'panjang_m',
                            'label'=>'Qty',
                            'format'=>'decimal',
                            'pageSummary' => true,
                            'hAlign' => 'right'
                        ],
                        [
                            'attribute'=>'mesin'
                        ],
                        [
                            'label'=>'Unit',
                            'value'=>function($data) use($greigeGroup){
                                return $greigeGroup->unitName;
                            },
                        ],
                        [
                            'label'=>'Grade',
                            'value'=>function($data){
                                /* @var $data TrnKartuProsesPfpItem*/
                                return TrnStockGreige::gradeOptions()[$data->stock->grade];
                            },
                        ],
                        [
                            'attribute'=>'tube',
                            'value'=>function($data){
                                /* @var $data TrnKartuProsesPfpItem*/
                                return $data::tubeOptions()[$data->tube];
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filterWidgetOptions' => [
                                'data' => TrnKartuProsesPfpItem::tubeOptions(),
                                'options' => ['placeholder' => '...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ],
                        ],
                        //'note:ntext',
                        //'status',
                        //'created_at',
                        //'created_by',
                        //'updated_at',
                        //'updated_by',

                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'controller' => 'trn-kartu-proses-pfp-item',
                            'template' => '{delete} {print}',
                            'buttons' => [
                                'delete' => function($url, $model, $key) use($canCreateItem) {
                                    /* @var $model TrnKartuProsesPfpItem*/
                                    if($canCreateItem){
                                        return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, [
                                            'class' => 'btn btn-xs btn-danger',
                                            'title' => 'Delete: '.$model->id,
                                            'data' => [
                                                'confirm' => 'Are you sure you want to delete this item?',
                                                'method' => 'post',
                                            ],
                                        ]);
                                    }

                                    return '';
                                },
                            ]
                        ],
                    ],
                ])?>
            </div>
        </div>
    </div>
</div>
<?php
$js = <<<JS
// Fungsi pembuka Select2 Stock ID (cek berulang sampai siap)
function openStockIdSelect2Pfp() {
    var field = $('#trnkartuprosespfpitem-stock_id'); // sesuaikan ID ini jika beda
    if (field.length && field.data('select2')) {
        field.select2('open');
        return true;
    }
    return false;
}

// Saat modal Add Items ditampilkan
$(document).on('shown.bs.modal', '#kartuProsesPfpModal', function () {
    var modal = $(this);

    // Coba buka segera
    if (!openStockIdSelect2Pfp()) {
        // Kalau Select2 belum siap, ulangi tiap 300ms selama maksimal 3 detik
        var attempts = 0;
        var timer = setInterval(function(){
            attempts++;
            if (openStockIdSelect2Pfp() || attempts > 10) {
                clearInterval(timer);
            }
        }, 300);
    }
});
JS;
$this->registerJs($js);
?>