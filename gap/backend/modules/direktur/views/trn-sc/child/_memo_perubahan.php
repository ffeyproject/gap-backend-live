<?php

use common\models\ar\TrnSc;
use common\models\ar\TrnScMemo;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrnSc */

$dataProvider = new ActiveDataProvider([
    'query' => $model->getTrnScMemos(),
    'pagination' => false,
    'sort' => false
]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'ScMemoItemsGrid',
    'pjax' => true,
    'responsiveWrap' => false,
    'resizableColumns' => false,
    'showPageSummary' => true,
    'toolbar' => [
        [
            'content'=>
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['trn-sc-memo/create', 'scId' => $model->id], [
                    'class' => 'btn btn-xs btn-success',
                    'title' => 'Add Memo Perubahan',
                    'data-toggle'=>"modal",
                    'data-target'=>"#trnScModal",
                    'data-title' => 'Add Memo Perubahan'
                ])
        ]
    ],
    'panel' => [
        'heading' => '<strong>Memo Perubahan</strong>',
        'type' => GridView::TYPE_DEFAULT,
        //'before' => false,
        'after' => false,
        'footer' => false
    ],
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],

        'memo:html',
        'created_at:datetime',

        [
            'class' => 'kartik\grid\ActionColumn',
            'controller' => 'trn-sc-memo',
            'template' => '{update} {delete} {display}',
            'buttons' => [
                'update' => function($url, $model, $key){
                    /* @var $model TrnScMemo*/
                    return Html::a('<i class="glyphicon glyphicon-pencil"></i>', $url, [
                        'class' => 'btn btn-xs btn-warning',
                        'title' => 'Update Memo Perubahan',
                        'data-toggle'=>"modal",
                        'data-target'=>"#trnScModal",
                        'data-title' => 'Ubah Memo Perubahan: '.$model->id
                    ]);
                },
                'delete' => function($url, $model, $key){
                    /* @var $model TrnScMemo*/
                    return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, [
                        'class' => 'btn btn-xs btn-danger',
                        'title' => 'Delete Memo Perubahan: '.$model->id,
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this memo perubahan?',
                            'method' => 'post',
                        ],
                    ]);
                },
                'display' => function($url, $model, $key){
                    /* @var $model TrnScMemo*/
                    return Html::a('<i class="glyphicon glyphicon-print"></i>', $url, [
                        'class' => 'btn btn-xs btn-default',
                        'title' => 'Print Memo Perubahan',
                        'data-toggle'=>"modal",
                        'data-target'=>"#trnScModal",
                        'data-title' => 'Print Memo Perubahan',
                    ]);
                },
            ]
        ],
    ],
]);