<?php

use common\models\ar\TrnKartuProsesPrinting;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesPrintingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Proccessing Printing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-printing-index">
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

            //'id',
            //'wo_id',
            [
                'attribute'=>'woNo',
                'label'=>'Nomor WO',
                'value'=>'wo.no'
            ],
            'no_proses',
            'no',
            [
                'label'=>'Color',
                'attribute'=>'moColorColor',
                'value'=>'woColor.moColor.color'
            ],
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
                    @var $data TrnKartuProsesPrinting
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnKartuProsesPrinting::statusOptions(),
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
            [
                'label'=>'Panjang',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    $totalPanjang = 0;
                    foreach ($data->trnKartuProsesPrintingItems as $trnKartuProsesPrintingItems) {
                        $stockGreige = $trnKartuProsesPrintingItems->stock->toArray();
                        $totalPanjang += $stockGreige['panjang_m'];
                    }
                    return $totalPanjang;
                },
                'format'=>'decimal',
                'pageSummary' => true,
                'hAlign' => 'right'
            ],
            //'created_at:datetime',
            //'created_by',
            //'updated_at:datetime',
            //'updated_by',
        ],
    ]); ?>


</div>
