<?php

use common\models\ar\TrnBuyGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnBuyGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Beli Greige';
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
            [
                'label'=>'Greige Group',
                'attribute'=>'greigeGroupNamaKain',
                'value'=>'greigeGroupName'
            ],
            [
                'label'=>'Greige',
                'attribute'=>'greigeNamaKain',
                'value'=>'greigeName'
            ],
            'no_document',
            'vendor',
            //'note:ntext',
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
            [
                'attribute'=>'status',
                'value'=>function($data){
                    /* @var $data TrnBuyGreige*/
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnBuyGreige::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'approval_id',
            //'approval_time:datetime',
            //'reject_note:ntext',
        ],
    ]); ?>


</div>
