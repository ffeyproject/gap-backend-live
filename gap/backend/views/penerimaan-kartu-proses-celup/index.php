<?php
use common\models\ar\TrnKartuProsesCelup;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesCelupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Penerimaan Kartu Proses Celup';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kartu-proses-dyeing-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before'=>Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
                ['class'=>'btn-group', 'role'=>'group']
            ),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        //'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            //'id',
            //'order_pfp_id',
            [
                'attribute'=>'orderCelupNo',
                'label'=>'Nomor Order Celup',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesCelup*/
                    return Html::a($data->orderCelup->no, ['/trn-order-celup/view', 'id'=>$data->order_celup_id], ['title'=>'Lihat Order Celup', 'target'=>'blank']);
                },
                'format'=>'raw'
            ],
            'no_proses',
            'no',
            [
                'attribute'=>'asal_greige',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesCelup*/
                    return TrnStockGreige::asalGreigeOptions()[$data->asal_greige];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::asalGreigeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            'dikerjakan_oleh',
            'lusi',
            'pakan',
            //'note:ntext',
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
            //'created_at:datetime',
            //'created_by',
            //'updated_at:datetime',
            //'updated_by',
        ],
    ]); ?>


</div>
