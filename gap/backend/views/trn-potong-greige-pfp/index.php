<?php

use common\models\ar\TrnPotongGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnPotongGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Potong Greige';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-potong-greige-index">
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
                'label'=>'Greige Group',
                'attribute'=>'greigeGroupNamaKain',
                'value'=>'stockGreige.greigeGroup.nama_kain'
            ],
            [
                'label'=>'Greige',
                'attribute'=>'greigeNamaKain',
                'value'=>'stockGreige.greige.nama_kain'
            ],
            [
                'label'=>'Stock No. Doc.',
                'attribute'=>'stockNoDocument',
                'value'=>'stockGreige.no_document'
            ],
            //'stock_greige_id',
            'no_urut',
            'no',
            //'note:ntext',
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
            //'posted_at',
            //'approved_at',
            //'approved_by',
            [
                'attribute'=>'status',
                'value'=>function($data){
                    /* @var $data TrnPotongGreige*/
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnPotongGreige::statusOptions(),
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
        ],
    ]); ?>


</div>
