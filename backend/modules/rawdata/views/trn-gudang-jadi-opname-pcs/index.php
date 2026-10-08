<?php

use common\models\ar\TrnGudangJadiOpnamePcs;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnGudangJadiOpnamePcsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Raw Data Gudang Jadi Stok Opname';
$this->params['breadcrumbs'][] = $this->title;

$gradeLabels = [
    1 => 'Grade A',
    2 => 'Grade B',
    3 => 'Grade C',
    4 => 'Grade D',
    5 => 'Grade E',
    6 => 'Grade F',
    7 => 'Grade G',
];
?>
<div class="trn-gudang-jadi-opname-pcs-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'GdJadiOpnamePcsGrid',
        'resizableColumns' => false,
        'responsiveWrap' => false,
        'toolbar' => [
            '{toggleData}',
            '{export}'
        ],
        'panel' => [
            'type' => 'default',
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh', ['index'], ['class' => 'btn btn-default']) . ' ' .
                        Html::a('<i class="glyphicon glyphicon-plus"></i> Tambah Baru', ['create'], ['class' => 'btn btn-success']),
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
            ],
            [
                'attribute' => 'id',
                'label' => 'ID',
                'headerOptions' => ['style' => 'width:70px;'],
            ],
            [
                'attribute' => 'id_trn_gudang_jadi',
                'label' => 'ID Gudang Jadi',
                'headerOptions' => ['style' => 'width:110px;'],
            ],
            [
                'attribute' => 'opname_code',
                'label' => 'Kode Opname',
            ],
            [
                'attribute' => 'qr_code',
                'label' => 'QR Code',
            ],
            [
                'attribute' => 'qr_code_desc',
                'label' => 'Deskripsi QR / Motif',
            ],
            [
                'attribute' => 'woNo',
                'label' => 'Nomor WO',
                'value' => function($model) {
                    return ($model->gudangJadi && $model->gudangJadi->wo) ? $model->gudangJadi->wo->no : null;
                }
            ],
            [
                'attribute' => 'customerName',
                'label' => 'Buyer',
                'value' => function($model) {
                    return ($model->gudangJadi && $model->gudangJadi->wo && $model->gudangJadi->wo->mo && $model->gudangJadi->wo->mo->scGreige && $model->gudangJadi->wo->mo->scGreige->sc && $model->gudangJadi->wo->mo->scGreige->sc->cust) ? $model->gudangJadi->wo->mo->scGreige->sc->cust->name : null;
                }
            ],
            [
                'attribute' => 'qty',
                'format' => ['decimal', 2],
                'hAlign' => 'right',
            ],
            [
                'attribute' => 'unit',
                'value' => function($model) {
                    return $model->getUnitName();
                },
                'headerOptions' => ['style' => 'width:80px;'],
            ],
            [
                'attribute' => 'grade',
                'value' => function($model) use ($gradeLabels) {
                    return isset($gradeLabels[$model->grade]) ? $gradeLabels[$model->grade] : $model->grade;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $gradeLabels,
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ],
                'headerOptions' => ['style' => 'width:110px;'],
            ],
            [
                'attribute' => 'locs_code',
                'label' => 'Lokasi / Rak',
            ],
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return $model->getStatusName();
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnGudangJadiOpnamePcs::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ],
                'headerOptions' => ['style' => 'width:110px;'],
            ],
            [
                'attribute' => 'join_piece',
            ],
            [
                'attribute' => 'remark',
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
        ],
    ]); ?>

</div>
