<?php
use common\models\ar\TrnMo;
use common\models\ar\TrnMoSearch;
use common\models\ar\TrnScGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel TrnMoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Approval Marketing Order';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-mo-index">
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

            'id',
            [
                'attribute' => 'process',
                'value' => function($data){
                    /* @var $data TrnMo*/
                    return TrnScGreige::processOptions()[$data->process];
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
                'attribute'=>'scGreigeNamaKain',
                'label'=>'Motif Greige',
                'value'=>'scGreige.greigeGroup.nama_kain',
            ],

            [
                'attribute' => 'dateRange',
                'label' => 'Tanggal MO',
                'value' => 'date',
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
        ],
    ]); ?>
</div>
