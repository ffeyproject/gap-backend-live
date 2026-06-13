<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $selectedMonth string */
/* @var $stats array */
/* @var $dataProvider yii\data\ArrayDataProvider */
/* @var $rawRecords array */

$this->title = 'Rekap Realisasi QO';
$this->params['breadcrumbs'][] = ['label' => 'PROCESSING', 'url' => '#'];
$this->params['breadcrumbs'][] = ['label' => 'REKAP', 'url' => '#'];
$this->params['breadcrumbs'][] = 'Qo';

// Generate month options for dropdown (current month down to January of the current year)
$currentYear = date('Y');
$currentMonth = date('n');
$monthOptions = [];
for ($i = $currentMonth; $i >= 1; $i--) {
    $monthNumber = str_pad($i, 2, '0', STR_PAD_LEFT);
    $time = strtotime("$currentYear-$monthNumber-01");
    $value = date('Y-m', $time);
    // Localized month name
    $label = date('F Y', $time);
    $monthOptions[$value] = $label;
}

$evaluatedMonthTime = strtotime($selectedMonth . '-01');
$evaluatedMonthLabel = date('F Y', $evaluatedMonthTime);
?>

<div class="processing-rekap-qo-index">
    
    <!-- Header Card -->
    <div class="box box-solid" style="border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-top: 3px solid #3c8dbc; margin-bottom: 20px;">
        <div class="box-body" style="padding: 20px;">
            <div class="row" style="display: flex; align-items: center; flex-wrap: wrap;">
                <div class="col-md-6">
                    <h3 style="margin: 0; font-family: 'Outfit', 'Inter', sans-serif; font-weight: 700; color: #2c3e50; text-transform: uppercase; letter-spacing: 0.5px;">
                        Realisasi QO Bulan <?= Html::encode($evaluatedMonthLabel) ?>
                    </h3>
                    <p class="text-muted" style="margin: 5px 0 0 0; font-size: 13px;">
                        Menampilkan data kartu proses (NK) yang tidak mencapai target verpacking dalam 14 hari dari Tanggal Buka Greige.
                    </p>
                </div>
                <div class="col-md-6 text-right" style="display: flex; justify-content: flex-end; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <!-- Filter Form -->
                    <form method="get" action="<?= Url::to(['index']) ?>" class="form-inline" style="display: inline-block;">
                        <div class="form-group" style="margin: 0;">
                            <label style="margin-right: 8px; font-weight: 600; color: #555;">Pilih Bulan Buka Greige:</label>
                            <select name="month" class="form-control" onchange="this.form.submit();" style="border-radius: 4px; border: 1px solid #ccc; min-width: 160px; font-weight: 500;">
                                <?php foreach ($monthOptions as $val => $lbl): ?>
                                    <option value="<?= $val ?>" <?= $selectedMonth === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                    
                    <!-- Show Grafik Button -->
                    <button type="button" class="btn btn-info" id="btnShowGrafik" data-toggle="modal" data-target="#grafikModal" style="border-radius: 4px; font-weight: bold; background: linear-gradient(135deg, #3498db, #2980b9); border: none; box-shadow: 0 2px 5px rgba(52,152,219,0.3); transition: all 0.3s ease;">
                        <i class="glyphicon glyphicon-stats"></i> Show Grafik
                    </button>
                    
                    <!-- Export Button -->
                    <a href="<?= Url::to(['export-excel', 'month' => $selectedMonth]) ?>" class="btn btn-success" style="border-radius: 4px; font-weight: bold; background: linear-gradient(135deg, #2ecc71, #27ae60); border: none; box-shadow: 0 2px 5px rgba(46,204,113,0.3); transition: all 0.3s ease;">
                        <i class="glyphicon glyphicon-file"></i> Download Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Summary Section -->
    <div class="box box-solid" style="border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px;">
        <div class="box-header with-border" style="background-color: #fafbfc; border-top-left-radius: 8px; border-top-right-radius: 8px;">
            <h4 class="box-title" style="font-weight: 700; color: #34495e; font-size: 16px;">
                <i class="glyphicon glyphicon-stats" style="margin-right: 8px; color: #3c8dbc;"></i> REKAP PERSENTASE PENCAPAIAN
            </h4>
        </div>
        <div class="box-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table table-bordered text-center" style="margin: 0; font-size: 14px; border: none;">
                    <thead>
                        <tr style="background-color: #f8f9fa; color: #2c3e50; font-weight: bold;">
                            <th style="width: 80px; vertical-align: middle;">NO</th>
                            <th style="text-align: left; padding-left: 20px; vertical-align: middle;">KATEGORI</th>
                            <th style="color: #27ae60; font-weight: bold; vertical-align: middle;">TERCAPAI (&le; 14 HARI)</th>
                            <th style="color: #e74c3c; font-weight: bold; vertical-align: middle;">TIDAK TERCAPAI (&gt; 14 HARI / BELUM)</th>
                            <th style="font-weight: bold; vertical-align: middle;">TOTAL</th>
                            <th style="background-color: #ebf5fb; color: #2980b9; font-weight: bold; vertical-align: middle; width: 220px;">PERSENTASE TERCAPAI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="transition: background 0.2s;">
                            <td style="font-weight: bold; color: #7f8c8d; vertical-align: middle;">1</td>
                            <td style="text-align: left; padding-left: 20px; font-weight: 600; color: #2c3e50; vertical-align: middle;">JUMLAH WO (Working Order)</td>
                            <td style="vertical-align: middle;"><span class="badge bg-green" style="padding: 5px 10px; font-size: 13px;"><?= $stats['wo']['tercapai'] ?></span></td>
                            <td style="vertical-align: middle;"><span class="badge bg-red" style="padding: 5px 10px; font-size: 13px;"><?= $stats['wo']['tidak_tercapai'] ?></span></td>
                            <td style="font-weight: 600; vertical-align: middle;"><?= $stats['wo']['total'] ?></td>
                            <td style="background-color: #f7f9fa; font-weight: 700; color: #2980b9; font-size: 15px; vertical-align: middle;"><?= $stats['wo']['persentase'] ?> %</td>
                        </tr>
                        <tr style="transition: background 0.2s;">
                            <td style="font-weight: bold; color: #7f8c8d; vertical-align: middle;">2</td>
                            <td style="text-align: left; padding-left: 20px; font-weight: 600; color: #2c3e50; vertical-align: middle;">JUMLAH BATCH (NK / Kartu Proses)</td>
                            <td style="vertical-align: middle;"><span class="badge bg-green" style="padding: 5px 10px; font-size: 13px;"><?= $stats['batch']['tercapai'] ?></span></td>
                            <td style="vertical-align: middle;"><span class="badge bg-red" style="padding: 5px 10px; font-size: 13px;"><?= $stats['batch']['tidak_tercapai'] ?></span></td>
                            <td style="font-weight: 600; vertical-align: middle;"><?= $stats['batch']['total'] ?></td>
                            <td style="background-color: #f7f9fa; font-weight: 700; color: #2980b9; font-size: 15px; vertical-align: middle;"><?= $stats['batch']['persentase'] ?> %</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detailed List Section -->
    <div class="box box-solid" style="border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <div class="box-header with-border" style="background-color: #fafbfc; border-top-left-radius: 8px; border-top-right-radius: 8px;">
            <h4 class="box-title" style="font-weight: 700; color: #34495e; font-size: 16px;">
                <i class="glyphicon glyphicon-list" style="margin-right: 8px; color: #3c8dbc;"></i> DATA KAIN YANG TARGET DELIVERY TIDAK TERCAPAI
            </h4>
        </div>
        <div class="box-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered" style="margin: 0; font-size: 13px;">
                    <thead>
                        <tr style="background-color: #f8f9fa; color: #2c3e50; font-weight: bold;">
                            <th style="width: 50px; text-align: center; vertical-align: middle;">NO</th>
                            <th style="text-align: center; vertical-align: middle;">TANGGAL WO</th>
                            <th style="vertical-align: middle;">BUYER</th>
                            <th style="vertical-align: middle;">WO</th>
                            <th style="vertical-align: middle;">MOTIF</th>
                            <th style="vertical-align: middle;">WARNA</th>
                            <th style="text-align: center; vertical-align: middle;">NK</th>
                            <th style="text-align: center; vertical-align: middle;">BUKA GREIGE</th>
                            <th style="text-align: center; vertical-align: middle;">TGL PACKING</th>
                            <th style="text-align: center; vertical-align: middle;">TARGET PACKING</th>
                            <th style="vertical-align: middle; width: 300px;">KETERANGAN</th>
                            <th style="text-align: center; vertical-align: middle; width: 90px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rawRecords)): ?>
                            <tr>
                                <td colspan="12" class="text-center text-muted" style="padding: 30px; font-size: 15px;">
                                    <i class="glyphicon glyphicon-info-sign" style="font-size: 20px; display: block; margin-bottom: 8px; color: #bdc3c7;"></i>
                                    Tidak ada data kartu proses (NK) yang lambat atau tidak tercapai untuk bulan ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($rawRecords as $record): ?>
                                <tr id="row-<?= $record['id'] ?>" style="transition: all 0.2s;">
                                    <td style="text-align: center; vertical-align: middle; font-weight: bold; color: #7f8c8d;"><?= $no++ ?></td>
                                    <td style="text-align: center; vertical-align: middle;"><?= Html::encode(date('d-M-Y', strtotime($record['tanggal_wo']))) ?></td>
                                    <td style="vertical-align: middle; font-weight: 600; color: #34495e;"><?= Html::encode($record['buyer']) ?></td>
                                    <td style="vertical-align: middle; font-weight: 500;"><?= Html::encode($record['wo_no']) ?></td>
                                    <td style="vertical-align: middle;"><?= Html::encode($record['motif']) ?></td>
                                    <td style="vertical-align: middle;"><?= Html::encode($record['warna']) ?></td>
                                    <td style="text-align: center; vertical-align: middle; font-weight: bold; color: #2c3e50;">
                                        <a href="<?= Url::to(['/processing-dyeing/view', 'id' => $record['id']]) ?>" target="_blank" style="color: #3c8dbc; text-decoration: underline;">
                                            <?= Html::encode($record['nk']) ?>
                                        </a>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle; color: #27ae60; font-weight: 600;"><?= Html::encode(date('d-M-Y', strtotime($record['buka_greige']))) ?></td>
                                    <td style="text-align: center; vertical-align: middle; font-weight: 600; color: <?= $record['tgl_packing'] === 'Belum Packing' ? '#e74c3c' : '#7f8c8d' ?>;">
                                        <?= Html::encode($record['tgl_packing']) ?>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle; color: #e67e22; font-weight: 600;"><?= Html::encode(date('d-M-Y', strtotime($record['buka_greige'] . ' + 14 days'))) ?></td>
                                    <td style="vertical-align: middle; padding: 4px;">
                                        <textarea class="form-control ket-textarea" data-id="<?= $record['id'] ?>" rows="1" style="resize: vertical; border-radius: 4px; padding: 4px 8px; font-size: 12px; transition: border-color 0.2s;" placeholder="Ketik keterangan..."><?= Html::encode($record['keterangan']) ?></textarea>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <button class="btn btn-sm btn-primary btn-save-qo" data-id="<?= $record['id'] ?>" style="border-radius: 4px; font-weight: bold; background: linear-gradient(135deg, #3498db, #2980b9); border: none; box-shadow: 0 1px 3px rgba(0,0,0,0.15); transition: all 0.2s ease;">
                                            <i class="glyphicon glyphicon-floppy-disk"></i> Simpan
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for Premium Design & Micro-animations -->
<style>
.processing-rekap-qo-index select:focus, 
.processing-rekap-qo-index textarea:focus {
    outline: none !important;
    border-color: #3c8dbc !important;
    box-shadow: 0 0 5px rgba(60, 141, 188, 0.4) !important;
}
.btn-save-qo:active {
    transform: scale(0.95);
}
.btn-save-qo:hover {
    filter: brightness(1.08);
}
.table-hover tbody tr:hover {
    background-color: #f1f6f9 !important;
}
.toast-notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #2ecc71;
    color: white;
    padding: 12px 24px;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 9999;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.3s ease;
}
.toast-notification.show {
    opacity: 1;
    transform: translateY(0);
}
</style>

<!-- Modal Grafik -->
<div class="modal fade" id="grafikModal" tabindex="-1" role="dialog" aria-labelledby="grafikModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="border-radius: 8px;">
      <div class="modal-header" style="background-color: #fafbfc; border-top-left-radius: 8px; border-top-right-radius: 8px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="grafikModalLabel" style="font-weight: 700; color: #2c3e50;">
            <i class="glyphicon glyphicon-stats"></i> Grafik Tren Persentase Tercapai (Tahun <?= date('Y') ?>)
        </h4>
      </div>
      <div class="modal-body">
        <div id="chartLoading" class="text-center" style="padding: 40px; color: #7f8c8d;">
            <i class="glyphicon glyphicon-refresh" style="animation: spin 1s linear infinite; font-size: 24px;"></i>
            <p style="margin-top: 10px;">Memuat data grafik...</p>
        </div>
        <canvas id="qoChartCanvas" style="height: 350px; width: 100%; display: none;"></canvas>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- jQuery AJAX Keterangan Save Logic -->
<?php
$this->registerJs(<<<JS
    // Toast Notification creation helper
    function showToast(message, isSuccess = true) {
        var toast = $('<div class="toast-notification"><i class="glyphicon ' + (isSuccess ? 'glyphicon-ok-sign' : 'glyphicon-remove-sign') + '"></i> ' + message + '</div>');
        if (!isSuccess) {
            toast.css('background-color', '#e74c3c');
        }
        $('body').append(toast);
        setTimeout(function() {
            toast.addClass('show');
        }, 100);
        
        setTimeout(function() {
            toast.removeClass('show');
            setTimeout(function() {
                toast.remove();
            }, 300);
        }, 3000);
    }

    $('.btn-save-qo').on('click', function() {
        var btn = $(this);
        var id = btn.data('id');
        var row = $('#row-' + id);
        var textarea = row.find('.ket-textarea');
        var keterangan = textarea.val();
        
        // Disable button & animate save state
        btn.prop('disabled', true);
        var originalHtml = btn.html();
        btn.html('<i class="glyphicon glyphicon-refresh" style="animation: spin 1s linear infinite;"></i> Menyimpan...');
        
        $.ajax({
            url: '/processing-rekap-qo/update-keterangan',
            type: 'POST',
            data: {
                id: id,
                keterangan: keterangan,
                _csrf: yii.getCsrfToken()
            },
            success: function(response) {
                btn.prop('disabled', false);
                if (response.success) {
                    btn.html('<i class="glyphicon glyphicon-ok"></i> Sukses');
                    btn.css('background', 'linear-gradient(135deg, #2ecc71, #27ae60)');
                    showToast(response.message, true);
                    
                    // Reset button to original state after 2 seconds
                    setTimeout(function() {
                        btn.html(originalHtml);
                        btn.css('background', '');
                    }, 2000);
                } else {
                    btn.html(originalHtml);
                    showToast(response.message, false);
                }
            },
            error: function() {
                btn.prop('disabled', false);
                btn.html(originalHtml);
                showToast('Gagal terhubung ke server.', false);
            }
        });
    });

    // Chart Logic
    var chartInstance = null;
    $('#grafikModal').on('shown.bs.modal', function () {
        if (chartInstance !== null) {
            return; // Already loaded
        }
        
        $.ajax({
            url: '/processing-rekap-qo/grafik-data',
            type: 'GET',
            success: function(response) {
                $('#chartLoading').hide();
                $('#qoChartCanvas').show();
                
                var ctx = document.getElementById('qoChartCanvas').getContext('2d');
                chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: response.labels,
                        datasets: [
                            {
                                label: 'Persentase WO Tercapai (%)',
                                data: response.wo,
                                borderColor: '#e74c3c',
                                backgroundColor: 'rgba(231, 76, 60, 0.1)',
                                borderWidth: 2,
                                pointBackgroundColor: '#c0392b',
                                pointRadius: 4,
                                fill: true,
                                tension: 0.3
                            },
                            {
                                label: 'Persentase Batch Tercapai (%)',
                                data: response.batch,
                                borderColor: '#f39c12',
                                backgroundColor: 'rgba(243, 156, 18, 0.1)',
                                borderWidth: 2,
                                pointBackgroundColor: '#d35400',
                                pointRadius: 4,
                                fill: true,
                                tension: 0.3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Persentase (%)'
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        }
                    }
                });
            },
            error: function() {
                $('#chartLoading').html('<p class="text-danger">Gagal memuat grafik. Coba lagi nanti.</p>');
            }
        });
    });

    // Add keyframes for rotating sync/refresh icon
    $('<style>@keyframes spin { 100% { transform: rotate(360deg); } }</style>').appendTo('head');
JS
);
?>
