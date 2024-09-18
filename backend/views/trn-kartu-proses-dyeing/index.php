<?php
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Kartu Proses Dyeing';
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
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']).
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success']),
                ['class'=>'btn-group', 'role'=>'group']
            ),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            'id',
            //'wo_id',
            [
                'attribute'=>'woNo',
                'label'=>'Nomor WO',
                'value'=>'wo.no'
            ],
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
                    //return $data->status;
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnKartuProsesDyeing::statusOptions(),
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
            [
                'label'=>'Panjang',
                'value'=>function($data){
                    // /* @var $data TrnKartuProsesDyeing*/
                    // $totalPanjang = 0;
                    // foreach ($data->trnKartuProsesDyeingItems as $trnKartuProsesDyeingItem) {
                    //     $stockGreige = $trnKartuProsesDyeingItem->stock->toArray();
                    //     $totalPanjang += $stockGreige['panjang_m'];
                    // }
                    $panjangTotal = $data->getTrnKartuProsesDyeingItems()->sum('panjang_m');
                    $panjangTotal = $panjangTotal === null ? 0 : $panjangTotal;
                    return $panjangTotal;
                },
                'format'=>'decimal',
                'pageSummary' => true,
                'hAlign' => 'right'
            ],
            'nomor_kartu',
            //'created_at:datetime',
            //'created_by',
            //'updated_at:datetime',
            //'updated_by',
        ],
    ]); ?>


</div>
