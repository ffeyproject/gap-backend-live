<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrnSc */

$dataProvider = new ActiveDataProvider([
    'query' => $model->getTrnScGreiges(),
    'pagination' => false,
    'sort' => false
]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'ScGreigeItems',
    'pjax' => true,
    'responsiveWrap' => false,
    'resizableColumns' => false,
    'toolbar' => [
        [
            'content' =>
                $model->status == $model::STATUS_DRAFT ? Html::a('<i class="glyphicon glyphicon-plus"></i>', ['/trn-sc-greige/create', 'scId' => $model->id], [
                    'class' => 'btn btn-xs btn-success',
                    'title' => 'Add Greige Group',
                    'data-toggle' => "modal",
                    'data-target' => "#trnScModal",
                    'data-title' => 'Add Greige Group'
                ]) : ''
        ]
    ],
    'panel' => [
        'heading' => '<strong>Greiges</strong>',
        'type' => GridView::TYPE_DEFAULT,
        //'before' => false,
        'after' => false,
        'footer' => false
    ],
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],

        //'greigeGroup.nama_kain',
        [
            'label' => 'Nama Kain',
            //'value' => 'greigeGroup.nama_kain',
            'value' => function($data){
                /* @var $data TrnScGreige*/
                $greigeGroup = $data->greigeGroup;
                return $greigeGroup->nama_kain.' ('.$greigeGroup::unitOptions()[$greigeGroup->unit].')';
            }
        ],
        [
            'label' => 'Artikel',
            'attribute' => 'artikel_sc',
            'value' => function($data){
                /* @var $data TrnScGreige*/
                return $data->artikel_sc;
            },
        ],
        /*[
            'label' => 'Unit',
            'value' => function($data){
                /* @var $data TrnScGreige
                return MstGreigeGroup::unitOptions()[$data->greigeGroup->unit];
            }
        ],*/
        //'proccess.name',
        [
            'label' => 'Prosess',
            'value' => function($data){
                /* @var $data TrnScGreige*/
                return TrnScGreige::processOptions()[$data->process];
            }
        ],
        //'lebarKainLebar',
        //'merek',
        //'grade',
        //'piece_length',
        [
            'attribute' => 'qty',
            'format' => 'decimal',
            'hAlign' => 'right',
        ],
        [
            'attribute' => 'qtyBatchToUnit',
            'format' => 'decimal',
            'hAlign' => 'right',
        ],
        [
            'attribute' => 'qtyBatchToMeter',
            'format' => 'decimal',
            'hAlign' => 'right',
        ],
        [
            'attribute' => 'qtyFinish',
            'format' => 'decimal',
            'hAlign' => 'right',
        ],
        [
            'attribute' => 'qtyFinishToYard',
            'format' => 'decimal',
            'hAlign' => 'right',
        ],
        [
            'attribute' => 'unit_price',
            'format' => 'decimal',
            'hAlign' => 'right',
        ],
        [
            'attribute' => 'price_param',
            'value' => function($data){
                /* @var $data TrnScGreige*/
                return TrnScGreige::priceParamOptions()[$data->price_param];
            },
            'hAlign' => 'center',
        ],
        [
            'attribute' => 'totalPrice',
            'format' => 'decimal',
            'hAlign' => 'right',
        ],
        [
            'attribute' => 'closed',
            'format' => 'boolean',
            'hAlign' => 'center',
        ],

        [
            'class' => 'kartik\grid\ActionColumn',
            'controller' => 'trn-sc-greige',
            'template' => '{view} {update} {delete} {create-mo} {close} {display-order-greige}',
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    /* @var $model TrnScGreige */
                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', $url, [
                        'class' => 'btn btn-xs btn-info',
                        'title' => 'View',
                        'data-toggle' => "modal",
                        'data-target' => "#trnScModal",
                        'data-title' => 'Detail Greige Group: ' . $model->greigeGroup->nama_kain
                    ]);
                },
                'update' => function ($url, $model, $key) {
                    /* @var $model TrnScGreige */
                    if ($model->sc->status == TrnSc::STATUS_DRAFT) {
                        return Html::a('<i class="glyphicon glyphicon-pencil"></i>', $url, [
                            'class' => 'btn btn-xs btn-warning',
                            'title' => 'Update',
                            'data-toggle' => "modal",
                            'data-target' => "#trnScModal",
                            'data-title' => 'Ubah Greige Group: ' . $model->id
                        ]);
                    }
                    return '';
                },
                'delete' => function ($url, $model, $key) {
                    /* @var $model TrnScGreige */
                    if ($model->sc->status == TrnSc::STATUS_DRAFT) {
                        return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, [
                            'class' => 'btn btn-xs btn-danger',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this greige group?',
                                'method' => 'post',
                            ],
                        ]);
                    }
                    return '';
                },
                'create-mo' => function ($url, $model, $key) {
                    /* @var $model TrnScGreige */
                    if ($model->sc->status == TrnSc::STATUS_APPROVED) {
                        return Html::a('+MO', ['/trn-mo/create', 'scGreigeId' => $model->id], [
                            'class' => 'btn btn-xs btn-success',
                            'title' => 'Buat MO'
                        ]);
                    }
                    return '';
                },
                'close' => function ($url, $model, $key) {
                    /* @var $model TrnScGreige */
                    if ($model->sc->status == TrnSc::STATUS_APPROVED && $model->closed === false) {
                        return Html::a('<i class="glyphicon glyphicon-off"></i>', $url, [
                            'class' => 'btn btn-xs btn-danger',
                            'onclick' => 'closeGreigeGroup(event);',
                            'title' => 'Close Greige Group: ' . $model->id
                        ]);
                    }
                    return '';
                },
                'display-order-greige' => function ($url, $model, $key) {
                    /* @var $model TrnScGreige */
                    return Html::a('<i class="glyphicon glyphicon-print"></i>', $url, [
                        'class' => 'btn btn-xs btn-default',
                        'title' => 'Print Order Greige',
                        'data-toggle'=>"modal",
                        'data-target'=>"#trnScModal",
                        'data-title' => 'Print Order Greige',
                    ]);
                },
            ]
        ],
    ],
]);