<?php

use backend\components\ajax_modal\AjaxModal;
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inspect Kartu Proses Dyeing';
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="kartu-proses-dyeing-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['kartu-proses-dyeing'], ['class' => 'btn btn-default']),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar'=>false,
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            //'id',
            //'wo_id',
            [
                'attribute'=>'woNo',
                'label'=>'Nomor WO',
                'value'=>'wo.no'
            ],
            //'no_proses',
            'no',
            [
                'attribute'=>'asal_greige',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return TrnStockGreige::asalGreigeOptions()[$data->asal_greige];
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
            'dikerjakan_oleh',
            'lusi',
            'pakan',
            //'note:ntext',
            [
                'attribute'=>'status',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => [
                        TrnKartuProsesDyeing::STATUS_APPROVED => TrnKartuProsesDyeing::statusOptions()[TrnKartuProsesDyeing::STATUS_APPROVED],
                        TrnKartuProsesDyeing::STATUS_INSPECTED => TrnKartuProsesDyeing::statusOptions()[TrnKartuProsesDyeing::STATUS_INSPECTED],
                    ],
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
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
            /*[
                'label'=>'Panjang',
                'value'=>function($data){
                    ///* @var $data KartuProsesDyeing
                    $totalPanjang = 0;
                    foreach ($data->kartuProsesDyeingItems as $kartuProsesDyeingItem) {
                        $stockGreige = $kartuProsesDyeingItem->stock->toArray();
                        $totalPanjang += $stockGreige['panjang_m'];
                    }
                    return $totalPanjang;
                },
                'format'=>'decimal',
                'pageSummary' => true,
                'hAlign' => 'right'
            ],*/
            //'created_at:datetime',
            //'created_by',
            //'updated_at:datetime',
            //'updated_by',

            [
                'class' => 'kartik\grid\ActionColumn',
                'template'=>'{view} {inspect} {reject}',
                'buttons'=>[
                    'view'=>function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', ['/trn-kartu-proses-dyeing/view', 'id'=>$model->id], ['title'=>'Detail Kartu Proses', 'target'=>'_blank']);
                    },
                    'reject'=>function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', ['/inspecting-dyeing-reject/create', 'kartu_proses_id'=>$key], [
                            'title'=>'Kembalikan Kartu Proses Dyeing',
                            'data-toggle' => "modal",
                            'data-target' => "#trnInspectingModal",
                            'data-title' => 'Kembalikan Kartu Proses Dyeing'
                        ]);
                    },
                    'inspect'=>function ($url, $model, $key) {
                        /* @var $model TrnKartuProsesDyeing*/
                        return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>', ['create', 'process'=>\common\models\ar\TrnScGreige::PROCESS_DYEING, 'processId'=>$model->id], ['title'=>'Buat Inspecting']);
                    },
                ]
            ],
        ],
    ]); ?>
</div>

<?php
echo AjaxModal::widget([
    'id' => 'trnInspectingModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);
