<?php
use common\models\ar\TrnMo;
use common\models\ar\TrnMoColor;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrnMo */

$dataProvider = new ActiveDataProvider([
    'query' => $model->getTrnMoColors(),
    'pagination' => false,
    'sort' => false
]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'TrnMoColorItems',
    'pjax' => true,
    'responsiveWrap' => false,
    'resizableColumns' => false,
    'showPageSummary' => true,
    'toolbar' => [
        [
            'content'=>
                $model->status == $model::STATUS_DRAFT ? Html::a('<i class="glyphicon glyphicon-plus"></i>', ['/trn-mo-color/create', 'moId' => $model->id], [
                    'class' => 'btn btn-xs btn-success',
                    'title' => 'Add Color',
                    'data-toggle'=>"modal",
                    'data-target'=>"#trnMoModal",
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
        [
            'attribute'=>'color',
            'hAlign' => 'center'
        ],
        [
            'attribute'=>'qty',
            'format' => 'decimal',
            'pageSummary' => true,
            'hAlign' => 'right'
        ],

        [
            'class' => 'kartik\grid\ActionColumn',
            'controller' => 'trn-mo-color',
            'template' => '{update} {delete}',
            'buttons' => [
                'update' => function($url, $data, $key) use($model){
                    /* @var $data TrnMoColor*/
                    if($model->status == $model::STATUS_DRAFT){
                        return Html::a('<i class="glyphicon glyphicon-pencil"></i>', $url, [
                            'class' => 'btn btn-xs btn-warning',
                            'title' => 'Update Color',
                            'data-toggle'=>"modal",
                            'data-target'=>"#trnMoModal",
                            'data-title' => 'Ubah Color: '.$data->color
                        ]);
                    }
                    return '';
                },
                'delete' => function($url, $data, $key) use($model){
                    /* @var $data TrnMoColor*/
                    if($model->status == $model::STATUS_DRAFT){
                        return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, [
                            'class' => 'btn btn-xs btn-danger',
                            'title' => 'Delete Color: '.$data->color,
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this item?',
                                'method' => 'post',
                            ],
                        ]);
                    }
                    return '';
                },
            ]
        ],
    ],
]);
?>