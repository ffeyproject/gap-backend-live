<?php

use backend\components\Converter;
use common\models\ar\InspectingItem;
use common\models\ar\TrnScGreige;
use common\models\ar\MstGreigeGroup;
/* @var $model common\models\ar\InspectingMklBj */

/* @var $scGreige TrnScGreige */

$formatter = Yii::$app->formatter;
$wo = $model->wo;
$mo = $wo->mo;
$sc = $mo->sc;
$kombinasi = $model->wo->greigeNamaKain;
$design = $model->mo->design;
$buyer = $sc->cust->cust_no;
$warna = $model->colorName;


$formatter = Yii::$app->formatter;
$kombinasi = '';
$stamping = $model->mo->face_stamping;
$woNo = $model->wo->no;
$motif = $model->wo->greigeNamaKain;
$pieceLength = $model->mo->piece_length;
$noKartu = '';
$design = $model->mo->design;
$jenisOrder = '';
$status = '';
$n_design = '';

$j_proses = $wo->scGreige->process;

if ($j_proses == '2') {
    $n_design = $design;
    $n_artikel = $model->mo->article;
    $tanda = '/';
}elseif ($j_proses == '1') {
    $n_design = $model->mo->article;
    $n_artikel = '';
    $tanda = '';
}

$greige = $model->wo->greige;
$unitName = MstGreigeGroup::unitOptions()[$model->satuan];
$perBatch = $greige->group->qty_per_batch;

$totalQtyGrade = [
    InspectingItem::GRADE_A => 0,
    InspectingItem::GRADE_B => 0,
    InspectingItem::GRADE_C => 0,
    InspectingItem::GRADE_PK => 0,
    InspectingItem::GRADE_SAMPLE => 0,
    InspectingItem::GRADE_A_PLUS => 0,
    InspectingItem::GRADE_A_ASTERISK => 0,
];
$totalPiecesGrade = [
    InspectingItem::GRADE_A => 0,
    InspectingItem::GRADE_B => 0,
    InspectingItem::GRADE_C => 0,
    InspectingItem::GRADE_PK => 0,
    InspectingItem::GRADE_SAMPLE => 0,
    InspectingItem::GRADE_A_PLUS => 0,
    InspectingItem::GRADE_A_ASTERISK => 0,
];
$totalRollGrade = [
    InspectingItem::GRADE_A => 0,
    InspectingItem::GRADE_B => 0,
    InspectingItem::GRADE_C => 0,
    InspectingItem::GRADE_PK => 0,
    InspectingItem::GRADE_SAMPLE => 0,
    InspectingItem::GRADE_A_PLUS => 0,
    InspectingItem::GRADE_A_ASTERISK => 0,
];
$joinPieces = [
    InspectingItem::GRADE_A => [],
    InspectingItem::GRADE_B => [],
    InspectingItem::GRADE_C => [],
    InspectingItem::GRADE_PK => [],
    InspectingItem::GRADE_SAMPLE => [],
    InspectingItem::GRADE_A_PLUS => [],
    InspectingItem::GRADE_A_ASTERISK => [],
];

$inspectingItems = $model->getItems()->orderBy(['no_urut' => SORT_ASC])->all();
$indexLimit = round(count($inspectingItems) / 2);
?>

<table>
    <tbody>
        <tr>
            <td style="width: 100px;">No. Kirim</td>
            <td width="1%">:</td>
            <td><?=$model->no?></td>
            <td style="width: 50px;">&nbsp;</td>
            <td style="width: 100px;">Tgl. Inspek</td>
            <td width="1%">:</td>
            <td><?=$model->tgl_inspeksi?></td>
            <td style="width: 50px;">&nbsp;</td>
            <td style="width: 100px;">Kombinasi</td>
            <td width="1%">:</td>
            <td><?=$warna?></td>
        </tr>
        <tr>
            <td>Tgl. Kirim</td>
            <td width="1%">:</td>
            <td><?=$model->tgl_kirim?></td>
            <td>&nbsp;</td>
            <td>No. Lot</td>
            <td>:</td>
            <td><?=$model->no_lot?></td>
            <td>&nbsp;</td>
            <td>Stamping</td>
            <td>:</td>
            <td><?=$stamping?></td>
        </tr>
        <tr>
            <td>No. WO</td>
            <td width="1%">:</td>
            <td><?=$woNo?></td>
            <td>&nbsp;</td>
            <td>Motif</td>
            <td>:</td>
            <td><?=$motif?></td>
            <td>&nbsp;</td>
            <td>Piece Length</td>
            <td>:</td>
            <td><?=$pieceLength?></td>
        </tr>
        <tr>
            <td>No. Kartu</td>
            <td>:</td>
            <td><?=$noKartu?></td>
            <td>&nbsp;</td>
            <td>Design/Artikel</td>
            <td>:</td>
            <td><?=$n_artikel?><?=$tanda?><?=$n_design?></td>
            <td>&nbsp;</td>
            <td>Jenis Order</td>
            <td>:</td>
            <td><?=\common\models\ar\TrnScGreige::processOptions()[$j_proses]?></td>
        </tr>
        <tr>
            <td>Buyer</td>
            <td>:</td>
            <td><?=$buyer?></td>
            <td>&nbsp;</td>
            <td>Satuan</td>
            <td>:</td>
            <td><?=$unitName?></td>
            <td>&nbsp;</td>
            <td>Lokal/Export</td>
            <td>:</td>
            <td><?=\common\models\ar\TrnSc::tipeKontrakOptions()[$sc->tipe_kontrak]?></td>
        </tr>
        <tr>
            <td colspan="11">&nbsp;</td>
        </tr>
        <!-- Item Packing List -->
        <tr>
            <td colspan="11">
                <!-- <p><strong>Packing List</strong></p> -->
                <table>
                    <thead>
                        <tr>
                            <td>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="bordered" style="width: 40px; text-align: center;" rowspan="2">
                                                No.</th>
                                            <th class="bordered" style="text-align: center;" colspan="7">Grade</th>
                                            <th class="bordered" style="text-align: center;" rowspan="2">Keterangan</th>
                                        </tr>
                                        <tr>
                                            <th class="bordered" style="width: 30px; text-align: center;">A</th>
                                            <th class="bordered" style="width: 30px; text-align: center;">A+</th>
                                            <th class="bordered" style="width: 30px; text-align: center;">A*</th>
                                            <th class="bordered" style="width: 30px; text-align: center;">B</th>
                                            <th class="bordered" style="width: 30px; text-align: center;">C</th>
                                            <th class="bordered" style="width: 30px; text-align: center;">P/K</th>
                                            <th class="bordered" style="width: 30px; text-align: center;">CTH</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($inspectingItems as $index=>$item):?>
                                        <?php
                                        if ($index == $indexLimit) {
                                            break;
                                        }
                                    ?>
                                        <?php
                                        if($item['qty'] > 0){
                                            // akumulasi hanya berlaku jika qty > 0
                                            if ($item['grade_up'] <> NULL) {
                                                $totalQtyGrade[$item['grade_up']] += $item['qty'];
                                                $totalPiecesGrade[$item['grade_up']]++;
                                                if(empty($item['join_piece'])){
                                                    $totalRollGrade[$item['grade_up']]++;
                                                }else{
                                                    if(!in_array($item['join_piece'], $joinPieces[$item['grade_up']])){
                                                        $totalRollGrade[$item['grade_up']]++;
                                                        $joinPieces[$item['grade_up']][] = $item['join_piece'];
                                                    }
                                                }
                                            } else {
                                                $totalQtyGrade[$item['grade']] += $item['qty'];
                                                $totalPiecesGrade[$item['grade']]++;
                                                if(empty($item['join_piece'])){
                                                    $totalRollGrade[$item['grade']]++;
                                                }else{
                                                    if(!in_array($item['join_piece'], $joinPieces[$item['grade']])){
                                                        $totalRollGrade[$item['grade']]++;
                                                        $joinPieces[$item['grade']][] = $item['join_piece'];
                                                    }
                                                }
                                            }

                                        }
                                    ?>
                                        <tr>
                                            <td class="bordered" style="text-align: center;">
                                                <?=($index+1).$item['join_piece']?></td>
                                            <td class="bordered" style="text-align: center;">
                                                <?php
                                                    if ($item['grade_up'] <> NULL) {
                                                        if($item['grade_up'] === InspectingItem::GRADE_A){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    } else {
                                                        if($item['grade'] === InspectingItem::GRADE_A){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    }
                                                ?>
                                            </td>
                                            <td class="bordered" style="text-align: center;">
                                                <?php
                                                    if ($item['grade_up'] <> NULL) {
                                                        if($item['grade_up'] === InspectingItem::GRADE_A_PLUS){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    } else {
                                                        if($item['grade'] === InspectingItem::GRADE_A_PLUS){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    }
                                                ?>
                                            </td>
                                            <td class="bordered" style="text-align: center;">
                                                <?php
                                                    if ($item['grade_up'] <> NULL) {
                                                        if($item['grade_up'] === InspectingItem::GRADE_A_ASTERISK){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    } else {
                                                        if($item['grade'] === InspectingItem::GRADE_A_ASTERISK){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    }
                                                ?>
                                            </td>
                                            <td class="bordered" style="text-align: center;">
                                                <?php
                                                    if ($item['grade_up'] <> NULL) {
                                                        if($item['grade_up'] === InspectingItem::GRADE_B){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    } else {
                                                        if($item['grade'] === InspectingItem::GRADE_B){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    }
                                                ?>
                                            </td>
                                            <td class="bordered" style="text-align: center;">
                                                <?php
                                                    if ($item['grade_up'] <> NULL) {
                                                        if($item['grade_up'] === InspectingItem::GRADE_C){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    } else {
                                                        if($item['grade'] === InspectingItem::GRADE_C){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    }
                                                ?>
                                            </td>
                                            <td class="bordered" style="text-align: center;">
                                                <?php
                                                    if ($item['grade_up'] <> NULL) {
                                                        if($item['grade_up'] === InspectingItem::GRADE_PK){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    } else {
                                                        if($item['grade'] === InspectingItem::GRADE_PK){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    }
                                                ?>
                                            </td>
                                            <td class="bordered" style="text-align: center;">
                                                <?php
                                                    if ($item['grade_up'] <> NULL) {
                                                        if($item['grade_up'] === InspectingItem::GRADE_SAMPLE){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    } else {
                                                        if($item['grade'] === InspectingItem::GRADE_SAMPLE){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    }
                                                ?>
                                            </td>

                                            <td class="bordered" style="text-align: center;"><?=$item['note']?></td>
                                        </tr>
                                        <?php endforeach;?>
                                    </tbody>
                                </table>
                            </td>
                            <td>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="bordered" style="width: 40px; text-align: center;" rowspan="2">
                                                No.</th>
                                            <th class="bordered" style="text-align: center;" colspan="7">Grade</th>
                                            <th class="bordered" style="text-align: center;" rowspan="2">Keterangan</th>
                                        </tr>
                                        <tr>
                                            <th class="bordered" style="width: 30px; text-align: center;">A</th>
                                            <th class="bordered" style="width: 30px; text-align: center;">A+</th>
                                            <th class="bordered" style="width: 30px; text-align: center;">A*</th>
                                            <th class="bordered" style="width: 30px; text-align: center;">B</th>
                                            <th class="bordered" style="width: 30px; text-align: center;">C</th>
                                            <th class="bordered" style="width: 30px; text-align: center;">P/K</th>
                                            <th class="bordered" style="width: 30px; text-align: center;">CTH</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($inspectingItems as $index=>$item):?>
                                        <?php
                                        if ($index < $indexLimit) {
                                            continue;
                                        } elseif($index > (count($inspectingItems))) {
                                            break;
                                        }
                                    ?>
                                        <?php
                                        if($item['qty'] > 0){
                                            // akumulasi hanya berlaku jika qty > 0
                                            if ($item['grade_up'] <> NULL) {
                                                $totalQtyGrade[$item['grade_up']] += $item['qty'];
                                                $totalPiecesGrade[$item['grade_up']]++;
                                                if(empty($item['join_piece'])){
                                                    $totalRollGrade[$item['grade_up']]++;
                                                }else{
                                                    if(!in_array($item['join_piece'], $joinPieces[$item['grade_up']])){
                                                        $totalRollGrade[$item['grade_up']]++;
                                                        $joinPieces[$item['grade_up']][] = $item['join_piece'];
                                                    }
                                                }
                                            } else {
                                                $totalQtyGrade[$item['grade']] += $item['qty'];
                                                $totalPiecesGrade[$item['grade']]++;
                                                if(empty($item['join_piece'])){
                                                    $totalRollGrade[$item['grade']]++;
                                                }else{
                                                    if(!in_array($item['join_piece'], $joinPieces[$item['grade']])){
                                                        $totalRollGrade[$item['grade']]++;
                                                        $joinPieces[$item['grade']][] = $item['join_piece'];
                                                    }
                                                }
                                            }

                                        }
                                    ?>
                                        <tr>
                                            <td class="bordered" style="text-align: center;">
                                                <?=($index+1).$item['join_piece']?></td>
                                            <td class="bordered" style="text-align: center">
                                                <?php
                                                    if ($item['grade_up'] <> NULL) {
                                                        if($item['grade_up'] === InspectingItem::GRADE_A){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    } else {
                                                        if($item['grade'] === InspectingItem::GRADE_A){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    }
                                                ?>
                                            </td>
                                            <td class="bordered" style="text-align: center">
                                                <?php
                                                    if ($item['grade_up'] <> NULL) {
                                                        if($item['grade_up'] === InspectingItem::GRADE_A_PLUS){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    } else {
                                                        if($item['grade'] === InspectingItem::GRADE_A_PLUS){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    }
                                                ?>
                                            </td>
                                            <td class="bordered" style="text-align: center">
                                                <?php
                                                    if ($item['grade_up'] <> NULL) {
                                                        if($item['grade_up'] === InspectingItem::GRADE_A_ASTERISK){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    } else {
                                                        if($item['grade'] === InspectingItem::GRADE_A_ASTERISK){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    }
                                                ?>
                                            </td>
                                            <td class="bordered" style="text-align: center">
                                                <?php
                                                    if ($item['grade_up'] <> NULL) {
                                                        if($item['grade_up'] === InspectingItem::GRADE_B){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    } else {
                                                        if($item['grade'] === InspectingItem::GRADE_B){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    }
                                                ?>
                                            </td>
                                            <td class="bordered" style="text-align: center">
                                                <?php
                                                    if ($item['grade_up'] <> NULL) {
                                                        if($item['grade_up'] === InspectingItem::GRADE_C){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    } else {
                                                        if($item['grade'] === InspectingItem::GRADE_C){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    }
                                                ?>
                                            </td>
                                            <td class="bordered" style="text-align: center">
                                                <?php
                                                    if ($item['grade_up'] <> NULL) {
                                                        if($item['grade_up'] === InspectingItem::GRADE_PK){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    } else {
                                                        if($item['grade'] === InspectingItem::GRADE_PK){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    }
                                                ?>
                                            </td>
                                            <td class="bordered" style="text-align: center">
                                                <?php
                                                    if ($item['grade_up'] <> NULL) {
                                                        if($item['grade_up'] === InspectingItem::GRADE_SAMPLE){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    } else {
                                                        if($item['grade'] === InspectingItem::GRADE_SAMPLE){
                                                            echo $formatter->asDecimal($item['qty']);
                                                        }else{
                                                            echo '0';
                                                        }
                                                    }
                                                ?>
                                            </td>

                                            <td class="bordered" style="text-align: center"><?=$item['note']?></td>
                                        </tr>
                                        <?php endforeach;?>
                                        <?php
                                            if (count($inspectingItems) % 2 !== 0) {
                                                echo '<tr><td class="bordered" style="text-align: center">&nbsp;</td><td class="bordered" style="text-align: center">&nbsp;</td><td class="bordered" style="text-align: center">&nbsp;</td><td class="bordered" style="text-align: center">&nbsp;</td><td class="bordered" style="text-align: center">&nbsp;</td><td class="bordered" style="text-align: center">&nbsp;</td><td class="bordered" style="text-align: center">&nbsp;</td><td class="bordered" style="text-align: center">&nbsp;</td><td class="bordered" style="text-align: center">&nbsp;</td></tr>';
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </thead>
                </table>
            </td>
        </tr>
        <!--  -->
    </tbody>
</table>

<?php
$totalPieces = $totalPiecesGrade[InspectingItem::GRADE_A] + $totalPiecesGrade[InspectingItem::GRADE_A_PLUS] + $totalPiecesGrade[InspectingItem::GRADE_A_ASTERISK] + $totalPiecesGrade[InspectingItem::GRADE_B] + $totalPiecesGrade[InspectingItem::GRADE_C] + $totalPiecesGrade[InspectingItem::GRADE_PK] + $totalPiecesGrade[InspectingItem::GRADE_SAMPLE];;
$totalQty = $totalQtyGrade[InspectingItem::GRADE_A] + $totalQtyGrade[InspectingItem::GRADE_A_PLUS] + $totalQtyGrade[InspectingItem::GRADE_A_ASTERISK] + $totalQtyGrade[InspectingItem::GRADE_B] + $totalQtyGrade[InspectingItem::GRADE_C] + $totalQtyGrade[InspectingItem::GRADE_PK] + $totalQtyGrade[InspectingItem::GRADE_SAMPLE];
$totalRoll = $totalRollGrade[InspectingItem::GRADE_A] + $totalRollGrade[InspectingItem::GRADE_A_PLUS] + $totalRollGrade[InspectingItem::GRADE_A_ASTERISK] + $totalRollGrade[InspectingItem::GRADE_B] + $totalRollGrade[InspectingItem::GRADE_C] + $totalRollGrade[InspectingItem::GRADE_PK] + $totalRollGrade[InspectingItem::GRADE_SAMPLE];

if($model->satuan == MstGreigeGroup::UNIT_YARD){
    $totalM = Converter::yardToMeter($totalQty);
}else{
    $totalM = $totalQty;
}
$susutM = $perBatch - $totalM;
$susutPcnt = (($perBatch-$totalM) / $perBatch) * 100;
?>

<br>

<table>
    <thead>
        <tr>
            <th class="bordered">Total</th>
            <th class="bordered">Total Pieces</th>
            <th class="bordered">Total Roll</th>
            <th class="bordered">Total Ukuran</th>
            <th>&nbsp;</th>
            <th class="text-center">Dikirim Oleh</th>
            <th>&nbsp;</th>
            <th class="text-center">Diterima Oleh</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th class="bordered">Total Grade A (1)</th>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalPiecesGrade[InspectingItem::GRADE_A]);
            ?>
            </td>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalRollGrade[InspectingItem::GRADE_A]);
            ?>
            </td>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalQtyGrade[InspectingItem::GRADE_A]);
            ?>
            </td>
            <td rowspan="8">&nbsp;</td>
            <td rowspan="7">&nbsp;</td>
            <td rowspan="8">&nbsp;</td>
            <td rowspan="7">&nbsp;</td>
        </tr>
        <tr>
            <th class="bordered">Total Grade A+ (2)</th>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalPiecesGrade[InspectingItem::GRADE_A_PLUS]);
            ?>
            </td>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalRollGrade[InspectingItem::GRADE_A_PLUS]);
            ?>
            </td>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalQtyGrade[InspectingItem::GRADE_A_PLUS]);
            ?>
            </td>
        </tr>
        <tr>
            <th class="bordered">Total Grade A* (3)</th>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalPiecesGrade[InspectingItem::GRADE_A_ASTERISK]);
            ?>
            </td>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalRollGrade[InspectingItem::GRADE_A_ASTERISK]);
            ?>
            </td>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalQtyGrade[InspectingItem::GRADE_A_ASTERISK]);
            ?>
            </td>
        </tr>
        <tr>
            <th class="bordered">Total Grade B (4)</th>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalPiecesGrade[InspectingItem::GRADE_B]);
            ?>
            </td>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalRollGrade[InspectingItem::GRADE_B]);
            ?>
            </td>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalQtyGrade[InspectingItem::GRADE_B]);
            ?>
            </td>
        </tr>
        <tr>
            <th class="bordered">Total Grade C</th>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalPiecesGrade[InspectingItem::GRADE_C]);
            ?>
            </td>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalRollGrade[InspectingItem::GRADE_C]);
            ?>
            </td>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalQtyGrade[InspectingItem::GRADE_C]);
            ?>
            </td>
        </tr>
        <tr>
            <th class="bordered">Total Piece Kecil</th>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalPiecesGrade[InspectingItem::GRADE_PK]);
            ?>
            </td>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalRollGrade[InspectingItem::GRADE_PK]);
            ?>
            </td>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalQtyGrade[InspectingItem::GRADE_PK]);
            ?>
            </td>
        </tr>
        <tr>
            <th class="bordered">Total Contoh</th>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalPiecesGrade[InspectingItem::GRADE_SAMPLE]);
            ?>
            </td>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalRollGrade[InspectingItem::GRADE_SAMPLE]);
            ?>
            </td>
            <td class="bordered">
                <?php
            echo $formatter->asDecimal($totalQtyGrade[InspectingItem::GRADE_SAMPLE]);
            ?>
            </td>
        </tr>

        <tr>
            <th class="bordered">Grand Total</th>
            <th class="bordered"><?=$formatter->asDecimal($totalPieces)?></th>
            <th class="bordered"><?=$formatter->asDecimal($totalRoll)?></th>
            <th class="bordered"><?=$formatter->asDecimal($totalQty)?></th>
            <th class="text-center">(...................)</th>
            <th class="text-center">(...................)</th>
        </tr>
    </tbody>
</table>