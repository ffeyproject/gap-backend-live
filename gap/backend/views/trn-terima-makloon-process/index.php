<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnTerimaMakloonProcess;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnTerimaMakloonProcessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Penerimaan Makloon Proses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-terima-makloon-process-index">
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

            'id',
            //'jenis_gudang',
            //'sc_id',
            /*[
                'attribute'=>'scNo',
                'label'=>'Nomor sc',
                'value'=>'sc.no'
            ],*/
            //'sc_greige_id',
            //'mo_id',
            /*[
                'attribute'=>'moNo',
                'label'=>'Nomor MO',
                'value'=>'mo.no'
            ],*/
            //'wo_id',
            [
                'attribute'=>'woNo',
                'label'=>'Nomor WO',
                'value'=>'wo.no'
            ],
            [
                'label'=>'Color',
                'value'=>'woColor.moColor.color'
            ],
            //'vendor_id',
            [
                'label' => 'Vendor',
                'attribute'=>'vendorName',
                'value'=>'vendor.name'
            ],
            //'date',
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
            //'no_urut',
            'no',
            [
                'attribute' => 'status',
                'value' => function($data){
                    /* @var $data TrnTerimaMakloonProcess*/
                    return TrnTerimaMakloonProcess::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnTerimaMakloonProcess::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            [
                'label' => 'Qty',
                'value' => function($data){
                    /* @var $data TrnTerimaMakloonProcess*/
                    return $data->getTrnTerimaMakloonProcessItems()->sum('qty');
                },
                'format' => 'decimal',
                'pageSummary' => true,
            ],
            //'unit',
            [
                'attribute' => 'unit',
                'value' => function($data){
                    /* @var $data TrnTerimaMakloonProcess*/
                    return MstGreigeGroup::unitOptions()[$data->unit];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => MstGreigeGroup::unitOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            //'note:ntext',
            'created_at:datetime',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'pengirim',
        ],
    ]); ?>


</div>
