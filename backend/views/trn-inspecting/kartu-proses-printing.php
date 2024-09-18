<?php

use backend\components\ajax_modal\AjaxModal;
use common\models\ar\TrnKartuProsesPrinting;
use common\models\ar\TrnStockGreige;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesPrintingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inspect Kartu Proses Printing';
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="kartu-proses-printing-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['kartu-proses-printing'], ['class' => 'btn btn-default']),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar'=>false,
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            //'id',
            //'wo_id',
            /*[
                'attribute'=>'woNo',
                'label'=>'Nomor WO',
                'value'=>'wo.no'
            ],*/
            'no_proses',
            'no',
            [
                'attribute'=>'asal_greige',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
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
            /*[
                'attribute'=>'status',
                'value'=>function($data){
                    ///* @var $data KartuProsesPrinting
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => KartuProsesPrinting::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],*/
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
                    ///* @var $data KartuProsesPrinting
                    $totalPanjang = 0;
                    foreach ($data->kartuProsesPrintingItems as $kartuProsesPrintingItem) {
                        $stockGreige = $kartuProsesPrintingItem->stock->toArray();
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
                        return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', ['/trn-kartu-proses-printing/view', 'id'=>$model->id], ['title'=>'Detail Kartu Proses', 'target'=>'_blank']);
                    },
                    'reject'=>function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', ['/inspecting-printing-reject/create', 'kartu_proses_id'=>$key], [
                            'title'=>'Kembalikan Kartu Proses Printing',
                            'data-toggle' => "modal",
                            'data-target' => "#trnInspectingModal",
                            'data-title' => 'Kembalikan Kartu Proses Printing'
                        ]);
                    },
                    'inspect'=>function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>', ['create', 'process'=>\common\models\ar\TrnScGreige::PROCESS_PRINTING, 'processId'=>$model->id], ['title'=>'Buat Inspecting']);
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
