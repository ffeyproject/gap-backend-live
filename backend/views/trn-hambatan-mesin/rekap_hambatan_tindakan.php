<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use kartik\widgets\Select2;
use kartik\widgets\DatePicker;
use common\models\ar\MstMesinProses;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel yii\base\DynamicModel */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rekap Hambatan dan Tindakan';
$this->params['breadcrumbs'][] = ['label' => 'Hambatan Per Mesin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Get distinct models for dropdown
$modelsList = MstMesinProses::find()->select(['model_mesin'])->distinct()->asArray()->all();
$modelsMap = [];
foreach ($modelsList as $m) {
    if (!empty($m['model_mesin'])) {
        $modelsMap[$m['model_mesin']] = $m['model_mesin'];
    }
}

// Get all machines
$machinesList = MstMesinProses::find()->orderBy(['nama_mesin' => SORT_ASC])->asArray()->all();
$machinesMap = ArrayHelper::map($machinesList, 'id', 'nama_mesin');

function formatTanggalIndo($tanggal) {
    if (empty($tanggal)) {
        return '-';
    }
    $timestamp = strtotime($tanggal);
    if (!$timestamp) {
        return $tanggal;
    }
    $bulanIndo = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $d = date('d', $timestamp);
    $m = (int)date('m', $timestamp);
    $y = date('Y', $timestamp);
    
    return $d . ' ' . $bulanIndo[$m] . ' ' . $y;
}
?>

<style>
@media print {
    /* Sembunyikan elemen navigasi, header, footer, pager, dan kontrol halaman */
    .main-sidebar, .main-header, .breadcrumb, .box-default, .btn, .btn-input-tindakan, .pagination, .pagination-container, .pager, ul.pagination, .panel-heading, .kv-panel-after, .panel-footer, .footer, .export-menu {
        display: none !important;
    }
    
    /* Reset margin dan background area konten */
    .content-wrapper, .content, .right-side {
        margin-left: 0 !important;
        padding: 0 !important;
        background: none !important;
    }
    
    .box {
        border: none !important;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Hilangkan teks URL di sebelah link (misal link sort pada header) */
    a[href]:after {
        content: "" !important;
    }
    
    /* Paksa struktur tabel dan border solid */
    table, table.table {
        display: table !important;
        width: 100% !important;
        border-collapse: collapse !important;
        margin: 0 !important;
        padding: 0 !important;
        page-break-inside: auto !important;
    }
    
    table.table tr {
        display: table-row !important;
        page-break-inside: avoid !important;
        page-break-after: auto !important;
    }
    
    table.table thead {
        display: table-header-group !important;
    }
    
    table.table tbody {
        display: table-row-group !important;
    }
    
    table.table th, table.table td {
        display: table-cell !important;
        border: 1px solid #000000 !important;
        padding: 6px 8px !important;
        font-size: 11px !important;
        line-height: 1.2 !important;
        color: #000000 !important;
        background-color: transparent !important;
        word-wrap: break-word !important;
    }
    
    table.table th {
        font-weight: bold !important;
        background-color: #f2f2f2 !important;
        text-align: center !important;
    }
    
    /* Hindari scrollbar merusak layout cetak */
    .table-responsive {
        display: block !important;
        width: 100% !important;
        overflow-x: visible !important;
        border: none !important;
    }
}
</style>

<div class="rekap-hambatan-tindakan-index">
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-filter"></i> Filter Pencarian</h3>
        </div>
        <div class="box-body">
            <?php $form = ActiveForm::begin([
                'action' => ['rekap-hambatan-tindakan'],
                'method' => 'get',
            ]); ?>

            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'tanggal_mulai')->widget(DatePicker::classname(), [
                        'options' => ['placeholder' => 'Pilih Tanggal Mulai...'],
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true
                        ]
                    ])->label('Tanggal Mulai') ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($searchModel, 'tanggal_selesai')->widget(DatePicker::classname(), [
                        'options' => ['placeholder' => 'Pilih Tanggal Selesai...'],
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true
                        ]
                    ])->label('Tanggal Selesai') ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($searchModel, 'model_mesin')->widget(Select2::classname(), [
                        'data' => $modelsMap,
                        'options' => ['placeholder' => 'Pilih Model Mesin...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label('Model Mesin') ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($searchModel, 'mst_mesin_proses_id')->widget(Select2::classname(), [
                        'data' => $machinesMap,
                        'options' => ['placeholder' => 'Pilih Mesin...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label('Mesin') ?>
                </div>
            </div>

            <div class="form-group" style="margin-top: 10px;">
                <?= Html::submitButton('<i class="glyphicon glyphicon-search"></i> Cari', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('<i class="glyphicon glyphicon-refresh"></i> Reset', ['rekap-hambatan-tindakan'], ['class' => 'btn btn-default']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <?php
    $titleRange = 'REKAP HAMBATAN DAN TINDAKAN';
    if (!empty($searchModel->tanggal_mulai) || !empty($searchModel->tanggal_selesai)) {
        $tglDari = !empty($searchModel->tanggal_mulai) ? formatTanggalIndo($searchModel->tanggal_mulai) : null;
        $tglSampai = !empty($searchModel->tanggal_selesai) ? formatTanggalIndo($searchModel->tanggal_selesai) : null;
        if ($tglDari && $tglSampai) {
            $titleRange .= " ({$tglDari} - {$tglSampai})";
        } elseif ($tglDari) {
            $titleRange .= " (Mulai {$tglDari})";
        } elseif ($tglSampai) {
            $titleRange .= " (Sampai {$tglSampai})";
        }
    }
    ?>

    <?php \yii\widgets\Pjax::begin(['id' => 'rekap-hambatan-pjax']); ?>

    <div class="text-center" style="margin-top: 15px; margin-bottom: 25px;">
        <h3 class="text-uppercase" style="font-weight: bold; margin: 0; color: #222; font-family: sans-serif;">
            <?= Html::encode($titleRange) ?>
        </h3>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before' => Html::a('<i class="glyphicon glyphicon-print"></i> Cetak Rekap (PDF View)', 
                array_merge(['rekap-hambatan-tindakan', 'print' => 'pdf'], Yii::$app->request->queryParams), 
                [
                    'class' => 'btn btn-info',
                    'target' => '_blank',
                    'data-pjax' => '0'
                ]
            ),
            'after' => false,
            'footer' => false,
        ],
        'toolbar' => [
            '{export}',
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'trnHambatanMesin.tanggal',
                'label' => 'Tanggal',
                'value' => function ($data) {
                    return formatTanggalIndo($data->trnHambatanMesin ? $data->trnHambatanMesin->tanggal : null);
                },
                'format' => 'raw',
            ],
            [
                'label' => 'Model Mesin',
                'value' => function ($data) {
                    return $data->mstMesinProses ? ($data->mstMesinProses->model_mesin ?: '-') : '-';
                },
            ],
            [
                'label' => 'Nama Mesin',
                'value' => 'mstMesinProses.nama_mesin',
            ],
            [
                'label' => 'Jenis Hambatan',
                'value' => function ($data) {
                    $names = [];
                    foreach ($data->mstJenisHambatans as $jh) {
                        $names[] = $jh->nama;
                    }
                    return implode(', ', $names);
                },
            ],
            'keterangan:ntext',
            [
                'label' => 'Lama',
                'value' => function ($data) {
                    $tanggal = $data->trnHambatanMesin ? $data->trnHambatanMesin->tanggal : date('Y-m-d');
                    $start_time = strtotime($tanggal . ' ' . $data->start_time);
                    $stop_time = strtotime($tanggal . ' ' . $data->stop_time);
                    if ($start_time && $stop_time) {
                        if ($stop_time < $start_time) {
                            $stop_time += 86400; // crosses midnight
                        }
                        $diffSeconds = $stop_time - $start_time;
                        $hours = floor($diffSeconds / 3600);
                        $minutes = floor(($diffSeconds % 3600) / 60);
                        return sprintf('%02d:%02d', $hours, $minutes);
                    }
                    return '-';
                },
            ],
            [
                'label' => 'Tindakan',
                'format' => 'raw',
                'value' => function ($data) {
                    $tindakanText = $data->tindakan ? Html::tag('div', Html::encode($data->tindakan), ['style' => 'margin-bottom: 5px; white-space: pre-wrap;']) : Html::tag('em', 'Belum ada tindakan', ['class' => 'text-muted']);
                    
                    $btnClass = $data->tindakan ? 'btn btn-xs btn-info' : 'btn btn-xs btn-success';
                    $btnLabel = $data->tindakan ? '<i class="glyphicon glyphicon-pencil"></i> Ubah Tindakan' : '<i class="glyphicon glyphicon-plus"></i> Input Tindakan';
                    
                    $machineName = $data->mstMesinProses ? $data->mstMesinProses->nama_mesin : '-';
                    $jenisHambatans = [];
                    foreach ($data->mstJenisHambatans as $jh) {
                        $jenisHambatans[] = $jh->nama;
                    }
                    $hambatanStr = implode(', ', $jenisHambatans);
                    
                    $btn = Html::button($btnLabel, [
                        'class' => $btnClass . ' btn-input-tindakan',
                        'data-id' => $data->id,
                        'data-mesin' => $machineName,
                        'data-hambatan' => $hambatanStr,
                        'data-keterangan' => $data->keterangan ?: '-',
                        'data-tindakan' => $data->tindakan ?: '',
                    ]);
                    
                    return Html::tag('div', $tindakanText . $btn);
                },
            ],
        ],
    ]); ?>

    <?php \yii\widgets\Pjax::end(); ?>
</div>

<?php
\yii\bootstrap\Modal::begin([
    'header' => '<h4><i class="glyphicon glyphicon-edit"></i> Input / Edit Tindakan</h4>',
    'id' => 'modal-tindakan',
    'size' => 'modal-md',
]);
?>
<div id="modal-tindakan-content">
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-12">
            <table class="table table-bordered table-striped" style="margin-bottom: 0;">
                <tr>
                    <th style="width: 30%;">Mesin</th>
                    <td id="info-mesin">-</td>
                </tr>
                <tr>
                    <th>Hambatan</th>
                    <td id="info-hambatan">-</td>
                </tr>
                <tr>
                    <th>Keterangan</th>
                    <td id="info-keterangan">-</td>
                </tr>
            </table>
        </div>
    </div>
    
    <form id="form-update-tindakan" method="post">
        <input type="hidden" id="input-tindakan-id" name="id">
        <div class="form-group">
            <label for="input-tindakan-val">Tindakan Hambatan <span class="text-danger">*</span></label>
            <textarea class="form-control" id="input-tindakan-val" name="tindakan" rows="4" required placeholder="Tuliskan tindakan yang diambil untuk mengatasi hambatan..."></textarea>
        </div>
        <div class="text-right">
            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-save"></i> Simpan</button>
        </div>
    </form>
</div>
<?php
\yii\bootstrap\Modal::end();
?>

<?php
$updateUrl = \yii\helpers\Url::to(['update-tindakan']);
$js = <<<JS
$(document).on('click', '.btn-input-tindakan', function() {
    var id = $(this).data('id');
    var mesin = $(this).data('mesin');
    var hambatan = $(this).data('hambatan');
    var keterangan = $(this).data('keterangan');
    var tindakan = $(this).data('tindakan');
    
    $('#input-tindakan-id').val(id);
    $('#info-mesin').text(mesin);
    $('#info-hambatan').text(hambatan);
    $('#info-keterangan').text(keterangan);
    $('#input-tindakan-val').val(tindakan);
    
    $('#modal-tindakan').modal('show');
});

$('#form-update-tindakan').on('submit', function(e) {
    e.preventDefault();
    var id = $('#input-tindakan-id').val();
    var tindakan = $('#input-tindakan-val').val();
    
    $.ajax({
        url: '{$updateUrl}?id=' + id,
        type: 'POST',
        data: {
            tindakan: tindakan,
            [yii.getCsrfParam()]: yii.getCsrfToken()
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#modal-tindakan').modal('hide');
                $.pjax.reload({container: '#rekap-hambatan-pjax'});
            } else {
                alert(response.message);
            }
        },
        error: function() {
            alert('Terjadi kesalahan koneksi saat menyimpan.');
        }
    });
});
JS;
$this->registerJs($js);
?>
