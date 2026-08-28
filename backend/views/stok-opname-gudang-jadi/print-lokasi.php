<?php

/* @var $this yii\web\View */
/* @var $locs_code string */
/* @var $opname_code string */
/* @var $groups array */
/* @var $totalSummary array */

use common\models\ar\TrnStockGreige;
use yii\helpers\Html;

$this->title = 'Print Lembar Palet - Lokasi: ' . ($locs_code ?: 'TRANSIT');
?>

<div class="box box-solid">
    <div class="box-header with-border hidden-print">
        <h3 class="box-title"><i class="fa fa-print"></i> PRATINJAU CETAK LEMBAR PALET LOKASI</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="fa fa-print"></i> Cetak Dokumen</button>
        </div>
    </div>
    <div class="box-body" id="print-palet-content">
        <style type="text/css">
            @media print {
                .hidden-print, .main-footer { display: none !important; }
                body { font-family: "Courier New", Courier, monospace, sans-serif; font-size: 11px; margin: 5mm; padding: 0; }
                @page { size: auto; margin: 0mm; }
                .table-palet { page-break-inside: auto; }
                .table-palet tr { page-break-inside: avoid; page-break-after: auto; }
            }
            .palet-wrapper {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 11px;
                color: #000;
                max-width: 900px;
                margin: 0 auto;
            }
            .palet-header-table {
                width: 100%;
                margin-bottom: 8px;
            }
            .palet-header-table td {
                vertical-align: top;
            }
            .palet-title-box {
                background-color: #777;
                color: #fff;
                font-weight: bold;
                font-size: 16px;
                text-align: center;
                padding: 6px;
                letter-spacing: 1px;
            }
            .table-palet {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 10px;
            }
            .table-palet th, .table-palet td {
                border: 1px solid #444;
                padding: 3px 4px;
                font-size: 10px;
            }
            .table-palet th {
                text-align: center;
                background-color: #f2f2f2;
                font-weight: bold;
                text-transform: uppercase;
            }
            .piece-cell {
                width: 28px;
                text-align: center;
            }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .summary-box {
                width: 320px;
                border-collapse: collapse;
                margin-top: 10px;
            }
            .summary-box td {
                padding: 2px 5px;
                font-size: 11px;
                border: none;
            }
        </style>

        <div class="palet-wrapper">
            <!-- Header Table -->
            <table class="palet-header-table">
                <tr>
                    <td width="35%">
                        <strong>PALET NO :</strong> <?= Html::encode($locs_code ?: 'TRANSIT') ?><br>
                        <strong>CHECKER 1 :</strong> _____________<br>
                        <strong>CHECKER 2 :</strong> _____________
                    </td>
                    <td width="40%" class="text-center">
                        <div class="palet-title-box">
                            PALET: <?= Html::encode($locs_code ?: 'TRANSIT') ?>
                        </div>
                    </td>
                    <td width="25%" class="text-right">
                        <span>Page 1 of 1</span><br>
                        <strong><?= date('d F Y') ?></strong>
                    </td>
                </tr>
            </table>

            <!-- Main Items Table -->
            <table class="table-palet">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 22%;">NO DO / MOTIF</th>
                        <th rowspan="2" style="width: 12%;">COLOR</th>
                        <th colspan="10">PIECE LENGTH</th>
                        <th rowspan="2" style="width: 8%;">GRADE</th>
                        <th colspan="2">TOTAL</th>
                    </tr>
                    <tr>
                        <th class="piece-cell">1</th>
                        <th class="piece-cell">2</th>
                        <th class="piece-cell">3</th>
                        <th class="piece-cell">4</th>
                        <th class="piece-cell">5</th>
                        <th class="piece-cell">6</th>
                        <th class="piece-cell">7</th>
                        <th class="piece-cell">8</th>
                        <th class="piece-cell">9</th>
                        <th class="piece-cell">10</th>
                        <th style="width: 6%;">PCS</th>
                        <th style="width: 8%;">YARD</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($groups)): ?>
                        <tr>
                            <td colspan="15" class="text-center">Tidak ada data stok di lokasi ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($groups as $g): ?>
                            <?php 
                                $pieces = $g['pieces'];
                                $chunks = array_chunk($pieces, 10);
                                $totalRows = count($chunks) ?: 1;
                            ?>
                            <?php foreach ($chunks as $rowIdx => $chunk): ?>
                                <tr>
                                    <?php if ($rowIdx === 0): ?>
                                        <td rowspan="<?= $totalRows ?>">
                                            <strong><?= Html::encode($g['motif']) ?></strong>
                                        </td>
                                        <td rowspan="<?= $totalRows ?>">
                                            <?= Html::encode($g['color']) ?>
                                        </td>
                                    <?php endif; ?>

                                    <!-- 10 Piece Length Columns -->
                                    <?php for ($i = 0; $i < 10; $i++): ?>
                                        <td class="piece-cell">
                                            <?= isset($chunk[$i]) ? (float)$chunk[$i]['qty'] : '' ?>
                                        </td>
                                    <?php endfor; ?>

                                    <?php if ($rowIdx === 0): ?>
                                        <td rowspan="<?= $totalRows ?>" class="text-center">
                                            <?= Html::encode($g['grade_name']) ?>
                                        </td>
                                        <td rowspan="<?= $totalRows ?>" class="text-center">
                                            <strong><?= $g['total_pcs'] ?></strong>
                                        </td>
                                        <td rowspan="<?= $totalRows ?>" class="text-right">
                                            <strong><?= Yii::$app->formatter->asDecimal($g['total_qty'], 0) ?></strong>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Summary Table -->
            <table class="summary-box" style="margin-top: 10px; width: 320px;">
                <tr>
                    <td width="100"><strong>TOTAL :</strong></td>
                    <td width="50" class="text-right"><strong><?= $totalSummary['total_pcs'] ?></strong></td>
                    <td width="20" class="text-center">:</td>
                    <td class="text-right"><strong><?= Yii::$app->formatter->asDecimal($totalSummary['total_qty'], 0) ?></strong></td>
                    <td><strong>YARD</strong></td>
                </tr>
                <?php foreach ($totalSummary['grades'] as $gradeLabel => $gStat): ?>
                    <tr>
                        <td>GRADE <?= Html::encode($gradeLabel) ?> :</td>
                        <td class="text-right"><?= $gStat['pcs'] ?></td>
                        <td class="text-center">:</td>
                        <td class="text-right"><?= Yii::$app->formatter->asDecimal($gStat['qty'], 0) ?></td>
                        <td>YARD</td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <!-- Note / Keterangan Mutasi Stok (Hanya Muncul Jika Ada Data History) -->
            <?php 
                $filteredNotes = [];
                if (!empty($notes)) {
                    foreach (array_unique($notes) as $rawNote) {
                        $parts = explode('|', $rawNote);
                        foreach ($parts as $noteText) {
                            $noteText = trim($noteText);
                            if (empty($noteText)) continue;
                            if (strpos($noteText, 'Hasil Pemotongan') !== false || strpos($noteText, 'Dari inspecting') !== false || strpos($noteText, 'Barang Keluar:') !== false) {
                                continue;
                            }
                            if (strpos($noteText, 'Pemotongan ID:') !== false && strpos($noteText, 'dipotong') === false) {
                                if (preg_match('/Pemotongan ID:\s*(\d+)/i', $noteText, $matches)) {
                                    $potongId = (int)$matches[1];
                                    $potongModel = \common\models\ar\TrnPotongStock::findOne($potongId);
                                    if ($potongModel && $potongModel->stock) {
                                        $initialQty = (float)$potongModel->stock->qty;
                                        $pItems = [];
                                        foreach ($potongModel->trnPotongStockItems as $pItem) {
                                            $pItems[] = (float)$pItem->qty;
                                        }
                                        $sumP = array_sum($pItems);
                                        $remP = $initialQty - $sumP;
                                        if ($remP > 0) {
                                            $pItems[] = (float)$remP;
                                        }
                                        $filteredNotes[] = 'Pemotongan ID: ' . $potongId . ' qty: ' . $initialQty . ' dipotong ' . implode(' dan ', $pItems);
                                    } else {
                                        $filteredNotes[] = $noteText;
                                    }
                                } else {
                                    $filteredNotes[] = $noteText;
                                }
                            } else {
                                $filteredNotes[] = $noteText;
                            }
                        }
                    }
                    $filteredNotes = array_values(array_unique($filteredNotes));
                }
            ?>
            <?php if (!empty($filteredNotes)): ?>
                <table style="width: 100%; border: 1px dashed #666; padding: 6px; font-size: 10px; margin-top: 15px;">
                    <tr>
                        <td colspan="2" style="font-weight: bold; border-bottom: 1px dashed #ccc; padding-bottom: 4px; margin-bottom: 4px;">
                            Catatan / Keterangan Mutasi Stok Palet Ini:
                        </td>
                    </tr>
                    <?php foreach ($filteredNotes as $noteText): ?>
                        <tr>
                            <td width="15" style="vertical-align: top; font-weight: bold;">=</td>
                            <td style="padding-bottom: 3px;"><?= Html::encode($noteText) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
