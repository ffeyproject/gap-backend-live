<?php
use yii\helpers\Html;
$formatter = Yii::$app->formatter;

$bulanNama = [
    1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
    5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
    9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
];
?>

<h4 class="text-center text-primary">
    History Opname Bulan <?= $bulanNama[(int)$bulanSekarang] ?> - <?= Html::encode($namaMotif) ?>
</h4>

<!-- ✅ Ringkasan Total -->
<div class="alert alert-info text-center" style="font-size:15px;">
    <strong>Total Bulan <?= $bulanNama[(int)$bulanSebelumnya] ?>:</strong>
    <?= number_format($totalSebelumnya['total_panjang'] ?? 0, 2) ?> |
    <strong>Valid:</strong>
    <?= number_format($totalSebelumnya['total_valid'] ?? 0, 2) ?><br>
    <strong>Total Bulan <?= $bulanNama[(int)$bulanSekarang] ?>:</strong>
    <?= number_format($totalSekarang['total_panjang'] ?? 0, 2) ?> |
    <strong>Valid:</strong>
    <?= number_format($totalSekarang['total_valid'] ?? 0, 2) ?>
</div>

<!-- Tabel Harian -->
<table class="table table-bordered table-striped">
    <thead>
        <tr class="bg-info">
            <th style="width:150px;">Tanggal</th>
            <th class="text-right">Total</th>
            <th class="text-right">Valid</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($data)): ?>
        <tr>
            <td colspan="3" class="text-center text-muted">Belum ada data untuk bulan ini</td>
        </tr>
        <?php else: ?>
        <?php foreach ($data as $row): ?>
        <tr>
            <td><?= $formatter->asDate($row['date'], 'php:d M Y') ?></td>
            <td class="text-right"><?= number_format($row['total_panjang'], 2) ?></td>
            <td class="text-right"><?= number_format($row['total_valid'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>