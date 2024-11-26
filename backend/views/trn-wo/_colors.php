<?php
use common\models\ar\TrnWo;
use common\models\ar\TrnWoColor;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrnWo */

$dataProvider = new ActiveDataProvider([
    'query' => $model->getTrnWoColors(),
    'pagination' => false,
    'sort' => false
]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'WoColorItemsGrid',
    'pjax' => true,
    'responsiveWrap' => false,
    'resizableColumns' => false,
    'showPageSummary' => true,
    'toolbar' => [
        [
            'content'=>
                $model->status == $model::STATUS_DRAFT ? Html::a('<i class="glyphicon glyphicon-plus"></i>', ['/trn-wo-color/create', 'woId' => $model->id], [
                    'class' => 'btn btn-xs btn-success',
                    'title' => 'Add Color',
                    'data-toggle'=>"modal",
                    'data-target'=>"#trnWoModal",
                    'data-title' => 'Add Color'
                ]) : ''
        ]
    ],
    'panel' => [
        'heading' => '<strong>Colors</strong>',
        'type' => GridView::TYPE_DEFAULT,
        //'before' => false,
        'after' => false,
        'footer' => false
    ],
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],

        'id',
        //'mo_color_id',
        //'color',
        [
            'label'=>'Color',
            'value'=>function($data){
                /* @var $data TrnWoColor*/
                return $data->moColor->color;
            }
        ],
        [
            'label'=>'Qty Batch',
            'attribute'=>'qty',
            'format' => 'decimal',
            'pageSummary' => true,
            'hAlign' => 'right'
        ],
        [
            'label'=>'Greige',
            'attribute' => 'qtyBatchToMeter',
            'format' => 'decimal',
            'pageSummary' => true,
            'hAlign' => 'right'
        ],
        [
            'label'=>'Finish',
            'attribute' => 'qtyFinish',
            'format' => 'decimal',
            'pageSummary' => true,
            'hAlign' => 'right'
        ],
        [
            'label'=>'Finish (Yard)',
            'attribute' => 'qtyFinishToYard',
            'format' => 'decimal',
            'pageSummary' => true,
            'hAlign' => 'right'
        ],
        [
            'label'=>'Ready Colour',
            'attribute' => 'ready_colour',
            'format' => 'boolean',
            'hAlign' => 'right'
        ],
        [
            'label'=>'Ready Colour Date',
            'attribute' => 'date_ready_colour',
            'format' => 'date',
            'hAlign' => 'right'
        ],
        [
            'class' => 'kartik\grid\ActionColumn',
            'controller' => 'trn-wo-color',
            'template' => '{delete} {update} {batal} {ready-colour}',
            'buttons' => [
                'delete' => function($url, $data, $key) use($model){
                    /* @var $data TrnWoColor*/
                    if($model->status == $model::STATUS_DRAFT){
                        return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, [
                            'class' => 'btn btn-xs btn-danger',
                            'title' => 'Delete Color: '.$data->moColor->color,
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this item?',
                                'method' => 'post',
                            ],
                        ]);
                    }
                    return '';
                },
                'update' => function($url, $data, $key) use($model){
                    /* @var $data TrnWoColor*/
                    if($model->status == $model::STATUS_DRAFT){
                        return Html::a('<i class="glyphicon glyphicon-pencil"></i>', $url, [
                            'class' => 'btn btn-xs btn-warning',
                            'title' => 'Update Color',
                            'data-toggle'=>"modal",
                            'data-target'=>"#trnWoModal",
                            'data-title' => 'Update Color'
                        ]);
                    }
                    return '';
                },
                // 'batal' => function($url, $data, $key)use($model){
                //     /* @var $data TrnWoColor*/
                //     if($model->status == $model::STATUS_APPROVED){
                //         return Html::a('<i class="glyphicon glyphicon-remove"></i>', $url, [
                //             'class' => 'btn btn-xs btn-danger',
                //             'title' => 'Batalkan WO Color: '.$data->moColor->color,
                //             'data' => [
                //                 'confirm' => 'Are you sure you want to batal this item?',
                //                 'method' => 'post',
                //             ],
                //         ]);
                //     }
                //     return '';
                // },
                'ready-colour' => function($url, $data, $key)use($model){
                    /* @var $data TrnWoColor*/
                    if($model->status == $model::STATUS_APPROVED){
                        if ($data->ready_colour){
                            return Html::a('<i class="glyphicon glyphicon-remove-sign"></i>', $url, [
                                'class' => 'btn btn-xs btn-warning',
                                'title' => 'Mark As Not Ready Colour: '.$data->moColor->color,
                                'data' => [
                                    'confirm' => 'Are you sure you want to mark this item?',
                                    'method' => 'post',
                                ],
                            ]);
                        }else{
                            return Html::a('<i class="glyphicon glyphicon-ok-sign"></i>', $url, [
                                'class' => 'btn btn-xs btn-warning',
                                'title' => 'Mark As Ready Colour: '.$data->moColor->color,
                                'data' => [
                                    'confirm' => 'Are you sure you want to mark this item?',
                                    'method' => 'post',
                                ],
                            ]);
                        }

                    }
                    return '';
                },
            ]
        ],
    ],
]);
