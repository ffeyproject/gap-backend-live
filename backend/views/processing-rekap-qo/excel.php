<?php
use yii\helpers\Html;

/* @var $month string */
/* @var $records array */
/* @var $stats array */

$evaluatedMonthTime = strtotime($month . '-01');
$evaluatedMonthLabel = date('F Y', $evaluatedMonthTime);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        .title {
            font-family: Arial, sans-serif;
            font-size: 16px;
            font-weight: bold;
            text-align: left;
            margin-bottom: 15px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #555555;
            padding: 6px 8px;
        }
        th {
            background-color: #ECF0F5;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        .badge-green {
            color: #27ae60;
            font-weight: bold;
        }
        .badge-red {
            color: #e74c3c;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="title">REKAP REALISASI QO BULAN <?= Html::encode(strtoupper($evaluatedMonthLabel)) ?></div>
    
    <!-- Rekap Summary Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">NO</th>
                <th class="text-left" style="width: 250px;">KATEGORI</th>
                <th>TERCAPAI (&le; 14 HARI)</th>
                <th>TIDAK TERCAPAI (&gt; 14 HARI / BELUM)</th>
                <th>TOTAL</th>
                <th style="background-color: #D9EDF7; width: 180px;">PERSENTASE TERCAPAI</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td class="text-left">JUMLAH WO (Working Order)</td>
                <td class="text-center"><?= $stats['wo']['tercapai'] ?></td>
                <td class="text-center"><?= $stats['wo']['tidak_tercapai'] ?></td>
                <td class="text-center"><?= $stats['wo']['total'] ?></td>
                <td class="text-center" style="font-weight: bold; background-color: #F5F5F5;"><?= $stats['wo']['persentase'] ?> %</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td class="text-left">JUMLAH BATCH (NK / Kartu Proses)</td>
                <td class="text-center"><?= $stats['batch']['tercapai'] ?></td>
                <td class="text-center"><?= $stats['batch']['tidak_tercapai'] ?></td>
                <td class="text-center"><?= $stats['batch']['total'] ?></td>
                <td class="text-center" style="font-weight: bold; background-color: #F5F5F5;"><?= $stats['batch']['persentase'] ?> %</td>
            </tr>
        </tbody>
    </table>

    <br><br>

    <div class="title" style="font-size: 14px;">DATA KAIN YANG TARGET DELIVERY TIDAK TERCAPAI</div>

    <!-- Detailed Late List Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">NO</th>
                <th>TANGGAL WO</th>
                <th>BUYER</th>
                <th>WO</th>
                <th>MOTIF</th>
                <th>WARNA</th>
                <th>NK</th>
                <th>BUKA GREIGE</th>
                <th>TGL PACKING</th>
                <th>TARGET PACKING</th>
                <th style="width: 300px;">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($records)): ?>
                <tr>
                    <td colspan="11" class="text-center" style="font-style: italic; color: #7f8c8d; padding: 20px;">
                        Tidak ada data kartu proses (NK) yang lambat atau tidak tercapai untuk bulan ini.
                    </td>
                </tr>
            <?php else: ?>
                <?php 
                $rowspans = [
                    'tanggal_wo' => [], 'buyer' => [], 'wo_no' => [], 'motif' => [], 'warna' => []
                ];
                $cols = ['tanggal_wo', 'buyer', 'wo_no', 'motif', 'warna'];
                $startIndices = array_fill_keys($cols, 0);
                $prevValues = array_fill_keys($cols, null);

                foreach ($records as $index => $record) {
                    $currentValues = [];
                    $currentValues['tanggal_wo'] = $record['tanggal_wo'] !== '-' ? date('j/n', strtotime($record['tanggal_wo'])) : '-';
                    $currentValues['buyer'] = $record['buyer'];
                    $currentValues['wo_no'] = $record['wo_no'];
                    $currentValues['motif'] = $record['motif'];
                    $currentValues['warna'] = $record['warna'];

                    foreach ($cols as $col) {
                        $rowspans[$col][$index] = 1;
                    }

                    if ($index === 0) {
                        $prevValues = $currentValues;
                        continue;
                    }

                    $isSame = true;
                    foreach ($cols as $col) {
                        if ($isSame && $currentValues[$col] === $prevValues[$col]) {
                            $rowspans[$col][$startIndices[$col]]++;
                            $rowspans[$col][$index] = 0;
                        } else {
                            $isSame = false;
                            $startIndices[$col] = $index;
                            $prevValues[$col] = $currentValues[$col];
                        }
                    }
                }
                
                $no = 1; 
                foreach ($records as $index => $record): 
                ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        
                        <?php if ($rowspans['tanggal_wo'][$index] > 0): ?>
                            <td class="text-center" rowspan="<?= $rowspans['tanggal_wo'][$index] ?>" style="vertical-align: middle; mso-number-format:'\@';"><?= Html::encode($record['tanggal_wo'] !== '-' ? date('j/n', strtotime($record['tanggal_wo'])) : '-') ?></td>
                        <?php endif; ?>
                        
                        <?php if ($rowspans['buyer'][$index] > 0): ?>
                            <td rowspan="<?= $rowspans['buyer'][$index] ?>" style="vertical-align: middle;"><?= Html::encode($record['buyer']) ?></td>
                        <?php endif; ?>
                        
                        <?php if ($rowspans['wo_no'][$index] > 0): ?>
                            <td rowspan="<?= $rowspans['wo_no'][$index] ?>" style="vertical-align: middle;"><?= Html::encode($record['wo_no']) ?></td>
                        <?php endif; ?>
                        
                        <?php if ($rowspans['motif'][$index] > 0): ?>
                            <td rowspan="<?= $rowspans['motif'][$index] ?>" style="vertical-align: middle;"><?= Html::encode($record['motif']) ?></td>
                        <?php endif; ?>
                        
                        <?php if ($rowspans['warna'][$index] > 0): ?>
                            <td rowspan="<?= $rowspans['warna'][$index] ?>" style="vertical-align: middle;"><?= Html::encode($record['warna']) ?></td>
                        <?php endif; ?>
                        
                        <td class="text-center" style="font-weight: bold; mso-number-format:'\@';"><?= Html::encode($record['nk']) ?></td>
                        <td class="text-center" style="color: #27ae60; font-weight: bold; mso-number-format:'\@';"><?= Html::encode($record['buka_greige'] !== '-' ? date('j/n', strtotime($record['buka_greige'])) : '-') ?></td>
                        <td class="text-center" style="font-weight: bold; color: <?= $record['tgl_packing'] === 'Belum Packing' ? '#e74c3c' : '#7f8c8d' ?>; mso-number-format:'\@';">
                            <?= Html::encode($record['tgl_packing'] === 'Belum Packing' ? 'Belum Packing' : date('j/n', strtotime($record['tgl_packing']))) ?>
                        </td>
                        <td class="text-center" style="color: #e67e22; font-weight: bold; mso-number-format:'\@';">
                            <?= Html::encode(date('j/n', strtotime($record['buka_greige'] . ' + 14 days'))) ?>
                        </td>
                        <td><?= Html::encode($record['keterangan']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
