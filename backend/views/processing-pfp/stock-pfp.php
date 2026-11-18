<?php

use common\models\ar\TrnStockGreige;
use kartik\dialog\Dialog;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnStockGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Stock Pfp (Gudang 2)';
$this->params['breadcrumbs'][] = ['label' => 'Processing Pfp', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-stock-greige-index">

    <?= GridView::widget([
        'id' => 'stock-pfp-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'pjax' => true,
        'pjaxSettings' => [
            'options' => ['id' => 'stock-pfp-pjax']
        ],
        'panel' => [
            'type' => 'default',
            'before' => Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['stock-pfp'], ['class' => 'btn btn-default', 'title' => 'Refresh']) .
                Html::button('<i class="fa fa-exchange"></i> Pindah Jenis Printing (<span id="count-selected">0</span>)', [
                    'class' => 'btn btn-warning disabled',
                    'id' => 'btn-bulk-pindah',
                    'title' => 'Pindahkan jenis printing untuk item yang diceklis'
                ]) .
                Html::a('<i class="fa fa-history"></i> Riwayat Perpindahan', ['riwayat-pindah-printing'], [
                    'class' => 'btn btn-info',
                    'title' => 'Lihat seluruh riwayat perpindahan jenis printing'
                ]),
                ['class' => 'btn-group', 'role' => 'group']
            ),
        ],
        'showPageSummary' => true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\CheckboxColumn',
                'rowSelectedClass' => GridView::TYPE_INFO,
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    /* @var $model TrnStockGreige */
                    return [
                        'value' => $model->id,
                        'class' => 'stock-pfp-checkbox',
                        'data-id' => $model->id,
                        'data-panjang' => $model->panjang_m,
                        'data-greige' => $model->greige ? $model->greige->nama_kain : '-',
                        'data-jenis' => $model->jenis_printing,
                        'data-jenis-name' => $model->jenisPrintingName,
                    ];
                }
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'controller' => 'trn-stock-pfp',
                'template' => '{view} {pindah} {riwayat}',
                'buttons' => [
                    'pindah' => function ($url, $model, $key) {
                        /* @var $model TrnStockGreige */
                        return Html::a('<span class="glyphicon glyphicon-random"></span>', '#', [
                            'title' => 'Pindah Jenis Printing Roll Ini',
                            'class' => 'btn-row-pindah',
                            'data-id' => $model->id,
                            'data-panjang' => $model->panjang_m,
                            'data-greige' => $model->greige ? $model->greige->nama_kain : '-',
                            'data-jenis' => $model->jenis_printing,
                            'data-jenis-name' => $model->jenisPrintingName,
                        ]);
                    },
                    'riwayat' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-time"></span>', ['riwayat-pindah-printing', 'TrnStockPfpPindahPrintingLogSearch[stock_id]' => $model->id], [
                            'title' => 'Lihat Riwayat Perpindahan Roll Ini',
                        ]);
                    },
                ],
            ],

            'id',
            [
                'attribute' => 'dateRange',
                'label' => 'TANGGAL',
                'value' => 'date',
                'format' => 'date',
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
            'no_document',
            'no_lapak',
            [
                'attribute' => 'status_tsd',
                'value' => function ($data) {
                    /* @var $data TrnStockGreige */
                    return $data::tsdOptions()[$data->status_tsd];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::tsdOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'label' => 'Greige',
                'attribute' => 'greigeNamaKain',
                'value' => 'greige.nama_kain'
            ],
            [
                'attribute' => 'grade',
                'value' => function ($data) {
                    /* @var $data TrnStockGreige */
                    return $data::gradeOptions()[$data->grade];
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
            'lot_lusi',
            'lot_pakan',
            'no_set_lusi',
            [
                'attribute' => 'panjang_m',
                'format' => 'decimal',
                'pageSummary' => true
            ],
            'color',
            [
                'attribute' => 'status',
                'value' => function ($data) {
                    /* @var $data TrnStockGreige */
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'attribute' => 'asal_greige',
                'value' => function ($data) {
                    /* @var $data TrnStockGreige */
                    return $data::asalGreigeOptions()[$data->asal_greige];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::asalGreigeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'attribute' => 'pfp_jenis_gudang',
                'value' => 'pfpJenisGudangName',
                'filter' => [TrnStockGreige::PFP_JG_TWO => 'Gudang 2'],
            ],
            [
                'attribute' => 'jenis_printing',
                'value' => function ($data) {
                    /* @var $data TrnStockGreige */
                    if ($data->jenis_printing == TrnStockGreige::JENIS_PRINTING_DIGITAL) {
                        return '<span class="label label-primary">Digital Printing</span>';
                    } elseif ($data->jenis_printing == TrnStockGreige::JENIS_PRINTING_KONVENSIONAL) {
                        return '<span class="label label-success">Konvensional</span>';
                    }
                    return '<span class="label label-default">' . Html::encode($data->jenisPrintingName) . '</span>';
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
        ],
    ]); ?>
</div>

<!-- Modal Perpindahan Jenis Printing -->
<div class="modal fade" id="modalPindahJenis" tabindex="-1" role="dialog" aria-labelledby="modalPindahJenisLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalPindahJenisLabel"><i class="fa fa-exchange"></i> Perpindahan Jenis Printing (Gudang 2)</h4>
            </div>
            <div class="modal-body">
                <div class="callout callout-info" style="margin-bottom: 12px; padding: 10px 15px;">
                    <h5 style="margin-top: 0; margin-bottom: 5px; font-weight: bold;"><i class="fa fa-info-circle"></i> Ringkasan Roll Terpilih:</h5>
                    <p id="pindah-summary" style="margin-bottom: 0;">0 roll terpilih</p>
                </div>

                <div id="pindah-items-preview" style="max-height: 140px; overflow-y: auto; margin-bottom: 15px; border: 1px solid #ddd; padding: 8px; border-radius: 4px; background: #fafafa; font-size: 12px;"></div>

                <div class="form-group">
                    <label for="select-jenis-tujuan">Pilih Jenis Printing Tujuan: <span class="text-danger">*</span></label>
                    <select id="select-jenis-tujuan" class="form-control" style="width: 100%;">
                        <option value="">-- Pilih Jenis Printing Tujuan --</option>
                        <option value="<?= TrnStockGreige::JENIS_PRINTING_DIGITAL ?>">Digital Printing</option>
                        <option value="<?= TrnStockGreige::JENIS_PRINTING_KONVENSIONAL ?>">Konvensional</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="text-catatan-pindah">Catatan / Alasan Perpindahan:</label>
                    <textarea id="text-catatan-pindah" class="form-control" rows="3" placeholder="Masukkan alasan perpindahan (opsional)..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-submit-pindah"><i class="fa fa-check"></i> Simpan Perpindahan</button>
            </div>
        </div>
    </div>
</div>

<?php
$urlPindah = Url::to(['pindah-jenis-printing']);
$js = <<<JS
var currentSelectedItems = [];

function updateSelectionState() {
    currentSelectedItems = [];
    $('.stock-pfp-checkbox:checked').each(function() {
        var el = $(this);
        currentSelectedItems.push({
            id: el.val(),
            panjang: parseFloat(el.data('panjang')) || 0,
            greige: el.data('greige') || '-',
            jenis: el.data('jenis'),
            jenisName: el.data('jenis-name') || '-'
        });
    });

    var count = currentSelectedItems.length;
    $('#count-selected').text(count);

    if (count > 0) {
        $('#btn-bulk-pindah').removeClass('disabled').prop('disabled', false);
    } else {
        $('#btn-bulk-pindah').addClass('disabled').prop('disabled', true);
    }
}

function showPindahModal(items) {
    if (!items || items.length === 0) {
        $.alert({
            title: 'Perhatian',
            content: 'Silakan pilih / ceklis minimal 1 roll stock terlebih dahulu.'
        });
        return;
    }

    var totalPanjang = 0;
    var previewHtml = '<table class="table table-condensed table-striped" style="margin-bottom:0;"><thead><tr><th>ID</th><th>Greige</th><th>Panjang (M)</th><th>Jenis Saat Ini</th></tr></thead><tbody>';
    
    items.forEach(function(item) {
        totalPanjang += item.panjang;
        previewHtml += '<tr><td>#' + item.id + '</td><td>' + item.greige + '</td><td>' + item.panjang.toLocaleString() + '</td><td><span class="label label-default">' + item.jenisName + '</span></td></tr>';
    });
    previewHtml += '</tbody></table>';

    $('#pindah-summary').html('<strong>' + items.length + ' Roll</strong> terpilih | Total Panjang: <strong>' + totalPanjang.toLocaleString() + ' M</strong>');
    $('#pindah-items-preview').html(previewHtml);
    $('#select-jenis-tujuan').val('');
    $('#text-catatan-pindah').val('');

    $('#modalPindahJenis').modal('show');
}

// Event listener checkbox grid
$(document).on('change', '.stock-pfp-checkbox, .select-on-check-all', function() {
    updateSelectionState();
});

// Event listener tombol bulk pindah
$(document).on('click', '#btn-bulk-pindah', function(e) {
    e.preventDefault();
    if ($(this).hasClass('disabled')) return;
    showPindahModal(currentSelectedItems);
});

// Event listener tombol pindah di baris (ActionColumn)
$(document).on('click', '.btn-row-pindah', function(e) {
    e.preventDefault();
    var el = $(this);
    var singleItem = [{
        id: el.data('id'),
        panjang: parseFloat(el.data('panjang')) || 0,
        greige: el.data('greige') || '-',
        jenis: el.data('jenis'),
        jenisName: el.data('jenis-name') || '-'
    }];
    showPindahModal(singleItem);
});

// Event listener simpan perpindahan
$('#btn-submit-pindah').on('click', function(e) {
    e.preventDefault();
    var btn = $(this);
    var jenisTujuan = $('#select-jenis-tujuan').val();
    var note = $('#text-catatan-pindah').val();

    if (!jenisTujuan) {
        $.alert({
            title: 'Peringatan',
            content: 'Harap pilih jenis printing tujuan terlebih dahulu.'
        });
        return;
    }

    var stockIds = currentSelectedItems.map(function(item) { return item.id; });
    if (stockIds.length === 0) {
        var previewIds = [];
        $('#pindah-items-preview table tbody tr').each(function() {
            var textId = $(this).find('td:first').text().replace('#', '');
            if (textId) previewIds.push(textId);
        });
        stockIds = previewIds;
    }

    if (stockIds.length === 0) {
        $.alert({
            title: 'Peringatan',
            content: 'Tidak ada item yang dipilih.'
        });
        return;
    }

    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memproses...');

    $.ajax({
        url: '{$urlPindah}',
        type: 'POST',
        dataType: 'json',
        data: {
            stock_ids: stockIds,
            jenis_printing_tujuan: jenisTujuan,
            note: note
        },
        success: function(res) {
            btn.prop('disabled', false).html('<i class="fa fa-check"></i> Simpan Perpindahan');
            if (res.success) {
                $('#modalPindahJenis').modal('hide');
                $.alert({
                    title: 'Berhasil',
                    content: res.message
                });
                if ($.pjax) {
                    $.pjax.reload({container: '#stock-pfp-pjax', async: false});
                } else {
                    location.reload();
                }
                updateSelectionState();
            } else {
                $.alert({
                    title: 'Gagal',
                    content: res.message
                });
            }
        },
        error: function(xhr, status, error) {
            btn.prop('disabled', false).html('<i class="fa fa-check"></i> Simpan Perpindahan');
            $.alert({
                title: 'Error',
                content: 'Terjadi kesalahan sistem: ' + (xhr.responseText || error)
            });
        }
    });
});

// Re-init listener on pjax complete
$(document).on('pjax:complete', function() {
    updateSelectionState();
});
JS;

$this->registerJs($js, View::POS_END);
?>
