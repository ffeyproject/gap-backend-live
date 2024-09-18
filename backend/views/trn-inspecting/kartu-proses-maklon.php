<?php
use common\models\ar\TrnKartuProsesMaklon;
use common\models\ar\TrnStockGreige;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesMaklonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inspect Kartu Proses Maklon';
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="kartu-proses-maklon-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['kartu-proses-maklon'], ['class' => 'btn btn-default']),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar'=>false,
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            //'id',
            //'wo_id',
            /*[
                'attribute'=>'woNo',
                'label'=>'Nomor WO',
                'value'=>'wo.no'
            ],*/
            'no',
            [
                'attribute'=>'asal_greige',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesMaklon*/
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
            [
                'attribute'=>'vendorName',
                'label'=>'Vendor',
                'value'=>'vendor.name'
            ],
            //'note:ntext',
            /*[
                'attribute'=>'status',
                'value'=>function($data){
                    ///* @var $data KartuProsesMaklon
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => KartuProsesMaklon::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],*/
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
            /*[
                'label'=>'Panjang',
                'value'=>function($data){
                    ///* @var $data KartuProsesMaklon
                    $totalPanjang = 0;
                    foreach ($data->kartuProsesMaklonItems as $kartuProsesMaklonItem) {
                        $stockGreige = $kartuProsesMaklonItem->stock->toArray();
                        $totalPanjang += $stockGreige['panjang_m'];
                    }
                    return $totalPanjang;
                },
                'format'=>'decimal',
                'pageSummary' => true,
                'hAlign' => 'right'
            ],*/
            //'created_at:datetime',
            //'created_by',
            //'updated_at:datetime',
            //'updated_by',

            [
                'class' => 'kartik\grid\ActionColumn',
                'template'=>'{view} {inspect}',
                'buttons'=>[
                    'view'=>function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', ['/trn-kartu-proses-maklon/view', 'id'=>$model->id], ['title'=>'Detail Kartu Proses', 'target'=>'_blank']);
                    },
                    /*'kembalikan'=>function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, [
                            'title'=>'Kembalikan Kartu Proses',
                            'data' => [
                                'confirm' => 'Are you sure you want to kembalikan this item?',
                                'method' => 'post',
                            ],
                        ]);
                    },*/
                    'inspect'=>function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>', ['create-maklon', 'processId'=>$model->id], ['title'=>'Buat Inspecting']);
                    },
                ]
            ],
        ],
    ]); ?>


</div>
