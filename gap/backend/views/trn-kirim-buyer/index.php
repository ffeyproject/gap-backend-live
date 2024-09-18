<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnKirimBuyer;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKirimBuyerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pengiriman Ke Buyer';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kirim-buyer-index">
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
        'toolbar'=>false,
        //'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template' => '{view}'],

            //'id',
            //'sc_id',
            //'sc_greige_id',
            //'mo_id',
            //'wo_id',
            [
                'label' => 'No. SC',
                'attribute'=>'scNo',
                'value'=>'sc.no'
            ],
            [
                'label' => 'No. MO',
                'attribute'=>'moNo',
                'value'=>'mo.no'
            ],
            [
                'label' => 'No. WO',
                'attribute'=>'woNo',
                'value'=>'wo.no'
            ],
            [
                'label' => 'Buyer',
                'attribute'=>'custName',
                'value'=>'sc.cust.name'
            ],
            //'date',
            [
                'attribute' => 'dateRange',
                'label' => 'Tanggal WO',
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
            'nama_kain_alias',
            //'note:ntext',
            //'status',
            [
                'attribute' => 'status',
                'value' => function($data){
                    /* @var $data TrnKirimBuyer*/
                    return TrnKirimBuyer::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnKirimBuyer::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            [
                'label' => 'Qty',
                'value' => function($data){
                    /* @var $data TrnKirimBuyer*/
                    return $data->getTrnKirimBuyerItems()->sum('qty');
                },
                'format' => 'decimal'
            ],
            //'unit',
            [
                'attribute' => 'unit',
                'value' => function($data){
                    /* @var $data TrnKirimBuyer*/
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
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            'penerima',
        ],
    ]); ?>


</div>
