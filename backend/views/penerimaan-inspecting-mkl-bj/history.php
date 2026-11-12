<?php

use backend\components\PenerimaanGudangJadiHelper;
use common\models\ar\InspectingMklBj;
use common\models\ar\InspectingMklBjSearch;
use common\models\ar\InspectingMklBjItems;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnWo;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel InspectingMklBjSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $gradeTotals array */
/* @var $rowTotals array */
/* @var $inspectingTotals array */
/* @var $isPartialMap array */
/* @var $receiptDates array */
/* @var $hasValidDateRange bool */
/* @var $summaryByUnit array */

$this->title = 'Riwayat Penerimaan Packing Makloon Dan Barang Jadi';
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
    $params[0] = 'history';
    $params['per-page'] = $size;
    unset($params['page']);
    $perPageLinks[] = Html::a($label, $params, [
        'class' => 'btn btn-sm ' . ($pageSize == $size ? 'btn-primary' : 'btn-default'),
    ]);
}

$woNoFilter = '';
if (!empty($searchModel->wo_id)) {
    $woModel = TrnWo::findOne($searchModel->wo_id);
    $woNoFilter = $woModel ? $woModel->no : '';
}
?>
<div class="inspecting-index gj-responsive-grid">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if ($hasValidDateRange): ?>
        <?php
        $summaryGrades = [
            InspectingMklBjItems::GRADE_A => 'Total Grade A',
            InspectingMklBjItems::GRADE_A_PLUS => 'Total Grade A+',
            InspectingMklBjItems::GRADE_A_ASTERISK => 'Total Grade A*',
            InspectingMklBjItems::GRADE_B => 'Total Grade B',
            InspectingMklBjItems::GRADE_C => 'Total Grade C',
            InspectingMklBjItems::GRADE_PK => 'Total Piece Kecil',
            InspectingMklBjItems::GRADE_SAMPLE => 'Total Contoh',
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
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['history'], ['class' => 'btn btn-default'])
                . ' <div class="btn-group gj-per-page-group" style="margin-left: 8px;" role="group">' . implode('', $perPageLinks) . '</div>',
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar' => false,
        'showPageSummary' => true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            //['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            'id',
            [
                'attribute' => 'wo_id',
                'label' => 'WO No.',
                'value' => 'woNo',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'initValueText' => $woNoFilter, // set the initial display text
                    'options' => ['placeholder' => 'Cari ...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['/ajax/lookup-wo-all']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(member) { return member.text; }'),
                        'templateSelection' => new JsExpression('function (member) { return member.text; }'),
                    ],
                ],
            ],
            'colorName',
            'tgl_inspeksi:date',
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
                'attribute' => 'deliveredAtRange',
                'label' => 'Tanggal Penerimaan',
                'value' => function ($data) use ($receiptDates) {
                    /* @var $data InspectingMklBj */
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
            'no_lot',
            //'jenis',
            [
                'attribute' => 'jenis',
                'value' => 'jenisName',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => InspectingMklBj::jenisOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'attribute' => 'satuan',
                'value' => 'satuanName',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => MstGreigeGroup::unitOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            /*[
                'attribute'=>'status',
                'value'=>'statusName',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => InspectingMklBj::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],*/
            [
                'attribute' => 'grade_a',
                'filter' => false,
                'label' => 'Total Grade A',
                'value' => function ($data) use ($getGradeTotal) {
                    /* @var $data InspectingMklBj */
                    return $getGradeTotal($data->id, InspectingMklBjItems::GRADE_A);
                },
                'format' => 'decimal'
            ],
            [
                'attribute' => 'grade_a_plus',
                'filter' => false,
                'label' => 'Total Grade A+',
                'value' => function ($data) use ($getGradeTotal) {
                    /* @var $data InspectingMklBj */
                    return $getGradeTotal($data->id, InspectingMklBjItems::GRADE_A_PLUS);
                },
                'format' => 'decimal'
            ],
            [
                'attribute' => 'grade_a_asterisk',
                'filter' => false,
                'label' => 'Total Grade A*',
                'value' => function ($data) use ($getGradeTotal) {
                    /* @var $data InspectingMklBj */
                    return $getGradeTotal($data->id, InspectingMklBjItems::GRADE_A_ASTERISK);
                },
                'format' => 'decimal'
            ],
            [
                'attribute' => 'grade_b',
                'filter' => false,
                'label' => 'Total Grade B',
                'value' => function ($data) use ($getGradeTotal) {
                    /* @var $data InspectingMklBj */
                    return $getGradeTotal($data->id, InspectingMklBjItems::GRADE_B);
                },
                'format' => 'decimal'
            ],
            [
                'attribute' => 'grade_c',
                'filter' => false,
                'label' => 'Total Grade C',
                'value' => function ($data) use ($getGradeTotal) {
                    /* @var $data InspectingMklBj */
                    return $getGradeTotal($data->id, InspectingMklBjItems::GRADE_C);
                },
                'format' => 'decimal'
            ],
            [
                'attribute' => 'grade_pk',
                'filter' => false,
                'label' => 'Total Piece Kecil',
                'value' => function ($data) use ($getGradeTotal) {
                    /* @var $data InspectingMklBj */
                    return $getGradeTotal($data->id, InspectingMklBjItems::GRADE_PK);
                },
                'format' => 'decimal'
            ],
            [
                'attribute' => 'grade_sample',
                'filter' => false,
                'label' => 'Total Contoh',
                'value' => function ($data) use ($getGradeTotal) {
                    /* @var $data InspectingMklBj */
                    return $getGradeTotal($data->id, InspectingMklBjItems::GRADE_SAMPLE);
                },
                'format' => 'decimal'
            ],
            [
                'attribute' => 'total_diterima',
                'filter' => false,
                'label' => 'Total Diterima',
                'value' => function ($data) use ($rowTotals) {
                    /* @var $data InspectingMklBj */
                    return isset($rowTotals[$data->id]) ? $rowTotals[$data->id] : 0;
                },
                'format' => 'decimal'
            ],
            [
                'attribute' => 'total_inspecting',
                'filter' => false,
                'label' => 'Total Inspecting',
                'value' => function ($data) use ($inspectingTotals) {
                    /* @var $data InspectingMklBj */
                    return isset($inspectingTotals[$data->id]) ? $inspectingTotals[$data->id] : 0;
                },
                'format' => 'decimal'
            ],
            [
                'label' => 'Status Penerimaan',
                'format' => 'raw',
                'hAlign' => 'center',
                'value' => function ($data) use ($isPartialMap) {
                    /* @var $data InspectingMklBj */
                    if (!empty($isPartialMap[$data->id])) {
                        return Html::tag('span', 'Diterima Partial', ['class' => 'label label-warning']);
                    }
                    return Html::tag('span', 'Diterima Penuh', ['class' => 'label label-success']);
                },
            ],
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'status',
        ],
    ]); ?>

</div>
