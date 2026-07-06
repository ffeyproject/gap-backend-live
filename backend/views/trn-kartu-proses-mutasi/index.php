<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $searchDyeing common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $dataProviderDyeing yii\data\ActiveDataProvider */
/* @var $searchPrinting common\models\ar\TrnKartuProsesPrintingSearch */
/* @var $dataProviderPrinting yii\data\ActiveDataProvider */

$this->title = 'Pembuatan Kartu Stock Mutasi';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-mutasi-index">
    <p>
        <?= Html::a('Buat Kartu Dyeing', ['create-dyeing'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Buat Kartu Printing', ['create-printing'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php
    $dyeingGrid = GridView::widget([
        'dataProvider' => $dataProviderDyeing,
        'filterModel' => $searchDyeing,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'urlCreator' => function($action, $model, $key, $index) {
                    return \yii\helpers\Url::to(['/trn-kartu-proses-dyeing/view', 'id' => $model->id]);
                }
            ],
            'id',
            'nomor_kartu',
            [
                'attribute' => 'woNo',
                'label' => 'No. WO',
                'value' => 'wo.no',
            ],
            [
                'label' => 'Greige / Motif',
                'attribute' => 'greigeNamaKain',
                'value' => 'greige.nama_kain',
            ],
            'date:date',
            [
                'attribute' => 'status',
                'value' => function($data){
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\TrnKartuProsesDyeing::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            'dikerjakan_oleh',
        ],
    ]);

    $printingGrid = GridView::widget([
        'dataProvider' => $dataProviderPrinting,
        'filterModel' => $searchPrinting,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'urlCreator' => function($action, $model, $key, $index) {
                    return \yii\helpers\Url::to(['/trn-kartu-proses-printing/view', 'id' => $model->id]);
                }
            ],
            'id',
            'nomor_kartu',
            [
                'attribute' => 'woNo',
                'label' => 'No. WO',
                'value' => 'wo.no',
            ],
            [
                'label' => 'Greige / Motif',
                'attribute' => 'greigeNamaKain',
                'value' => 'greige.nama_kain',
            ],
            'date:date',
            [
                'attribute' => 'status',
                'value' => function($data){
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\TrnKartuProsesPrinting::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            'dikerjakan_oleh',
        ],
    ]);

    echo Tabs::widget([
        'items' => [
            [
                'label' => 'Kartu Proses Dyeing (Mutasi)',
                'content' => $dyeingGrid,
                'active' => true
            ],
            [
                'label' => 'Kartu Proses Printing (Mutasi)',
                'content' => $printingGrid,
            ],
        ],
    ]);
    ?>
</div>
