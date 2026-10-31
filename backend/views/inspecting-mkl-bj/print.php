<?php

use backend\components\Converter;
use common\models\ar\InspectingItem;
use common\models\ar\TrnScGreige;
use common\models\ar\MstGreigeGroup;
/* @var $model common\models\ar\InspectingMklBj */
/* @var $columns int jumlah kolom tabel packing list (diatur controller agar muat 1 lembar) */

/* @var $scGreige TrnScGreige */

$columns = isset($columns) ? max(1, (int)$columns) : 2;

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

// urutan kolom grade pada tabel packing list
$gradeColumns = [
    InspectingItem::GRADE_A => 'A',
    InspectingItem::GRADE_A_PLUS => 'A+',
    InspectingItem::GRADE_A_ASTERISK => 'A*',
    InspectingItem::GRADE_B => 'B',
    InspectingItem::GRADE_C => 'C',
    InspectingItem::GRADE_PK => 'P/K',
    InspectingItem::GRADE_SAMPLE => 'CTH',
];

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

$inspectingItems = $model->getItems()
    ->alias('it')
    ->select(['it.*', 'gj.id AS gj_id'])
    ->leftJoin('trn_gudang_jadi gj', 'gj.id_from = it.id AND gj.trans_from = \'MKL\'')
    ->where(['it.is_posted' => true])
    ->orderBy([
        new \yii\db\Expression("CASE WHEN it.no_urut IS NULL THEN 1 ELSE 0 END"),
        'it.no_urut' => SORT_ASC,
        'it.id' => SORT_ASC,
    ])
    ->asArray()
    ->all();

$receivedJoinPieces = [];
foreach ($inspectingItems as $item) {
    if ($item['gj_id'] !== null && !empty($item['join_piece'])) {
        $receivedJoinPieces[$item['join_piece']] = true;
    }
}

$postedDates = array_unique(array_filter(array_column($inspectingItems, 'posted_at')));
sort($postedDates);
$tglKirimHeader = empty($postedDates) ? $model->tgl_kirim : implode(', ', $postedDates);

// siapkan baris + akumulasi total
$rows = [];
foreach ($inspectingItems as $item) {
    $isReceived = ($item['gj_id'] !== null) || (!empty($item['join_piece']) && isset($receivedJoinPieces[$item['join_piece']]));
    $item['qty'] = $isReceived ? 0 : $item['qty'];
    $grade = $item['grade_up'] <> NULL ? $item['grade_up'] : $item['grade'];

    if($item['qty'] > 0){
        // akumulasi hanya berlaku jika qty > 0
        $totalQtyGrade[$grade] += $item['qty'];
        $totalPiecesGrade[$grade]++;
        if(empty($item['join_piece'])){
            $totalRollGrade[$grade]++;
        }else{
            if(!in_array($item['join_piece'], $joinPieces[$grade])){
                $totalRollGrade[$grade]++;
                $joinPieces[$grade][] = $item['join_piece'];
            }
        }
    }

    $rows[] = [
        'no' => $item['no_urut'].$item['join_piece'].($isReceived ? ' (V)' : ''),
        'grade' => $grade,
        'qty' => $item['qty'],
        'posted_at' => $item['posted_at'],
        'note' => $item['note'],
    ];
}

$rowsPerColumn = max(1, (int)ceil(count($rows) / $columns));
$rowChunks = array_chunk($rows, $rowsPerColumn);
$rowChunks = array_pad($rowChunks, $columns, []);
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
            <td><?=$tglKirimHeader?></td>
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
                <table>
                    <tr>
                        <?php foreach ($rowChunks as $chunk):?>
                        <td style="width: <?=round(100 / $columns, 2)?>%;">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="bordered" style="text-align: center;" rowspan="2">No.</th>
                                        <th class="bordered" style="text-align: center;" colspan="<?=count($gradeColumns)?>">Grade</th>
                                        <th class="bordered" style="text-align: center;" rowspan="2">Tgl. Kirim</th>
                                        <th class="bordered" style="text-align: center;" rowspan="2">Keterangan</th>
                                    </tr>
                                    <tr>
                                        <?php foreach ($gradeColumns as $gradeLabel):?>
                                        <th class="bordered" style="text-align: center;"><?=$gradeLabel?></th>
                                        <?php endforeach;?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($chunk as $row):?>
                                    <tr>
                                        <td class="bordered" style="text-align: center;"><?=$row['no']?></td>
                                        <?php foreach ($gradeColumns as $gradeKey => $gradeLabel):?>
                                        <td class="bordered" style="text-align: center;"><?=$row['grade'] === $gradeKey ? $formatter->asDecimal($row['qty']) : '0'?></td>
                                        <?php endforeach;?>
                                        <td class="bordered" style="text-align: center;"><?=$row['posted_at']?></td>
                                        <td class="bordered" style="text-align: center;"><?=$row['note']?></td>
                                    </tr>
                                    <?php endforeach;?>
                                    <?php for ($i = count($chunk); $i < $rowsPerColumn; $i++):?>
                                    <tr>
                                        <?php for ($c = 0; $c < count($gradeColumns) + 3; $c++):?>
                                        <td class="bordered">&nbsp;</td>
                                        <?php endfor;?>
                                    </tr>
                                    <?php endfor;?>
                                </tbody>
                            </table>
                        </td>
                        <?php endforeach;?>
                    </tr>
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

$totalLabels = [
    InspectingItem::GRADE_A => 'Total Grade A (1)',
    InspectingItem::GRADE_A_PLUS => 'Total Grade A+ (2)',
    InspectingItem::GRADE_A_ASTERISK => 'Total Grade A* (3)',
    InspectingItem::GRADE_B => 'Total Grade B (4)',
    InspectingItem::GRADE_C => 'Total Grade C',
    InspectingItem::GRADE_PK => 'Total Piece Kecil',
    InspectingItem::GRADE_SAMPLE => 'Total Contoh',
];
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
        <?php $first = true; foreach ($totalLabels as $gradeKey => $label):?>
        <tr>
            <th class="bordered"><?=$label?></th>
            <td class="bordered"><?=$formatter->asDecimal($totalPiecesGrade[$gradeKey])?></td>
            <td class="bordered"><?=$formatter->asDecimal($totalRollGrade[$gradeKey])?></td>
            <td class="bordered"><?=$formatter->asDecimal($totalQtyGrade[$gradeKey])?></td>
            <?php if ($first): $first = false;?>
            <td rowspan="8">&nbsp;</td>
            <td rowspan="7">&nbsp;</td>
            <td rowspan="8">&nbsp;</td>
            <td rowspan="7">&nbsp;</td>
            <?php endif;?>
        </tr>
        <?php endforeach;?>

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

<p>Keterangan: (V) = Sudah diterima di Gudang Jadi</p>
