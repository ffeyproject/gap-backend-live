<?php

use backend\components\ajax_modal\AjaxModal;
use common\models\ar\TrnGudangInspect;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnGudangInspectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'GD GREIGE : Stock Gudang Inspect';
$this->params['breadcrumbs'][] = $this->title;

\backend\assets\DataTablesAsset::register($this);

echo AjaxModal::widget([
    'id' => 'allStockModal',
    'size' => 'modal-md',
    'header' => '<h4 class="modal-title">...</h4>',
]);
?>
<div class="trn-stock-greige-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Html::a('Lihat Stock Keseluruhan', ['seluruh-stock-gudang-inspect'], [
            'class' => 'btn btn-success',
            'title' => 'Lihat Stock Keseluruhan',
            'data-toggle'=>"modal",
            'data-target'=>"#allStockModal",
            'data-title' => 'Lihat Stock Keseluruhan'
        ])?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'StockGreigeGrid',
        'responsiveWrap' => false,
        'pjax' => true,
        'panel' => [
            'type' => 'default',
            'before'=>Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index-gudang-inspect'], ['class' => 'btn btn-default']),
                ['class'=>'btn-group', 'role'=>'group']
            ),
            'after'=>false,
            //'footer'=>false
        ],
        'showPageSummary'=>true,
        'columns' => [
            //['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view-gudang-inspect}',
                'buttons' => [
                    'view-gudang-inspect' => function ($url, $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                            ['view-gudang-inspect', 'id' => $model->id],
                            [
                                'title' => 'Lihat Gudang Inspect',
                                'aria-label' => 'Lihat Gudang Inspect',
                                'data-pjax' => '0', // agar tidak dijalankan melalui PJAX
                            ]
                        );
                    },
                ],
            ],
            

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
            'no_document',
            'no_lapak',
            [
                'attribute'=>'status_tsd',
                'value'=>function($data){
                    /* @var $data TrnGudangInspect*/
                    return $data::tsdOptions()[$data->status_tsd];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\TrnGudangInspect::tsdOptions(),
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
            // [
            //     'attribute'=>'grade',
            //     'value'=>function($data){
            //         /* @var $data TrnGudangInspect*/
            //         return $data::gradeOptions()[$data->grade];
            //     },
            //     'filterType' => GridView::FILTER_SELECT2,
            //     'filterWidgetOptions' => [
            //         'data' => TrnGudangInspect::gradeOptions(),
            //         'options' => ['placeholder' => '...'],
            //         'pluginOptions' => [
            //             'allowClear' => true
            //         ],
            //     ],
            // ],
            'lot_lusi',
            'lot_pakan',
            [
                'attribute'=>'status',
                'value'=>function($data){
                    /* @var $data TrnGudangInspect*/
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnGudangInspect::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'attribute'=>'asal_greige',
                'value'=>function($data){
                    /* @var $data TrnGudangInspect*/
                    return $data::asalGreigeOptions()[$data->asal_greige];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnGudangInspect::asalGreigeOptions(),
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
                    'data' => TrnGudangInspect::jenisGudangOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],*/
            'is_pemotongan:boolean',
            'is_hasil_mix:boolean',
            //'pengirim',
            //'mengetahui',
            //'note:ntext',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
        ],
    ]); ?>

</div>
