<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimBuyerHeader */
/* @var $dataProviderKirimBuyer \yii\data\ActiveDataProvider*/
/* @var $formatter \yii\i18n\Formatter */

use common\models\ar\{ TrnKirimBuyer, MstGreigeGroup, TrnStockGreige , TrnScGreige };
use yii\helpers\Html;

?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">PACKING LIST</h3>
        <div class="box-tools pull-right">
            <?=Html::button('<i class="fa fa-print" aria-hidden="true"></i>', ['class'=>'btn btn-default btn-xs', 'onclick'=>'printDivPL("packingList")'])?>
        </div>
    </div>
    <div class="box-body" id="packingList">
        <style type="text/css">
        #packingList {
            font-family: Calibri, "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 12px;
        }
        #packingList .piece-cell {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }
        #packingList .piece-cell.empty {
            background-color: transparent;
        }
        #packingList table.pl-table {
            width: 100%;
            border-collapse: collapse;
        }
        #packingList table.pl-table,
        #packingList table.pl-table > thead > tr > th,
        #packingList table.pl-table > tbody > tr > td {
            border: 1px solid #000;
        }
        /* Nested tables inside piece length */
        #packingList table.pl-nested {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        #packingList table.pl-nested > tbody > tr > td,
        #packingList table.pl-nested > tr > td {
            border: none;
        }
        #packingList table.pl-pieces {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border: none;
        }
        #packingList table.pl-pieces td {
            border: 1px solid #000;
        }
        #packingList table.pl-pieces tr:first-child td {
            border-top: none;
        }
        #packingList table.pl-pieces tr:last-child td {
            border-bottom: none;
        }
        #packingList table.pl-pieces tr td:first-child {
            border-left: none;
        }
        #packingList table.pl-pieces tr td:last-child {
            border-right: none;
        }
        </style>
        <table width="100%">
            <tr>
                <td width="40%">
                    <?php //= Html::img(Yii::getAlias('@web/images/logo/logo-gap.png'), ['style'=>'height:65px;', 'onerror'=>"this.src='/gap/backend/web/images/logo/logo-gap.png';"])?><!-- <br> -->
                </td>
                <td width="20%"><strong>PACKING LIST</strong></td>
                <td width="40%" style="text-align: right;"><strong>GAP-FRM-GDJ-02</strong></td>
            </tr>
        </table>

        <p></p>

        <table width="100%">
            <tr>
                <td width="50%" style="text-align: left;">
                    <span style="margin-right: 15px;">NAMA BUYER :</span> <?=$model->nama_buyer?> <br>
                    <span style="margin-right: 15px;">ALAMAT :</span> <?=$model->alamat_buyer?>

                </td>
                <td width="50%" style="text-align: right;">
                    <span style="margin-right: 15px;">TANGGAL KIRIM :</span> <?=$formatter->asDate($model->date)?>
                </td>
            </tr>
        </table>

        <p style="margin: 4px 0;"></p>

        <table class="pl-table">
            <thead>
                <tr>
                    <th width="3%" rowspan="2" style="text-align: center;">BAL</th>
                    <th rowspan="2" style="text-align: center;">NO WO</th>
                    <th rowspan="2" style="text-align: center;">DESIGN</th>
                    <th colspan="3" style="text-align: center;">JUMLAH</th>
                    <th rowspan="2" style="text-align: center;">PIECE LENGTH</th>
                </tr>
                <tr>
                    <th style="text-align: center;">COLOR</th>
                    <th width="5%" style="text-align: center;">SATUAN 1</th>
                    <th style="text-align: center;">SATUAN 2</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $groupedByNoBal = [];
                foreach ($dataProviderKirimBuyer->models as $kirimBuyerModel) {
                    foreach ($kirimBuyerModel->trnKirimBuyerItems as $pssk) {
                        $no_bal = $pssk['no_bal'];
                        $qty = $pssk['qty'];
                        $no_wo = $kirimBuyerModel->wo ? $kirimBuyerModel->wo->no : '-';
                        $color = $pssk->stock ? $pssk->stock->color : '';
                        $grade = $pssk->stock ? $pssk->stock->grade : '';
                        $design = $kirimBuyerModel->nama_kain_alias ?: '-';
                        $artikel = ($kirimBuyerModel->wo && $kirimBuyerModel->wo->mo) ? $kirimBuyerModel->wo->mo->article : ($kirimBuyerModel->nama_kain_alias ?: '-');
                        $unit = $kirimBuyerModel->unit;
                        $jenisProcess = ($kirimBuyerModel->wo && $kirimBuyerModel->wo->mo) ? $kirimBuyerModel->wo->mo->process : null;
                        $kodeDesign = ($kirimBuyerModel->wo && $kirimBuyerModel->wo->mo) ? $kirimBuyerModel->wo->mo->design : '';
                        $batch = ($pssk->stock && method_exists($pssk->stock, 'getBatchNo')) ? $pssk->stock->getBatchNo() : (($pssk->stock && method_exists($pssk->stock, 'getNoLot')) ? $pssk->stock->noLot : '-');
                        $noLot = ($pssk->stock && method_exists($pssk->stock, 'getLotNo')) ? $pssk->stock->getLotNo() : '-';
                        $note = $kirimBuyerModel->note ?: '-';

                        if($grade === TrnStockGreige::GRADE_A_ASTERISK || $grade === TrnStockGreige::GRADE_A_PLUS){
                            $grade = TrnStockGreige::GRADE_A;
                        }

                        if (!isset($groupedByNoBal[$no_bal])) {
                            $groupedByNoBal[$no_bal] = [];
                        }

                        if (!isset($groupedByNoBal[$no_bal][$no_wo])) {
                            $groupedByNoBal[$no_bal][$no_wo] = [
                                'colors' => [],
                                'total_qty' => 0,
                                'jenis_process' => $jenisProcess,
                                'artikel' => $artikel,
                                'design' => $kodeDesign,
                                'note' => $note,
                                'unit' => $unit,
                            ];
                        }

                        // Sum the total quantity for this no_wo
                        $groupedByNoBal[$no_bal][$no_wo]['total_qty'] += $qty;

                        // Check if the color already exists and append the qty
                        if (!isset($groupedByNoBal[$no_bal][$no_wo]['colors'][$color])) {
                            $groupedByNoBal[$no_bal][$no_wo]['colors'][$color] = [];
                        }

                        $groupedByNoBal[$no_bal][$no_wo]['colors'][$color]['batch'][$batch]['lots'][$noLot]['grade'][$grade][] = [
                            'grade_id' => $grade,
                            'grade' => TrnStockGreige::gradeOptions()[$grade],
                            'qty' => $qty,
                        ];
                    }
                }
                ksort($groupedByNoBal);
                $totalSatuan1 = 0;
                $totalSatuan2 = [];
            ?>

                <?php foreach ($groupedByNoBal as $no_bal => $woGroup): ?>
                <?php
                $balRowspan = 0;
                foreach ($woGroup as $no_wo => $colorGroup) {
                    foreach ($colorGroup['colors'] as $color => $batches) {
                        $balRowspan += count($batches['batch']);
                    }
                }
                ?>
                <tr>
                    <td rowspan="<?= $balRowspan ?>" style="text-align: center;"><?= $no_bal ?></td>
                    <?php foreach ($woGroup as $no_wo => $colorGroup): ?>
                    <?php
                    $woRowspan = 0;
                    foreach ($colorGroup['colors'] as $color => $batches) {
                        $woRowspan += count($batches['batch']);
                    }
                    $isFirstColorRow = true;
                    ?>
                    <td rowspan="<?= $woRowspan ?>" style="text-align: center;"><?= $no_wo ?></td>

                    <!-- design -->
                    <?php if ($jenisProcess == TrnScGreige::PROCESS_PRINTING || $jenisProcess == TrnScGreige::PROCESS_DIGITAL_PRINTING) { ?>
                    <td rowspan="<?= $woRowspan ?>" style="text-align: center;"><?= $colorGroup['artikel'] ?> <br>
                        <?= $colorGroup['design'] ?></td>
                    <?php } else { ?>
                    <td rowspan="<?= $woRowspan ?>" style="text-align: center;"><?= $colorGroup['artikel'] ?></td>
                    <?php } ?>
                    <!-- design -->

                    <?php foreach ($colorGroup['colors'] as $color => $batches): ?>
                    <?php foreach ($batches['batch'] as $batchNo => $lotsData): ?>

                    <?php if (!$isFirstColorRow): ?>
                <tr>
                    <?php endif; ?>
                    <td style="text-align: center;"><?= $color ?></td>

                    <!-- Menampilkan kolom satuan 1 (jumlah PCS dan grade per batch) -->
                    <?php 
                        $gradeCount = []; 
                        $recount_qtys = 0; 
                        $recount_subtotal = 0; 
                        $gradeTotalQty = []; 
                        $mstunit = MstGreigeGroup::unitOptions()[$colorGroup['unit']];
                        $sample = 0;
                        
                        foreach ($lotsData['lots'] as $lNo => $grades) {
                            foreach ($grades['grade'] as $qtys) {
                                foreach ($qtys as $q) {
                                    $gradeID = $q['grade_id'];
                                    $gradena = $q['grade'];
                                    if ($gradeID == TrnStockGreige::GRADE_A_ASTERISK || $gradeID == TrnStockGreige::GRADE_A_PLUS) {
                                        $gradena = TrnStockGreige::gradeOptions()[TrnStockGreige::GRADE_A];
                                    }
                                    if ($gradena == TrnStockGreige::gradeOptions()[TrnStockGreige::GRADE_E]) {
                                        continue;
                                    }
                                    $recount_qtys += 1;
                                    $recount_subtotal += $q['qty'];
                                    $gradeCount[$gradena] = ($gradeCount[$gradena] ?? 0) + 1;
                                    $gradeTotalQty[$gradena] = ($gradeTotalQty[$gradena] ?? 0) + $q['qty'];
                                }
                            }
                        }

                        $output = implode('<br> ', array_map(function($gradenih, $qtycount) {
                            return "- $gradenih = $qtycount PCS";
                        }, array_keys($gradeCount), $gradeCount));

                        $output_total = implode('<br> ', array_map(function($gradenih, $qtytotalcount) use ($mstunit) {
                            return "- $gradenih = $qtytotalcount $mstunit";
                        }, array_keys($gradeTotalQty), $gradeTotalQty));

                        $totalSatuan1 += $recount_qtys;

                        if (!isset($totalSatuan2[$colorGroup['unit']])) {
                            $totalSatuan2[$colorGroup['unit']] = 0;
                        }
                        $totalSatuan2[$colorGroup['unit']] += $recount_subtotal;
                    ?>

                    <td style="text-align: left; padding:0 5px; white-space: nowrap;">
                        <?= $recount_qtys.' PCS <br> '.$output ?></td>
                    <td style="text-align: left; padding:0 5px; white-space: nowrap;">
                        <?= $recount_subtotal.' '.$mstunit.' <br>'.$output_total ?></td>

                    <!-- Batch & Lot Table -->
                    <td style="padding: 0;">
                        <table class="pl-nested">
                            <tr>
                                <td style="text-align: center; padding: 2px 4px; white-space: nowrap; width: 45px; border-right: 1px solid #000; vertical-align: middle;" title="Batch">
                                    <?= $batchNo ?>
                                </td>
                                <td style="padding: 0;">
                                    <?php 
                                    $lotIdx = 0;
                                    $lotCount = count($lotsData['lots']);
                                    foreach ($lotsData['lots'] as $noLot => $grades): 
                                        $lotIdx++;
                                        $borderBottom = ($lotIdx < $lotCount) ? 'border-bottom: 1px solid #000;' : '';
                                    ?>
                                    <table class="pl-nested" style="<?= $borderBottom ?>">
                                        <tr>
                                            <td style="text-align: center; padding: 2px 4px; white-space: nowrap; width: 45px; border-right: 1px solid #000; vertical-align: middle;" title="No. Lot">
                                                <?= $noLot ?>
                                            </td>
                                            <td style="padding: 0;">
                                                <?php 
                                                $gradeIdx = 0;
                                                $gradeCountTotal = count($grades['grade']);
                                                foreach ($grades['grade'] as $gradeID => $qtys): 
                                                    $gradeIdx++;
                                                    $gradeBorderBottom = ($gradeIdx < $gradeCountTotal) ? 'border-bottom: 1px solid #000;' : '';
                                                ?>
                                                <table class="pl-nested" style="<?= $gradeBorderBottom ?>">
                                                    <tr>
                                                        <?php if($gradeID !== TrnStockGreige::GRADE_E){ ?>
                                                        <td style="text-align: center; padding: 2px 4px; white-space: nowrap; width: 28px; border-right: 1px solid #000; vertical-align: middle;" title="Grade">
                                                            <?=TrnStockGreige::gradeOptions()[$gradeID]?></td>
                                                        <td style="padding: 0;">
                                                            <table class="pl-pieces">
                                                                <?php
                                                                $count = count($qtys);
                                                                $itemsPerRow = 10;
                                                                $totalRows = ceil($count / $itemsPerRow);
                                                                for ($row = 0; $row < $totalRows; $row++) {
                                                                    echo '<tr>';
                                                                    for ($col = 0; $col < $itemsPerRow; $col++) {
                                                                        $index = $row * $itemsPerRow + $col;
                                                                        if ($index < $count) {
                                                                            $valQty = (float)$qtys[$index]['qty'];
                                                                            echo '<td class="piece-cell" style="text-align: center; width: 10%;">' . $valQty .'</td>';
                                                                        } else {
                                                                            echo '<td class="piece-cell empty" style="width: 10%;">&nbsp;</td>';
                                                                        }
                                                                    }
                                                                    echo '</tr>';
                                                                }
                                                                ?>
                                                            </table>
                                                        </td>
                                                        <?php }else{ 
                                                                foreach ($qtys as $key => $value) {
                                                                    $sample += $value['qty'];
                                                                }
                                                            } ?>
                                                    </tr>
                                                </table>
                                                <?php endforeach; ?>
                                            </td>
                                        </tr>
                                    </table>
                                    <?php endforeach; ?>
                                    <?php 
                                        if($sample > 0){
                                            echo '<table class="pl-nested"><tr>';
                                                echo '<td colspan="12" style="text-align: center;">' . $sample . ' ' . $mstunit . ' Sample</td>';
                                            echo '</tr></table>';
                                        }
                                        if(!empty($colorGroup['note']) && $colorGroup['note'] !== '-'){
                                            echo '<table class="pl-nested"><tr>';
                                                echo '<td colspan="12" style="text-align: center;">' . $colorGroup['note'] . '</td>';
                                            echo '</tr></table>';
                                        }
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php $isFirstColorRow = false; ?>
                <?php endforeach; ?>

                <?php endforeach; ?>
                <?php endforeach; ?>
                <?php endforeach; ?>


                <tr>
                    <td><b>TOTAL</b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: left; padding:0 5px; white-space: nowrap;">
                        <b><?= $totalSatuan1.' PCS <br> '?></b>
                    </td>
                    <td style="text-align: left; padding:0 5px; white-space: nowrap;">
                        <b>
                            <?php foreach($totalSatuan2 as $unit => $total): ?>
                            <?= $total.' '.MstGreigeGroup::unitOptions()[$unit].' <br>'?>
                            <?php endforeach; ?>
                        </b>
                    </td>
                    <td style="text-align: left; padding:0 5px; white-space: nowrap;">
                        <b><?= $model->note ?></b>
                    </td>
                </tr>
            </tbody>
        </table>
        <div style="text-align: right; margin-right: 10em; margin-top: 10px; font-weight: bold;">Admin : </div>
    </div>
</div>