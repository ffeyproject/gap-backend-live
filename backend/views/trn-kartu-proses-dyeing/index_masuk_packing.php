<?php
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use common\models\ar\MstProcessDyeing;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rekap Dyeing Masuk Packing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kartu-proses-dyeing-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before'=>Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['get-data-masuk-packing'], ['class' => 'btn btn-default']).
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success']),
                ['class'=>'btn-group', 'role'=>'group']
            ),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/processing-dyeing/view/', 'id' => $model->id], [
                            'title' => Yii::t('yii', 'View'),
                            'data-pjax' => '0',
                            'target' => '_blank',
                        ]);
                    },
                ],
            ],

            'id',
            //'wo_id',
            [
                'attribute' => 'dateRangeMasukPacking',
                'label' => 'TANGGAL MASUK',
                'value' => 'approved_at',
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
                'attribute' => 'woDateRange',
                'label' => 'TANGGAL WO',
                'value' => 'wo.date',
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
                'attribute'=>'woNo',
                'label'=>'Nomor WO',
                'value'=>'wo.no'
            ],
            [   
                'attribute'=>'marketingName',
                'label'=>'Nama Marketing',
                'value'=>'wo.marketingName'
            ],
            'nomor_kartu',
            'no',
            [
                'attribute'=>'status',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    //return $data->status;
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
            //'note:ntext',
            [
                'attribute' => 'openDateRange',
                'label' => 'TANGGAL BUKA',
                'value' => 'tanggalKartuProcessDyeingProcess',
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
                'label'=>'Panjang Jadi',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return $data->getResinFinish();
                },
                'format'=>'decimal',
                'pageSummary' => true,
                'hAlign' => 'right'
            ],
            
            // [
            //     'label'=>'Panjang',
            //     'value'=>function($data){
            //         // /* @var $data TrnKartuProsesDyeing*/
            //         // $totalPanjang = 0;
            //         // foreach ($data->trnKartuProsesDyeingItems as $trnKartuProsesDyeingItem) {
            //         //     $stockGreige = $trnKartuProsesDyeingItem->stock->toArray();
            //         //     $totalPanjang += $stockGreige['panjang_m'];
            //         // }
            //         $panjangTotal = $data->getTrnKartuProsesDyeingItems()->sum('panjang_m');
            //         $panjangTotal = $panjangTotal === null ? 0 : $panjangTotal;
            //         return $panjangTotal;
            //     },
            //     'format'=>'decimal',
            //     'pageSummary' => true,
            //     'hAlign' => 'right'
            // ],
            //'created_at:datetime',
            //'created_by',
            //'updated_at:datetime',
            //'updated_by',
        ],
    ]); ?>


</div>
