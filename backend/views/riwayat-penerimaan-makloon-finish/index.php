<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnTerimaMakloonFinish;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnTerimaMakloonFinishSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Riwayat Penerimaan Makloon Finish';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-terima-makloon-finish-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            //'before'=>'',
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        //'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            //'id',
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
                'label' => 'Qty',
                'value' => function($data){
                    /* @var $data TrnTerimaMakloonFinish*/
                    return $data->getTrnTerimaMakloonFinishItems()->sum('qty');
                },
                'format' => 'decimal'
            ],
            //'unit',
            [
                'attribute' => 'unit',
                'value' => function($data){
                    /* @var $data TrnTerimaMakloonFinish*/
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
