<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $models common\models\ar\TrnKartuProsesPfp[] */
/* @var $shiftPagiFilter string */
/* @var $shiftPagiFilter string */
/* @var $shiftSiangFilter string */
/* @var $tanggalFilter string */

$shiftGroups = [
    'A' => 'A',
    'B' => 'B',
    'C' => 'C',
    'D' => 'D',
];

$groupedModels = [];
foreach ($models as $model) {
    $processId = \common\models\ar\MstProcessPfp::find()->select('id')->where(['order' => 1])->scalar() ?: 1;
    $kpdp = $model->getKartuProcessPfpProcesses()->where(['process_id' => $processId])->one();
    $shiftGroup = '-';
    $tanggal = '-';
    
    if ($kpdp) {
        try {
            $json = \yii\helpers\Json::decode($kpdp->value);
            if (!empty($json['shift_operator'])) {
                $shiftGroup = $json['shift_operator'];
            }
        } catch (\Throwable $t) {}
    }
    
    if (!isset($groupedModels[$shiftGroup])) {
        $groupedModels[$shiftGroup] = [];
    }
    $groupedModels[$shiftGroup][] = $model;
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
        <h3 style="text-align: center; font-weight: bold;">LAPORAN HARIAN PERSIAPAN PFP</h3>
        <table class="table table-bordered small">
        <thead>
            <tr>
                <th>NO</th>
                <th>Nomor Order</th>
                <th>Motif</th>
                <th>NK</th>
                <th>Pjg</th>
                <th>Berat</th>
                <th>Gul</th>
                <th>MC</th>
                <th>NR</th>
            </tr>
        </thead>
            <tr><td colspan="9">Tidak ada data.</td></tr>
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
                        $firstModel = reset($items);
                        $processIdFirst = \common\models\ar\MstProcessPfp::find()->select('id')->where(['order' => 1])->scalar() ?: 1;
                        $kpdpFirst = $firstModel->getKartuProcessPfpProcesses()->where(['process_id' => $processIdFirst])->one();
                        if ($kpdpFirst) {
                            $jsonFirst = \yii\helpers\Json::decode($kpdpFirst->value);
                            $headerDate = isset($jsonFirst['tanggal']) && !empty($jsonFirst['tanggal']) ? date('j/n/y', strtotime($jsonFirst['tanggal'])) : '-';
                        }
                    }
                ?>
                <p style="text-align: right; font-weight: bold; margin-bottom: 5px;">
                    Tanggal: <?= Html::encode($headerDate) ?>
                </p>
                <h3 style="text-align: center; font-weight: bold;">LAPORAN HARIAN PERSIAPAN PFP SHIFT: <?= Html::encode($shiftLabel) ?></h3>
                <table class="table table-bordered small">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>Nomor Order</th>
                    <th>Motif</th>
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
                
                foreach ($items as $model): 
                    $processId = \common\models\ar\MstProcessPfp::find()->select('id')->where(['order' => 1])->scalar() ?: 1;
                    $kpdp = $model->getKartuProcessPfpProcesses()->where(['process_id' => $processId])->one();
                    $tanggal = '-';
                    $noMesin = '-';
                    $keterangan = '-';
                    $gangguan = '-';
                    
                    if ($kpdp) {
                        try {
                            $json = \yii\helpers\Json::decode($kpdp->value);
                            if (!empty($json['tanggal'])) $tanggal = date('j/n/y', strtotime($json['tanggal']));
                            if (!empty($json['no_mesin'])) $noMesin = $json['no_mesin'];
                            
                            $ketArr = [];
                            if (!empty($json['keterangan'])) $ketArr[] = trim($json['keterangan']);
                            if (!empty($json['gangguan_produksi'])) $ketArr[] = trim($json['gangguan_produksi']);
                            if (!empty($ketArr)) $keterangan = implode(', ', $ketArr);
                        } catch (\Throwable $t) {}
                    }
                    if ($tanggal === '-' && $model->tanggalKartuProcessPfpProcess) {
                        $tanggal = date('j/n/y', strtotime($model->tanggalKartuProcessPfpProcess));
                    }
                    
                    $orderNo = $model->orderPfp ? $model->orderPfp->no : '-';
                    
                    $lusi  = $model->lusi ?? '';
                    $motifName = $model->greige->nama_kain ?? '';
                    $pakan = $model->pakan ?? '';
                    $motif = trim($lusi . ' ' . $motifName . ' ' . $pakan);
                    
                    $nomorKartu = $model->nomor_kartu;
                    
                    $panjang = 0;
                    $gul = 0;
                    foreach ($model->trnKartuProsesPfpItems as $item) {
                        $gul++;
                        $panjang += (float)($item->panjang_m ?? 0);
                    }
                    
                    $berat = $model->berat ? number_format((float)$model->berat, 1) : '-';
                    
                    $totalPanjang += $panjang;
                    $totalGul += $gul;
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $orderNo ?></td>
                    <td class="text-left" style="white-space: nowrap;"><?= Html::encode($motif) ?></td>
                    <td><?= $nomorKartu ?></td>
                    <td><?= number_format($panjang, 0) ?></td>
                    <td><?= $berat ?></td>
                    <td><?= $gul ?></td>
                    <td><?= Html::encode($noMesin) ?></td>
                    <td><?= Html::encode($keterangan) ?></td>
                </tr>
                <?php endforeach; ?>
                
                <tr class="footer-row">
                    <td colspan="4"></td>
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
