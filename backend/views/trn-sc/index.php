<?php

use common\models\ar\TrnSc;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnScSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sales Contract';
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-sc-index">
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
                Html::a('<i class="glyphicon glyphicon-plus"></i> Lokal', ['create-local'], [
                    'class' => 'btn btn-success',
                    'title' => 'Buat Kontrak Pemesanan Lokal',
                ]).
                Html::a('<i class="glyphicon glyphicon-plus"></i> Export', ['create-export'], [
                    'class' => 'btn btn-info',
                    'title' => 'Buat Kontrak Pemesanan Export',
                ]).
                Html::a('Close Manual', ['close-manual'], [
                    'class' => 'btn btn-danger',
                    'title' => 'Close Kontrak Pemesanan Yang Sudah Lebih Dari 6 Bulan',
                    'data' => [
                        'confirm' => 'Anda yakin akan melakukan proses close manual ini?',
                        'method' => 'post',
                    ],
                ]),
                ['class'=>'btn-group', 'role'=>'group']
            ),
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
