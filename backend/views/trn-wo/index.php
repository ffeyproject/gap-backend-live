<?php

use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnWo;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnWoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Work Order';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-wo-index">
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
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template' => '{view}'],

            'id',
            'no_urut',
            [
                'attribute' => 'no',
                'format' => 'html',
                'value' => function($data){
                    /* @var $data TrnWo*/
                    return Html::a($data->no, ['view', 'id'=>$data->id], ['title'=>'Lihat detail WO']);
                },
                'hAlign' => 'center'
            ],
            [
                'attribute' => 'dateRange',
                'label' => 'Tanggal WO',
                'value' => 'date',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        //'timePicker'=>true,
                        //'timePickerIncrement'=>5,
                        'locale'=>[
                            //'format'=>'Y-m-d H:i:s',
                            'format'=>'Y-m-d',
                            'separator'=>' to ',
                        ]
                    ]
                ]
            ],
            'mo_id',
            [
                'label' => 'No. MO',
                'attribute'=>'moNo',
                'format' => 'html',
                'value' => function($data){
                    /* @var $data TrnMo*/
                    $sc = $data->mo;
                    return Html::a($sc->no, ['/trn-mo/view', 'id'=>$data->mo_id], ['title'=>'Lihat detail MO']);
                },
            ],
            [
                'label' => 'No. SC',
                'attribute'=>'scNo',
                'format' => 'html',
                'value' => function($data){
                    /* @var $data TrnWo*/
                    $sc = $data->sc;
                    return Html::a($sc->no, ['/trn-sc/view', 'id'=>$data->sc_id], ['title'=>'Lihat detail SC']);
                },
            ],
            [
                'label' => 'Jenis Proses',
                'attribute' => 'proccess',
                'value' => function($data){
                    /* @var $data TrnWo*/
                    return TrnScGreige::processOptions()[$data->scGreige->process];
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
                'attribute' => 'jenis_order',
                'value' => function($data){
                    /* @var $data TrnWo*/
                    return $data->jenisOrderName;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnSc::jenisOrderOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            [
                'attribute'=>'scGreigeNamaKain',
                'label'=>'Motif Greige',
                'value'=>'mo.scGreige.greigeGroup.nama_kain',
            ],
            [
                'attribute' => 'status',
                'label' => '__Status__',
                'value' => function($data){
                    /* @var $data TrnWo*/
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnWo::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            //greige
            /*[
                'attribute' => 'id',
                'format' => 'html',
                'value' => function($data){
                    //@var $data TrnWo
                    return Html::a($data->id, ['view', 'id'=>$data->id], ['title'=>'Lihat detail WO']);
                },
                'hAlign' => 'center'
            ],
            [
                'attribute' => 'no_urut',
                'format' => 'html',
                'value' => function($data){
                    //@var $data TrnWo
                    return Html::a($data->no_urut, ['view', 'id'=>$data->id], ['title'=>'Lihat detail WO']);
                },
                'hAlign' => 'center'
            ],*/
            'marketingName',
            //'creatorName',
            /*[
                'attribute'=>'created_at',
                'format'=>['datetime', 'php:Y-m-d H:i:s'],
                'filter'=>false
            ],*/
            //'date',
            //'papper_tube',
            //'plastic_size',
            //'shipping_mark:ntext',
            //'note:ntext',
            //'note_two:ntext',
            //'mengetahui_id',
            //'posted',
            //'apv_by_mengetahui',
            'apv_mengetahui_at:datetime',
            //'apv_note_mengetahui:ntext',
            //'apv_by_marketing',
            //'apv_note_marketing:ntext',
            'apv_marketing_at:datetime',
            //'closed',
            //'closing_note:ntext',
            //'batal',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
        ],
    ]); ?>


</div>
