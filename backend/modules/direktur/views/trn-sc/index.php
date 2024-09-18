<?php

use common\models\ar\TrnSc;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnScSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sales Contract';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-sc-index">
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
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],
            'id',
            //'no_urut',
            'no',
            [
                'attribute' => 'dateRange',
                'label' => 'TANGGAL SC',
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
                'attribute' => 'tipe_kontrak',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnSc::tipeKontrakOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
                'value' => function($data){
                    /* @var $data TrnSc*/
                    return TrnSc::tipeKontrakOptions()[$data->tipe_kontrak];
                },
            ],
            [
                'attribute' => 'currency',
                'label' => 'Currency',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnSc::currencyOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
                'value' => function($data){
                    /* @var $data TrnSc*/
                    return TrnSc::currencyOptions()[$data->currency];
                },
            ],
            [
                'attribute' => 'customerName',
                'label' => 'Customer',
                'value' => 'customerName'
            ],
            [
                'attribute' => 'marketingName',
                'label' => 'Marketing',
                'value' => 'marketing.full_name'
            ],
            /*[
                'attribute' => 'creatorName',
                'label' => 'Created By',
                'value' => 'createdBy.full_name'
            ],*/
            //'jet_black:boolean',
            //'no_po',
            [
                'attribute' => 'jenis_order',
                'value' => function($data){
                    /* @var $data TrnSc*/
                    return TrnSc::jenisOrderOptions()[$data->jenis_order];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnSc::jenisOrderOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'label' => '____ Status ____',
                'attribute' => 'status',
                'value' => function($data){
                    /* @var $data TrnSc*/
                    return TrnSc::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnSc::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'label'=>'Qty Kontrak',
                'value' => function($data){
                    /* @var $data TrnSc*/
                    return $data->getQtyScGreige();
                },
                'format'=>'decimal'
            ],
            [
                'label'=>'Sisa By MO',
                'value' => function($data){
                    /* @var $data TrnSc*/
                    return $data->getQtyScGreige() - $data->getQtyMoColorNotBatal();
                },
                'format'=>'decimal'
            ],
            [
                'label'=>'Sisa By WO',
                'value' => function($data){
                    /* @var $data TrnSc*/
                    return $data->getQtyScGreige() - $data->getQtyWoColorNotBatal();
                },
                'format'=>'decimal'
            ]
        ],
    ]) ?>


</div>
