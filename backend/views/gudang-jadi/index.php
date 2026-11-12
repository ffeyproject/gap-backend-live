<?php
use backend\components\PenerimaanGudangJadiHelper;
use common\models\ar\TrnInspecting;
use common\models\ar\TrnInspectingSearch;
use common\models\ar\TrnScGreige;
use common\models\ar\InspectingItem;
use common\models\ar\MstGreigeGroup;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel TrnInspectingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $gradeTotals array */
/* @var $rowTotals array */
/* @var $inspectingTotals array */
/* @var $isPartialMap array */
/* @var $receiptDates array */
/* @var $hasValidDateRange bool */
/* @var $summaryByUnit array */

$this->title = 'Riwayat Penerimaan Packing';
$this->params['breadcrumbs'][] = $this->title;
PenerimaanGudangJadiHelper::registerResponsiveGridAssets($this);
$gradeTotals = isset($gradeTotals) ? $gradeTotals : [];
$rowTotals = isset($rowTotals) ? $rowTotals : [];
$inspectingTotals = isset($inspectingTotals) ? $inspectingTotals : [];
$isPartialMap = isset($isPartialMap) ? $isPartialMap : [];
$receiptDates = isset($receiptDates) ? $receiptDates : [];
$hasValidDateRange = isset($hasValidDateRange) ? $hasValidDateRange : false;
$summaryByUnit = isset($summaryByUnit) ? $summaryByUnit : [];
$formatter = Yii::$app->formatter;
$getGradeTotal = function ($inspectingId, $grade) use ($gradeTotals) {
    return isset($gradeTotals[$inspectingId][$grade]) ? $gradeTotals[$inspectingId][$grade] : 0;
};
$pageSize = $dataProvider->pagination->getPageSize();
$perPageOptions = [10 => '10 / hal', 25 => '25 / hal', 50 => '50 / hal', 100 => '100 / hal', 200 => '200 / hal'];
$perPageLinks = [];
foreach ($perPageOptions as $size => $label) {
    $params = Yii::$app->request->queryParams;
    $params[0] = 'index';
    $params['per-page'] = $size;
    unset($params['page']);
    $perPageLinks[] = Html::a($label, $params, [
        'class' => 'btn btn-sm ' . ($pageSize == $size ? 'btn-primary' : 'btn-default'),
    ]);
}
?>
<div class="inspecting-index gj-responsive-grid">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
        if (!empty($searchModel->dateRange)) {
            $periodeParts[] = 'Tgl Kirim: ' . $searchModel->dateRange;
        }
        if (!empty($searchModel->deliveredAtRange)) {
            $periodeParts[] = 'Tgl Penerimaan: ' . $searchModel->deliveredAtRange;
        }
        ?>
        <?= PenerimaanGudangJadiHelper::renderSummaryBox([
            'title' => 'Total Hasil Pencarian',
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
            : '<div class="alert alert-info" style="margin: 10px 0;"><i class="fa fa-info-circle"></i> Silakan pilih rentang <strong>Tanggal Kirim</strong> atau <strong>Tanggal Penerimaan</strong> terlebih dahulu (maksimal 1 bulan 1 minggu / 38 hari dari tanggal pertama yang dipilih) untuk menampilkan data.</div>',
        'panel' => [
            'type' => 'default',
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default'])
                . ' <div class="btn-group gj-per-page-group" style="margin-left: 8px;" role="group">' . implode('', $perPageLinks) . '</div>',
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar'=>false,
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            'id',
            [
                'attribute'=>'jenis_process',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    $scGreige = null;
                    if ($data->kartu_process_dyeing_id !== null && $data->kartuProcessDyeing) {
                        $scGreige = $data->kartuProcessDyeing->scGreige;
                    } elseif ($data->kartu_process_printing_id !== null && $data->kartuProcessPrinting) {
                        $scGreige = $data->kartuProcessPrinting->scGreige;
                    } elseif ($data->memo_repair_id !== null && $data->memoRepair) {
                        $scGreige = $data->memoRepair->scGreige;
                    }
                    if ($scGreige !== null) {
                        $options = $scGreige::processOptions();
                        return isset($options[$scGreige->process]) ? $options[$scGreige->process] : '-';
                    }
                    $options = TrnScGreige::processOptions();
                    return isset($options[$data->jenis_process]) ? $options[$data->jenis_process] : '-';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnScGreige::processOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            //'process_dyeing_id',
            //'process_printing_id',
            //'no_urut',
            [
                'attribute' => 'dateRange',
                'label' => 'Tanggal Kirim',
                'value' => 'date',
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
                'attribute' => 'deliveredAtRange',
                'label' => 'Tanggal Penerimaan',
                'value' => function ($data) use ($receiptDates) {
                    /* @var $data TrnInspecting */
                    return isset($receiptDates[$data->id]) ? $receiptDates[$data->id] : '-';
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
            'no',
            'woNo',
            'kombinasi',
            'no_lot',
            [
                'attribute'=>'kpdNo',
                'label'=>'No Kartu Proses Dyeing',
                'value'=>'kartuProcessDyeing.no'
            ],
            [
                'attribute'=>'kppNo',
                'label'=>'No Kartu Proses Printing',
                'value'=>'kartuProcessPrinting.no'
            ],
            [
                'attribute' => 'grade_a',
                'filter' => false,
                'label' => 'Total Grade A',
                'value' => function ($data) use ($getGradeTotal) {
                    /* @var $data TrnInspecting */
                    return $getGradeTotal($data->id, InspectingItem::GRADE_A);
                },
                'format' => 'decimal'
            ],
            [
                'attribute' => 'grade_a_plus',
                'filter' => false,
                'label' => 'Total Grade A+',
                'value' => function ($data) use ($getGradeTotal) {
                    /* @var $data TrnInspecting */
                    return $getGradeTotal($data->id, InspectingItem::GRADE_A_PLUS);
                },
                'format' => 'decimal'
            ],
            [
                'attribute' => 'grade_a_asterisk',
                'filter' => false,
                'label' => 'Total Grade A*',
                'value' => function ($data) use ($getGradeTotal) {
                    /* @var $data TrnInspecting */
                    return $getGradeTotal($data->id, InspectingItem::GRADE_A_ASTERISK);
                },
                'format' => 'decimal'
            ],
            [
                'attribute' => 'grade_b',
                'filter' => false,
                'label' => 'Total Grade B',
                'value' => function ($data) use ($getGradeTotal) {
                    /* @var $data TrnInspecting */
                    return $getGradeTotal($data->id, InspectingItem::GRADE_B);
                },
                'format' => 'decimal'
            ],
            [
                'attribute' => 'grade_c',
                'filter' => false,
                'label' => 'Total Grade C',
                'value' => function ($data) use ($getGradeTotal) {
                    /* @var $data TrnInspecting */
                    return $getGradeTotal($data->id, InspectingItem::GRADE_C);
                },
                'format' => 'decimal'
            ],
            [
                'attribute' => 'grade_pk',
                'filter' => false,
                'label' => 'Total Piece Kecil',
                'value' => function ($data) use ($getGradeTotal) {
                    /* @var $data TrnInspecting */
                    return $getGradeTotal($data->id, InspectingItem::GRADE_PK);
                },
                'format' => 'decimal'
            ],
            [
                'attribute' => 'grade_sample',
                'filter' => false,
                'label' => 'Total Contoh',
                'value' => function ($data) use ($getGradeTotal) {
                    /* @var $data TrnInspecting */
                    return $getGradeTotal($data->id, InspectingItem::GRADE_SAMPLE);
                },
                'format' => 'decimal'
            ],
            [
                'attribute' => 'total_diterima',
                'filter' => false,
                'label' => 'Total Diterima',
                'value' => function ($data) use ($rowTotals) {
                    /* @var $data TrnInspecting */
                    return isset($rowTotals[$data->id]) ? $rowTotals[$data->id] : 0;
                },
                'format' => 'decimal'
            ],
            [
                'attribute' => 'total_inspecting',
                'filter' => false,
                'label' => 'Total Inspecting',
                'value' => function ($data) use ($inspectingTotals) {
                    /* @var $data TrnInspecting */
                    return isset($inspectingTotals[$data->id]) ? $inspectingTotals[$data->id] : 0;
                },
                'format' => 'decimal'
            ],
            [
                'label' => 'Status Penerimaan',
                'format' => 'raw',
                'hAlign' => 'center',
                'value' => function ($data) use ($isPartialMap) {
                    /* @var $data TrnInspecting */
                    if (!empty($isPartialMap[$data->id])) {
                        return Html::tag('span', 'Diterima Partial', ['class' => 'label label-warning']);
                    }
                    return Html::tag('span', 'Diterima Penuh', ['class' => 'label label-success']);
                },
            ],
            [
                'attribute'=>'unit',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                   return \common\models\ar\MstGreigeGroup::unitOptions()[$data->unit];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' =>  \common\models\ar\MstGreigeGroup::unitOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            //'date',
            //'tanggal_inspeksi',
            //'no_lot',
            //'kombinasi',
            //'note:ntext',
            /*[
                'attribute'=>'status',
                'value'=>function($data){
                    ///* @var $data Inspecting
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => Inspecting::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],*/
            //'grade_a_pcs',
            //'grade_b_pcs',
            //'grade_c_pcs',
            //'piece_kecil_pcs',
            //'sample_pcs',
            //'grade_a_roll',
            //'grade_b_roll',
            //'grade_c_roll',
            //'piece_kecil_roll',
            //'sample_roll',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'approved_at',
            //'approved_by',

            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],
        ],
    ]); ?>


</div>
