<?php

use common\models\ar\TrnKartuProsesMaklon;
use common\models\ar\TrnScGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesMaklonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Kartu Proses Maklon';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-maklon-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
            //'sc_id',
            //'sc_greige_id',
            //'mo_id',
            //'wo_id',
            [
                'attribute'=>'woNo',
                'label'=>'Nomor WO',
                'value'=>'wo.no'
            ],
            //'vendor_id',
            [
                'attribute'=>'vendorName',
                'label'=>'Vendor',
                'value'=>'vendor.name'
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
            [
                'label'=>'Panjang',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesMaklon*/
                    $totalPanjang = $data->getTrnKartuProsesMaklonItems()->sum('qty');
                    $totalPanjang = $totalPanjang > 0 ? $totalPanjang : 0;

                    return $totalPanjang;
                },
                'format'=>'decimal',
                'pageSummary' => true,
                'hAlign' => 'right'
            ],
            //'no_urut',
            //'no',
            //'asal_greige',
            //'penerima',
            //'note:ntext',
            //'date',
            //'posted_at',
            //'approved_at',
            //'approved_by',
            [
                'attribute'=>'status',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesMaklon*/
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnKartuProsesMaklon::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            //'created_at:datetime',
            //'created_by',
            //'updated_at:datetime',
            //'updated_by',
        ],
    ]); ?>


</div>
