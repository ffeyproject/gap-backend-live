<?php
use yii\helpers\Html;
use common\models\ar\MstMesinProses;

/* @var $this yii\web\View */
/* @var $searchModel yii\base\DynamicModel */
/* @var $dataProvider yii\data\ActiveDataProvider */

function formatTanggalIndoPdf($tanggal) {
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

$titleRange = 'REKAP HAMBATAN DAN TINDAKAN';
if (!empty($searchModel->tanggal_mulai) || !empty($searchModel->tanggal_selesai)) {
    $tglDari = !empty($searchModel->tanggal_mulai) ? formatTanggalIndoPdf($searchModel->tanggal_mulai) : null;
    $tglSampai = !empty($searchModel->tanggal_selesai) ? formatTanggalIndoPdf($searchModel->tanggal_selesai) : null;
    if ($tglDari && $tglSampai) {
        $titleRange .= " ({$tglDari} - {$tglSampai})";
    } elseif ($tglDari) {
        $titleRange .= " (Mulai {$tglDari})";
    } elseif ($tglSampai) {
        $titleRange .= " (Sampai {$tglSampai})";
    }
}
?>

<div class="rekap-hambatan-pdf">
    <h3 style="text-align: center; text-transform: uppercase; margin-bottom: 20px; font-family: sans-serif; font-weight: bold; color: #222;">
        <?= Html::encode($titleRange) ?>
    </h3>

    <table class="table-pdf" style="width: 100%; border-collapse: collapse; font-family: sans-serif; font-size: 10px;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="border: 1px solid #000000; padding: 6px 4px; width: 30px; text-align: center; font-weight: bold;">#</th>
                <th style="border: 1px solid #000000; padding: 6px 4px; width: 90px; text-align: center; font-weight: bold;">Tanggal</th>
                <th style="border: 1px solid #000000; padding: 6px 4px; width: 80px; text-align: center; font-weight: bold;">Mesin</th>
                <th style="border: 1px solid #000000; padding: 6px 4px; width: 80px; text-align: center; font-weight: bold;">Model Mesin</th>
                <th style="border: 1px solid #000000; padding: 6px 4px; width: 80px; text-align: center; font-weight: bold;">Nama Mesin</th>
                <th style="border: 1px solid #000000; padding: 6px 4px; width: 120px; text-align: center; font-weight: bold;">Jenis Hambatan</th>
                <th style="border: 1px solid #000000; padding: 6px 4px; text-align: left; font-weight: bold;">Keterangan</th>
                <th style="border: 1px solid #000000; padding: 6px 4px; width: 50px; text-align: center; font-weight: bold;">Lama</th>
                <th style="border: 1px solid #000000; padding: 6px 4px; width: 180px; text-align: left; font-weight: bold;">Tindakan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $models = $dataProvider->getModels();
            if (empty($models)): ?>
                <tr>
                    <td colspan="9" style="border: 1px solid #000000; padding: 10px; text-align: center; font-style: italic;">
                        Tidak ada data ditemukan.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($models as $item): 
                    $machineName = $item->mstMesinProses ? $item->mstMesinProses->nama_mesin : '-';
                    $modelName = $item->mstMesinProses ? ($item->mstMesinProses->model_mesin ?: '-') : '-';
                    
                    $jenisHambatans = [];
                    foreach ($item->mstJenisHambatans as $jh) {
                        $jenisHambatans[] = $jh->nama;
                    }
                    $hambatanStr = implode(', ', $jenisHambatans);
                    
                    // Duration
                    $durationStr = '-';
                    $tanggal = $item->trnHambatanMesin ? $item->trnHambatanMesin->tanggal : date('Y-m-d');
                    $start_time = strtotime($tanggal . ' ' . $item->start_time);
                    $stop_time = strtotime($tanggal . ' ' . $item->stop_time);
                    if ($start_time && $stop_time) {
                        if ($stop_time < $start_time) {
                            $stop_time += 86400;
                        }
                        $diffSeconds = $stop_time - $start_time;
                        $hours = floor($diffSeconds / 3600);
                        $minutes = floor(($diffSeconds % 3600) / 60);
                        $durationStr = sprintf('%02d:%02d', $hours, $minutes);
                    }
                ?>
                    <tr>
                        <td style="border: 1px solid #000000; padding: 5px 4px; text-align: center;"><?= $no++ ?></td>
                        <td style="border: 1px solid #000000; padding: 5px 4px; text-align: left;">
                            <?= Html::encode(formatTanggalIndoPdf($item->trnHambatanMesin ? $item->trnHambatanMesin->tanggal : null)) ?>
                        </td>
                        <td style="border: 1px solid #000000; padding: 5px 4px; text-align: left;"><?= Html::encode($machineName) ?></td>
                        <td style="border: 1px solid #000000; padding: 5px 4px; text-align: left;"><?= Html::encode($modelName) ?></td>
                        <td style="border: 1px solid #000000; padding: 5px 4px; text-align: left;"><?= Html::encode($machineName) ?></td>
                        <td style="border: 1px solid #000000; padding: 5px 4px; text-align: left;"><?= Html::encode($hambatanStr) ?></td>
                        <td style="border: 1px solid #000000; padding: 5px 4px; text-align: left;"><?= nl2br(Html::encode($item->keterangan)) ?></td>
                        <td style="border: 1px solid #000000; padding: 5px 4px; text-align: left;"><?= Html::encode($durationStr) ?></td>
                        <td style="border: 1px solid #000000; padding: 5px 4px; text-align: left;"><?= nl2br(Html::encode($item->tindakan)) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
