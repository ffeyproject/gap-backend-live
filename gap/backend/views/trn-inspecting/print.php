<?php
/* @var $model common\models\ar\TrnInspecting */

use backend\components\Converter;
use common\models\ar\InspectingItem;
use common\models\ar\MstGreigeGroup;

$formatter = Yii::$app->formatter;
$kombinasi = '';
$stamping = $model->mo->face_stamping;
$woNo = $model->wo->no;
$motif = $model->wo->greigeNamaKain;
$pieceLength = $model->mo->piece_length;
$noKartu = '';
$design = $model->mo->design;
$jenisOrder = '';
$buyer = $model->sc->customerName;
$status = '';
if($model->kartu_process_dyeing_id !== null){
    $kombinasi = $model->kartuProcessDyeing->woColor->moColor->color;
    $noKartu = $model->kartuProcessDyeing->no;
    $jenisOrder = 'Dyeing';
}elseif ($model->kartu_process_printing_id !== null){
    $kombinasi = $model->kartuProcessPrinting->woColor->moColor->color;
    $noKartu = $model->kartuProcessPrinting->no;
    $jenisOrder = 'Printing';
}

$greige = $model->wo->greige;
$unitName = MstGreigeGroup::unitOptions()[$model->unit];
$perBatch = $greige->group->qty_per_batch;

$totalQtyGrade = [
    InspectingItem::GRADE_A => 0,
    InspectingItem::GRADE_B => 0,
    InspectingItem::GRADE_C => 0,
    InspectingItem::GRADE_PK => 0,
    InspectingItem::GRADE_SAMPLE => 0,
];
$totalPiecesGrade = [
    InspectingItem::GRADE_A => 0,
    InspectingItem::GRADE_B => 0,
    InspectingItem::GRADE_C => 0,
    InspectingItem::GRADE_PK => 0,
    InspectingItem::GRADE_SAMPLE => 0,
];
$totalRollGrade = [
    InspectingItem::GRADE_A => 0,
    InspectingItem::GRADE_B => 0,
    InspectingItem::GRADE_C => 0,
    InspectingItem::GRADE_PK => 0,
    InspectingItem::GRADE_SAMPLE => 0,
];
$joinPieces = [
    InspectingItem::GRADE_A => [],
    InspectingItem::GRADE_B => [],
    InspectingItem::GRADE_C => [],
    InspectingItem::GRADE_PK => [],
    InspectingItem::GRADE_SAMPLE => [],
];
?>

<table>
    <tr>
        <td>No. Kirim</td>
        <td width="1%">:</td>
        <td><?=$model->no?></td>
        <td>&nbsp;</td>
        <td>Tgl. Inspeksi</td>
        <td width="1%">:</td>
        <td><?=$model->tanggal_inspeksi?></td>
        <td>&nbsp;</td>
        <td>Kombinasi</td>
        <td width="1%">:</td>
        <td><?=$kombinasi?></td>
    </tr>
    <tr>
        <td>Tgl. Kirim</td>
        <td>:</td>
        <td><?=$model->date?></td>
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
        <td>:</td>
        <td><?=$woNo?></td>
        <td>&nbsp;</td>
        <td>Motif</td>
        <td>:</td>
        <td><?=$model->no_lot?></td>
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
        <td>Design</td>
        <td>:</td>
        <td><?=$design?></td>
        <td>&nbsp;</td>
        <td>Jenis Order</td>
        <td>:</td>
        <td><?=$jenisOrder?></td>
    </tr>
    <tr>
        <td>Buyer</td>
        <td>:</td>
        <td><?=$buyer?></td>
        <td>&nbsp;</td>
        <td>Status</td>
        <td>:</td>
        <td><?=MstGreigeGroup::unitOptions()[$model->unit]?></td>
        <td>&nbsp;</td>
        <td>Lokal/Export</td>
        <td>:</td>
        <td><?=\common\models\ar\TrnSc::tipeKontrakOptions()[$model->sc->tipe_kontrak]?></td>
    </tr>
</table>

<br>

<p><strong>Packing List</strong></p>

<table class="bordered">
    <thead>
    <tr>
        <th>No. Packing</th>
        <th>Grade A</th>
        <th>Grade B</th>
        <th>Grade C</th>
        <th>Piece Kecil</th>
        <th>Contoh</th>
        <th>Keterangan</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($model->inspectingItems as $index=>$item):?>
        <?php
        $totalQtyGrade[$item['grade']] += $item['qty'];
        $totalPiecesGrade[$item['grade']] ++;

        if(empty($item['join_piece'])){
            $totalRollGrade[$item['grade']] ++;
        }else{
            if(!in_array($item['join_piece'], $joinPieces[$item['grade']])){
                $totalRollGrade[$item['grade']] ++;
                $joinPieces[$item['grade']][] = $item['join_piece'];
            }
        }
        ?>

        <tr>
            <td><?=($index+1).$item['join_piece']?></td>
            <td>
                <?php
                if($item->grade === InspectingItem::GRADE_A){
                    echo $formatter->asDecimal($item->qty);
                }else{
                    echo '0';
                }
                ?>
            </td>
            <td>
                <?php
                if($item->grade === InspectingItem::GRADE_B){
                    echo $formatter->asDecimal($item['qty']);
                }else{
                    echo '0';
                }
                ?>
            </td>
            <td>
                <?php
                if($item['grade'] === InspectingItem::GRADE_C){
                    echo $formatter->asDecimal($item['qty']);
                }else{
                    echo '0';
                }
                ?>
            </td>
            <td>
                <?php
                if($item['grade'] === InspectingItem::GRADE_PK){
                    echo $formatter->asDecimal($item['qty']);
                }else{
                    echo '0';
                }
                ?>
            </td>
            <td>
                <?php
                if($item['grade'] === InspectingItem::GRADE_SAMPLE){
                    echo $formatter->asDecimal($item['qty']);
                }else{
                    echo '0';
                }
                ?>
            </td>
            <td><?=$item['note']?></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

<?php
$totalPieces = $totalPiecesGrade[InspectingItem::GRADE_A] + $totalPiecesGrade[InspectingItem::GRADE_B] + $totalPiecesGrade[InspectingItem::GRADE_C] + $totalPiecesGrade[InspectingItem::GRADE_PK] + $totalPiecesGrade[InspectingItem::GRADE_SAMPLE];;
$totalQty = $totalQtyGrade[InspectingItem::GRADE_A] + $totalQtyGrade[InspectingItem::GRADE_B] + $totalQtyGrade[InspectingItem::GRADE_C] + $totalQtyGrade[InspectingItem::GRADE_PK] + $totalQtyGrade[InspectingItem::GRADE_SAMPLE];
$totalRoll = $totalRollGrade[InspectingItem::GRADE_A] + $totalRollGrade[InspectingItem::GRADE_B] + $totalRollGrade[InspectingItem::GRADE_C] + $totalRollGrade[InspectingItem::GRADE_PK] + $totalRollGrade[InspectingItem::GRADE_SAMPLE];

if($model->unit == MstGreigeGroup::UNIT_YARD){
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
        <th class="bordered">Total Grade A</th>
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
        <td rowspan="6">&nbsp;</td>
        <td rowspan="5">&nbsp;</td>
        <td rowspan="6">&nbsp;</td>
        <td rowspan="5">&nbsp;</td>
    </tr>
    <tr>
        <th class="bordered">Total Grade B</th>
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
