<?php

use common\models\ar\TrnMo;
use common\models\ar\TrnScGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnMoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rekap Marketing Order';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-mo-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'resizableColumns' => false,
        'responsiveWrap' => false,
        'showPageSummary'=>true,
        //'floatHeader' => true,
        //'floatHeaderOptions'=>['scrollingTop'=>'50', 'zIndex'=>800],
        //'floatOverflowContainer' => true,
        //'perfectScrollbar' => true,
        //'containerOptions' => ['style' => 'height: 80%;'],
        'toolbar' => [
            [
                'content'=>
                    Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['mo'], [
                        'class' => 'btn btn-default',
                        'title' => 'Refresh data'
                    ])
            ],
            '{export}'
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}'
            ],

            'id',
            [
                'attribute' => 'no_urut',
                'format' => 'html',
                'value' => function($data){
                    //@var $data TrnMo
                    return Html::a($data->no_urut, ['view', 'id'=>$data->id], ['title'=>'Lihat detail MO']);
                },
                'hAlign' => 'center'
            ],

            [
                'attribute' => 'no',
                'format' => 'html',
                'value' => function($data){
                    /* @var $data TrnMo*/
                    return Html::a($data->no, ['/trn-mo/view', 'id'=>$data->id], ['title'=>'Lihat detail MO']);
                },
                'hAlign' => 'center'
            ],
            [
                'attribute' => 'dateRange',
                'label' => 'Tanggal MO',
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
                'attribute' => 'nomorSc',
                'format' => 'html',
                'value' => function($data){
                    /* @var $data TrnMo*/
                    $sc = $data->sc;
                    return Html::a($sc->no, ['/trn-sc/view', 'id'=>$data->sc_id], ['title'=>'Lihat detail SC']);
                },
            ],
            [
                'attribute'=>'customerName',
                'label' => 'NAMA BUYER',
                'value' => 'scGreige.sc.cust.name',
                'headerOptions' => ['class'=>'text-center', 'style'=>'vertical-align:middle;'],
            ],
            'marketingName',
            [
                'attribute'=>'scGreigeNamaKain',
                'label'=>'Motif Greige',
                'value'=>'scGreige.greigeGroup.nama_kain',
            ],
            [
                'attribute' => 'process',
                'value' => function($data){
                    /* @var $data TrnMo*/
                    return TrnScGreige::processOptions()[$data->process];
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
            'no_lab_dip',
            [
                'label'=>'Dibuat Oleh',
                'value'=>function($data){
                    /* @var $data TrnMo*/
                    return $data->createdBy->full_name;
                },
                'hAlign'=>'right'
            ],
            [
                'attribute'=>'colorQty',
                'label'=>'Qty (Batch)',
                'format'=>'decimal',
                'hAlign'=>'right',
                'pageSummary'=>true
            ],
            [
                'attribute'=>'colorQty',
                'format'=>'decimal',
                'hAlign'=>'right',
                'pageSummary'=>true
            ],
            [
                'attribute'=>'trnWoColorsOkQty',
                'label'=>'WO Turun',
                'format'=>'decimal',
                'hAlign'=>'right',
                'pageSummary'=>true
            ],
            [
                'label'=>'Sisa (Batch)',
                'format'=>'decimal',
                'value'=>function($data){
                    /* @var $data TrnMo*/
                    return $data->colorQty - $data->trnWoColorsOkQty;
                },
                'hAlign'=>'right',
                'pageSummary'=>true
            ],
        ],
    ]); ?>
</div>
