<?php

use common\models\ar\TrnMo;
use common\models\ar\TrnScGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnMoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Marketing Order';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-mo-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'resizableColumns' => false,
        //'floatHeader' => true,
        //'floatHeaderOptions'=>['scrollingTop'=>'50', 'zIndex'=>800],
        //'toolbar' => false,
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index-sisa'], ['class' => 'btn btn-default']),
        ],
        /*'toolbar' => [
            [
                'content'=>
                    Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], [
                        'class' => 'btn btn-default',
                        'title' => 'Refresh data'
                    ])
            ],
        ],*/
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            // [
            //     'class' => 'kartik\grid\ActionColumn',
            //     'template' => '{view}'
            // ],

            [
                'attribute' => 'id',
                'format' => 'html',
                'value' => function($data){
                    return Html::a($data['id'], ['view', 'id'=>$data['id']], ['title'=>'Lihat detail MO']);
                },
                'hAlign' => 'center'
            ],
            [
                'attribute' => 'no_urut',
                'format' => 'html',
                'value' => function($data){
                    return Html::a($data['no_urut'], ['view', 'id'=>$data['id']], ['title'=>'Lihat detail MO']);
                },
                'hAlign' => 'center'
            ],
            [
                'attribute' => 'no',
                'format' => 'html',
                'value' => function($data){
                    return Html::a($data['no'], ['view', 'id'=>$data['id']], ['title'=>'Lihat detail MO']);
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
                        'locale'=>[
                            'format'=>'Y-m-d',
                            'separator'=>' to ',
                        ],
                        'maxSpan' => [
                            'days' => 31,
                        ],
                    ],
                    'options' => [
                        'autocomplete' => 'off'
                    ]
                ]
            ],            
            [
                'attribute' => 'nomorSc',
                'format' => 'html',
                'value' => function($data){
                    $sc = $data['sc'];
                    return Html::a($sc['no'], ['/trn-sc/view', 'id'=>$data['sc_id']], ['title'=>'Lihat detail SC']);
                },
            ],
            [
                'attribute'=>'customerName',
                'label' => 'NAMA BUYER',
                'value' => 'sc.cust.name',
                'headerOptions' => ['class'=>'text-center', 'style'=>'vertical-align:middle;'],
            ],
            [
                'attribute'=>'marketingName',
                'value' => 'sc.marketing.full_name',
                'headerOptions' => ['class'=>'text-center', 'style'=>'vertical-align:middle;'],
            ],
            [
                'attribute'=>'scGreigeNamaKain',
                'label'=>'Motif Greige',
                'value'=>'scGreige.greigeGroup.nama_kain',
            ],
            [
                'attribute' => 'process',
                'value' => function($data){
                    return TrnScGreige::processOptions()[$data['process']];
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
            're_wo',
            'design',
            'sulam_pinggir',
            'heat_cut:boolean',
            'jet_black:boolean',
            [
                'attribute'=>'creatorName',
                'value' => 'createdBy.full_name',
                'headerOptions' => ['class'=>'text-center', 'style'=>'vertical-align:middle;'],
            ],
            [
                'label' => '____ Status ____',
                'attribute' => 'status',
                'value' => function($data){
                    return TrnMo::statusOptions()[$data['status']];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnMo::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            [
                'attribute'=>'colorQty',
                'format'=>'decimal',
                'hAlign'=>'right',
                'value'=> function($data){
                    return isset($data['color_qty']) ? $data['color_qty'] : 0;
                }
            ],
            [
                'attribute'=>'trnWoColorsAktifQty',
                'label'=>'WO Aktif',
                'format'=>'decimal',
                'hAlign'=>'right',
                'value'=> function($data){
                    return isset($data['wo_color_qty']) ? $data['wo_color_qty'] : 0;
                }

            ],
            [
                'label'=>'WO Sisa (Batch)',
                'format'=>'decimal',
                'value'=>function($data){
                    return isset($data['wo_sisa_batch']) ? $data['wo_sisa_batch'] : 0;
                },
                'hAlign'=>'right'
            ],
        ],
    ]); ?>
</div>
