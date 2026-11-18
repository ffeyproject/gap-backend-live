<?php
use common\models\ar\TrnBuyPfp;
use common\models\ar\TrnStockGreige;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnBuyPfpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Input PFP';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-input-pfp-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before' => Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']) .
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success']),
                ['class' => 'btn-group', 'role' => 'group']
            ),
        ],
        'showPageSummary' => true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template' => '{view}'],

            [
                'attribute' => 'dateRange',
                'label' => 'TANGGAL',
                'value' => 'date',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'Y-m-d',
                            'separator' => ' to ',
                        ]
                    ]
                ],
            ],
            [
                'attribute' => 'jenis_printing',
                'label' => 'Jenis Printing',
                'value' => function ($data) {
                    /* @var $data TrnBuyPfp */
                    return $data->jenisPrintingName;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::jenisPrintingOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'label' => 'Greige Group',
                'attribute' => 'greigeGroupNamaKain',
                'value' => 'greigeGroupName'
            ],
            [
                'label' => 'Greige',
                'attribute' => 'greigeNamaKain',
                'value' => 'greigeName'
            ],
            [
                'attribute' => 'jenis',
                'value' => function ($data) {
                    /* @var $data TrnBuyPfp */
                    return $data::jenisOptions()[$data->jenis];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnBuyPfp::jenisOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            'no_referensi',
            'no_document',
            'vendor',
            'color',
            [
                'attribute' => 'status',
                'value' => function ($data) {
                    /* @var $data TrnBuyPfp */
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnBuyPfp::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
        ],
    ]); ?>

</div>
