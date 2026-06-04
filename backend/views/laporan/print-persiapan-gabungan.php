<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ArrayDataProvider */
/* @var $mcFilter array */
/* @var $shiftPagiFilter string */
/* @var $shiftSiangFilter string */
/* @var $tanggalFilter string */

$this->title = 'Print Laporan Persiapan (Gabungan)';
?>
<div class="print-persiapan-gabungan">
    
    <div class="row">
        <div class="col-xs-12">
            <h3 class="text-center">LAPORAN PERSIAPAN (DYEING & PFP)</h3>
            <p class="text-center">
                <?= !empty($tanggalFilter) ? "Tanggal: " . $tanggalFilter : "" ?><br>
                <?= !empty($mcFilter) ? "MC: " . implode(', ', $mcFilter) : "" ?>
            </p>
        </div>
    </div>
    
    <table class="table table-bordered table-condensed" style="font-size: 12px;">
        <thead>
            <tr>
                <th>No</th>
                <th>SHIFT</th>
                <th>Tanggal</th>
                <th>Nomor WO</th>
                <th>Motif</th>
                <th>Warna</th>
                <th>Nomor Kartu</th>
                <th>Panjang</th>
                <th>Berat</th>
                <th>Gul</th>
                <th>MC</th>
                <th>Ket.</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $models = $dataProvider->getModels();
            $totalPanjang = 0;
            $totalBerat = 0;
            $totalGul = 0;
            if (empty($models)) {
                echo '<tr><td colspan="12" class="text-center">Tidak ada data</td></tr>';
            } else {
                $no = 1;
                foreach ($models as $model) {
                    $isDyeing = $model->tipe_laporan === 'Dyeing';
                    $processId = $isDyeing ? 1 : (\common\models\ar\MstProcessPfp::find()->select('id')->where(['order'=>1])->scalar() ?: 1);
                    $kpdp = $isDyeing ? $model->getKartuProcessDyeingProcesses()->where(['process_id'=>$processId])->one() : $model->getKartuProcessPfpProcesses()->where(['process_id'=>$processId])->one();
                    
                    $tanggal = '-';
                    $shift = '-';
                    $mc = '-';
                    $ket = '-';
                    if ($kpdp) {
                        try {
                            $json = \yii\helpers\Json::decode($kpdp->value);
                            if (!empty($json['tanggal'])) $tanggal = date('j M Y', strtotime($json['tanggal']));
                            if ($isDyeing && !empty($json['shift_group'])) $shift = $json['shift_group'];
                            if (!$isDyeing && !empty($json['shift_operator'])) $shift = $json['shift_operator'];
                            if (!empty($json['no_mesin'])) $mc = $json['no_mesin'];
                            if (!empty($json['keterangan'])) $ket = $json['keterangan'];
                        } catch (\Throwable $t) {}
                    }
                    if ($tanggal === '-') {
                        $fallbackDate = $isDyeing ? $model->tanggalKartuProcessDyeingProcess : $model->tanggalKartuProcessPfpProcess;
                        if ($fallbackDate) $tanggal = date('j M Y', strtotime($fallbackDate));
                    }
                    
                    $orderNo = $isDyeing ? ($model->wo ? $model->wo->no : '-') : ($model->orderPfp ? $model->orderPfp->no : '-');
                    
                    $lusi = $model->lusi ?? '';
                    $pakan = $model->pakan ?? '';
                    $motifName = $isDyeing ? ($model->wo->greigeNamaKain ?? '') : ($model->greige->nama_kain ?? '');
                    $motif = trim($lusi . ' ' . $motifName . ' ' . $pakan);
                    
                    $warna = $isDyeing ? ($model->woColor->moColor->color ?? '-') : '-';
                    $nomor_kartu = $model->nomor_kartu;
                    
                    if ($isDyeing) {
                        $panjangTotal = $model->getTrnKartuProsesDyeingItems()->sum('panjang_m') ?: 0;
                        $jumlahRoll = $model->getTrnKartuProsesDyeingItems()->count('id') ?: 0;
                    } else {
                        $panjangTotal = $model->getTrnKartuProsesPfpItems()->sum('panjang_m') ?: 0;
                        $jumlahRoll = $model->getTrnKartuProsesPfpItems()->count('id') ?: 0;
                    }
                    $berat = $model->berat ?: 0;
                    
                    $totalPanjang += $panjangTotal;
                    $totalBerat += $berat;
                    $totalGul += $jumlahRoll;
                    
                    $panjangStr = number_format((float)$panjangTotal, 0);
                    $beratStr = number_format((float)$berat, 1);
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= Html::encode($shift) ?></td>
                        <td><?= Html::encode($tanggal) ?></td>
                        <td><?= Html::encode($orderNo) ?></td>
                        <td><?= Html::encode($motif) ?></td>
                        <td><?= Html::encode($warna) ?></td>
                        <td><?= Html::encode($nomor_kartu) ?></td>
                        <td><?= Html::encode($panjangStr) ?></td>
                        <td><?= Html::encode($beratStr) ?></td>
                        <td><?= Html::encode($jumlahRoll) ?></td>
                        <td><?= Html::encode($mc) ?></td>
                        <td><?= Html::encode($ket) ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
        <?php if (!empty($models)): ?>
        <tfoot>
            <tr>
                <th colspan="7" class="text-right">TOTAL</th>
                <th><?= number_format((float)$totalPanjang, 0) ?></th>
                <th><?= number_format((float)$totalBerat, 1) ?></th>
                <th><?= $totalGul ?></th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
</div>
