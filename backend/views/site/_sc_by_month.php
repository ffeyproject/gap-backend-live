<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $data array */
/* @var $currentYear string */
/* @var $buyerName string|null */

$months = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
    7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
?>

<div class="sc-by-month">
    <h4 class="text-primary" style="font-weight: bold; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 20px;">
        <i class="glyphicon glyphicon-list-alt"></i> 
        <?= $buyerName ? "Ringkasan Per Bulan: " . Html::encode($buyerName) : "Total Ringkasan Per Bulan" ?> 
        (Tahun <?= $currentYear ?>)
    </h4>
    <p class="text-info small"><i class="glyphicon glyphicon-info-sign"></i> Klik pada nama bulan untuk melihat detail Sales Contract.</p>
    <table class="table table-bordered table-striped">
        <thead>
            <tr class="bg-primary">
                <th>Bulan</th>
                <th class="text-center">Jumlah SC</th>
                <th class="text-right">Total Qty Batch</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data)): ?>
                <tr>
                    <td colspan="3" class="text-center">Tidak ada data untuk tahun ini.</td>
                </tr>
            <?php else: ?>
                <?php 
                $totalSc = 0;
                $totalQty = 0;
                foreach ($data as $row): 
                    $totalSc += $row['total_sc'];
                    $totalQty += $row['total_qty_batch'];
                ?>
                    <tr>
                        <td style="font-weight: bold;">
                            <?= Html::a($months[(int)$row['bulan']], ['sc-detail-by-month', 'month' => (int)$row['bulan'], 'buyerName' => $buyerName], [
                                'style' => 'text-decoration: underline; cursor: pointer;',
                                'class' => 'ajax-modal-click', // Gunakan class untuk handling manual jika modal sudah terbuka
                                'data-title' => 'Detail SC: ' . $months[(int)$row['bulan']] . ($buyerName ? ' - ' . $buyerName : ''),
                            ]) ?>
                        </td>
                        <td class="text-center"><?= number_format($row['total_sc']) ?></td>
                        <td class="text-right"><?= number_format($row['total_qty_batch'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="info" style="font-weight: bold;">
                    <td>TOTAL</td>
                    <td class="text-center"><?= number_format($totalSc) ?></td>
                    <td class="text-right"><?= number_format($totalQty, 2) ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
    </div>
</div>
