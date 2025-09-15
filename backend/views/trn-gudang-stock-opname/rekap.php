<?php

use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnStockGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rekap Stock Greige';
$this->params['breadcrumbs'][] = $this->title;

\backend\assets\DataTablesAsset::register($this);
?>
<div class="trn-stock-greige-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'StockGreigeGrid',
        'responsiveWrap' => false,
        'pjax' => true,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
            'after'=>false,
            //'footer'=>false
        ],
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            //'id',
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
            'lot_lusi',
            'greigeNamaKain',
            'lot_pakan',
            'note',
            [
                'attribute'=>'status_tsd',
                'label' => 'Kondisi Greige',
                'value'=>function($data){
                    /* @var $data TrnStockGreige*/
                    return $data::tsdOptions()[$data->status_tsd];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\TrnStockGreige::tsdOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'label'=>'Lebar Kain',
                'value' => function($data){
                    /* @var $data TrnStockGreige*/
                    return $data->greigeGroup->lebarKainName;
                }
            ],
            [
                'label'=>'Total Stock',
                'value' => function($data){
                    /* @var $data TrnStockGreige*/
                    return $data->greige->available;
                },
                'format' => 'decimal'
            ],
        ],
    ]); ?>
</div>