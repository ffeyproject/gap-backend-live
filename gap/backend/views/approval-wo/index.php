<?php

use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnWo;
use common\models\ar\TrnWoSearch;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel TrnWoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Persetujuan Working Order';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-wo-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'resizableColumns' => false,
        'toolbar' => [
            [
                'content'=>
                    Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], [
                        'class' => 'btn btn-default',
                        'title' => 'Refresh data'
                    ])
            ],
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template' => '{view}'],

            [
                'label' => 'Process',
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
                ]
            ],
            [
                'attribute' => 'jenis_order',
                'value' => function($data){
                    /* @var $data TrnWo*/
                    return TrnSc::jenisOrderOptions()[$data->jenis_order];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnSc::jenisOrderOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'attribute' => 'id',
                'format' => 'html',
                'value' => function($data){
                    /* @var $data TrnWo*/
                    return Html::a($data->id, ['view', 'id'=>$data->id], ['title'=>'Lihat detail']);
                },
                'hAlign' => 'center'
            ],
            'no_urut',
            'no',
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
            'marketingName',
            'creatorName',
            'created_at:datetime',
            //'date',
            //'papper_tube',
            //'plastic_size',
            //'shipping_mark:ntext',
            //'note:ntext',
            //'note_two:ntext',
            //'mengetahui_id',
            //'posted',
            //'apv_by_mengetahui',
            //'apv_mengetahui_time:datetime',
            //'apv_note_mengetahui:ntext',
            //'apv_by_marketing',
            //'apv_note_marketing:ntext',
            //'apv_marketing_time:datetime',
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