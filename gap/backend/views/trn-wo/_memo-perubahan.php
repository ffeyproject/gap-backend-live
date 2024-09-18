<?php

use common\models\ar\TrnWo;
use common\models\ar\TrnWoMemo;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrnWo */

$dataProvider = new ActiveDataProvider([
    'query' => $model->getTrnWoMemos(),
    'pagination' => false,
    'sort' => false
]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'WoMemoItemsGrid',
    'pjax' => true,
    'responsiveWrap' => false,
    'resizableColumns' => false,
    'showPageSummary' => true,
    'toolbar' => [
        [
            'content'=>
                $model->status == $model::STATUS_APPROVED ? Html::a('<i class="glyphicon glyphicon-plus"></i>', ['/trn-wo-memo/create', 'woId' => $model->id], [
                    'class' => 'btn btn-xs btn-success',
                    'title' => 'Add Memo Perubahan',
                    'data-toggle'=>"modal",
                    'data-target'=>"#trnWoModal",
                    'data-title' => 'Add Memo Perubahan'
                ]) : ''
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

        'no',
        'memo:html',
        'created_at:datetime',

        [
            'class' => 'kartik\grid\ActionColumn',
            'controller' => 'trn-wo-memo',
            'template' => '{delete} {display}',
            'buttons' => [
                'delete' => function($url, $model, $key){
                    /* @var $model TrnWoMemo*/
                    return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, [
                        'class' => 'btn btn-xs btn-danger',
                        'title' => 'Delete Memo Perubahan: '.$model->id,
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]);
                },
                'display' => function($url, $model, $key){
                    /* @var $model TrnWoMemo*/
                    return Html::a('<i class="glyphicon glyphicon-print"></i>', $url, [
                        'class' => 'btn btn-xs btn-default',
                        'title' => 'Print Memo Perubahan',
                        'data-toggle'=>"modal",
                        'data-target'=>"#trnWoModal",
                        'data-title' => 'Print Memo Perubahan',
                    ]);
                },
            ]
        ],
    ],
]);