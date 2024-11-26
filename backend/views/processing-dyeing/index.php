<?php
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Processing Dyeing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kartu-proses-dyeing-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar'=>[
            '{export}',
            '{toggleData}'
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
            [
                'label'=>'Warna',
                'value'=>'woColor.moColor.color'
            ],
            //'note:ntext',
            [
                'attribute'=>'status',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
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
                    /* @var $data TrnKartuProsesDyeing*/
                    $totalPanjang = 0;
                    foreach ($data->trnKartuProsesDyeingItems as $trnKartuProsesDyeingItem) {
                        $stockGreige = $trnKartuProsesDyeingItem->stock->toArray();
                        $totalPanjang += $stockGreige['panjang_m'];
                    }
                    return $totalPanjang;
                },
                'format'=>'decimal',
                'pageSummary' => true,
                'hAlign' => 'right'
            ],
            [
                'label'=>'Matching Colour',
                'value'=> function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    $isReadyColour = $data->woColor->ready_colour ? 'Ya' : 'Tidak';
                    $dateReadyColour = $data->woColor->date_ready_colour;
                    return $isReadyColour . ' / ' . ($dateReadyColour !== null ? date('Y-m-d', $dateReadyColour) : '');

                },
                'hAlign' => 'right'
            ],
            [
                'label'=>'Toping Matching',
                'value'=> function($data){
                    $topingMatching = $data->toping_matching ? 'Ya' : 'Tidak';
                    $dateTopingMatching = $data->date_toping_matching;
                    return $topingMatching . ' / ' . ($dateTopingMatching !== null ? date('Y-m-d', $dateTopingMatching) : '');
                },
                'hAlign' => 'right'
            ],
            //'created_at:datetime',
            //'created_by',
            //'updated_at:datetime',
            //'updated_by',
        ],
    ]); ?>


</div>
