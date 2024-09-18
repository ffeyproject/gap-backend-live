<?php

use common\models\ar\TrnGreigeKeluar;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnGreigeKeluarSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Greige Keluar Makloon';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-greige-keluar-index">
    <!--<div class="box">
        <div class="box-body">
            <blockquote>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
                <footer>Someone famous in <cite title="Source Title">Source Title</cite></footer>
            </blockquote>
        </div>
    </div>-->

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before'=>Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']).
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success']),
                ['class'=>'btn-group', 'role'=>'group']
            ),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            'id',
            'no_urut',
            'no',
            [
                'attribute'=>'woNo',
                'label'=>'Nomor WO',
                'value'=>function($data){
                    return $data->wo_id !== null ? Html::a($data->wo->no , ['/trn-wo/view', 'id'=>$data->wo_id], ['title'=>'Lihat WO', 'target'=>'blank']) : null;
                },
                'format'=>'raw'
            ],
            [
                'attribute'=>'jenis',
                'value'=>function($data){
                    /* @var $data TrnGreigeKeluar*/
                    return $data::jenisOptions()[$data->jenis];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => [
                        TrnGreigeKeluar::JENIS_MAKLOON => TrnGreigeKeluar::jenisOptions()[TrnGreigeKeluar::JENIS_MAKLOON]
                    ],
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'attribute' => 'dateRange',
                'label' => 'TANGGAL',
                'value' => 'date',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        'locale'=>[
                            'format'=>'Y-m-d',
                            'separator'=>' to ',
                        ]
                    ]
                ],
            ],
            //'note:ntext',
            //'posted_at',
            //'approved_at',
            //'approved_by',
            'destinasi',
            [
                'attribute'=>'status',
                'value'=>function($data){
                    /* @var $data TrnGreigeKeluar*/
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnGreigeKeluar::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
        ],
    ]); ?>


</div>
