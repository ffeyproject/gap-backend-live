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

<div class="alert alert-info text-center" style="font-size:15px;">
    <strong>Total Bulan <?= $bulanNama[(int)$bulanSebelumnya] ?>:</strong>
    <?= number_format($totalSebelumnya['total_panjang'] ?? 0, 2) ?> |
    <strong>Valid:</strong>
    <?= number_format($totalSebelumnya['total_valid'] ?? 0, 2) ?><br>
    <strong>Total Bulan <?= $bulanNama[(int)$bulanSekarang] ?>:</strong>
    <?= number_format($totalSekarang['total_panjang'] ?? 0, 2) ?> |
    <strong>Valid:</strong>
    <?= number_format($totalSekarang['total_valid'] ?? 0, 2) ?><br>
    <strong>Keluaran Dyeing Bulan Ini:</strong>
    <?= number_format($totalKeluarDyeing ?? 0, 2) ?> |
    <strong>Keluaran PFP Bulan Ini:</strong>
    <?= number_format($totalKeluarPfp ?? 0, 2) ?>
</div>

<table class="table table-bordered table-striped">
    <thead>
        <tr class="bg-info">
            <th style="width:150px;">Tanggal</th>
            <th class="text-right">Total</th>
            <th class="text-right">Valid</th>
            <th class="text-right">Keluaran Dyeing</th>
            <th class="text-right">Keluaran PFP</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($data)): ?>
        <tr>
            <td colspan="5" class="text-center text-muted">Belum ada data untuk bulan ini</td>
        </tr>
        <?php else: ?>
        <?php foreach ($data as $row): ?>
        <tr>
            <td><?= $formatter->asDate($row['date'], 'php:d M Y') ?></td>
            <td class="text-right"><?= number_format($row['total_panjang'], 2) ?></td>
            <td class="text-right"><?= number_format($row['total_valid'], 2) ?></td>
            <td class="text-right"><?= number_format($row['keluar_dyeing'] ?? 0, 2) ?></td>
            <td class="text-right"><?= number_format($row['keluar_pfp'] ?? 0, 2) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>