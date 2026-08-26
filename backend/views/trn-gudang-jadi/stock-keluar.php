<?php

use common\models\ar\MstGreige;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnGudangJadiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Stock Gudang Jadi Keluar';
$this->params['breadcrumbs'][] = ['label' => 'Gudang Jadi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-gudang-jadi-stock-keluar">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'GdJadiKeluarGrid',
        'resizableColumns' => false,
        'responsiveWrap' => false,
        'pjax' => true,
        'panel' => [
            'type' => 'danger',
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-export"></i> ' . Html::encode($this->title) . '</h3>',
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh Data', ['stock-keluar'], ['class' => 'btn btn-default']),
            'after' => false,
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width: 70px; text-align: center;'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'bg_type',
                'label' => 'Jenis Gudang',
                'value' => function($model) {
                    return $model::jenisGudangOptions()[$model->jenis_gudang] ?? '-';
                },
                'filter' => TrnGudangJadi::jenisGudangOptions(),
            ],
            [
                'attribute' => 'source',
                'value' => function($model) {
                    return $model::sourceOptions()[$model->source] ?? '-';
                },
                'filter' => TrnGudangJadi::sourceOptions(),
            ],
            [
                'attribute' => 'source_ref',
                'label' => 'Ref Source',
            ],
            [
                'attribute' => 'woNo',
                'label' => 'No WO / Motif',
                'value' => function($model) {
                    return $model->wo ? $model->wo->no . ' / ' . $model->wo->greigeNamaKain : '-';
                },
            ],
            [
                'attribute' => 'color',
                'label' => 'Color',
            ],
            [
                'attribute' => 'grade',
                'label' => 'Grade',
                'value' => function($model) {
                    return TrnStockGreige::gradeOptions()[$model->grade] ?? '-';
                },
                'filter' => TrnStockGreige::gradeOptions(),
            ],
            [
                'attribute' => 'qty',
                'format' => ['decimal', 2],
                'contentOptions' => ['class' => 'text-right'],
            ],
            [
                'attribute' => 'unit',
                'value' => function($model) {
                    return MstGreigeGroup::unitOptions()[$model->unit] ?? '-';
                },
                'filter' => MstGreigeGroup::unitOptions(),
            ],
            [
                'attribute' => 'locs_code',
                'label' => 'Lokasi Asal',
            ],
            [
                'attribute' => 'status',
                'label' => 'Status Keluar',
                'value' => function($model) {
                    return $model::statusOptions()[$model->status] ?? '-';
                },
                'filter' => $searchModel::statusOptions(),
            ],
            [
                'attribute' => 'note',
                'label' => 'Keterangan Mutasi / Keluar',
                'format' => 'ntext',
            ],
            [
                'attribute' => 'updated_at',
                'label' => 'Tgl Keluar',
                'value' => function($model) {
                    return $model->updated_at ? date('Y-m-d H:i', $model->updated_at) : '-';
                },
                'filter' => false,
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['view', 'id' => $model->id], [
                            'title' => 'Detail Stok',
                            'class' => 'btn btn-xs btn-default',
                        ]);
                    }
                ]
            ],
        ],
    ]); ?>
</div>
