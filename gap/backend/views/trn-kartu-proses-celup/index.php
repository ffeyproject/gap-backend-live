<?php

use common\models\ar\TrnKartuProsesCelup;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesCelupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Kartu Proses Celup';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-celup-index">
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
            //'order_celup_id',
            [
                'attribute'=>'orderCelupNo',
                'label'=>'Nomor Order Celup',
                'value'=>'orderPfp.no'
            ],
            //'no_urut',
            'no',
            //'no_proses',
            //'asal_greige',
            [
                'attribute'=>'asal_greige',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesCelup*/
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
            //'date',
            //'posted_at',
            //'approved_at',
            //'approved_by',
            //'status',
            [
                'attribute'=>'status',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesCelup*/
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnKartuProsesCelup::statusOptions(),
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
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'delivered_at',
            //'delivered_by',
            //'reject_notes:ntext',
            //'berat',
            //'lebar',
            //'k_density_lusi',
            //'k_density_pakan',
            //'gramasi',
            //'lebar_preset',
            //'lebar_finish',
            //'berat_finish',
            //'t_density_lusi',
            //'t_density_pakan',
            //'handling',
        ],
    ]); ?>


</div>
