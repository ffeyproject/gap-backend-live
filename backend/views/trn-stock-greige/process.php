<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnStockGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Process Packing List Greige';
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

$pcBtn = Html::a('<i class="glyphicon glyphicon-ok"></i>', ['execute-process', 'noDoc'=>$searchModel->no_document], [
    'class' => 'btn btn-success',
    'data' => [
        'confirm' => 'Anda yakin akan memproses packing list ini?',
        'method' => 'post',
    ],
]);
if($searchModel->hasErrors()){
    $pcBtn = Html::a('<i class="glyphicon glyphicon-ok"></i>', '#', [
        'class'=>'btn btn-success',
        'disabled'=>'disabled'
    ]);
}
?>
<div class="trn-stock-greige-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before'=>Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['process'], ['class' => 'btn btn-default']).
                $pcBtn,
                ['class'=>'btn-group', 'role'=>'group']
            ),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar' => [
            [
                'content'=>$this->render('_search-no-doc', ['model' => $searchModel])
            ],
        ],
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            //['class' => '\kartik\grid\CheckboxColumn'],

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
            //'no_document',
            'no_lapak',
            [
                'attribute'=>'status_tsd',
                'value'=>function($data){
                    /* @var $data \common\models\ar\TrnStockGreige*/
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
                'label'=>'Greige',
                'attribute'=>'greigeNamaKain',
                'value'=>'greige.nama_kain'
            ],
            [
                'attribute'=>'grade',
                'value'=>function($data){
                    /* @var $data \common\models\ar\TrnStockGreige*/
                    return $data::gradeOptions()[$data->grade];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\TrnStockGreige::gradeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            'lot_lusi',
            'lot_pakan',
            'no_set_lusi',
            [
                'attribute'=>'panjang_m',
                'format'=>'decimal',
                'pageSummary'=>true
            ],
            [
                'attribute'=>'status',
                'value'=>function($data){
                    /* @var $data \common\models\ar\TrnStockGreige*/
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\TrnStockGreige::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            //'pengirim',
            //'mengetahui',
            //'note:ntext',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',

            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],
        ],
    ]); ?>


</div>
