<?php

use backend\components\PenerimaanGudangJadiHelper;
use backend\models\search\PenerimaanGabunganSearch;
use common\models\ar\InspectingItem;
use common\models\ar\InspectingMklBj;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnScGreige;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel PenerimaanGabunganSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $gradeTotals array */
/* @var $rowTotals array */
/* @var $inspectingTotals array */
/* @var $isPartialMap array */
/* @var $receiptDates array */
/* @var $hasValidDateRange bool */
/* @var $summaryByUnit array */

$this->title = 'Riwayat Penerimaan Gabungan (Packing & Packing MKL BJ)';
$this->params['breadcrumbs'][] = ['label' => 'Penerimaan', 'url' => '#'];
$this->params['breadcrumbs'][] = 'Riwayat Penerimaan Gabungan';

PenerimaanGudangJadiHelper::registerResponsiveGridAssets($this);

$gradeTotals = isset($gradeTotals) ? $gradeTotals : [];
$rowTotals = isset($rowTotals) ? $rowTotals : [];
$inspectingTotals = isset($inspectingTotals) ? $inspectingTotals : [];
$isPartialMap = isset($isPartialMap) ? $isPartialMap : [];
$receiptDates = isset($receiptDates) ? $receiptDates : [];
$hasValidDateRange = isset($hasValidDateRange) ? $hasValidDateRange : false;
$summaryByUnit = isset($summaryByUnit) ? $summaryByUnit : [];
$formatter = Yii::$app->formatter;

$processOptions = TrnScGreige::processOptions();
$mklJenisOptions = InspectingMklBj::jenisOptions();
$unitOptions = MstGreigeGroup::unitOptions();

$getGradeTotal = function ($source, $id, $grade) use ($gradeTotals) {
    $key = $source . '_' . (int)$id;
    return isset($gradeTotals[$key][$grade]) ? $gradeTotals[$key][$grade] : 0;
};

$pageSize = $dataProvider->pagination->getPageSize();
$perPageOptions = [10 => '10 / hal', 25 => '25 / hal', 50 => '50 / hal', 100 => '100 / hal', 200 => '200 / hal'];
$perPageLinks = [];
foreach ($perPageOptions as $size => $label) {
    $params = Yii::$app->request->queryParams;
    $params[0] = 'riwayat-gabungan';
    $params['per-page'] = $size;
    unset($params['page']);
    $perPageLinks[] = Html::a($label, $params, [
        'class' => 'btn btn-sm ' . ($pageSize == $size ? 'btn-primary' : 'btn-default'),
    ]);
}
?>
<div class="riwayat-gabungan-index gj-responsive-grid">
    <?php if ($hasValidDateRange): ?>
        <?php
        $summaryGrades = [
            InspectingItem::GRADE_A => 'Total Grade A',
            InspectingItem::GRADE_A_PLUS => 'Total Grade A+',
            InspectingItem::GRADE_A_ASTERISK => 'Total Grade A*',
            InspectingItem::GRADE_B => 'Total Grade B',
            InspectingItem::GRADE_C => 'Total Grade C',
            InspectingItem::GRADE_PK => 'Total Piece Kecil',
            InspectingItem::GRADE_SAMPLE => 'Total Contoh',
        ];
        ?>
        <?php
        $periodeParts = [];
        if (!empty($searchModel->tglKirimRange)) {
            $periodeParts[] = 'Tgl Kirim: ' . $searchModel->tglKirimRange;
        }
        if (!empty($searchModel->deliveredAtRange)) {
            $periodeParts[] = 'Tgl Penerimaan: ' . $searchModel->deliveredAtRange;
        }
        ?>
        <?= PenerimaanGudangJadiHelper::renderSummaryBox([
            'title' => 'Total Hasil Pencarian Gabungan',
            'periodeParts' => $periodeParts,
            'totalCount' => $dataProvider->getTotalCount(),
            'summaryGrades' => $summaryGrades,
            'summaryByUnit' => $summaryByUnit,
            'formatter' => $formatter,
        ]) ?>
    <?php endif; ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive' => true,
        'responsiveWrap' => false,
        'resizableColumns' => false,
        'hover' => true,
        'striped' => true,
        'condensed' => true,
        'containerOptions' => ['class' => 'gj-grid-scroll'],
        'tableOptions' => ['class' => 'gj-grid-table'],
        'emptyText' => $hasValidDateRange
            ? 'Data tidak ditemukan.'
            : '<div class="alert alert-info" style="margin: 10px 0;"><i class="fa fa-info-circle"></i> Silakan pilih rentang <strong>Tanggal Kirim</strong> atau <strong>Tanggal Penerimaan</strong> terlebih dahulu (maksimal 1 bulan 1 minggu / 38 hari dari tanggal pertama yang dipilih) untuk menampilkan data gabungan Packing &amp; Packing MKL BJ.</div>',
        'panel' => [
            'type' => 'default',
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['riwayat-gabungan'], ['class' => 'btn btn-default'])
                . ' <div class="btn-group gj-per-page-group" style="margin-left: 8px;" role="group">' . implode('', $perPageLinks) . '</div>',
        ],
        'toolbar' => [
            '{export}',
            '{toggleData}',
        ],
        'showPageSummary' => true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        $route = $model['source'] === PenerimaanGabunganSearch::SOURCE_PACKING
                            ? ['/gudang-jadi/view', 'id' => $model['id']]
                            : ['/penerimaan-inspecting-mkl-bj/view', 'id' => $model['id']];
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $route, [
                            'title' => 'Lihat Detail',
                            'target' => '_blank',
                            'data-pjax' => '0',
                        ]);
                    },
                ],
            ],
            [
                'attribute' => 'source',
                'label' => 'Sumber',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model['source'] === PenerimaanGabunganSearch::SOURCE_PACKING) {
                        return Html::tag('span', 'Packing', ['class' => 'label label-primary']);
                    }
                    return Html::tag('span', 'Packing MKL BJ', ['class' => 'label label-success']);
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => PenerimaanGabunganSearch::sourceOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => ['allowClear' => true],
                ],
                'hAlign' => 'center',
            ],
            [
                'attribute' => 'id',
                'label' => 'ID',
            ],
            [
                'attribute' => 'jenisFilter',
                'label' => 'Jenis / Proses',
                'value' => function ($model) use ($processOptions, $mklJenisOptions) {
                    $code = (int)$model['jenis_code'];
                    if ($model['source'] === PenerimaanGabunganSearch::SOURCE_PACKING) {
                        return isset($processOptions[$code]) ? $processOptions[$code] : '-';
                    }
                    return isset($mklJenisOptions[$code]) ? $mklJenisOptions[$code] : '-';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => PenerimaanGabunganSearch::jenisFilterOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            [
                'attribute' => 'tglKirimRange',
                'label' => 'Tanggal Kirim',
                'value' => 'tgl_kirim',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'Y-m-d',
                            'separator' => ' to ',
                        ],
                        'maxSpan' => [
                            'days' => 38,
                        ],
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Pilih tanggal (maks. 1 bln 1 mgg)...',
                    ],
                ],
            ],
            [
                'attribute' => 'tglInspeksiRange',
                'label' => 'Tanggal Inspeksi',
                'value' => 'tgl_inspeksi',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'Y-m-d',
                            'separator' => ' to ',
                        ],
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Pilih tanggal...',
                    ],
                ],
            ],
            [
                'attribute' => 'deliveredAtRange',
                'label' => 'Tanggal Penerimaan',
                'value' => function ($model) use ($receiptDates) {
                    $key = $model['source'] . '_' . (int)$model['id'];
                    return isset($receiptDates[$key]) ? $receiptDates[$key] : '-';
                },
                'format' => 'raw',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'Y-m-d',
                            'separator' => ' to ',
                        ],
                        'maxSpan' => [
                            'days' => 38,
                        ],
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Pilih tanggal (maks. 1 bln 1 mgg)...',
                    ],
                ],
            ],
            [
                'attribute' => 'no',
                'label' => 'No. Inspecting',
                'value' => function ($model) {
                    return !empty($model['no']) ? $model['no'] : '-';
                },
            ],
            [
                'attribute' => 'wo_no',
                'label' => 'No. WO',
                'value' => function ($model) {
                    return !empty($model['wo_no']) ? $model['wo_no'] : '-';
                },
            ],
            [
                'attribute' => 'kombinasi',
                'label' => 'Warna / Kombinasi',
                'value' => function ($model) {
                    return !empty($model['kombinasi']) ? $model['kombinasi'] : '-';
                },
            ],
            [
                'attribute' => 'no_lot',
                'label' => 'No. Lot',
                'value' => function ($model) {
                    return !empty($model['no_lot']) ? $model['no_lot'] : '-';
                },
            ],
            [
                'attribute' => 'kartu_proses_no',
                'label' => 'No. Kartu Proses',
                'value' => function ($model) {
                    return !empty($model['kartu_proses_no']) ? $model['kartu_proses_no'] : '-';
                },
            ],
            [
                'attribute' => 'grade_a',
                'filter' => false,
                'label' => 'Total Grade A',
                'value' => function ($model) use ($getGradeTotal) {
                    return $getGradeTotal($model['source'], $model['id'], InspectingItem::GRADE_A);
                },
                'format' => 'decimal',
                'hAlign' => 'right',
            ],
            [
                'attribute' => 'grade_a_plus',
                'filter' => false,
                'label' => 'Total Grade A+',
                'value' => function ($model) use ($getGradeTotal) {
                    return $getGradeTotal($model['source'], $model['id'], InspectingItem::GRADE_A_PLUS);
                },
                'format' => 'decimal',
                'hAlign' => 'right',
            ],
            [
                'attribute' => 'grade_a_asterisk',
                'filter' => false,
                'label' => 'Total Grade A*',
                'value' => function ($model) use ($getGradeTotal) {
                    return $getGradeTotal($model['source'], $model['id'], InspectingItem::GRADE_A_ASTERISK);
                },
                'format' => 'decimal',
                'hAlign' => 'right',
            ],
            [
                'attribute' => 'grade_b',
                'filter' => false,
                'label' => 'Total Grade B',
                'value' => function ($model) use ($getGradeTotal) {
                    return $getGradeTotal($model['source'], $model['id'], InspectingItem::GRADE_B);
                },
                'format' => 'decimal',
                'hAlign' => 'right',
            ],
            [
                'attribute' => 'grade_c',
                'filter' => false,
                'label' => 'Total Grade C',
                'value' => function ($model) use ($getGradeTotal) {
                    return $getGradeTotal($model['source'], $model['id'], InspectingItem::GRADE_C);
                },
                'format' => 'decimal',
                'hAlign' => 'right',
            ],
            [
                'attribute' => 'grade_pk',
                'filter' => false,
                'label' => 'Total Piece Kecil',
                'value' => function ($model) use ($getGradeTotal) {
                    return $getGradeTotal($model['source'], $model['id'], InspectingItem::GRADE_PK);
                },
                'format' => 'decimal',
                'hAlign' => 'right',
            ],
            [
                'attribute' => 'grade_sample',
                'filter' => false,
                'label' => 'Total Contoh',
                'value' => function ($model) use ($getGradeTotal) {
                    return $getGradeTotal($model['source'], $model['id'], InspectingItem::GRADE_SAMPLE);
                },
                'format' => 'decimal',
                'hAlign' => 'right',
            ],
            [
                'attribute' => 'total_diterima',
                'filter' => false,
                'label' => 'Total Diterima',
                'value' => function ($model) use ($rowTotals) {
                    $key = $model['source'] . '_' . (int)$model['id'];
                    return isset($rowTotals[$key]) ? $rowTotals[$key] : 0;
                },
                'format' => 'decimal',
                'hAlign' => 'right',
            ],
            [
                'attribute' => 'total_inspecting',
                'filter' => false,
                'label' => 'Total Inspecting',
                'value' => function ($model) use ($inspectingTotals) {
                    $key = $model['source'] . '_' . (int)$model['id'];
                    return isset($inspectingTotals[$key]) ? $inspectingTotals[$key] : 0;
                },
                'format' => 'decimal',
                'hAlign' => 'right',
            ],
            [
                'label' => 'Status Penerimaan',
                'format' => 'raw',
                'hAlign' => 'center',
                'value' => function ($model) use ($isPartialMap) {
                    $key = $model['source'] . '_' . (int)$model['id'];
                    if (!empty($isPartialMap[$key])) {
                        return Html::tag('span', 'Diterima Partial', ['class' => 'label label-warning']);
                    }
                    return Html::tag('span', 'Diterima Penuh', ['class' => 'label label-success']);
                },
            ],
            [
                'attribute' => 'unit',
                'label' => 'Satuan',
                'value' => function ($model) use ($unitOptions) {
                    $u = (int)$model['unit'];
                    return isset($unitOptions[$u]) ? $unitOptions[$u] : '-';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $unitOptions,
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
        ],
    ]); ?>
</div>

