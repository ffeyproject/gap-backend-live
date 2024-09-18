<?php

use common\models\ar\TrnBuyGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnBuyGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rekap Kedatangan Greige';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-buy-greige-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['rekap'], ['class' => 'btn btn-default']),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            //['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            //'id',
            [
                'attribute' => 'dateRange',
                'label' => 'Tanggal',
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
            'no_document',
            [
                'label'=>'Motif',
                'attribute'=>'greigeNamaKain',
                'value'=>'greigeName'
            ],
            [
                'label'=>'Quantity',
                'value'=>function($data){
                    /* @var $data TrnBuyGreige*/
                    return $data->getTrnBuyGreigeItems()->sum('qty');
                },
                'format'=>'decimal',
                'pageSummary'=>true,
            ],
            [
                'label'=>'Satuan',
                'value'=>function($data){
                    /* @var $data TrnBuyGreige*/
                    return $data->greigeGroup->unitName;
                }
            ],
            [
                'label'=>'Pcs',
                'value'=>function($data){
                    /* @var $data TrnBuyGreige*/
                    return $data->getTrnBuyGreigeItems()->count();
                },
                'format'=>'decimal',
                'pageSummary'=>true,
            ],
            [
                'label'=>'Supplier',
                'value'=>function($data){
                    /* @var $data TrnBuyGreige*/
                    return '-';
                }
            ],
            [
                'attribute'=>'jenis_beli',
                'value'=>function($data){
                    /* @var $data TrnBuyGreige*/
                    return $data::jenisBeliOptions()[$data->jenis_beli];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnBuyGreige::jenisBeliOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            'note:ntext',
        ],
    ]); ?>
</div>
