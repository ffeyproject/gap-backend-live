<?php

use common\models\ar\TrnStockGreige;
use common\models\ar\TrnStockPfpPindahPrintingLog;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnStockPfpPindahPrintingLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Riwayat Perpindahan Jenis Printing (Gudang 2)';
$this->params['breadcrumbs'][] = ['label' => 'Processing Pfp', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Stock Pfp', 'url' => ['stock-pfp']];
$this->params['breadcrumbs'][] = 'Riwayat Perpindahan';
?>
<div class="trn-stock-pfp-pindah-printing-log-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before' => Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-arrow-left"></i> Kembali ke Stock PFP', ['stock-pfp'], ['class' => 'btn btn-default']) .
                Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh', ['riwayat-pindah-printing'], ['class' => 'btn btn-default']),
                ['class' => 'btn-group', 'role' => 'group']
            ),
        ],
        'showPageSummary' => true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'label' => 'ID Log',
                'width' => '70px',
            ],
            [
                'attribute' => 'dateRange',
                'label' => 'WAKTU PINDAH',
                'value' => 'created_at',
                'format' => 'datetime',
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
                'attribute' => 'stock_id',
                'label' => 'Stock ID',
                'format' => 'raw',
                'value' => function ($data) {
                    /* @var $data TrnStockPfpPindahPrintingLog */
                    return Html::a($data->stock_id, ['/trn-stock-pfp/view', 'id' => $data->stock_id], ['target' => '_blank', 'data-pjax' => '0']);
                },
            ],
            [
                'label' => 'Greige',
                'attribute' => 'greigeNamaKain',
                'value' => 'greige.nama_kain',
            ],
            [
                'attribute' => 'panjang_m',
                'label' => 'Panjang (M)',
                'format' => 'decimal',
                'pageSummary' => true,
            ],
            [
                'attribute' => 'jenis_printing_asal',
                'label' => 'Dari Jenis Printing',
                'value' => function ($data) {
                    /* @var $data TrnStockPfpPindahPrintingLog */
                    if ($data->jenis_printing_asal == TrnStockGreige::JENIS_PRINTING_DIGITAL) {
                        return '<span class="label label-primary">Digital Printing</span>';
                    } elseif ($data->jenis_printing_asal == TrnStockGreige::JENIS_PRINTING_KONVENSIONAL) {
                        return '<span class="label label-success">Konvensional</span>';
                    }
                    return '<span class="label label-default">-</span>';
                },
                'format' => 'raw',
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
                'attribute' => 'jenis_printing_tujuan',
                'label' => 'Ke Jenis Printing',
                'value' => function ($data) {
                    /* @var $data TrnStockPfpPindahPrintingLog */
                    if ($data->jenis_printing_tujuan == TrnStockGreige::JENIS_PRINTING_DIGITAL) {
                        return '<span class="label label-primary">Digital Printing</span>';
                    } elseif ($data->jenis_printing_tujuan == TrnStockGreige::JENIS_PRINTING_KONVENSIONAL) {
                        return '<span class="label label-success">Konvensional</span>';
                    }
                    return '<span class="label label-default">-</span>';
                },
                'format' => 'raw',
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
                'attribute' => 'note',
                'label' => 'Catatan / Alasan',
                'value' => function ($data) {
                    /* @var $data TrnStockPfpPindahPrintingLog */
                    return $data->note ? $data->note : '-';
                },
            ],
            [
                'attribute' => 'createdByName',
                'label' => 'Dipindahkan Oleh',
                'value' => function ($data) {
                    /* @var $data TrnStockPfpPindahPrintingLog */
                    return $data->createdBy ? $data->createdBy->full_name : '-';
                },
            ],
        ],
    ]); ?>

</div>
