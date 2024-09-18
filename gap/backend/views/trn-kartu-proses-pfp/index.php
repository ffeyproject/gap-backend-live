<?php

use common\models\ar\TrnKartuProsesPfp;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesPfpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Kartu Proses PFP';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-pfp-index">
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
        //'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            'id',
            //'greige_group_id',
            //'greige_id',
            //'order_pfp_id',
            [
                'attribute'=>'orderPfpNo',
                'label'=>'Nomor Order PFP',
                'value'=>'orderPfp.no'
            ],
            //'no_urut',
            'no',
            //'no_proses',
            [
                'attribute'=>'asal_greige',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPfp*/
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
                    /* @var $data TrnKartuProsesPfp*/
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnKartuProsesPfp::statusOptions(),
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
            'nomor_kartu',
            //'posted_at',
            //'approved_at',
            //'approved_by',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'delivered_at',
            //'delivered_by',
            //'reject_notes:ntext',
            //'pc_bukaan:ntext',
            //'pc_scouring:ntext',
            //'pc_relaxing:ntext',
            //'pc_scutcher:ntext',
            //'pc_preset:ntext',
            //'pc_weight_reducetion:ntext',
            //'pc_washing_off:ntext',
            //'pc_heat_sett:ntext',
            //'pc_padding:ntext',
        ],
    ]); ?>


</div>
