<?php

use common\models\ar\TrnMo;
use common\models\ar\TrnMoMemo;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrnMo */

$dataProvider = new ActiveDataProvider([
    'query' => $model->getTrnMoMemos(),
    'pagination' => false,
    'sort' => false
]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'MoMemoItemsGrid',
    'pjax' => true,
    'responsiveWrap' => false,
    'resizableColumns' => false,
    'showPageSummary' => true,
    'toolbar' => [
        [
            'content'=>
                $model->status == $model::STATUS_APPROVED ? Html::a('<i class="glyphicon glyphicon-plus"></i>', ['/trn-mo-memo/create', 'moId' => $model->id], [
                    'class' => 'btn btn-xs btn-success',
                    'title' => 'Add Memo Perubahan',
                    'data-toggle'=>"modal",
                    'data-target'=>"#trnMoModal",
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

        'memo:html',
        'created_at:datetime',

        [
            'class' => 'kartik\grid\ActionColumn',
            'controller' => 'trn-mo-memo',
            'template' => '{delete} {display}',
            'buttons' => [
                'delete' => function($url, $data, $key) use($model){
                    /* @var $data TrnMoMemo*/
                    return $model->status != TrnMo::STATUS_APPROVED ? '' : Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, [
                        'class' => 'btn btn-xs btn-danger',
                        'title' => 'Delete Memo Perubahan: '.$data->id,
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]);
                },
                'display' => function($url, $model, $key){
                    return Html::a('<i class="glyphicon glyphicon-print"></i>', $url, [
                        'class' => 'btn btn-xs btn-default',
                        'title' => 'Print Memo Perubahan',
                        'data-toggle'=>"modal",
                        'data-target'=>"#trnMoModal",
                        'data-title' => 'Print Memo Perubahan',
                    ]);
                },
            ]
        ],
    ],
]);
?>