<?php

use common\models\ar\TrnReturBuyer;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnStockGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Stock Ex Finish';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-stock-greige-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            //['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            'id',
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
            //'no_lapak',
            /*[
                'attribute'=>'status_tsd',
                'value'=>function($data){
                    @var $data TrnStockGreige
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
            ],*/
            [
                'label'=>'Greige',
                'attribute'=>'greigeNamaKain',
                'value'=>'greige.nama_kain'
            ],
            [
                'attribute'=>'grade',
                'value'=>function($data){
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
            //'lot_lusi',
            //'lot_pakan',
            //'no_set_lusi',
            [
                'attribute'=>'panjang_m',
                'format'=>'decimal',
                'pageSummary'=>true
            ],
            [
                'attribute'=>'status',
                'value'=>function($data){
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
            [
                'attribute'=>'asal_greige',
                'value'=>function($data){
                    /* @var $data TrnStockGreige*/
                    return $data::asalGreigeOptions()[$data->asal_greige];
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
            /*[
                'attribute'=>'jenis_gudang',
                'value'=>'jenisGudangName',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::jenisGudangOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],*/
            //'pengirim',
            //'mengetahui',
            //'note:ntext',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            'nomor_wo',
            [
                'attribute' => 'keputusan_qc',
                'value' => function($data){
                    /* @var $data TrnReturBuyer*/
                    return $data->keputusan_qc !== null ? TrnReturBuyer::keputusanQcOptions()[$data->keputusan_qc] : null;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnReturBuyer::keputusanQcOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
        ],
    ]); ?>


</div>
