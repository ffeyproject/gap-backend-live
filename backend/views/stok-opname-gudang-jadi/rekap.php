<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadiOpnamePcs;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\StokOpnameGudangJadiRekapSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $totalPcsAll int */
/* @var $totalQtyAll float */
/* @var $totalVerified int */
/* @var $totalDraft int */

$this->title = 'Rekap Stok Opname Gudang Jadi';
$this->params['breadcrumbs'][] = ['label' => 'Gudang Jadi', 'url' => ['/trn-gudang-jadi/index']];
$this->params['breadcrumbs'][] = ['label' => 'Data Stok Opname (Pcs)', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stok-opname-gudang-jadi-rekap">

    <!-- Summary Info Box Cards -->
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-cubes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Roll / Pcs</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asInteger($totalPcsAll) ?> Roll</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-green">
                <span class="info-box-icon"><i class="fa fa-dashboard"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Kuantitas</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asDecimal($totalQtyAll) ?></span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-teal">
                <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pcs Terverifikasi</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asInteger($totalVerified) ?> Roll</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-yellow">
                <span class="info-box-icon"><i class="fa fa-pencil"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pcs Draft</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asInteger($totalDraft) ?> Roll</span>
                </div>
            </div>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'RekapStokOpnameGrid',
        'resizableColumns' => false,
        'responsiveWrap' => false,
        'pjax' => true,
        'showPageSummary' => true,
        'toolbar' => [
            '{toggleData}',
            '{export}'
        ],
        'panel' => [
            'type' => 'success',
            'heading' => '<h3 class="panel-title"><i class="fa fa-pie-chart"></i> Ringkasan Rekap Stok Opname per Motif & Warna</h3>',
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh', ['rekap'], ['class' => 'btn btn-default']) . ' ' .
                        Html::a('<i class="fa fa-map-marker"></i> Cari Berdasarkan Lokasi', '#', [
                            'class' => 'btn btn-warning',
                            'id' => 'btn-open-search-lokasi',
                            'title' => 'Cari dan tampilkan list stok pcs berdasarkan lokasi'
                        ]) . ' ' .
                        Html::a('<i class="fa fa-list"></i> Lihat Detail Pcs', ['index'], ['class' => 'btn btn-info']),
            'after' => false,
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            [
                'attribute' => 'opname_code',
                'label' => 'Kode Opname',
                'format' => 'raw',
                'value' => function($data) {
                    return Html::encode($data['opname_code']);
                },
                'pageSummary' => 'TOTAL Halaman Ini',
            ],
            [
                'attribute' => 'dateRange',
                'label' => 'Rentang Tgl. Opname',
                'value' => function($data) {
                    return '-';
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
                'attribute' => 'locs_code',
                'label' => 'Kode Lokasi',
                'value' => function($data) {
                    return !empty($data['locs_code']) ? $data['locs_code'] : 'TRANSIT';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \yii\helpers\ArrayHelper::map(
                        (new \yii\db\Query())
                            ->select(['locs_code' => "COALESCE(locs_code, '')"])
                            ->from('trn_gudang_jadi_opname_pcs')
                            ->distinct()
                            ->orderBy(['locs_code' => SORT_ASC])
                            ->all(),
                        'locs_code',
                        function($element) {
                            return !empty($element['locs_code']) ? $element['locs_code'] : 'TRANSIT';
                        }
                    ),
                    'options' => ['placeholder' => 'Cari Lokasi...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'attribute' => 'motif',
                'label' => 'Motif / Nama Kain',
                'format' => 'raw',
                'value' => function($data) {
                    return '<strong>' . Html::encode($data['motif']) . '</strong>';
                }
            ],
            [
                'attribute' => 'color',
                'label' => 'Color / Warna',
                'value' => function($data) {
                    return Html::encode($data['color']);
                }
            ],
            [
                'attribute' => 'grade',
                'label' => 'Grade',
                'value' => function($data) {
                    $gradeVal = (int)$data['grade'];
                    return isset(TrnStockGreige::gradeOptions()[$gradeVal]) ? TrnStockGreige::gradeOptions()[$gradeVal] : 'Grade ' . $gradeVal;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::gradeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'attribute' => 'status',
                'label' => 'Status Opname',
                'format' => 'raw',
                'value' => function($data) {
                    $statusVal = (int)$data['status'];
                    $statusName = isset(TrnGudangJadiOpnamePcs::statusOptions()[$statusVal]) ? TrnGudangJadiOpnamePcs::statusOptions()[$statusVal] : '-';
                    if ($statusVal === TrnGudangJadiOpnamePcs::STATUS_VERIFIED) {
                        return '<span class="label label-success">' . Html::encode($statusName) . '</span>';
                    }
                    return '<span class="label label-warning">' . Html::encode($statusName) . '</span>';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnGudangJadiOpnamePcs::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'attribute' => 'unit',
                'label' => 'Satuan',
                'value' => function($data) {
                    $u = $data['unit'];
                    if (is_numeric($u) && isset(MstGreigeGroup::unitOptions()[(int)$u])) {
                        return MstGreigeGroup::unitOptions()[(int)$u];
                    }
                    $uUpper = strtoupper(trim((string)$u));
                    if (in_array($uUpper, ['1', 'YARD', 'YARDS', 'YD'])) return 'Yard';
                    if (in_array($uUpper, ['2', 'METER', 'METERS', 'MTR', 'M'])) return 'Meter';
                    if (in_array($uUpper, ['3', 'PCS', 'PIECE', 'PIECES'])) return 'Pcs';
                    if (in_array($uUpper, ['4', 'KILOGRAM', 'KG', 'KILOGRAMS'])) return 'Kilogram';
                    return !empty($u) ? $u : '-';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => MstGreigeGroup::unitOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'attribute' => 'total_pcs',
                'label' => 'Total Roll / Pcs',
                'format' => ['decimal', 0],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM,
            ],
            [
                'attribute' => 'total_qty',
                'label' => 'Total Kuantitas',
                'format' => ['decimal', 2],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM,
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'header' => 'Aksi',
                'template' => '{view-pcs} {print-lokasi} {view-index}',
                'buttons' => [
                    'view-pcs' => function ($url, $data, $key) {
                        return Html::a('<i class="fa fa-eye"></i> List Pcs', '#', [
                            'class' => 'btn btn-xs btn-primary btn-view-pcs-list',
                            'title' => 'Lihat Rincian List Pcs Lokasi Ini',
                            'data-opname-code' => $data['opname_code'],
                            'data-locs-code' => $data['locs_code'],
                            'data-motif' => $data['motif'],
                            'data-color' => $data['color'],
                            'data-grade' => $data['grade'],
                            'data-status' => $data['status'],
                            'data-pjax' => '0',
                        ]);
                    },
                    'print-lokasi' => function ($url, $data, $key) {
                        return Html::a('<i class="fa fa-print"></i> Print Lokasi', [
                            'print-lokasi',
                            'locs_code' => $data['locs_code'],
                            'opname_code' => $data['opname_code'],
                        ], [
                            'class' => 'btn btn-xs btn-warning',
                            'title' => 'Cetak Lembar Palet per Lokasi ini',
                            'target' => '_blank',
                            'data-pjax' => '0',
                        ]);
                    },
                    'view-index' => function ($url, $data, $key) {
                        return Html::a('<i class="fa fa-list"></i> Data Filtered', [
                            'index',
                            'TrnGudangJadiOpnamePcsSearch[opname_code]' => $data['opname_code'],
                            'TrnGudangJadiOpnamePcsSearch[locs_code]' => $data['locs_code'],
                            'TrnGudangJadiOpnamePcsSearch[motif]' => $data['motif'] !== '-' ? $data['motif'] : '',
                            'TrnGudangJadiOpnamePcsSearch[color]' => $data['color'] !== '-' ? $data['color'] : '',
                            'TrnGudangJadiOpnamePcsSearch[grade]' => $data['grade'],
                            'TrnGudangJadiOpnamePcsSearch[status]' => $data['status'],
                        ], [
                            'class' => 'btn btn-xs btn-default',
                            'title' => 'Buka di Halaman Data Pcs dengan Filter Ini',
                            'target' => '_blank',
                            'data-pjax' => '0',
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>

<?php
$pcsListUrl = \yii\helpers\Url::to(['list-pcs-ajax']);
$js = <<<JS
$('.btn-view-pcs-list').on('click', function(e) {
    e.preventDefault();
    var btn = $(this);
    var modal = $('#modalPcsList');
    var modalBody = modal.find('.modal-body');
    
    modalBody.html('<div class="text-center" style="padding: 30px;"><i class="fa fa-spinner fa-spin fa-2x"></i><p style="margin-top:10px;">Memuat list pcs lokasi...</p></div>');
    modal.modal('show');
    
    $.ajax({
        url: '$pcsListUrl',
        type: 'GET',
        data: {
            opname_code: btn.data('opname-code'),
            locs_code: btn.data('locs-code'),
            motif: btn.data('motif'),
            color: btn.data('color'),
            grade: btn.data('grade'),
            status: btn.data('status')
        },
        success: function(res) {
            modalBody.html(res);
        },
        error: function() {
            modalBody.html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Gagal mengambil data list pcs.</div>');
        }
    });
});
JS;

$jsSearchLokasi = <<<JS
$('#btn-open-search-lokasi').on('click', function(e) {
    e.preventDefault();
    $('#modalSearchLokasi').modal('show');
});

$('#form-search-lokasi').on('submit', function(e) {
    e.preventDefault();
    var locCode = $('#input-search-loc-code').val();
    if (!locCode) {
        alert('Silakan pilih atau ketik kode lokasi terlebih dahulu!');
        return;
    }
    
    var modalSearch = $('#modalSearchLokasi');
    modalSearch.modal('hide');
    
    var modalPcs = $('#modalPcsList');
    var modalBody = modalPcs.find('.modal-body');
    modalBody.html('<div class="text-center" style="padding: 30px;"><i class="fa fa-spinner fa-spin fa-2x"></i><p style="margin-top:10px;">Memuat list pcs lokasi: ' + locCode + '...</p></div>');
    modalPcs.modal('show');
    
    $.ajax({
        url: '$pcsListUrl',
        type: 'GET',
        data: {
            locs_code: locCode
        },
        success: function(res) {
            modalBody.html(res);
        },
        error: function() {
            modalBody.html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Gagal mengambil data list pcs lokasi ' + locCode + '.</div>');
        }
    });
});
JS;
$this->registerJs($js);
$this->registerJs($jsSearchLokasi);

\yii\bootstrap\Modal::begin([
    'id' => 'modalPcsList',
    'header' => '<h4 class="modal-title"><i class="fa fa-cubes"></i> List Pcs Stok Opname per Lokasi</h4>',
    'size' => \yii\bootstrap\Modal::SIZE_LARGE,
]);
echo '<div class="modal-body"></div>';
\yii\bootstrap\Modal::end();

// Modal Pencarian Lokasi
\yii\bootstrap\Modal::begin([
    'id' => 'modalSearchLokasi',
    'header' => '<h4 class="modal-title"><i class="fa fa-map-marker"></i> Cari Stok Pcs Berdasarkan Lokasi</h4>',
    'size' => \yii\bootstrap\Modal::SIZE_DEFAULT,
]);
?>
<form id="form-search-lokasi">
    <div class="modal-body">
        <div class="form-group">
            <label for="input-search-loc-code">Pilih / Ketik Kode Lokasi:</label>
            <?= \kartik\widgets\Select2::widget([
                'name' => 'search_loc_code',
                'id' => 'input-search-loc-code',
                'data' => \yii\helpers\ArrayHelper::map(
                    (new \yii\db\Query())
                        ->select(['locs_code' => "COALESCE(locs_code, '')"])
                        ->from('trn_gudang_jadi_opname_pcs')
                        ->distinct()
                        ->orderBy(['locs_code' => SORT_ASC])
                        ->all(),
                    'locs_code',
                    function($element) {
                        return !empty($element['locs_code']) ? $element['locs_code'] : 'TRANSIT';
                    }
                ),
                'options' => ['placeholder' => 'Ketik atau pilih kode lokasi...'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]) ?>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-warning"><i class="fa fa-search"></i> Tampilkan List Pcs</button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Batal</button>
    </div>
</form>
<?php \yii\bootstrap\Modal::end(); ?>

</div>

