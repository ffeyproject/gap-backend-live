<?php

use backend\components\ajax_modal\AjaxModal;
use common\models\ar\TrnScGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnScGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sc Greiges';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-sc-greige-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'resizableColumns' => false,
        //'floatHeader' => true,
        //'floatHeaderOptions'=>['scrollingTop'=>'50', 'zIndex'=>800],
        //'toolbar' => false,
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'before'=>Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']).
                Html::a('<i class="glyphicon glyphicon-plus"></i>', '#', ['class' => 'btn btn-success']),
                ['class'=>'btn-group', 'role'=>'group']
            ),
        ],
        /*'toolbar' => [
            [
                'content'=>
                    Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], [
                        'class' => 'btn btn-default',
                        'title' => 'Refresh data'
                    ])
            ],
        ],*/
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        /* @var $model TrnScGreige */
                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', $url, [
                            'title' => 'View',
                            'data-toggle' => "modal",
                            'data-target' => "#trnScModal",
                            'data-title' => 'Detail Greige Group: ' . $model->greigeGroup->nama_kain
                        ]).' '. Html::a('<i class="glyphicon glyphicon-print"></i>', ['display-order-greige', 'id'=>$model->id], [
                            'title' => 'Print Order Greige',
                            'data-toggle'=>"modal",
                            'data-target'=>"#trnScModal",
                            'data-title' => 'Print Order Greige',
                        ]);
                    },
                ]
            ],

            'id',
            'sc_id',
            [
                'label'=>'Nomor SC',
                'attribute'=>'nomorSc',
                'value'=>'sc.no'
            ],
            [
                'label'=>'Greige Group',
                'attribute'=>'greigeGroupNamaKain',
                'value'=>'greigeGroup.nama_kain'
            ],
            [
                'attribute'=>'process',
                'value'=>function($data){
                    /* @var $data TrnScGreige*/
                    return $data::processOptions()[$data->process];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnScGreige::processOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            [
                'attribute'=>'lebar_kain',
                'value'=>function($data){
                    /* @var $data TrnScGreige*/
                    return $data::lebarKainOptions()[$data->lebar_kain];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnScGreige::lebarKainOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            'merek',
            [
                'attribute'=>'grade',
                'value'=>function($data){
                    /* @var $data TrnScGreige*/
                    return $data::gradeOptions()[$data->grade];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnScGreige::gradeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            'piece_length',
            'unit_price:decimal',
            [
                'attribute'=>'price_param',
                'value'=>function($data){
                    /* @var $data TrnScGreige*/
                    return $data::priceParamOptions()[$data->price_param];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnScGreige::priceParamOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            'qty:decimal',
            //'woven_selvedge:ntext',
            //'note:ntext',
            'closed:boolean',
            //'closing_note:ntext',
            'no_order_greige',
            //'no_urut_order_greige',
            //'order_greige_note:ntext',
            'order_grege_approved:boolean',
            'order_grege_approved_at:datetime',
            'order_grege_approved_by',
            'order_grege_approved_dir:boolean',
            'order_grege_approved_at_dir:datetime',
        ],
    ]); ?>


</div>
<?php
echo AjaxModal::widget([
    'id' => 'trnScModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);
