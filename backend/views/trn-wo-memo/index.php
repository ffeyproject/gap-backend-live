<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;


/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnWoMemoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Working Order Memo';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-wo-memo-index">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'resizableColumns' => false,

        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'before' => Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
                ['class' => 'btn-group', 'role' => 'group']
            ),
        ],

        
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'no',
            [
                'attribute' => 'wo_id',
                'value' => function ($model) {
                    return $model->wo ? Html::a($model->wo->no, ['/trn-wo/view', 'id' => $model->wo->id], ['title' => 'Lihat detail WO']) : null;
                },
                'label' => 'No WO',
                'format' => 'raw',
            ],
            [
                'attribute' => 'memo',
                'value' => function ($model) {
                    return strip_tags($model->memo);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'dateRange',
                'label' => 'Tanggal WO',
                'value' => function($model) {
                    return Yii::$app->formatter->asDate($model->created_at, 'php:d F Y');
                },
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'Y-m-d',
                            'separator' => ' to ',
                        ]
                    ]
                ]
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'header' => 'Aksi',
                'headerOptions' => ['style' => 'width:80px'],
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', $url, [
                            'title' => 'Lihat Detail',
                            'class' => 'btn btn-sm btn-primary',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>


</div>