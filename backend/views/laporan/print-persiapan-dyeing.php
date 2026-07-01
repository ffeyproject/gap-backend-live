<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $models common\models\ar\TrnKartuProsesDyeing[] */
/* @var $shiftPagiFilter string */
/* @var $shiftPagiFilter string */
/* @var $shiftSiangFilter string */
/* @var $tanggalFilter string */

$groupedModels = [];
foreach ($models as $model) {
    $kpdp = $model->getKartuProcessDyeingProcesses()->andWhere(['process_id' => 1])->one();
    $data = $kpdp ? \yii\helpers\Json::decode($kpdp->value) : [];
    $shiftGroup = $data['shift_group'] ?? '-';
    $groupedModels[$shiftGroup][] = [
        'model' => $model,
        'data' => $data
    ];
}

uksort($groupedModels, function($a, $b) use ($shiftPagiFilter, $shiftSiangFilter) {
    if (isset($shiftPagiFilter) && $a === $shiftPagiFilter && $b !== $shiftPagiFilter) return -1;
    if (isset($shiftPagiFilter) && $b === $shiftPagiFilter && $a !== $shiftPagiFilter) return 1;
    if (isset($shiftSiangFilter) && $a === $shiftSiangFilter && $b !== $shiftSiangFilter) return -1;
    if (isset($shiftSiangFilter) && $b === $shiftSiangFilter && $a !== $shiftSiangFilter) return 1;
    return strcmp($a, $b);
});
?>

<?php if (empty($groupedModels)): ?>
<div class="row">
        <p style="text-align: right; font-weight: bold; margin-bottom: 5px;">
            Tanggal: <?= !empty($tanggalFilter) ? Html::encode($tanggalFilter) : '-' ?>
        </p>
        <h3 style="text-align: center; font-weight: bold;">LAPORAN HARIAN PERSIAPAN DYEING</h3>
        <table class="table custom-table small">
        <thead>
            <tr>
                <th>NO</th>
                <th>Nomor WO</th>
                <th>Motif</th>
                <th>Warna</th>
                <th>NK</th>
                <th>Pjg</th>
                <th>Berat</th>
                <th>Gul</th>
                <th>MC</th>
                <th>NR</th>
            </tr>
        </thead>
            <tr><td colspan="10">Tidak ada data.</td></tr>
        </tbody>
        </table>
    </div>
</div>
<?php else: ?>

    <?php $isFirst = true; ?>
    <?php foreach ($groupedModels as $shiftGroup => $items): ?>
        <?php if (!$isFirst): ?>
            <pagebreak />
        <?php endif; ?>
        
        <div class="row">
            <div class="col-xs-12">
                <?php
                    $shiftLabel = $shiftGroup;
                    if (isset($shiftPagiFilter) && $shiftGroup === $shiftPagiFilter) {
                        $shiftLabel .= ' (Pagi)';
                    } elseif (isset($shiftSiangFilter) && $shiftGroup === $shiftSiangFilter) {
                        $shiftLabel .= ' (Siang)';
                    }
                    
                    // Coba ambil tanggal dari item pertama jika filter tanggal kosong
                    $headerDate = !empty($tanggalFilter) ? $tanggalFilter : '-';
                    if ($headerDate === '-' && !empty($items)) {
                        $firstData = reset($items)['data'];
                        $headerDate = isset($firstData['tanggal']) && !empty($firstData['tanggal']) ? date('j/n/y', strtotime($firstData['tanggal'])) : '-';
                    }
                ?>
                <p style="text-align: right; font-weight: bold; margin-bottom: 5px;">
                    Tanggal: <?= Html::encode($headerDate) ?>
                </p>
                <h3 style="text-align: center; font-weight: bold;">LAPORAN HARIAN PERSIAPAN DYEING SHIFT: <?= Html::encode($shiftLabel) ?></h3>
                <table class="table custom-table small">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>Nomor WO</th>
                    <th>Motif</th>
                    <th>Warna</th>
                    <th>NK</th>
                    <th>Pjg</th>
                    <th>Berat</th>
                    <th>Gul</th>
                    <th>MC</th>
                    <th>NR</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $totalPanjang = 0;
                $totalGul = 0;
                
                foreach ($items as $itemData): 
                    $model = $itemData['model'];
                    $data = $itemData['data'];
                    
                    $tanggal = isset($data['tanggal']) && !empty($data['tanggal']) ? date('j/n/y', strtotime($data['tanggal'])) : '-';
                    $noMesin = $data['no_mesin'] ?? '-';
                    $keterangan = $data['keterangan'] ?? '-';
                    
                    $wo = $model->wo;
                    $greige = $wo ? $wo->greige : null;
                    
                    $woNo = $wo ? $wo->no : '-';
                    $lusi = $model->lusi ?? '';
                    $pakan = $model->pakan ?? '';
                    $motifName = $greige ? $greige->nama_kain : '';
                    $motif = trim($lusi . ' ' . $motifName . ' ' . $pakan);
                    
                    $warna = ($model->woColor && $model->woColor->moColor) ? $model->woColor->moColor->color : '-';
                    $nama_warna = !empty($model->nama_warna) ? $model->nama_warna : '';
                    if ($nama_warna !== '') {
                        $warna .= ' (' . $nama_warna . ')';
                    }
                    $nomorKartu = $model->nomor_kartu;
                    
                    $panjang = 0;
                    $gul = 0;
                    foreach ($model->trnKartuProsesDyeingItems as $item) {
                        $gul++;
                        $panjang += ($item->stock ? $item->stock->panjang_m : 0);
                    }
                    
                    $berat = is_numeric($model->berat) ? (float)$model->berat : 0;
                    $beratStr = $berat > 0 ? number_format($berat, 1) : '-';
                    
                    $totalPanjang += $panjang;
                    $totalGul += $gul;
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $woNo ?></td>
                    <td class="text-left" style="white-space: nowrap;"><?= Html::encode($motif) ?></td>
                    <td style="white-space: nowrap;"><?= Html::encode($warna) ?></td>
                    <td><?= $nomorKartu ?></td>
                    <td><?= number_format($panjang, 0) ?></td>
                    <td><?= $beratStr ?></td>
                    <td><?= $gul ?></td>
                    <td><?= Html::encode($noMesin) ?></td>
                    <td><?= Html::encode($keterangan) ?></td>
                </tr>
                <?php endforeach; ?>
                
                <tr class="footer-row">
                    <td colspan="5"></td>
                    <td><?= number_format($totalPanjang, 0) ?></td>
                    <td></td>
                    <td><?= number_format($totalGul, 0) ?></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
            </div>
        </div>
        
        <?php $isFirst = false; ?>
    <?php endforeach; ?>

<?php endif; ?>
