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
                <?php
                    $shiftInfo = [];
                    if (!empty($shiftPagiFilter)) $shiftInfo[] = $shiftPagiFilter . ' (Pagi)';
                    if (!empty($shiftSiangFilter)) $shiftInfo[] = $shiftSiangFilter . ' (Siang)';
                    $shiftStr = !empty($shiftInfo) ? implode(' / ', $shiftInfo) : 'Semua Shift';
                ?>
                SHIFT: <?= Html::encode($shiftStr) ?><br>
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
                <th>Nomor WO</th>
                <th>Motif</th>
                <th>Warna</th>
                <th>Nomor Kartu</th>
                <th>Pjg</th>
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
                    
                    $warna = $isDyeing ? ($model->woColor->moColor->color ?? '-') : (!empty($model->orderPfp->dasar_warna) ? $model->orderPfp->dasar_warna : '-');
                    $nama_warna = !empty($model->nama_warna) ? $model->nama_warna : '';
                    if ($nama_warna !== '') {
                        $warna .= ' (' . $nama_warna . ')';
                    }
                    $nomor_kartu = $model->nomor_kartu;
                    
                    if ($isDyeing) {
                        $panjangTotal = (float)$model->getTrnKartuProsesDyeingItems()->sum('panjang_m');
                        $jumlahRoll = (int)$model->getTrnKartuProsesDyeingItems()->count('id');
                    } else {
                        $panjangTotal = (float)$model->getTrnKartuProsesPfpItems()->sum('panjang_m');
                        $jumlahRoll = (int)$model->getTrnKartuProsesPfpItems()->count('id');
                    }
                    $berat = is_numeric($model->berat) ? (float)$model->berat : 0;
                    
                    $totalPanjang += $panjangTotal;
                    $totalBerat += $berat;
                    $totalGul += $jumlahRoll;
                    
                    $panjangStr = number_format((float)$panjangTotal, 0);
                    $beratStr = number_format((float)$berat, 1);
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= Html::encode($shift) ?></td>
                        <td><?= Html::encode($orderNo) ?></td>
                        <td style="white-space: nowrap;"><?= Html::encode($motif) ?></td>
                        <td style="white-space: nowrap;"><?= Html::encode($warna) ?></td>
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
                <th colspan="6" class="text-right">TOTAL</th>
                <th><?= number_format((float)$totalPanjang, 0) ?></th>
                <th><?= number_format((float)$totalBerat, 1) ?></th>
                <th><?= $totalGul ?></th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
    
    <?php
    $mesinBukaGreigeIds = [];
    $processDyeing = \common\models\ar\MstProcessDyeing::findOne(1);
    if ($processDyeing) {
        foreach ($processDyeing->mstMesinProseses as $mesin) {
            if (!empty($mcFilter)) {
                if (in_array($mesin->model_mesin, $mcFilter) || in_array($mesin->nama_mesin, $mcFilter)) {
                    $mesinBukaGreigeIds[] = $mesin->id;
                }
            } else {
                $mesinBukaGreigeIds[] = $mesin->id;
            }
        }
    }

    if (!empty($mesinBukaGreigeIds) && !empty($tanggalFilter)) {
        $normalizedFilter = str_replace(' to ', ' - ', $tanggalFilter);
        $dates = explode(' - ', $normalizedFilter);
        $isRange = count($dates) == 2;
        
        $hambatanQuery = \common\models\ar\TrnHambatanMesin::find()
            ->joinWith(['trnHambatanMesinItems.mstJenisHambatans', 'trnHambatanMesinItems.mstMesinProses']);
            
        if ($isRange) {
            $hambatanQuery->where(['>=', 'trn_hambatan_mesin.tanggal', $dates[0]])
                          ->andWhere(['<=', 'trn_hambatan_mesin.tanggal', $dates[1]]);
        } else {
            $hambatanQuery->where(['trn_hambatan_mesin.tanggal' => $tanggalFilter]);
        }

        $shiftConditions = ['or'];
        if (!empty($shiftPagiFilter)) {
            $shiftConditions[] = ['trn_hambatan_mesin.shift' => $shiftPagiFilter];
        }
        if (!empty($shiftSiangFilter)) {
            $shiftConditions[] = ['trn_hambatan_mesin.shift' => $shiftSiangFilter];
        }
        if (count($shiftConditions) > 1) {
            $hambatanQuery->andWhere($shiftConditions);
        }

        $hambatanQuery->andWhere(['trn_hambatan_mesin_item.mst_mesin_proses_id' => $mesinBukaGreigeIds]);
        
        // Ensure distinct header results or we process properly
        $hambatans = $hambatanQuery->all();
        
        $hasItem = false;
        ob_start();
        echo '<div class="row" style="margin-top: 20px;">';
        echo '<div class="col-xs-12">';
        echo '<strong>HAMBATAN MESIN BUKA GREIGE:</strong>';
        echo '<table class="table table-bordered table-condensed" style="font-size: 11px; margin-top: 5px;">';
        echo '<thead><tr><th>Tanggal</th><th>Shift</th><th>Mesin</th><th>Jenis Hambatan</th><th>Mulai</th><th>Selesai</th><th>Total Menit</th><th>Keterangan</th></tr></thead>';
        echo '<tbody>';
        foreach ($hambatans as $h) {
            foreach ($h->trnHambatanMesinItems as $item) {
                if (in_array($item->mst_mesin_proses_id, $mesinBukaGreigeIds)) {
                    $hasItem = true;
                    $jenisHambatans = [];
                    foreach ($item->mstJenisHambatans as $jh) {
                        $jenisHambatans[] = $jh->nama;
                    }
                    $start_time = strtotime($h->tanggal . ' ' . $item->start_time);
                    $stop_time = strtotime($h->tanggal . ' ' . $item->stop_time);
                    $menit = 0;
                    if ($start_time && $stop_time) {
                        if ($stop_time < $start_time) {
                            $stop_time += 86400; // +1 day if crosses midnight
                        }
                        $menit = round(($stop_time - $start_time) / 60);
                    }
                    
                    echo '<tr>';
                    echo '<td>' . Html::encode(date('d M Y', strtotime($h->tanggal))) . '</td>';
                    echo '<td>' . Html::encode($h->shift) . '</td>';
                    echo '<td>' . Html::encode($item->mstMesinProses ? $item->mstMesinProses->nama_mesin : '-') . '</td>';
                    echo '<td>' . Html::encode(implode(', ', $jenisHambatans)) . '</td>';
                    echo '<td>' . Html::encode($item->start_time) . '</td>';
                    echo '<td>' . Html::encode($item->stop_time) . '</td>';
                    echo '<td>' . Html::encode($menit) . '</td>';
                    echo '<td>' . Html::encode($item->keterangan) . '</td>';
                    echo '</tr>';
                }
            }
        }
        echo '</tbody></table>';
        echo '</div></div>';
        $hambatanHtml = ob_get_clean();
        
        if ($hasItem) {
            echo $hambatanHtml;
        }
    }
    ?>
</div>
