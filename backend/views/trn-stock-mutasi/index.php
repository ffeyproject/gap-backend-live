<?php

use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnStockGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Stock Gudang Mutasi';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-stock-mutasi-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
        ],
        'showPageSummary' => true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'urlCreator' => function($action, $model, $key, $index) {
                    return \yii\helpers\Url::to(['/trn-stock-greige/view', 'id' => $model->id]);
                }
            ],

            'id',
            [
                'attribute' => 'dateRange',
                'label' => 'TANGGAL',
                'value' => 'date',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'Y-m-d',
                            'separator' => ' to ',
                        ]
                    ]
                ],
            ],
            'no_document',
            [
                'label' => 'No. WO',
                'attribute' => 'nomor_wo',
            ],
            [
                'label' => 'Greige',
                'attribute' => 'greigeNamaKain',
                'value' => 'greige.nama_kain'
            ],
            [
                'attribute' => 'grade',
                'value' => function($data){
                    /* @var $data TrnStockGreige*/
                    return $data::gradeOptions()[$data->grade];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::gradeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            'lot_lusi',
            [
                'attribute' => 'panjang_m',
                'format' => 'decimal',
                'pageSummary' => true
            ],
            'color',
            [
                'attribute' => 'status',
                'value' => function($data){
                    /* @var $data TrnStockGreige*/
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            'pengirim',
            'note:ntext',
        ],
    ]); ?>
</div>
