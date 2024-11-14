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
        <table width="100%">
            <tr>
                <td width="40%">
                    <?= Html::img('/gap/backend/web/images/logo/logo-gap.png', ['style'=>'height:100px;'])?><br>
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

        <p>&nbsp;</p>

        <table width="100%" border="1">
            <thead>
            <tr>
                <th width="3%" rowspan="2" style="text-align: center;">BAL</th>
                <th rowspan="2" style="text-align: center;">NO WO</th>
                <th rowspan="2" style="text-align: center;">DESIGN</th>
                <th colspan="3" style="text-align: center;">JUMLAH</th>
                <th rowspan="2" colspan="10" style="text-align: center;">PIECE LENGTH</th>
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
                        $no_wo = $kirimBuyerModel->wo->no;
                        $color = $pssk->stock->color ?? '';
                        $grade = $pssk->stock->grade ?? '';
                        $design = $kirimBuyerModel->nama_kain_alias;
                        $artikel = $kirimBuyerModel->wo->mo->article;
                        $unit = $kirimBuyerModel->unit;
                        $jenisProcess = $kirimBuyerModel->wo->mo->process;
                        $kodeDesign = $kirimBuyerModel->wo->mo->design;
                        $noLot  = $pssk->stock->noLot;
                        $note = $kirimBuyerModel->note;


                        ####################################### start of new code ver 1 ####################################################

                        // first groupBy no_bal then groupBy no_wo then groupBy grade and keep the qty track by grade then sum it

                        // Group by no_bal first
                        // if (!isset($groupedByNoBal[$no_bal])) {
                        //     $groupedByNoBal[$no_bal] = [];
                        // }

                        // // Then group by no_wo
                        // if (!isset($groupedByNoBal[$no_bal][$no_wo])) {
                        //     $groupedByNoBal[$no_bal][$no_wo] = [];
                        // }

                        // // Then group by color
                        // if (!isset($groupedByNoBal[$no_bal][$no_wo][$color])) {
                        //     $groupedByNoBal[$no_bal][$no_wo][$color] = [
                        //         'grades' => [],
                        //         'total_qty' => 0,
                        //         'qty_pieces' => [],
                        //     ];
                        // }

                        // // Sum qty based on grade and store individual qty pieces
                        // if (!isset($groupedByNoBal[$no_bal][$no_wo][$color]['grades'][$grade])) {
                        //     $groupedByNoBal[$no_bal][$no_wo][$color]['grades'][$grade] = 0;
                        // }

                        // $groupedByNoBal[$no_bal][$no_wo][$color]['grades'][$grade] += $qty;

                        // // Store the individual qty values
                        // $groupedByNoBal[$no_bal][$no_wo][$color]['qty_pieces'][] = [
                        //     'qty' => $qty,
                        //     'grade' => $grade,
                        // ];

                        // // Update total quantity for the color
                        // $groupedByNoBal[$no_bal][$no_wo][$color]['total_qty'] += $qty;

                        ####################################### end of new code ver 1 ######################################################

                        ####################################### start of new code ver 2 ####################################################

                            // Group by no_bal first
                            // if (!isset($groupedByNoBal[$no_bal])) {
                            //     $groupedByNoBal[$no_bal] = [];
                            // }

                            // // Then group by no_wo
                            // if (!isset($groupedByNoBal[$no_bal][$no_wo])) {
                            //     $groupedByNoBal[$no_bal][$no_wo] = [];
                            // }

                            // // Then group by grade
                            // if (!isset($groupedByNoBal[$no_bal][$no_wo][$grade])) {
                            //     $groupedByNoBal[$no_bal][$no_wo][$grade] = [
                            //         'design' => $design,
                            //         'total_qty' => 0,
                            //         'qty_pieces' => [],
                            //     ];
                            // }

                            // // Sum qty and store individual qty pieces
                            // $groupedByNoBal[$no_bal][$no_wo][$grade]['total_qty'] += $qty;

                            // // Store the individual qty values
                            // $groupedByNoBal[$no_bal][$no_wo][$grade]['qty_pieces'][] = $qty;
                        
                        ####################################### end of new code ver 2 ######################################################

                        ####################################### start of new code ver 3 ####################################################
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
                        // $groupedByNoBal[$no_bal][$no_wo]['colors'][$color]['batch'][$noLot][] = [
                        //     'grade_id' => $grade,
                        //     'grade' => TrnStockGreige::gradeOptions()[$grade],
                        //     'qty' => $qty,
                        // ];
                        $groupedByNoBal[$no_bal][$no_wo]['colors'][$color]['batch'][$noLot]['grade'][$grade][] = [
                            'grade_id' => $grade,
                            'grade' => TrnStockGreige::gradeOptions()[$grade],
                            'qty' => $qty,
                        ];
                        // $groupedByNoBal[$no_bal][$no_wo]['colors'][$color][] = [
                        //     'grade_id' => $grade,
                        //     'grade' => TrnStockGreige::gradeOptions()[$grade],
                        //     'qty' => $qty,
                        // ];

                        ####################################### end of new code ver 3 ######################################################

                        // old code

                        // Check if this no_bal already exists in the grouped array
                        // $groupedByNoBal[$no_bal]['color'] = $color;
                        // $groupedByNoBal[$no_bal]['no_bal'] = $no_bal;
                        // $groupedByNoBal[$no_bal]['no_wo'] = $kirimBuyerModel->wo->no;
                        // $groupedByNoBal[$no_bal]['design'] = $kirimBuyerModel->nama_kain_alias;
                        
                        // Initialize qty array if it doesn't exist and push the qty
                        // $groupedByNoBal[$no_bal]['qty'][] = $pssk['qty'];
                    }
                }
                ksort($groupedByNoBal);
                //log $groupedByNoBal
                //    print("<pre>".print_r($groupedByNoBal ,true)."</pre>");  
                //     die;
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
                <td rowspan="<?= $woRowspan ?>" style="text-align: center;"><?= $colorGroup['artikel'] ?> <br> <?= $colorGroup['design'] ?></td>
            <?php } else { ?>
                <td rowspan="<?= $woRowspan ?>" style="text-align: center;"><?= $colorGroup['artikel'] ?></td>
            <?php } ?>
            <!-- design -->

            <?php foreach ($colorGroup['colors'] as $color => $batches): ?>
                <?php foreach ($batches['batch'] as $noLot => $grades): ?>

                    <?php if (!$isFirstColorRow): ?>
                        <tr>
                    <?php endif; ?>
                    <td style="text-align: center;"><?= $color ?></td>

                    <!-- Menampilkan kolom satuan 1 (jumlah PCS dan grade per color) -->
                    <?php 
                        $gradeCount = []; 
                        $recount_qtys = 0; 
                        $recount_subtotal = 0; 
                        $gradeTotalQty = []; 
                        $mstunit = MstGreigeGroup::unitOptions()[$colorGroup['unit']];
                        
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

                    <td style="text-align: left; padding:0 5px; white-space: nowrap;"><?= $recount_qtys.' PCS <br> '.$output ?></td>
                    <td style="text-align: left; padding:0 5px; white-space: nowrap;"><?= $recount_subtotal.' '.$mstunit.' <br>'.$output_total ?></td>
                    
                    <!-- Batch Table -->
                    <td>
                        <table style="width: 100%; border-collapse: collapse;" border="">
                            <tr>
                                <td  style="text-align: center; padding:0 5px; white-space: nowrap;"><?= $noLot?></td>
                                <td>
                                    <?php $sample = 0;?>
                                    <?php foreach ($grades['grade'] as $gradeID => $qtys): ?>
                                        <table style="width: 100%; border-collapse: collapse;" border="">
                                            <tr>
                                                <?php if($gradeID !== TrnStockGreige::GRADE_E){ ?>
                                                    <td  style="text-align: center; padding:0 5px; white-space: nowrap;"><?=TrnStockGreige::gradeOptions()[$gradeID]?></td>
                                                    <td>
                                                        <table style="width: 100%; border-collapse: collapse;" border="1">
                                                                <tr>
                                                                    <?php
                                                                        $count = count($qtys);
                                                                        $itemsPerRow = 10;
                                                                        $totalRows = ceil($count / $itemsPerRow);
                                                                            for ($row = 0; $row < $totalRows; $row++) {

                                                                                echo '<tr>';
                                                                                for ($col = 0; $col < $itemsPerRow; $col++) {
                                                                                    $index = $row * $itemsPerRow + $col;
                                                                                    if ($index < $count) {
                                                                                        echo '<td style="text-align: center; width: 50px;">' . $qtys[$index]['qty'] .'</td>';
                                                                                    } else {
                                                                                        echo '<td style="width: 50px;"></td>';
                                                                                    }
                                                                                }
                                                                                echo '</tr>';
                                                                            }
                                                                    ?>
                                                                </tr>
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
                                    <?php 
                                        if($sample > 0){
                                            echo '<tr>';
                                                echo '<td colspan="11" style="text-align: center;">' . $sample . ' Yard Sample</td>';
                                            echo '</tr>';
                                        }
                                        echo '<tr>';
                                            echo '<td colspan="11" style="text-align: center;">' . $colorGroup['note'] . '</td>';
                                        echo '</tr>';

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
                <td style="text-align: left; padding:0 5px; white-space: nowrap;"><b><?= $totalSatuan1.' PCS <br> '?></b></td>
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
        <p></p>
    </div>
</div>
