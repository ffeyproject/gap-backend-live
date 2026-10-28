<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use kartik\widgets\Alert;
use yii\web\View;
use backend\modules\rawdata\models\TrnKirimBuyerItem;
use common\models\ar\TrnGudangJadi;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnKirimBuyerItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Raw Data Kirim Buyer Items';
$this->params['breadcrumbs'][] = $this->title;

$quickUpdateUrl = Url::to(['quick-update']);
$batchUpdateUrl = Url::to(['batch-update']);
$editableEditUrl = Url::to(['editable-edit']);
$getBalesUrl = Url::to(['get-bales']);
?>

<style>
/* Editable Table Cells */
td.cell-inline-editable {
    cursor: pointer !important;
    position: relative;
    transition: background-color 0.15s ease-in-out;
}
td.cell-inline-editable:hover {
    background-color: #fff9e6 !important;
}
td.cell-inline-editable .inline-editable-cell {
    display: block;
    width: 100%;
    min-height: 26px;
    padding: 3px 6px;
    border-radius: 3px;
    border: 1px dashed transparent;
    transition: all 0.15s ease-in-out;
    box-sizing: border-box;
}
td.cell-inline-editable:hover .inline-editable-cell {
    border-color: #f39c12;
}
td.cell-inline-editable .inline-edit-icon {
    font-size: 11px;
    margin-left: 5px;
    opacity: 0.35;
    transition: opacity 0.15s, color 0.15s;
}
td.cell-inline-editable:hover .inline-edit-icon {
    opacity: 1 !important;
    color: #e67e22 !important;
}
.inline-save-flash {
    background-color: #d4edda !important;
    border-color: #28a745 !important;
    animation: flashGreen 1.2s ease-out;
}
@keyframes flashGreen {
    0% { background-color: #a3e9a4; }
    100% { background-color: transparent; }
}
</style>

<div class="trn-kirim-buyer-item-index">

    <!-- Petunjuk & Peringatan -->
    <div class="box box-solid box-default" style="margin-bottom: 15px;">
        <div class="box-header with-border" style="cursor: pointer;" data-toggle="collapse" data-target="#info-petunjuk">
            <h3 class="box-title text-info" style="font-size: 14px;">
                <i class="glyphicon glyphicon-info-sign"></i> <strong>Panduan & Fitur Pembaruan Data</strong>
            </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-toggle="collapse" data-target="#info-petunjuk">
                    <i class="glyphicon glyphicon-chevron-down"></i>
                </button>
            </div>
        </div>
        <div id="info-petunjuk" class="box-body collapse">
            <div class="row">
                <div class="col-md-7">
                    <ul style="padding-left: 20px; margin-bottom: 0;">
                        <li><strong>Edit Inline:</strong> Klik langsung pada teks di kolom <em>No. Bal</em> atau <em>Catatan</em> di dalam tabel untuk mengubah nilai secara instan tanpa membuka modal. Tekan <strong>Enter</strong> untuk menyimpan atau <strong>Esc</strong> untuk membatalkan.</li>
                        <li><strong>Edit Cepat (Modal):</strong> Klik tombol <span class="label label-primary"><i class="glyphicon glyphicon-pencil"></i> Edit</span> pada baris item untuk mengubah No. Bal, Bal ID, Qty, dan Catatan sekaligus dengan saran otomatis bal.</li>
                        <li><strong>Update Massal (Batch Update):</strong> Centang beberapa item yang ingin diubah bersamaan, lalu klik tombol <span class="label label-success"><i class="glyphicon glyphicon-th-list"></i> Update Massal</span> di toolbar atas.</li>
                    </ul>
                </div>
                <div class="col-md-5">
                    <?= $this->render('ilustrasi/pergerakan_stock') ?>
                </div>
            </div>
        </div>
    </div>

    <?php Pjax::begin([
        'id' => 'KirimBuyerItemGrid-pjax',
        'timeout' => 15000,
        'enablePushState' => false,
    ]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'KirimBuyerItemGrid',
        'responsiveWrap' => false,
        'resizableColumns' => false,
        'hover' => true,
        'pjax' => false, // Handled by outer Pjax container
        'toolbar' => [
            [
                'content' =>
                    Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh', ['index'], [
                        'class' => 'btn btn-default',
                        'title' => 'Refresh Data',
                        'data-pjax' => 1,
                    ]) . ' ' .
                    Html::a('<i class="glyphicon glyphicon-erase"></i> Reset Filter', ['index'], [
                        'class' => 'btn btn-default',
                        'title' => 'Reset Pencarian',
                        'data-pjax' => 1,
                    ]) . ' ' .
                    Html::button('<i class="glyphicon glyphicon-th-list"></i> Update Massal (<span id="btn-batch-count">0</span>)', [
                        'class' => 'btn btn-default',
                        'id' => 'btn-trigger-batch',
                        'title' => 'Pilih satu atau lebih item terlebih dahulu untuk update massal',
                    ]),
            ],
            '{toggleData}',
        ],
        'panel' => [
            'type' => 'primary',
            'heading' => '<i class="glyphicon glyphicon-th-list"></i> Daftar Item Kirim Buyer',
            'before' => '<div style="margin-bottom: 5px;" class="text-muted small">
                <i class="glyphicon glyphicon-hand-right"></i> Klik langsung sel <strong>No. Bal</strong> atau <strong>Catatan</strong> untuk edit inline, atau gunakan <strong>Update Massal</strong> untuk perbarui banyak item sekaligus.
            </div>',
            'after' => '<div class="pull-left text-muted small" style="padding-top: 5px;">
                Total Data: <strong>' . number_format($dataProvider->getTotalCount(), 0, ',', '.') . '</strong> baris item
            </div><div class="clearfix"></div>',
        ],
        'columns' => [
            [
                'class' => 'kartik\grid\CheckboxColumn',
                'headerOptions' => ['style' => 'width: 35px; text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center; vertical-align: middle;'],
            ],
            [
                'class' => 'kartik\grid\SerialColumn',
                'width' => '35px',
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center; vertical-align: middle;'],
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'header' => 'Aksi',
                'template' => '{quick-edit} {view} {update} {delete}',
                'buttons' => [
                    'quick-edit' => function ($url, $model, $key) {
                        return Html::button('<i class="glyphicon glyphicon-pencil"></i> Edit', [
                            'class' => 'btn btn-xs btn-primary btn-quick-edit',
                            'title' => 'Edit Cepat Item #' . $model->id,
                            'data-id' => $model->id,
                            'data-header-id' => $model->kirimBuyer ? $model->kirimBuyer->header_id : '',
                            'data-kirim-buyer-id' => $model->kirim_buyer_id,
                            'data-stock-id' => $model->stock_id,
                            'data-wo-no' => ($model->stock && $model->stock->wo) ? $model->stock->wo->no : '-',
                            'data-buyer' => ($model->stock && $model->stock->wo && $model->stock->wo->mo && $model->stock->wo->mo->scGreige && $model->stock->wo->mo->scGreige->sc && $model->stock->wo->mo->scGreige->sc->cust) ? $model->stock->wo->mo->scGreige->sc->cust->name : '-',
                            'data-no-lot' => $model->noLot,
                            'data-qty' => $model->qty,
                            'data-no-bal' => $model->no_bal,
                            'data-bal-id' => $model->bal_id,
                            'data-note' => $model->note,
                        ]);
                    },
                    'view' => function ($url, $model, $key) {
                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['view', 'id' => $model->id], [
                            'class' => 'btn btn-xs btn-default',
                            'title' => 'Lihat Detail',
                            'data-pjax' => '0',
                        ]);
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::a('<i class="glyphicon glyphicon-new-window"></i>', ['update', 'id' => $model->id], [
                            'class' => 'btn btn-xs btn-default',
                            'title' => 'Ubah Halaman Penuh',
                            'data-pjax' => '0',
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<i class="glyphicon glyphicon-trash"></i>', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-xs btn-danger',
                            'title' => 'Hapus Item (Stok kembali ke STOCK)',
                            'data' => [
                                'confirm' => "Yakin ingin menghapus item #{$model->id}? Status stok akan dikembalikan menjadi STOCK.",
                                'method' => 'post',
                            ],
                            'data-pjax' => '0',
                        ]);
                    },
                ],
                'headerOptions' => ['style' => 'text-align: center; width: 140px;'],
                'contentOptions' => ['style' => 'text-align: center; white-space: nowrap; vertical-align: middle;'],
            ],
            [
                'attribute' => 'id',
                'label' => 'ID',
                'width' => '70px',
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center; font-weight: bold; vertical-align: middle;'],
            ],
            [
                'attribute' => 'header_id',
                'label' => 'Header ID',
                'value' => 'kirimBuyer.header_id',
                'width' => '90px',
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center; vertical-align: middle;'],
            ],
            [
                'attribute' => 'kirim_buyer_id',
                'label' => 'Kirim Buyer ID',
                'value' => 'kirimBuyer.id',
                'width' => '100px',
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center; vertical-align: middle;'],
            ],
            [
                'attribute' => 'woNo',
                'label' => 'Nomor WO',
                'value' => function ($model) {
                    return ($model->stock && $model->stock->wo) ? $model->stock->wo->no : '-';
                },
                'headerOptions' => ['style' => 'text-align: left; min-width: 120px;'],
                'contentOptions' => ['style' => 'white-space: nowrap; font-weight: 600; color: #2c3e50; vertical-align: middle;'],
            ],
            [
                'attribute' => 'customerName',
                'label' => 'Buyer',
                'value' => function ($model) {
                    if ($model->stock && $model->stock->wo && $model->stock->wo->mo && $model->stock->wo->mo->scGreige && $model->stock->wo->mo->scGreige->sc && $model->stock->wo->mo->scGreige->sc->cust) {
                        return $model->stock->wo->mo->scGreige->sc->cust->name;
                    }
                    return '-';
                },
                'headerOptions' => ['style' => 'text-align: left; min-width: 140px;'],
                'contentOptions' => ['style' => 'white-space: nowrap; vertical-align: middle;'],
            ],
            [
                'attribute' => 'stock_id',
                'label' => 'Stock ID',
                'value' => function ($data) {
                    return $data->stock ? $data->stock->id : '-';
                },
                'width' => '80px',
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center; vertical-align: middle;'],
            ],
            [
                'attribute' => 'noLot',
                'label' => 'No. Lot',
                'value' => function ($model) {
                    return $model->noLot;
                },
                'headerOptions' => ['style' => 'text-align: left; min-width: 100px;'],
                'contentOptions' => ['style' => 'white-space: nowrap; vertical-align: middle;'],
            ],
            [
                'attribute' => 'qty',
                'label' => 'Qty',
                'format' => ['decimal', 2],
                'headerOptions' => ['style' => 'text-align: right; width: 90px;'],
                'contentOptions' => ['style' => 'text-align: right; font-weight: bold; color: #1e824c; vertical-align: middle;'],
            ],
            [
                'attribute' => 'no_bal',
                'label' => 'No. Bal',
                'format' => 'raw',
                'value' => function ($model) {
                    $rawVal = $model->no_bal;
                    if (($rawVal === null || $rawVal === '') && $model->bal) {
                        $rawVal = $model->bal->no_bal;
                    }
                    $val = Html::encode($rawVal);
                    $display = !empty($rawVal) 
                        ? '<span class="inline-val-text">' . $val . '</span>' 
                        : '<span class="text-muted small inline-empty-text"><em>(Kosong)</em></span>';
                    return '<div class="inline-editable-cell" data-id="' . $model->id . '" data-attribute="no_bal" data-original="' . $val . '">' .
                        $display .
                        ' <i class="glyphicon glyphicon-pencil text-muted inline-edit-icon" title="Edit No. Bal"></i>' .
                    '</div>';
                },
                'headerOptions' => ['style' => 'text-align: center; width: 140px;'],
                'contentOptions' => [
                    'class' => 'cell-inline-editable col-inline-no-bal',
                    'style' => 'text-align: center; font-weight: bold; vertical-align: middle;',
                    'title' => 'Klik langsung untuk edit No. Bal secara instan',
                ],
            ],
            [
                'attribute' => 'bal_id',
                'label' => 'Bal ID',
                'width' => '80px',
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center; vertical-align: middle;', 'class' => 'col-bal-id'],
            ],
            [
                'attribute' => 'note',
                'label' => 'Catatan',
                'format' => 'raw',
                'value' => function ($model) {
                    $val = Html::encode($model->note);
                    $display = !empty($model->note) 
                        ? '<span class="inline-val-text">' . nl2br($val) . '</span>' 
                        : '<span class="text-muted small inline-empty-text"><em>(Tambah catatan...)</em></span>';
                    return '<div class="inline-editable-cell" data-id="' . $model->id . '" data-attribute="note" data-original="' . $val . '">' .
                        $display .
                        ' <i class="glyphicon glyphicon-pencil text-muted inline-edit-icon" title="Edit Catatan"></i>' .
                    '</div>';
                },
                'headerOptions' => ['style' => 'text-align: left; min-width: 170px;'],
                'contentOptions' => [
                    'class' => 'cell-inline-editable col-inline-note',
                    'style' => 'color: #555; vertical-align: middle;',
                    'title' => 'Klik langsung untuk edit Catatan secara instan',
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<!-- Modal Quick Edit Per Item -->
<?php
Modal::begin([
    'id' => 'modal-quick-edit',
    'header' => '<h4 class="modal-title" style="font-weight: 600;"><i class="glyphicon glyphicon-pencil text-primary"></i> Edit Cepat Item #<span id="qe-title-id"></span></h4>',
    'footer' => '
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Batal</button>
        <button type="button" class="btn btn-primary" id="btn-save-quick-edit"><i class="glyphicon glyphicon-floppy-disk"></i> Simpan Perubahan</button>
    ',
    'size' => Modal::SIZE_DEFAULT,
    'options' => [
        'tabindex' => false,
    ],
]);
?>
<form id="form-quick-edit" onsubmit="return false;">
    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
    <input type="hidden" id="qe-id" name="TrnKirimBuyerItem[id]">

    <!-- Ringkasan Info Item -->
    <div class="well well-sm" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; margin-bottom: 15px;">
        <div class="row">
            <div class="col-xs-6">
                <small class="text-muted">Nomor WO:</small><br>
                <strong id="qe-info-wo" class="text-primary">-</strong>
            </div>
            <div class="col-xs-6">
                <small class="text-muted">Buyer:</small><br>
                <strong id="qe-info-buyer">-</strong>
            </div>
        </div>
        <div class="row" style="margin-top: 8px;">
            <div class="col-xs-4">
                <small class="text-muted">Stock ID:</small><br>
                <span id="qe-info-stock-id" class="badge bg-gray">-</span>
            </div>
            <div class="col-xs-4">
                <small class="text-muted">Header ID:</small><br>
                <span id="qe-info-header-id" class="badge bg-blue">-</span>
            </div>
            <div class="col-xs-4">
                <small class="text-muted">No. Lot:</small><br>
                <span id="qe-info-no-lot">-</span>
            </div>
        </div>
    </div>

    <!-- Saran Bal Otomatis Quick Edit -->
    <div id="qe-bal-suggest-wrap" style="display: none; margin-bottom: 15px; background: #f0f7ff; border: 1px solid #cce5ff; border-radius: 4px; padding: 10px 12px;">
        <label style="font-size: 12px; color: #004085; margin-bottom: 4px;">
            <i class="glyphicon glyphicon-star text-primary"></i> <strong>Saran Bal Tersedia pada Header ID ini:</strong>
        </label>
        <div id="qe-bal-suggest-pills" style="margin-bottom: 6px; display: flex; flex-wrap: wrap; gap: 5px;"></div>
        <select class="form-control input-sm" id="qe-bal-suggest-select">
            <option value="">-- Atau Pilih Cepat dari Daftar Bal --</option>
        </select>
        <span class="help-block small text-muted" style="margin-bottom: 0; margin-top: 4px;">
            <i class="glyphicon glyphicon-info-sign"></i> Klik salah satu saran bal di atas untuk otomatis mengisi No. Bal dan Bal ID.
        </span>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="qe-no-bal">No. Bal <span class="text-danger">*</span></label>
                <input type="text" class="form-control input-lg" id="qe-no-bal" name="TrnKirimBuyerItem[no_bal]" list="qe-datalist-no-bal" placeholder="Contoh: 01, BAL-01">
                <datalist id="qe-datalist-no-bal"></datalist>
                <span class="help-block small text-muted">Nomor bal pengemasan barang.</span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="qe-bal-id">Bal ID</label>
                <input type="number" class="form-control input-lg" id="qe-bal-id" name="TrnKirimBuyerItem[bal_id]" list="qe-datalist-bal-id" placeholder="ID Bal (opsional)">
                <datalist id="qe-datalist-bal-id"></datalist>
                <span class="help-block small text-muted">ID referensi bal jika terhubung.</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="qe-qty">Qty <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control" id="qe-qty" name="TrnKirimBuyerItem[qty]" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="qe-kirim-buyer-id">Kirim Buyer ID <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="qe-kirim-buyer-id" name="TrnKirimBuyerItem[kirim_buyer_id]" required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="qe-note">Catatan / Note</label>
        <textarea class="form-control" id="qe-note" name="TrnKirimBuyerItem[note]" rows="3" placeholder="Catatan khusus untuk item ini..."></textarea>
    </div>
</form>
<?php Modal::end(); ?>

<!-- Modal Batch Update Multi-Item -->
<?php
Modal::begin([
    'id' => 'modal-batch-update',
    'header' => '<h4 class="modal-title" style="font-weight: 600;"><i class="glyphicon glyphicon-th-list text-success"></i> Update Massal (<span id="batch-modal-count">0</span> Item Dipilih)</h4>',
    'footer' => '
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Batal</button>
        <button type="button" class="btn btn-success" id="btn-save-batch-update"><i class="glyphicon glyphicon-ok"></i> Terapkan Perubahan Massal</button>
    ',
    'size' => Modal::SIZE_DEFAULT,
    'options' => [
        'tabindex' => false,
    ],
]);
?>
<form id="form-batch-update" onsubmit="return false;">
    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
    <div class="alert alert-info" style="margin-bottom: 15px; font-size: 13px;">
        <i class="glyphicon glyphicon-info-sign"></i> <strong>Petunjuk:</strong> Centang kotak di samping kolom yang ingin Anda perbarui. Bidang yang <em>tidak dicentang</em> tidak akan mengubah data yang sudah tersimpan pada item.
    </div>

    <!-- Saran Bal Otomatis Batch Update -->
    <div id="batch-bal-suggest-wrap" style="display: none; margin-bottom: 15px; background: #f0f7ff; border: 1px solid #cce5ff; border-radius: 4px; padding: 12px;">
        <label style="font-size: 13px; color: #004085; margin-bottom: 6px;">
            <i class="glyphicon glyphicon-star text-primary"></i> <strong>Saran Bal Tersedia (Berdasarkan Header ID / Kirim Buyer ID yang Dipilih):</strong>
        </label>
        <div id="batch-bal-suggest-loading" style="display: none; color: #31708f; margin-bottom: 8px;">
            <i class="glyphicon glyphicon-refresh glyphicon-spin"></i> Memuat daftar bal yang tersedia...
        </div>
        <div id="batch-bal-suggest-pills" style="margin-bottom: 8px; display: flex; flex-wrap: wrap; gap: 5px;"></div>
        <select class="form-control input-sm" id="batch-bal-suggest-select">
            <option value="">-- Atau Pilih Cepat dari Daftar Bal yang Ada --</option>
        </select>
        <span class="help-block small text-muted" style="margin-bottom: 0; margin-top: 6px;">
            <i class="glyphicon glyphicon-info-sign"></i> Mengklik salah satu bal di atas akan <strong>otomatis mencentang dan mengisi No. Bal serta Bal ID</strong> pada form di bawah.
        </span>
    </div>

    <!-- Opsi No Bal -->
    <div class="panel panel-default" style="margin-bottom: 12px;">
        <div class="panel-body" style="padding: 10px 15px;">
            <div class="checkbox" style="margin-top: 0; margin-bottom: 8px;">
                <label style="font-weight: bold;">
                    <input type="checkbox" id="batch-apply-no-bal" name="apply_no_bal" value="1">
                    Ubah <strong>No. Bal</strong> untuk semua item terpilih
                </label>
            </div>
            <div id="batch-input-no-bal-wrap" style="display: none; padding-left: 20px;">
                <input type="text" class="form-control" id="batch-set-no-bal" name="set_no_bal" list="batch-datalist-no-bal" placeholder="Masukkan No. Bal baru (misal: 01, BAL-01)">
                <datalist id="batch-datalist-no-bal"></datalist>
                <span class="help-block small text-muted" style="margin-bottom: 0;">Kosongkan jika ingin menghapus No. Bal.</span>
            </div>
        </div>
    </div>

    <!-- Opsi Bal ID -->
    <div class="panel panel-default" style="margin-bottom: 12px;">
        <div class="panel-body" style="padding: 10px 15px;">
            <div class="checkbox" style="margin-top: 0; margin-bottom: 8px;">
                <label style="font-weight: bold;">
                    <input type="checkbox" id="batch-apply-bal-id" name="apply_bal_id" value="1">
                    Ubah <strong>Bal ID</strong> untuk semua item terpilih
                </label>
            </div>
            <div id="batch-input-bal-id-wrap" style="display: none; padding-left: 20px;">
                <input type="number" class="form-control" id="batch-set-bal-id" name="set_bal_id" list="batch-datalist-bal-id" placeholder="Masukkan Bal ID baru">
                <datalist id="batch-datalist-bal-id"></datalist>
                <span class="help-block small text-muted" style="margin-bottom: 0;">Kosongkan jika ingin mengosongkan Bal ID.</span>
            </div>
        </div>
    </div>

    <!-- Opsi Catatan -->
    <div class="panel panel-default" style="margin-bottom: 12px;">
        <div class="panel-body" style="padding: 10px 15px;">
            <div class="checkbox" style="margin-top: 0; margin-bottom: 8px;">
                <label style="font-weight: bold;">
                    <input type="checkbox" id="batch-apply-note" name="apply_note" value="1">
                    Ubah <strong>Catatan / Note</strong> untuk semua item terpilih
                </label>
            </div>
            <div id="batch-input-note-wrap" style="display: none; padding-left: 20px;">
                <div style="margin-bottom: 8px;">
                    <label class="radio-inline">
                        <input type="radio" name="note_mode" value="replace" checked> Ganti seluruh catatan lama
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="note_mode" value="append"> Tambahkan ke catatan lama
                    </label>
                </div>
                <textarea class="form-control" id="batch-set-note" name="set_note" rows="3" placeholder="Masukkan catatan..."></textarea>
            </div>
        </div>
    </div>
</form>
<?php Modal::end(); ?>

<?php
$quickUpdateUrlJs = json_encode($quickUpdateUrl);
$batchUpdateUrlJs = json_encode($batchUpdateUrl);
$getBalesUrlJs = json_encode($getBalesUrl);
$editableEditUrlJs = json_encode($editableEditUrl);

$script = <<<JS
(function($) {
    var quickUpdateUrl = {$quickUpdateUrlJs};
    var batchUpdateUrl = {$batchUpdateUrlJs};
    var getBalesUrl = {$getBalesUrlJs};
    var editableEditUrl = {$editableEditUrlJs};

    var currentQeBales = [];
    var currentBatchBales = [];
    var selectedBatchKeys = [];

    // ==========================================
    // 1. INLINE EDITING DIRECT CELL CLICK
    // ==========================================
    function renderCellDisplay(attribute, val) {
        var displayHtml = '';
        var safeVal = $('<div>').text(val || '').html();
        if (val && $.trim(val) !== '') {
            displayHtml = '<span class="inline-val-text">' + safeVal.replace(/\\r?\\n/g, '<br>') + '</span>';
        } else {
            var emptyLabel = (attribute === 'no_bal') ? '(Kosong)' : '(Tambah catatan...)';
            displayHtml = '<span class="text-muted small inline-empty-text"><em>' + emptyLabel + '</em></span>';
        }
        var iconTitle = (attribute === 'no_bal') ? 'Edit No. Bal' : 'Edit Catatan';
        displayHtml += ' <i class="glyphicon glyphicon-pencil text-muted inline-edit-icon" title="' + iconTitle + '"></i>';
        return displayHtml;
    }

    $(document).on('click', 'td.cell-inline-editable, .inline-editable-cell', function(e) {
        // Jangan buka ulang jika klik berasal dari dalam form inline yang sedang aktif
        if ($(e.target).closest('.inline-edit-form').length > 0) {
            return;
        }

        var cell = $(this).hasClass('inline-editable-cell') ? $(this) : $(this).find('.inline-editable-cell');
        if (!cell.length) {
            return;
        }

        // Jika sel ini sudah dalam mode edit, fokuskan inputnya
        if (cell.find('.inline-edit-form').length > 0) {
            cell.find('.inline-input').focus();
            return;
        }

        // Batalkan / tutup inline form lain yang mungkin terbuka di tabel
        $('.inline-edit-form .btn-inline-cancel').trigger('click');

        var id = cell.attr('data-id');
        var attribute = cell.attr('data-attribute');
        var originalVal = cell.attr('data-original') || '';

        var formHtml = '';
        if (attribute === 'no_bal') {
            formHtml = '<div class="inline-edit-form" style="max-width: 150px; margin: 0 auto;">' +
                '<div class="input-group input-group-sm">' +
                    '<input type="text" class="form-control inline-input" style="text-align: center; font-weight: bold; height: 28px; padding: 2px 4px; font-size: 12px; border-color: #3c8dbc;" value="' + $('<div>').text(originalVal).html() + '" placeholder="No. Bal...">' +
                    '<span class="input-group-btn">' +
                        '<button type="button" class="btn btn-success btn-inline-save" title="Simpan (Enter)" style="height: 28px; padding: 2px 7px;"><i class="glyphicon glyphicon-ok"></i></button>' +
                        '<button type="button" class="btn btn-default btn-inline-cancel" title="Batal (Esc)" style="height: 28px; padding: 2px 7px;"><i class="glyphicon glyphicon-remove"></i></button>' +
                    '</span>' +
                '</div>' +
            '</div>';
        } else {
            formHtml = '<div class="inline-edit-form" style="min-width: 160px; width: 100%;">' +
                '<textarea class="form-control inline-input" rows="2" style="font-size: 12px; padding: 4px; margin-bottom: 4px; border-color: #3c8dbc; resize: vertical;" placeholder="Tulis catatan...">' + $('<div>').text(originalVal).html() + '</textarea>' +
                '<div style="text-align: right;">' +
                    '<button type="button" class="btn btn-xs btn-success btn-inline-save" style="padding: 2px 8px; margin-right: 4px;"><i class="glyphicon glyphicon-ok"></i> Simpan</button>' +
                    '<button type="button" class="btn btn-xs btn-default btn-inline-cancel" style="padding: 2px 8px;"><i class="glyphicon glyphicon-remove"></i> Batal</button>' +
                '</div>' +
            '</div>';
        }

        cell.html(formHtml);

        var input = cell.find('.inline-input');
        input.focus();
        if (attribute === 'no_bal') {
            input.select();
        }
    });

    function saveInlineCell(cell) {
        var id = cell.attr('data-id');
        var attribute = cell.attr('data-attribute');
        var input = cell.find('.inline-input');
        var newVal = input.val();
        var saveBtn = cell.find('.btn-inline-save');

        saveBtn.prop('disabled', true).html('<i class="glyphicon glyphicon-refresh glyphicon-spin"></i>');

        var csrfParam = $('meta[name="csrf-param"]').attr('content') || '_gap2_csrf-backend';
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        var postData = {
            id: id,
            attribute: attribute,
            value: newVal
        };
        if (csrfToken) {
            postData[csrfParam] = csrfToken;
        }

        $.ajax({
            url: editableEditUrl,
            type: 'POST',
            data: postData,
            dataType: 'json',
            headers: {
                'X-CSRF-Token': csrfToken
            },
            success: function(res) {
                if (res.success) {
                    var updatedVal = res.value !== undefined ? res.value : newVal;
                    cell.attr('data-original', updatedVal);
                    cell.html(renderCellDisplay(attribute, updatedVal));
                    cell.addClass('inline-save-flash');
                    setTimeout(function() {
                        cell.removeClass('inline-save-flash');
                    }, 1200);

                    // Update kolom Bal ID jika ada perubahan otomatis dari server
                    var tr = cell.closest('tr');
                    if (attribute === 'no_bal' && res.bal_id !== undefined) {
                        tr.find('.col-bal-id').text(res.bal_id || '');
                        tr.find('.btn-quick-edit').data('no-bal', updatedVal).data('bal-id', res.bal_id || '');
                    } else if (attribute === 'note') {
                        tr.find('.btn-quick-edit').data('note', updatedVal);
                    }
                } else {
                    alert('Gagal menyimpan: ' + (res.message || 'Terjadi kesalahan'));
                    var originalVal = cell.attr('data-original') || '';
                    cell.html(renderCellDisplay(attribute, originalVal));
                }
            },
            error: function(xhr, status, error) {
                var errMsg = 'Terjadi kesalahan jaringan atau server';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg += ': ' + xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        var parsed = JSON.parse(xhr.responseText);
                        errMsg += ': ' + (parsed.message || parsed.name || xhr.responseText);
                    } catch(e) {
                        errMsg += ' (' + xhr.status + ' ' + xhr.statusText + ')';
                    }
                }
                alert(errMsg);
                var originalVal = cell.attr('data-original') || '';
                cell.html(renderCellDisplay(attribute, originalVal));
            }
        });
    }

    // Tombol simpan inline
    $(document).on('click', '.btn-inline-save', function(e) {
        e.stopPropagation();
        var cell = $(this).closest('.inline-editable-cell');
        saveInlineCell(cell);
    });

    // Tombol batal inline
    $(document).on('click', '.btn-inline-cancel', function(e) {
        e.stopPropagation();
        var cell = $(this).closest('.inline-editable-cell');
        var attribute = cell.attr('data-attribute');
        var originalVal = cell.attr('data-original') || '';
        cell.html(renderCellDisplay(attribute, originalVal));
    });

    // Keyboard shortcut Enter & Esc
    $(document).on('keydown', '.inline-input', function(e) {
        var cell = $(this).closest('.inline-editable-cell');
        var isTextarea = $(this).is('textarea');

        if (e.keyCode === 13) { // Enter
            if (!isTextarea || e.ctrlKey) { // Enter on input, or Ctrl+Enter on textarea
                e.preventDefault();
                saveInlineCell(cell);
            }
        } else if (e.keyCode === 27) { // Escape
            e.preventDefault();
            var attribute = cell.attr('data-attribute');
            var originalVal = cell.attr('data-original') || '';
            cell.html(renderCellDisplay(attribute, originalVal));
        }
    });

    // Cegah klik di dalam form memicu cancel / event lain
    $(document).on('click', '.inline-edit-form', function(e) {
        e.stopPropagation();
    });

    // ==========================================
    // 2. HELPER & DATA GATHERING
    // ==========================================
    function getSelectedRowKeys() {
        var keys = [];

        // Prioritas 1: Ambil dari checkbox baris yang tercentang
        $('input[name="selection[]"]:checked, input.kv-row-checkbox:checked').each(function() {
            var val = $(this).val();
            var tr = $(this).closest('tr');
            var trKey = tr.attr('data-key');
            var key = (val && val !== 'on' && val !== '1') ? val : trKey;
            if (!key) {
                key = tr.find('.btn-quick-edit').data('id');
            }
            if (key) {
                var sKey = String(key);
                if (keys.indexOf(sKey) === -1) {
                    keys.push(sKey);
                }
            }
        });

        // Prioritas 2: Fallback ke yiiGridView
        if (keys.length === 0) {
            try {
                var yiiKeys = $('#KirimBuyerItemGrid').yiiGridView('getSelectedRows');
                if (Array.isArray(yiiKeys) && yiiKeys.length > 0) {
                    for (var i = 0; i < yiiKeys.length; i++) {
                        var k = String(yiiKeys[i]);
                        if (keys.indexOf(k) === -1) {
                            keys.push(k);
                        }
                    }
                }
            } catch (e) {}
        }

        return keys;
    }

    function populateBalSuggestions(target, bales) {
        var pillsContainer = $('#' + target + '-bal-suggest-pills');
        var selectElement = $('#' + target + '-bal-suggest-select');
        var datalistNoBal = $('#' + target + '-datalist-no-bal');
        var datalistBalId = $('#' + target + '-datalist-bal-id');

        pillsContainer.empty();
        selectElement.empty().append('<option value="">-- Atau Pilih Cepat dari Daftar Bal --</option>');
        datalistNoBal.empty();
        datalistBalId.empty();

        if (!Array.isArray(bales) || bales.length === 0) {
            pillsContainer.html('<span class="text-muted small"><i class="glyphicon glyphicon-info-sign"></i> Belum ada bal yang terdaftar pada header ini. Silakan input No. Bal / Bal ID baru.</span>');
            selectElement.hide();
            return;
        }

        selectElement.show();

        $.each(bales, function(idx, b) {
            var pillText = 'Bal ' + b.no_bal + (b.id ? ' (ID: ' + b.id + ')' : '');
            var pillBtn = $('<button type="button" class="btn btn-xs btn-default bal-pill-btn" style="margin-right: 5px; margin-bottom: 5px; font-weight: 600; border-color: #bce8f1; background-color: #fff;">' +
                '<i class="glyphicon glyphicon-tag text-info"></i> ' + $('<div>').text(pillText).html() +
            '</button>');
            pillBtn.data('no-bal', b.no_bal);
            pillBtn.data('bal-id', b.id || '');
            pillBtn.data('target', target);
            pillsContainer.append(pillBtn);

            var opt = $('<option>').val(idx).text(b.label || pillText);
            opt.data('no-bal', b.no_bal);
            opt.data('bal-id', b.id || '');
            selectElement.append(opt);

            datalistNoBal.append($('<option>').val(b.no_bal).text(b.label));
            if (b.id) {
                datalistBalId.append($('<option>').val(b.id).text('No. Bal: ' + b.no_bal));
            }
        });
    }

    function updateSelectedCounter() {
        var keys = getSelectedRowKeys();
        var count = keys.length;

        $('#btn-batch-count').text(count);
        $('#batch-modal-count').text(count);

        if (count > 0) {
            $('#btn-trigger-batch').removeClass('btn-default').addClass('btn-success');
        } else {
            $('#btn-trigger-batch').removeClass('btn-success').addClass('btn-default');
        }
    }

    $(document).on('change click', 'input[name="selection[]"], input[name="selection_all"], .select-on-check-all, .kv-row-checkbox, #KirimBuyerItemGrid input[type="checkbox"]', function() {
        setTimeout(updateSelectedCounter, 50);
    });

    $(document).on('pjax:end', '#KirimBuyerItemGrid-pjax', function() {
        setTimeout(updateSelectedCounter, 100);
    });

    // ==========================================
    // 3. QUICK EDIT MODAL
    // ==========================================
    $(document).on('click', '.btn-quick-edit', function(e) {
        e.preventDefault();
        var btn = $(this);
        var id = btn.data('id');

        $('#qe-title-id').text(id);
        $('#qe-id').val(id);
        $('#qe-info-wo').text(btn.data('wo-no') || '-');
        $('#qe-info-buyer').text(btn.data('buyer') || '-');
        $('#qe-info-stock-id').text(btn.data('stock-id') || '-');
        $('#qe-info-header-id').text(btn.data('header-id') || '-');
        $('#qe-info-no-lot').text(btn.data('no-lot') || '-');

        $('#qe-no-bal').val(btn.data('no-bal') || '');
        $('#qe-bal-id').val(btn.data('bal-id') || '');
        $('#qe-qty').val(btn.data('qty') || '');
        $('#qe-kirim-buyer-id').val(btn.data('kirim-buyer-id') || '');
        $('#qe-note').val(btn.data('note') || '');

        $('#qe-bal-suggest-wrap').hide();

        $.ajax({
            url: quickUpdateUrl + '?id=' + id,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.data) {
                    var d = res.data;
                    $('#qe-no-bal').val(d.no_bal || '');
                    $('#qe-bal-id').val(d.bal_id || '');
                    $('#qe-qty').val(d.qty || '');
                    $('#qe-note').val(d.note || '');
                    if (d.header_id) {
                        $('#qe-info-header-id').text(d.header_id);
                    }

                    if (Array.isArray(d.bales) && d.bales.length > 0) {
                        currentQeBales = d.bales;
                        populateBalSuggestions('qe', d.bales);
                        $('#qe-bal-suggest-wrap').slideDown(200);
                    } else {
                        currentQeBales = [];
                        $('#qe-bal-suggest-wrap').hide();
                    }
                }
            }
        });

        $('#modal-quick-edit').modal('show');
        setTimeout(function() {
            $('#qe-no-bal').focus().select();
        }, 500);
    });

    $(document).on('click', '#qe-bal-suggest-pills .bal-pill-btn', function() {
        var noBal = $(this).data('no-bal');
        var balId = $(this).data('bal-id');
        $('#qe-no-bal').val(noBal).trigger('change');
        $('#qe-bal-id').val(balId).trigger('change');
    });

    $(document).on('change', '#qe-bal-suggest-select', function() {
        var idx = $(this).val();
        if (idx !== '' && currentQeBales[idx]) {
            var b = currentQeBales[idx];
            $('#qe-no-bal').val(b.no_bal).trigger('change');
            $('#qe-bal-id').val(b.id || '').trigger('change');
        }
    });

    $(document).on('input change', '#qe-no-bal', function() {
        var val = $.trim($(this).val());
        if (val && currentQeBales.length > 0) {
            var matched = currentQeBales.find(function(b) {
                return String(b.no_bal).toLowerCase() === val.toLowerCase();
            });
            if (matched && matched.id) {
                $('#qe-bal-id').val(matched.id);
            }
        }
    });

    $(document).on('input change', '#qe-bal-id', function() {
        var val = $.trim($(this).val());
        if (val && currentQeBales.length > 0) {
            var matched = currentQeBales.find(function(b) {
                return String(b.id) === val;
            });
            if (matched && matched.no_bal) {
                $('#qe-no-bal').val(matched.no_bal);
            }
        }
    });

    $(document).on('click', '#btn-save-quick-edit', function(e) {
        e.preventDefault();
        var btn = $(this);
        var id = $('#qe-id').val();
        if (!id) return;

        var formData = $('#form-quick-edit').serialize();
        var originalBtnHtml = btn.html();

        btn.prop('disabled', true).html('<i class="glyphicon glyphicon-refresh glyphicon-spin"></i> Menyimpan...');

        $.ajax({
            url: quickUpdateUrl + '?id=' + id,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false).html(originalBtnHtml);
                if (res.success) {
                    $('#modal-quick-edit').modal('hide');
                    $.pjax.reload({container: '#KirimBuyerItemGrid-pjax', timeout: 15000});
                    if (typeof $.confirm === 'function') {
                        $.confirm({
                            title: 'Berhasil!',
                            content: res.message || 'Item berhasil diperbarui.',
                            type: 'green',
                            buttons: {
                                ok: { text: 'Tutup', btnClass: 'btn-success' }
                            }
                        });
                    } else {
                        alert(res.message || 'Item berhasil diperbarui.');
                    }
                } else {
                    if (typeof $.alert === 'function') {
                        $.alert({
                            title: 'Gagal!',
                            content: res.message || 'Terjadi kesalahan saat menyimpan data.',
                            type: 'red'
                        });
                    } else {
                        alert(res.message);
                    }
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false).html(originalBtnHtml);
                alert('Terjadi kesalahan jaringan atau server: ' + (xhr.responseJSON ? xhr.responseJSON.message : error));
            }
        });
    });

    // ==========================================
    // 4. BATCH UPDATE MODAL
    // ==========================================
    $(document).on('click', '#btn-trigger-batch', function(e) {
        e.preventDefault();
        var keys = getSelectedRowKeys();
        if (!Array.isArray(keys) || keys.length === 0) {
            if (typeof $.alert === 'function') {
                $.alert({
                    title: 'Peringatan',
                    content: 'Silakan centang satu atau lebih item pada tabel terlebih dahulu.',
                    type: 'orange'
                });
            } else {
                alert('Silakan centang satu atau lebih item pada tabel terlebih dahulu.');
            }
            return;
        }

        selectedBatchKeys = keys;

        $('#batch-modal-count').text(keys.length);
        $('#form-batch-update')[0].reset();
        $('#batch-input-no-bal-wrap').hide();
        $('#batch-input-bal-id-wrap').hide();
        $('#batch-input-note-wrap').hide();

        $('#batch-bal-suggest-wrap').show();
        $('#batch-bal-suggest-loading').show();
        $('#batch-bal-suggest-pills').empty();
        $('#batch-bal-suggest-select').empty().hide();

        currentBatchBales = [];

        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: getBalesUrl,
            type: 'POST',
            data: { 
                item_ids: keys,
                '_gap2_csrf-backend': csrfToken
            },
            dataType: 'json',
            headers: {
                'X-CSRF-Token': csrfToken
            },
            success: function(res) {
                $('#batch-bal-suggest-loading').hide();
                if (res.success && Array.isArray(res.bales) && res.bales.length > 0) {
                    currentBatchBales = res.bales;
                    populateBalSuggestions('batch', res.bales);
                } else {
                    $('#batch-bal-suggest-pills').html('<span class="text-muted small"><i class="glyphicon glyphicon-info-sign"></i> Belum ada bal terdaftar pada header ini. Masukkan No. Bal dan Bal ID secara manual.</span>');
                    $('#batch-bal-suggest-select').hide();
                }
            },
            error: function() {
                $('#batch-bal-suggest-loading').hide();
                $('#batch-bal-suggest-pills').html('<span class="text-muted small">Tidak dapat memuat saran bal otomatis.</span>');
            }
        });

        $('#modal-batch-update').modal('show');
    });

    function applyBalToBatchForm(noBal, balId) {
        $('#batch-apply-no-bal').prop('checked', true);
        $('#batch-input-no-bal-wrap').slideDown(200);
        $('#batch-set-no-bal').val(noBal);

        $('#batch-apply-bal-id').prop('checked', true);
        $('#batch-input-bal-id-wrap').slideDown(200);
        $('#batch-set-bal-id').val(balId);
    }

    $(document).on('click', '#batch-bal-suggest-pills .bal-pill-btn', function() {
        var noBal = $(this).data('no-bal');
        var balId = $(this).data('bal-id');
        applyBalToBatchForm(noBal, balId);
    });

    $(document).on('change', '#batch-bal-suggest-select', function() {
        var idx = $(this).val();
        if (idx !== '' && currentBatchBales[idx]) {
            var b = currentBatchBales[idx];
            applyBalToBatchForm(b.no_bal, b.id || '');
        }
    });

    $(document).on('input change', '#batch-set-no-bal', function() {
        var val = $.trim($(this).val());
        if (val && currentBatchBales.length > 0) {
            var matched = currentBatchBales.find(function(b) {
                return String(b.no_bal).toLowerCase() === val.toLowerCase();
            });
            if (matched && matched.id) {
                $('#batch-apply-bal-id').prop('checked', true);
                $('#batch-input-bal-id-wrap').slideDown(200);
                $('#batch-set-bal-id').val(matched.id);
            }
        }
    });

    $(document).on('input change', '#batch-set-bal-id', function() {
        var val = $.trim($(this).val());
        if (val && currentBatchBales.length > 0) {
            var matched = currentBatchBales.find(function(b) {
                return String(b.id) === val;
            });
            if (matched && matched.no_bal) {
                $('#batch-apply-no-bal').prop('checked', true);
                $('#batch-input-no-bal-wrap').slideDown(200);
                $('#batch-set-no-bal').val(matched.no_bal);
            }
        }
    });

    $(document).on('change', '#batch-apply-no-bal', function() {
        if ($(this).is(':checked')) {
            $('#batch-input-no-bal-wrap').slideDown(200);
            $('#batch-set-no-bal').focus();
        } else {
            $('#batch-input-no-bal-wrap').slideUp(200);
        }
    });

    $(document).on('change', '#batch-apply-bal-id', function() {
        if ($(this).is(':checked')) {
            $('#batch-input-bal-id-wrap').slideDown(200);
            $('#batch-set-bal-id').focus();
        } else {
            $('#batch-input-bal-id-wrap').slideUp(200);
        }
    });

    $(document).on('change', '#batch-apply-note', function() {
        if ($(this).is(':checked')) {
            $('#batch-input-note-wrap').slideDown(200);
            $('#batch-set-note').focus();
        } else {
            $('#batch-input-note-wrap').slideUp(200);
        }
    });

    $(document).on('click', '#btn-save-batch-update', function(e) {
        e.preventDefault();

        var keys = (selectedBatchKeys && selectedBatchKeys.length > 0) ? selectedBatchKeys : getSelectedRowKeys();
        if (!Array.isArray(keys) || keys.length === 0) {
            alert('Tidak ada item yang dipilih.');
            return;
        }

        var applyNoBal = $('#batch-apply-no-bal').is(':checked') ? 1 : 0;
        var applyBalId = $('#batch-apply-bal-id').is(':checked') ? 1 : 0;
        var applyNote = $('#batch-apply-note').is(':checked') ? 1 : 0;

        if (!applyNoBal && !applyBalId && !applyNote) {
            if (typeof $.alert === 'function') {
                $.alert({
                    title: 'Peringatan',
                    content: 'Centang minimal satu kolom (No. Bal, Bal ID, atau Catatan) yang ingin diubah.',
                    type: 'orange'
                });
            } else {
                alert('Centang minimal satu kolom yang ingin diubah.');
            }
            return;
        }

        var btn = $(this);
        var originalBtnHtml = btn.html();

        btn.prop('disabled', true).html('<i class="glyphicon glyphicon-refresh glyphicon-spin"></i> Memproses ' + keys.length + ' item...');

        var postData = $('#form-batch-update').serializeArray();
        for (var i = 0; i < keys.length; i++) {
            postData.push({name: 'ids[]', value: keys[i]});
        }

        var csrfParam = $('meta[name="csrf-param"]').attr('content') || '_gap2_csrf-backend';
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        if (csrfToken) {
            postData.push({name: csrfParam, value: csrfToken});
        }

        $.ajax({
            url: batchUpdateUrl,
            type: 'POST',
            data: postData,
            dataType: 'json',
            headers: {
                'X-CSRF-Token': csrfToken
            },
            success: function(res) {
                btn.prop('disabled', false).html(originalBtnHtml);
                if (res.success) {
                    $('#modal-batch-update').modal('hide');
                    selectedBatchKeys = [];
                    $.pjax.reload({container: '#KirimBuyerItemGrid-pjax', timeout: 15000});
                    if (typeof $.confirm === 'function') {
                        $.confirm({
                            title: 'Berhasil!',
                            content: res.message,
                            type: 'green',
                            buttons: {
                                ok: { text: 'Tutup', btnClass: 'btn-success' }
                            }
                        });
                    } else {
                        alert(res.message);
                    }
                } else {
                    if (typeof $.alert === 'function') {
                        $.alert({
                            title: 'Gagal!',
                            content: res.message || 'Gagal memproses update massal.',
                            type: 'red'
                        });
                    } else {
                        alert(res.message);
                    }
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false).html(originalBtnHtml);
                var errMsg = 'Terjadi kesalahan jaringan atau server';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg += ': ' + xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        var parsed = JSON.parse(xhr.responseText);
                        errMsg += ': ' + (parsed.message || parsed.name || xhr.responseText);
                    } catch(e) {
                        errMsg += ' (' + xhr.status + ' ' + xhr.statusText + ')';
                    }
                }
                if (typeof $.alert === 'function') {
                    $.alert({
                        title: 'Kesalahan Sistem',
                        content: errMsg,
                        type: 'red'
                    });
                } else {
                    alert(errMsg);
                }
            }
        });
    });

    setTimeout(updateSelectedCounter, 100);

})(jQuery);
JS;

$this->registerJs($script, View::POS_END);
?>