<?php

use common\models\ar\TrnOrderPfp;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnOrderPfpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order PFP';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-order-pfp-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'showPageSummary' => true,
        'panel' => [
            'before'=>Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']).
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success']),
                ['class'=>'btn-group', 'role'=>'group']
            ),
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            'id',
            //'greige_group_id',
            [
                'attribute'=>'greigeGroupNamaKain',
                'label'=>'Greige Group',
                'value'=>'greigeGroup.nama_kain'
            ],
            //'greige_id',
            [
                'attribute'=>'greigeNamaKain',
                'label'=>'Greige',
                'value'=>'greige.nama_kain'
            ],
            //'no_urut',
            'no',
            [
                'attribute' => 'qty',
                'format' => ['decimal', 2],
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM,
            ],
            //'note:ntext',
            [
                'label'=>'Greige',
                'attribute' => 'qtyBatchToMeter',
                'format' => 'decimal',
                'pageSummary' => true,
                'hAlign' => 'right'
            ],
            [
                'attribute' => 'status',
                'value' => function($data){
                    /* @var $data TrnOrderPfp*/
                    return TrnOrderPfp::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnOrderPfp::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            [
                'attribute' => 'dateRange',
                'label' => 'Tanggal',
                'value' => 'date',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        //'timePicker'=>true,
                        //'timePickerIncrement'=>5,
                        'locale'=>[
                            //'format'=>'Y-m-d H:i:s',
                            'format'=>'Y-m-d',
                            'separator'=>' to ',
                        ]
                    ]
                ]
            ],
            //'created_at:datetime',
            //'created_by',
            //'updated_at:datetime',
            //'updated_by',
        ],
    ]); ?>


</div>