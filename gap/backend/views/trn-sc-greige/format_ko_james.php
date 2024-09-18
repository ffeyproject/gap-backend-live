<?php

use backend\components\ajax_modal\AjaxModal;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnScGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sc Greiges';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-sc-greige-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
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
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        /* @var $model TrnScGreige */
                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', $url, [
                                'title' => 'View',
                                'data-toggle' => "modal",
                                'data-target' => "#trnScModal",
                                'data-title' => 'Detail Greige Group: ' . $model->greigeGroup->nama_kain
                            ]).' '. Html::a('<i class="glyphicon glyphicon-print"></i>', ['display-order-greige', 'id'=>$model->id], [
                                'title' => 'Print Order Greige',
                                'data-toggle'=>"modal",
                                'data-target'=>"#trnScModal",
                                'data-title' => 'Print Order Greige',
                            ]);
                    },
                ]
            ],

            'id',
            'sc_id',
            [
                'label'=>'Nomor SC',
                'attribute'=>'nomorSc',
                'value'=>'sc.no'
            ],
            [
                'attribute' => 'dateRange',
                'label' => 'TANGGAL SC',
                'value' => 'sc.date',
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
            [
                'attribute' => 'scTipeKontrak',
                'label' => 'Orientasi',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnSc::tipeKontrakOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
                'value' => function($data){
                    /* @var $data TrnScGreige*/
                    return TrnSc::tipeKontrakOptions()[$data->sc->tipe_kontrak];
                },
            ],
            [
                'attribute'=>'process',
                'value'=>function($data){
                    /* @var $data TrnScGreige*/
                    return $data::processOptions()[$data->process];
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
                'attribute' => 'scCustomerName',
                'label' => 'Nama Buyer',
                'value' => function($data){
                    /* @var $data TrnScGreige*/
                    return $data->sc->customerName;
                },
            ],
            [
                'attribute' => 'scMarketingName',
                'label' => 'Marketing',
                'value' => function($data){
                    /* @var $data TrnScGreige*/
                    return $data->sc->marketingName;
                },
            ],
            [
                'attribute' => 'scNoPo',
                'label' => 'No. PO',
                'value' => function($data){
                    /* @var $data TrnScGreige*/
                    return $data->sc->no_po;
                },
            ],
            [
                'label'=>'Nama Kain',
                'attribute'=>'greigeGroupNamaKain',
                'value'=>'greigeGroup.nama_kain'
            ],
            [
                'label'=>'Harga',
                'attribute'=>'unit_price',
                'format'=>'decimal'
            ],
            [
                'label'=>'Qty (Batch)',
                'attribute'=>'qty',
                'format'=>'decimal'
            ],
            [
                'label'=>'Qty Finish',
                'attribute'=>'qtyFinish',
                'format'=>'decimal'
            ],
            [
                'label'=>'Qty Finish (Yd)',
                'attribute'=>'qtyFinishToYard',
                'format'=>'decimal'
            ],
            [
                'attribute'=>'lebar_kain',
                'value'=>function($data){
                    /* @var $data TrnScGreige*/
                    return $data::lebarKainOptions()[$data->lebar_kain];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnScGreige::lebarKainOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            [
                'label'=>'Sisa MO (Batch)',
                'attribute'=>'sisaMoBatch',
                'format'=>'decimal'
            ],
            [
                'label'=>'Sisa WO (Batch)',
                'attribute'=>'sisaWoBatch',
                'format'=>'decimal'
            ],
        ],
    ]); ?>


</div>
<?php
echo AjaxModal::widget([
    'id' => 'trnScModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);
