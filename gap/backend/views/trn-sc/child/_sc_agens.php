<?php

use common\models\ar\TrnSc;
use common\models\ar\TrnScAgen;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrnSc */

$dataProvider = new ActiveDataProvider([
    'query' => $model->getTrnScAgens(),
    'pagination' => false,
    'sort' => false
]);
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'ScAgenItems',
    'pjax' => true,
    'responsiveWrap' => false,
    'resizableColumns' => false,
    'toolbar' => [
        [
            'content'=>
                $model->status == $model::STATUS_DRAFT ? Html::a('<i class="glyphicon glyphicon-plus"></i>', ['/trn-sc-agen/create', 'scId' => $model->id], [
                    'class' => 'btn btn-xs btn-success',
                    'title' => 'Add Agen',
                    'data-toggle'=>"modal",
                    'data-target'=>"#trnScModal",
                    'data-title' => 'Add Agen'
                ]) : ''
        ]
    ],
    'panel' => [
        'heading' => '<strong>Agen</strong>',
        'type' => GridView::TYPE_DEFAULT,
        //'before' => false,
        'after' => false,
        'footer' => false
    ],
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],

        'date:date',
        'nama_agen',
        'attention',
        'no_urut',
        'no',

        [
            'class' => 'kartik\grid\ActionColumn',
            'controller' => 'trn-sc-agen',
            'template' => '{update} {delete} {print-loa}',
            'buttons' => [
                'update' => function($url, $model, $key){
                    /* @var $model TrnScAgen*/
                    $sc = $model->sc;
                    if($sc->status == $sc::STATUS_DRAFT){
                        return Html::a('<i class="glyphicon glyphicon-pencil"></i>', $url, [
                            'class' => 'btn btn-xs btn-warning',
                            'title' => 'Update',
                            'data-toggle'=>"modal",
                            'data-target'=>"#trnScModal",
                            'data-title' => 'Ubah Agen: '.$model->nama_agen
                        ]);
                    }
                    return '';
                },
                'delete' => function($url, $model, $key){
                    /* @var $model TrnScAgen*/
                    $sc = $model->sc;
                    if($sc->status == $sc::STATUS_DRAFT){
                        return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, [
                            'class' => 'btn btn-xs btn-danger',
                            'title' => 'Delete Agen: '.$model->nama_agen,
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this agen?',
                                'method' => 'post',
                            ],
                        ]);
                    }
                    return '';
                },
                'print-loa' => function($url, $model, $key){
                    /* @var $model TrnScAgen*/
                    return Html::a('<i class="glyphicon glyphicon-print"></i>', $url, [
                        'class' => 'btn btn-xs btn-default',
                        'title' => 'Print LOA',
                        'data-toggle'=>"modal",
                        'data-target'=>"#trnScModal",
                        'data-title' => 'Print LOA: '.$model->nama_agen
                    ]);
                },
            ]
        ],
    ],
]); ?>