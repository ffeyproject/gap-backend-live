<?php

use common\models\ar\TrnSc;
use common\models\ar\TrnScKomisi;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrnSc */

$dataProvider = new ActiveDataProvider([
    'query' => $model->getTrnScKomisis(),
    'pagination' => false,
    'sort' => false
]);
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'ScKomisitemsGrid',
    'pjax' => true,
    'responsiveWrap' => false,
    'resizableColumns' => false,
    'toolbar' => [
        [
            'content'=>
                $model->status == $model::STATUS_DRAFT ?
                    Html::a('<i class="glyphicon glyphicon-plus"></i>', ['/trn-sc-komisi/create', 'scId' => $model->id], [
                        'class' => 'btn btn-xs btn-success',
                        'title' => 'Add Loa',
                        'data-toggle'=>"modal",
                        'data-target'=>"#trnScModal",
                        'data-title' => 'Add Loa'
                    ]) : ''
        ]
    ],
    'panel' => [
        'heading' => '<strong>Komisi</strong>',
        'type' => GridView::TYPE_DEFAULT,
        //'before' => false,
        'after' => false,
        'footer' => false
    ],
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],

        //'id',
        'namaAgen',
        [
            'label' => 'Greige',
            'value' => function($data){
                /* @var $data TrnScKomisi*/
                $scGreige = $data->scGreige;
                return $scGreige->greigeGroup->nama_kain.' ( '.$scGreige::processOptions()[$scGreige->process].')';
            }
        ],
        'komisi_amount:decimal',
        [
            'attribute' => 'tipe_komisi',
            'value' => function($data){
                /* @var $data TrnScKomisi*/
                return $data::tipeKomisiOptions()[$data->tipe_komisi];
            }
        ],
        'komisiTotal:decimal',

        [
            'class' => 'kartik\grid\ActionColumn',
            'controller' => 'trn-sc-komisi',
            'template' => '{update} {delete}',
            'buttons' => [
                'update' => function($url, $model, $key){
                    /* @var $model TrnScKomisi*/
                    $sc = $model->sc;
                    if($sc->status == $sc::STATUS_DRAFT){
                        return Html::a('<i class="glyphicon glyphicon-pencil"></i>', $url, [
                            'class' => 'btn btn-xs btn-warning',
                            'title' => 'Update',
                            'data-toggle'=>"modal",
                            'data-target'=>"#trnScModal",
                            'data-title' => 'Ubah Loa: '.$model->id
                        ]);
                    }
                    return '';
                },
                'delete' => function($url, $model, $key){
                    /* @var $model TrnScKomisi*/
                    $sc = $model->sc;
                    if($sc->status == $sc::STATUS_DRAFT){
                        return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, [
                            'class' => 'btn btn-xs btn-danger',
                            'title' => 'Delete Loa: '.$model->id,
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this komisi?',
                                'method' => 'post',
                            ],
                        ]);
                    }
                    return '';
                }
            ]
        ],
    ],
]); ?>
