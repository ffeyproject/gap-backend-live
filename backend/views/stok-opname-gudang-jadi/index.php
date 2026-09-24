<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\MstSubLocation;
use common\models\ar\TrnGudangJadiOpnamePcs;
use common\models\ar\TrnStockGreige;
use kartik\grid\GridView;
use kartik\select2\Select2;
use mdm\admin\components\Helper;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TrnGudangJadiOpnamePcsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $totalPcs int */
/* @var $totalQty float */
/* @var $totalVerified int */
/* @var $totalQtyVerified float */
/* @var $totalStock int */
/* @var $totalQtyStock float */
/* @var $totalOut int */
/* @var $totalQtyOut float */

$this->title = 'Stok Opname Gudang Jadi';
$this->params['breadcrumbs'][] = ['label' => 'Gudang Jadi', 'url' => ['/trn-gudang-jadi/index']];
$this->params['breadcrumbs'][] = $this->title;

$panelButtons = [];

if (Helper::checkRoute('index')) {
    $panelButtons[] = Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh', ['index'], ['class' => 'btn btn-default']);
}

if (Helper::checkRoute('move-location')) {
    $panelButtons[] = Html::button('<i class="fa fa-arrows"></i> Move Location <span class="badge bg-green" id="badge-move-count" style="display: none; margin-left: 5px;">0</span>', [
        'class' => 'btn btn-warning',
        'id' => 'btn-move-location',
        'onclick' => 'openModalMoveLocation(event);',
        'title' => 'Pindahkan lokasi untuk item yang dipilih'
    ]);
}

if (Helper::checkRoute('delete-batch')) {
    $panelButtons[] = Html::button('<i class="fa fa-trash"></i> Hapus Terpilih <span class="badge bg-red" id="badge-delete-count" style="display: none; margin-left: 5px;">0</span>', [
        'class' => 'btn btn-danger',
        'id' => 'btn-delete-batch',
        'onclick' => 'submitDeleteBatch(event);',
        'title' => 'Hapus item terpilih yang berstatus Stock'
    ]);
}

if (Helper::checkRoute('sync-status-out')) {
    $panelButtons[] = Html::a('<i class="fa fa-refresh"></i> Sync Status Out', ['sync-status-out'], [
        'class' => 'btn btn-default',
        'data-confirm' => 'Apakah Anda yakin ingin menyinkronkan status stok opname dengan status fisik Gudang Jadi saat ini?',
        'title' => 'Ubah status opname menjadi OUT jika stok di Gudang Jadi sudah bukan Stock',
    ]);
}

if (Helper::checkRoute('sync-status-stock-gudang-jadi')) {
    $panelButtons[] = Html::a('<i class="fa fa-cubes"></i> Sync Status Stock ke Gudang Jadi', ['sync-status-stock-gudang-jadi'], [
        'class' => 'btn btn-primary',
        'data-confirm' => 'Apakah Anda yakin ingin menyinkronkan status Gudang Jadi menjadi Stock untuk semua data opname yang berstatus Stock/Verified?',
        'title' => 'Ubah status Gudang Jadi menjadi Stock jika pada Stok Opname berstatus Stock/Verified',
    ]);
}

if (Helper::checkRoute('sync-stock-gudang-jadi')) {
    $panelButtons[] = Html::a('<i class="fa fa-plus-circle"></i> Sync & Tambah Stock Gudang Jadi', ['sync-stock-gudang-jadi'], [
        'class' => 'btn btn-success',
        'data-confirm' => 'Apakah Anda yakin ingin menyinkronkan data opname dan menambahkan stock ke Gudang Jadi yang belum memiliki ID Gudang Jadi?',
        'title' => 'Buat & sinkronkan stock Gudang Jadi dari data opname yang ID Gudang Jadi-nya masih kosong',
    ]);
}

if (Helper::checkRoute('rekap')) {
    $panelButtons[] = Html::a('<i class="fa fa-pie-chart"></i> Rekap Stok Opname', ['rekap'], ['class' => 'btn btn-info']);
}

$gridColumns = [];

if (Helper::checkRoute('move-location') || Helper::checkRoute('delete-batch')) {
    $gridColumns[] = [
        'class' => 'kartik\grid\CheckboxColumn',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
    ];
}

$gridColumns[] = ['class' => 'kartik\grid\SerialColumn'];

$gridColumns[] = [
    'class' => 'kartik\grid\ActionColumn',
    'template' => '{view} {delete}',
    'buttons' => [
        'delete' => function($url, $model, $key) {
            if ($model->status === TrnGudangJadiOpnamePcs::STATUS_STOCK) {
                return Html::a('<span class="glyphicon glyphicon-trash text-danger"></span>', ['delete', 'id' => $model->id], [
                    'title' => 'Hapus Opname (Status Stock)',
                    'data-confirm' => 'Apakah Anda yakin ingin menghapus data opname #' . $model->id . ' (' . $model->qr_code . ')?',
                    'data-method' => 'post',
                    'style' => 'margin-left: 5px;',
                ]);
            }
            return '';
        }
    ]
];

$gridColumns[] = [
    'attribute' => 'id',
    'label' => 'ID Opname',
    'format' => 'raw',
    'value' => function($model) {
        return Html::a('<strong>#' . $model->id . '</strong>', ['view', 'id' => $model->id], [
            'title' => 'Lihat Detail',
            'data-pjax' => '0',
        ]);
    },
    'pageSummary' => 'TOTAL Halaman Ini',
];
?>
<div class="stok-opname-gudang-jadi-index">

    <!-- Summary Info Box Cards -->
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-cubes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Roll / Pcs</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asInteger($totalPcs) ?> Roll (<?= Yii::$app->formatter->asDecimal($totalQty, 2) ?>)</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-green">
                <span class="info-box-icon"><i class="fa fa-dashboard"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Kuantitas</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asDecimal($totalQty, 2) ?></span>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-sm-6 col-xs-12">
            <div class="info-box bg-teal">
                <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Terverifikasi</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asInteger($totalVerified) ?> Roll (<?= Yii::$app->formatter->asDecimal($totalQtyVerified, 2) ?>)</span>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-sm-6 col-xs-12">
            <div class="info-box bg-yellow">
                <span class="info-box-icon"><i class="fa fa-cubes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Stock</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asInteger($totalStock) ?> Roll (<?= Yii::$app->formatter->asDecimal($totalQtyStock, 2) ?>)</span>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-sm-6 col-xs-12">
            <div class="info-box bg-red">
                <span class="info-box-icon"><i class="fa fa-times-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Out / Keluar</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asInteger($totalOut) ?> Roll (<?= Yii::$app->formatter->asDecimal($totalQtyOut, 2) ?>)</span>
                </div>
            </div>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'StokOpnameGdJadiGrid',
        'resizableColumns' => false,
        'responsiveWrap' => false,
        'pjax' => true,
        'showPageSummary' => true,
        'toolbar' => [
            '{toggleData}',
            '{export}'
        ],
        'panel' => [
            'type' => 'primary',
            'heading' => '<h3 class="panel-title"><i class="fa fa-list"></i> Data Stok Opname Gudang Jadi</h3>',
            'before' => implode(' ', $panelButtons),
            'after' => false,
        ],
        'columns' => array_merge($gridColumns, [
            [
                'attribute' => 'id_trn_gudang_jadi',
                'label' => 'ID Gudang Jadi',
                'format' => 'raw',
                'value' => function($model) {
                    if ($model->id_trn_gudang_jadi) {
                        return Html::a('<span class="label label-info">#' . $model->id_trn_gudang_jadi . '</span>', ['/trn-gudang-jadi/view', 'id' => $model->id_trn_gudang_jadi], [
                            'title' => 'Lihat Stok Gudang Jadi',
                            'target' => '_blank',
                            'data-pjax' => '0',
                        ]);
                    }
                    return '<span class="label label-default">Kosong</span> ' .
                        Html::a('<i class="fa fa-plus"></i> Buat Stock', ['create-stock', 'id' => $model->id], [
                            'class' => 'btn btn-xs btn-success',
                            'data-confirm' => 'Buat dan sinkronkan stock gudang jadi untuk item opname #' . $model->id . ' ini?',
                            'title' => 'Buat & Hubungkan ke Stock Gudang Jadi',
                            'data-pjax' => '0',
                        ]);
                }
            ],
            'opname_code',
            'qr_code',
            [
                'attribute' => 'woNo',
                'label' => 'No. WO',
                'value' => function($model) {
                    return $model->woNo;
                }
            ],
            [
                'attribute' => 'scNo',
                'label' => 'No. SC',
                'value' => function($model) {
                    return $model->scNo;
                }
            ],
            [
                'attribute' => 'marketingName',
                'label' => 'Marketing',
                'value' => function($model) {
                    return $model->marketingName;
                }
            ],
            [
                'attribute' => 'customerName',
                'label' => 'Buyer',
                'value' => function($model) {
                    return $model->customerName;
                }
            ],
            [
                'attribute' => 'motif',
                'label' => 'Motif / Kain',
                'value' => function($model) {
                    return $model->motif;
                }
            ],
            [
                'attribute' => 'color',
                'label' => 'Color / Warna',
                'value' => function($model) {
                    return $model->color;
                }
            ],
            [
                'attribute' => 'qty',
                'value' => function($model) {
                    return Yii::$app->formatter->asDecimal($model->qty);
                },
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM,
            ],
            [
                'attribute' => 'unit',
                'label' => 'Satuan',
                'value' => function($model) {
                    return $model->unitName;
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
                'attribute' => 'grade',
                'value' => function($model) {
                    return $model->gradeName;
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
            'locs_code',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    if ($model->status === TrnGudangJadiOpnamePcs::STATUS_VERIFIED) {
                        return '<span class="label label-success"><i class="fa fa-check"></i> ' . Html::encode($model->statusName) . '</span>';
                    } elseif ($model->status === TrnGudangJadiOpnamePcs::STATUS_OUT) {
                        return '<span class="label label-danger"><i class="fa fa-times-circle"></i> ' . Html::encode($model->statusName) . '</span>';
                    }
                    return '<span class="label label-warning"><i class="fa fa-cubes"></i> ' . Html::encode($model->statusName) . '</span>';
                },
                'format' => 'raw',
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
                'attribute' => 'dateRange',
                'label' => 'Tgl. Opname',
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
                ]
            ],

            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view} {create-stock} {delete}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        if (!Helper::checkRoute('view')) {
                            return '';
                        }
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $model->id], [
                            'title' => 'Detail Stok Opname Pcs',
                            'class' => 'btn btn-xs btn-default',
                        ]);
                    },
                    'create-stock' => function($url, $model, $key) {
                        if (!$model->id_trn_gudang_jadi && Helper::checkRoute('create-stock')) {
                            return Html::a('<span class="glyphicon glyphicon-plus"></span>', ['create-stock', 'id' => $model->id], [
                                'title' => 'Buat & Hubungkan ke Stock Gudang Jadi',
                                'class' => 'btn btn-xs btn-success',
                                'data-confirm' => 'Buat stock gudang jadi untuk item opname #' . $model->id . ' ini?',
                                'data-pjax' => '0',
                            ]);
                        }
                        return '';
                    },
                    'delete' => function($url, $model, $key) {
                        if (!Helper::checkRoute('delete')) {
                            return '';
                        }
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model->id], [
                            'title' => 'Hapus Stok Opname Pcs',
                            'class' => 'btn btn-xs btn-danger',
                            'data-confirm' => 'Apakah Anda yakin ingin menghapus data stok opname #' . $model->id . ' (' . $model->qr_code . ') ini?',
                            'data-method' => 'post',
                        ]);
                    },
                ]
            ]
        ]),
    ]); ?>
</div>

<!-- Modal Move Location -->
<div class="modal fade" id="modal-move-location" tabindex="-1" role="dialog" aria-labelledby="modalMoveLocationLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-yellow">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalMoveLocationLabel"><i class="fa fa-arrows"></i> Pindahkan Lokasi Barang</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    Jumlah item terpilih: <strong><span id="count-selected-items">0</span> item</strong>
                </div>
                <div class="form-group">
                    <label class="control-label" for="target-locs-code">Pilih Lokasi Tujuan: <span class="text-danger">*</span></label>
                    <select id="target-locs-code" name="target_locs_code" class="form-control" style="width: 100%;">
                        <option value="">-- Pilih Lokasi Tujuan --</option>
                        <?php foreach (MstSubLocation::optionList() as $locCode => $locDesc): ?>
                            <option value="<?= Html::encode($locCode) ?>"><?= Html::encode($locDesc) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="btn-submit-move-location" onclick="submitMoveLocation(event);"><i class="fa fa-check"></i> Simpan Perpindahan</button>
            </div>
        </div>
    </div>
</div>

<?php
$moveLocationUrl = Url::to(['move-location']);
$deleteBatchUrl = Url::to(['delete-batch']);
$js = <<<JS
window.getSelectedOpnameIds = function() {
    var ids = [];
    $('input[name="selection[]"]:checked').each(function() {
        var v = $(this).val();
        if (v) {
            ids.push(v);
        }
    });
    return ids;
};

window.syncMoveButtonBadge = function() {
    var ids = window.getSelectedOpnameIds();
    var count = ids.length;
    var \$badgeMove = $('#badge-move-count');
    var \$badgeDelete = $('#badge-delete-count');
    
    if (count > 0) {
        \$badgeMove.text(count).show();
        \$badgeDelete.text(count).show();
    } else {
        \$badgeMove.text('0').hide();
        \$badgeDelete.text('0').hide();
    }
};

window.openModalMoveLocation = function(e) {
    if (e) e.preventDefault();
    var ids = window.getSelectedOpnameIds();

    if (ids.length === 0) {
        alert('Harap pilih / centang minimal 1 data terlebih dahulu pada kotak pilihan tabel.');
        return false;
    }

    $('#count-selected-items').text(ids.length);
    $('#target-locs-code').val('');
    if ($.fn.select2) {
        $('#target-locs-code').select2({
            dropdownParent: $('#modal-move-location'),
            width: '100%'
        });
    }
    $('#modal-move-location').modal('show');
    return false;
};

window.submitMoveLocation = function(e) {
    if (e) e.preventDefault();
    var ids = window.getSelectedOpnameIds();
    if (ids.length === 0) {
        alert('Tidak ada data yang dipilih.');
        $('#modal-move-location').modal('hide');
        window.syncMoveButtonBadge();
        return false;
    }

    var targetLoc = $('#target-locs-code').val();
    if (!targetLoc) {
        alert('Harap pilih lokasi tujuan terlebih dahulu.');
        return false;
    }

    if (!confirm('Apakah Anda yakin ingin memindahkan ' + ids.length + ' item ke lokasi "' + targetLoc + '"?')) {
        return false;
    }

    var \$btn = $('#btn-submit-move-location');
    var oldText = \$btn.html();
    \$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memproses...');

    $.ajax({
        url: '{$moveLocationUrl}',
        type: 'POST',
        dataType: 'json',
        data: {
            ids: ids,
            target_locs_code: targetLoc
        },
        success: function(res) {
            if (res.success) {
                $('#modal-move-location').modal('hide');
                alert(res.message);
                $.pjax.reload({container: '#StokOpnameGdJadiGrid-pjax'});
            } else {
                alert(res.message || 'Gagal memindahkan lokasi.');
            }
        },
        error: function(xhr, status, error) {
            alert('Terjadi kesalahan pada server: ' + (xhr.responseText || error));
        },
        complete: function() {
            \$btn.prop('disabled', false).html(oldText);
            setTimeout(window.syncMoveButtonBadge, 300);
        }
    });
    return false;
};

window.submitDeleteBatch = function(e) {
    if (e) e.preventDefault();
    var ids = window.getSelectedOpnameIds();
    if (ids.length === 0) {
        alert('Harap pilih / centang minimal 1 data terlebih dahulu pada kotak pilihan tabel.');
        return false;
    }

    if (!confirm('Apakah Anda yakin ingin menghapus ' + ids.length + ' data terpilih yang berstatus Stock?')) {
        return false;
    }

    var \$btn = $('#btn-delete-batch');
    var oldText = \$btn.html();
    \$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menghapus...');

    $.ajax({
        url: '{$deleteBatchUrl}',
        type: 'POST',
        dataType: 'json',
        data: {
            ids: ids
        },
        success: function(res) {
            if (res.success) {
                alert(res.message);
                $.pjax.reload({container: '#StokOpnameGdJadiGrid-pjax'});
            } else {
                alert(res.message || 'Gagal menghapus data.');
            }
        },
        error: function(xhr, status, error) {
            alert('Terjadi kesalahan pada server: ' + (xhr.responseText || error));
        },
        complete: function() {
            \$btn.prop('disabled', false).html(oldText);
            setTimeout(window.syncMoveButtonBadge, 300);
        }
    });
    return false;
};

$(document).on('change', 'input[name="selection[]"], input[name="selection_all"], .select-on-check-all, .kv-all-select', function() {
    setTimeout(window.syncMoveButtonBadge, 50);
});

$(document).on('pjax:success pjax:complete pjax:end', function() {
    setTimeout(window.syncMoveButtonBadge, 50);
});

$(document).on('click', '#btn-move-location', function(e) {
    window.openModalMoveLocation(e);
});

$(document).on('click', '#btn-delete-batch', function(e) {
    window.submitDeleteBatch(e);
});

window.syncMoveButtonBadge();
JS;
$this->registerJs($js, \yii\web\View::POS_END);
?>

