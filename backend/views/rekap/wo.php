<?php

use common\models\ar\TrnMoColor;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnWo;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnWoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rekap Working Order';
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
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['mo-color'], ['class' => 'btn btn-default']),
            'after'=>false,
            //'footer'=>false
        ],
        'beforeHeader' => [
            [
                'columns'=>[
                    ['content'=>'SALES CONTRACT', 'options'=>['colspan'=>7, 'class'=>'text-center']],
                    ['content'=>'SC GREIGE GROUP', 'options'=>['colspan'=>10, 'class'=>'text-center']],
                    ['content'=>'MARKETING ORDER', 'options'=>['colspan'=>6, 'class'=>'text-center']],
                    ['content'=>'WORKING ORDER', 'options'=>['colspan'=>4, 'class'=>'text-center']],
                ]
            ],
        ],
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            //'id',
            [
                'label'=>'No. SC',
                'attribute'=>'scNo',
                'value' => function($data){
                    /* @var $data TrnMoColor*/
                    return Html::a($data->mo->scGreige->sc->no, ['/trn-sc/view', 'id'=>$data->mo->scGreige->sc->id], ['target'=>'blank']);
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'dateRangeSc',
                'label' => 'Tgl. SC',
                'value' => 'mo.scGreige.sc.date',
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
                'label'=>'Orientasi',
                'attribute'=>'scOrientasi',
                'value' => function($data){
                    /* @var $data TrnMoColor*/
                    return TrnSc::tipeKontrakOptions()[$data->mo->scGreige->sc->tipe_kontrak];
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
                'value'=>'mo.scGreige.sc.customerName'
            ],
            [
                'label'=>'Marketing',
                'attribute'=>'marketingName',
                'value'=>'mo.scGreige.sc.marketingName'
            ],
            [
                'label'=>'No. PO',
                'attribute'=>'scNoPo',
                'value'=>'mo.scGreige.sc.no_po'
            ],
            [
                'label'=>'Mata Uang',
                'attribute'=>'scCurrencyId',
                'value' => function($data){
                    /* @var $data TrnMoColor*/
                    return TrnSc::currencyOptions()[$data->mo->sc->currency];
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
                'value'=>'mo.scGreige.greigeGroup.nama_kain'
            ],
            [
                'label'=>'Jenis Proses',
                'attribute'=>'proccess',
                'value' => function($data){
                    /* @var $data TrnWo*/
                    return TrnScGreige::processOptions()[$data->mo->scGreige->process];
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
                'value'=>'mo.scGreige.unit_price',
                'format'=>'decimal'
            ],
            [
                'label'=>'Grade',
                'attribute'=>'scGreigeGrade',
                'value' => function($data){
                    /* @var $data TrnMoColor*/
                    return TrnScGreige::gradeOptions()[$data->mo->scGreige->grade];
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
                'value' => 'mo.scGreige.greigeGroup.unitName',
            ],
            [
                'label'=>'Qty (batch)',
                'value' => 'mo.scGreige.qty',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            [
                'label'=>'Qty (Unit)',
                'value' => 'mo.scGreige.qtyBatchToUnit',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            [
                'label'=>'Qty Finish (Unit)',
                'value' => 'mo.scGreige.qtyFinish',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            [
                'label'=>'Qty Finish (yd)',
                'value' => 'mo.scGreige.qtyFinishToYard',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            [
                'label'=>'No. MO',
                'attribute'=>'moNo',
                'value' => function($data){
                    /* @var $data TrnWo*/
                    return Html::a($data->mo->no, ['/trn-mo', ['id'=>$data->mo_id]]);
                },
                'format'=>'html'
            ],
            [
                'attribute' => 'dateRangeMo',
                'label' => 'Tgl. MO',
                'value' => 'mo.date',
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
                'label'=>'MO Qty (batch)',
                'value' => 'mo.colorQty',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            [
                'label'=>'MO Qty (unit)',
                'value' => 'mo.colorQtyBatchToUnit',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            [
                'label'=>'MO Qty Finish (unit)',
                'value' => 'mo.colorQtyFinish',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            [
                'label'=>'MO Qty Finish (yd)',
                'value' => 'mo.colorQtyFinishToYard',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary'=>true,
            ],
            [
                'label'=>'No. WO',
                'attribute'=>'no',
                'value' => function($data){
                    /* @var $data TrnWo*/
                    return Html::a($data->no, ['/trn-wo', ['id'=>$data->no]]);
                },
                'format'=>'html'
            ],
            [
                'attribute' => 'dateRange',
                'label' => 'Tgl. WO',
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
                'label'=>'WO Qty (Batch)',
                'attribute'=>'colorQty',
                'format' => 'decimal',
            ],
            [
                'label'=>'WO Qty (Unit)',
                'attribute'=>'colorQtyBatchToUnit',
                'format' => 'decimal',
            ],
        ],
    ]); ?>
</div>