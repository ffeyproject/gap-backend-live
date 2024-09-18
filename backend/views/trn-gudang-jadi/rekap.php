<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnGudangJadiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rekap Gudang Jadi';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-gudang-jadi-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'resizableColumns' => false,
        'responsiveWrap' => false,
        'toolbar' => [
            //'{toggleData}',
            '{export}'
        ],
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['rekap'], ['class' => 'btn btn-default']),
            'after'=>false,
            //'footer'=>false
        ],
        'beforeHeader' => [
            [
                'columns'=>[
                    ['content'=>'SALES CONTRACT', 'options'=>['colspan'=>7, 'class'=>'text-center']],
                    ['content'=>'SC GREIGE GROUP', 'options'=>['colspan'=>9, 'class'=>'text-center']],
                    ['content'=>'MARKETING ORDER', 'options'=>['colspan'=>6, 'class'=>'text-center']],
                    ['content'=>'WORK ORDER', 'options'=>['colspan'=>7, 'class'=>'text-center']],
                    ['content'=>'GUDANG JADI', 'options'=>['colspan'=>12, 'class'=>'text-center']]
                ]
            ],
        ],
        'showPageSummary'=>true,
        'columns' => [
            //['class' => 'kartik\grid\SerialColumn'],

            //'id',
            [
                'label'=>'No. SC',
                'attribute'=>'scNo',
                'value'=>'wo.mo.scGreige.sc.no',
            ],
            [
                'label'=>'Tgl. SC',
                'attribute'=>'scDate',
                'value'=>'wo.mo.scGreige.sc.date',
                'format'=>'date'
            ],
            [
                'label'=>'Orientasi',
                'attribute'=>'scOrientasi',
                'value' => function($data){
                    /* @var $data TrnGudangJadi*/
                    return TrnSc::tipeKontrakOptions()[$data->wo->mo->scGreige->sc->tipe_kontrak];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnSc::tipeKontrakOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            [
                'label'=>'Nama Buyer',
                'attribute'=>'customerName',
                'value'=>'wo.mo.scGreige.sc.customerName'
            ],
            [
                'label'=>'Marketing',
                'attribute'=>'marketingName',
                'value'=>'wo.mo.scGreige.sc.marketingName'
            ],
            [
                'label'=>'No. PO',
                'attribute'=>'scNoPo',
                'value'=>'wo.mo.scGreige.sc.no_po'
            ],
            [
                'label'=>'Mata Uang',
                'attribute'=>'scCurrencyId',
                'value' => function($data){
                    /* @var $data TrnGudangJadi*/
                    return TrnSc::currencyOptions()[$data->wo->sc->currency];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnSc::currencyOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            [
                'label'=>'Nama Kain',
                'attribute'=>'scGreigeNamaKain',
                'value'=>'wo.mo.scGreige.greigeGroup.nama_kain'
            ],
            [
                'label'=>'Proses',
                'attribute'=>'scGreigeProcessId',
                'value' => function($data){
                    /* @var $data TrnGudangJadi*/
                    return TrnScGreige::processOptions()[$data->wo->mo->scGreige->process];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnScGreige::processOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            [
                'label'=>'Harga',
                'value'=>'wo.mo.scGreige.unit_price',
                'format'=>'decimal'
            ],
            [
                'label'=>'Grade',
                'attribute'=>'scGreigeGrade',
                'value' => function($data){
                    /* @var $data TrnGudangJadi*/
                    return TrnScGreige::gradeOptions()[$data->wo->mo->scGreige->grade];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnScGreige::gradeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            [
                'label'=>'Unit',
                'value' => 'wo.mo.scGreige.greigeGroup.unitName',
            ],
            [
                'label'=>'Qty (batch)',
                'value' => 'wo.mo.scGreige.qty',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            [
                'label'=>'Qty (Unit)',
                'value' => 'wo.mo.scGreige.qtyBatchToUnit',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            [
                'label'=>'Qty Finish (Unit)',
                'value' => 'wo.mo.scGreige.qtyFinish',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            [
                'label'=>'Qty Finish (yd)',
                'value' => 'wo.mo.scGreige.qtyFinishToYard',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            [
                'label'=>'No. MO',
                'attribute'=>'moNo',
                'value'=>'wo.mo.no'
            ],
            [
                'label'=>'Tgl. MO',
                'value'=>'wo.mo.date',
                'format'=>'date'
            ],
            [
                'label'=>'MO Qty (batch)',
                'value' => 'wo.mo.colorQty',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            [
                'label'=>'MO Qty (unit)',
                'value' => 'wo.mo.colorQtyBatchToUnit',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            [
                'label'=>'MO Qty Finish (unit)',
                'value' => 'wo.mo.colorQtyFinish',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            [
                'label'=>'MO Qty Finish (yd)',
                'value' => 'wo.mo.colorQtyFinishToYard',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            [
                'label'=>'No. WO',
                'attribute'=>'woNo',
                'value'=>'wo.no'
            ],
            [
                'label'=>'Tgl. WO',
                'value'=>'wo.date',
                'format'=>'date'
            ],
            [
                'label'=>'Nama Kain',
                'value'=>'wo.greige.nama_kain',
            ],
            [
                'label'=>'WO Qty (batch)',
                'value' => 'wo.colorQty',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            [
                'label'=>'WO Qty (unit)',
                'value' => 'wo.colorQtyBatchToUnit',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            [
                'label'=>'WO Qty Finish (unit)',
                'value' => 'wo.colorQtyFinish',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            [
                'label'=>'WO Qty Finish (yd)',
                'value' => 'wo.colorQtyFinishToYard',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            /*[
                'label'=>'',
                'attribute'=>'',
                'value'=>''
            ],*/
            [
                'attribute' => 'jenis_gudang',
                'value' => function($data){
                    /* @var $data TrnGudangJadi*/
                    return TrnGudangJadi::jenisGudangOptions()[$data->jenis_gudang];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnGudangJadi::jenisGudangOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            'color',
            [
                'attribute' => 'source',
                'value' => function($data){
                    /* @var $data TrnGudangJadi*/
                    return TrnGudangJadi::sourceOptions()[$data->source];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnGudangJadi::sourceOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            'source_ref',
            [
                'attribute' => 'unit',
                'value' => function($data){
                    /* @var $data TrnGudangJadi*/
                    return MstGreigeGroup::unitOptions()[$data->unit];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => MstGreigeGroup::unitOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            [
                'attribute'=>'qty',
                'format'=>'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            //'no_urut',
            //'no',

            [
                'attribute' => 'dateRange',
                'label' => 'Tanggal',
                'value' => 'date',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        //'timePicker'=>true,
                        //'timePickerIncrement'=>5,
                        'locale'=>[
                            //'format'=>'Y-m-d H:i:s',
                            'format'=>'Y-m-d',
                            'separator'=>' to ',
                        ]
                    ]
                ]
            ],

            [
                'attribute' => 'status',
                'value' => function($data){
                    /* @var $data TrnGudangJadi*/
                    return TrnGudangJadi::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnGudangJadi::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            //'note:ntext',
            //'created_at:datetime',
            //'created_by',
            //'updated_at',
            //'updated_by',
        ],
    ]); ?>
</div>