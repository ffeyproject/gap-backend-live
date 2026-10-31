<?php
/* @var $model common\models\ar\TrnInspecting */
/* @var $columns int jumlah kolom tabel packing list (diatur controller agar muat 1 lembar) */

use backend\components\Converter;
use common\models\ar\InspectingItem;
use common\models\ar\MstGreigeGroup;

$columns = isset($columns) ? max(1, (int)$columns) : 2;

$formatter = Yii::$app->formatter;
$kombinasi = '';
$coba = $model->kombinasi;
$stamping = $model->mo->face_stamping;
$woNo = $model->wo->no;
$motif = $model->wo->greigeNamaKain;
$pieceLength = $model->mo->piece_length;
$noKartu = '';
$design = $model->mo->design;
$jenisOrder = '';
$jenisDesign = '';
$buyer = $model->sc->cust->cust_no;
$status = '';
if($model->kartu_process_dyeing_id !== null){
    $kombinasi = $coba;
    $noKartu = $model->kartuProcessDyeing->no;
    $jenisOrder = 'Dyeing';
    $jenisDesign = $model->mo->article;
}elseif ($model->kartu_process_printing_id !== null){
    $kombinasi = $model->kartuProcessPrinting->woColor->moColor->color;
    $noKartu = $model->kartuProcessPrinting->no;
    $jenisOrder = 'Printing';
    $jenisDesign = $design;
}



$greige = $model->wo->greige;
$unitName = MstGreigeGroup::unitOptions()[$model->unit];
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
    InspectingItem::GRADE_PUTIH => 'Putih',
];

$totalQtyGrade = [
    InspectingItem::GRADE_A => 0,
    InspectingItem::GRADE_B => 0,
    InspectingItem::GRADE_C => 0,
    InspectingItem::GRADE_PK => 0,
    InspectingItem::GRADE_SAMPLE => 0,
    InspectingItem::GRADE_A_PLUS => 0,
    InspectingItem::GRADE_A_ASTERISK => 0,
    InspectingItem::GRADE_PUTIH => 0,
];
$totalPiecesGrade = [
    InspectingItem::GRADE_A => 0,
    InspectingItem::GRADE_B => 0,
    InspectingItem::GRADE_C => 0,
    InspectingItem::GRADE_PK => 0,
    InspectingItem::GRADE_SAMPLE => 0,
    InspectingItem::GRADE_A_PLUS => 0,
    InspectingItem::GRADE_A_ASTERISK => 0,
    InspectingItem::GRADE_PUTIH => 0,
];
$totalRollGrade = [
    InspectingItem::GRADE_A => 0,
    InspectingItem::GRADE_B => 0,
    InspectingItem::GRADE_C => 0,
    InspectingItem::GRADE_PK => 0,
    InspectingItem::GRADE_SAMPLE => 0,
    InspectingItem::GRADE_A_PLUS => 0,
    InspectingItem::GRADE_A_ASTERISK => 0,
    InspectingItem::GRADE_PUTIH => 0,
];
$joinPieces = [
    InspectingItem::GRADE_A => [],
    InspectingItem::GRADE_B => [],
    InspectingItem::GRADE_C => [],
    InspectingItem::GRADE_PK => [],
    InspectingItem::GRADE_SAMPLE => [],
    InspectingItem::GRADE_A_PLUS => [],
    InspectingItem::GRADE_A_ASTERISK => [],
    InspectingItem::GRADE_PUTIH => [],
];

$postedJoinPieces = $model->getInspectingItems()
    ->alias('it')
    ->select('it.join_piece')
    ->leftJoin('trn_gudang_jadi gj', 'gj.id_from = it.id AND gj.trans_from = \'INS\'')
    ->where(['and',
        ['it.is_head' => 1],
        ['not', ['it.join_piece' => null]],
        ['<>', 'it.join_piece', '']
    ])
    ->andWhere(['or', ['it.is_posted' => true], ['not', ['gj.id' => null]]])
    ->column();

$postedJoinPieces = array_unique(array_filter($postedJoinPieces));

$query = $model->getInspectingItems()
    ->alias('it')
    ->select(['it.*', 'gj.id AS gj_id'])
    ->leftJoin('trn_gudang_jadi gj', 'gj.id_from = it.id AND gj.trans_from = \'INS\'');

$whereClause = ['or', ['it.is_posted' => true], ['not', ['gj.id' => null]]];
if (!empty($postedJoinPieces)) {
    $whereClause[] = ['in', 'it.join_piece', $postedJoinPieces];
}

$inspectingItems = $query
    ->where($whereClause)
    ->orderBy(new \yii\db\Expression('COALESCE(it.no_urut, it.id) ASC'))
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
$tglKirimHeader = empty($postedDates) ? $model->date : implode(', ', $postedDates);

// siapkan baris + akumulasi total
$rows = [];
foreach ($inspectingItems as $index => $item) {
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
        'no' => ($item['no_urut'] ?? ($index + 1)).$item['join_piece'].($isReceived ? ' (V)' : ''),
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
            <td><?=$model->tanggal_inspeksi?></td>
            <td style="width: 50px;">&nbsp;</td>
            <td style="width: 100px;">Kombinasi</td>
            <td width="1%">:</td>
            <td><?=$coba?></td>
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
            <td><?=$jenisDesign?></td>
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
            <td>Satuan</td>
            <td>:</td>
            <td><?=MstGreigeGroup::unitOptions()[$model->unit]?></td>
            <td>&nbsp;</td>
            <td>Lokal/Export</td>
            <td>:</td>
            <td><?=\common\models\ar\TrnSc::tipeKontrakOptions()[$model->sc->tipe_kontrak]?></td>
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
$totalPieces = $totalPiecesGrade[InspectingItem::GRADE_A] + $totalPiecesGrade[InspectingItem::GRADE_A_PLUS] + $totalPiecesGrade[InspectingItem::GRADE_A_ASTERISK] + $totalPiecesGrade[InspectingItem::GRADE_B] + $totalPiecesGrade[InspectingItem::GRADE_C] + $totalPiecesGrade[InspectingItem::GRADE_PK] + $totalPiecesGrade[InspectingItem::GRADE_SAMPLE] + $totalPiecesGrade[InspectingItem::GRADE_PUTIH];
$totalQty = $totalQtyGrade[InspectingItem::GRADE_A] + $totalQtyGrade[InspectingItem::GRADE_A_PLUS] + $totalQtyGrade[InspectingItem::GRADE_A_ASTERISK] + $totalQtyGrade[InspectingItem::GRADE_B] + $totalQtyGrade[InspectingItem::GRADE_C] + $totalQtyGrade[InspectingItem::GRADE_PK] + $totalQtyGrade[InspectingItem::GRADE_SAMPLE] + $totalQtyGrade[InspectingItem::GRADE_PUTIH];
$totalRoll = $totalRollGrade[InspectingItem::GRADE_A] + $totalRollGrade[InspectingItem::GRADE_A_PLUS] + $totalRollGrade[InspectingItem::GRADE_A_ASTERISK] + $totalRollGrade[InspectingItem::GRADE_B] + $totalRollGrade[InspectingItem::GRADE_C] + $totalRollGrade[InspectingItem::GRADE_PK] + $totalRollGrade[InspectingItem::GRADE_SAMPLE] + $totalRollGrade[InspectingItem::GRADE_PUTIH];

if($model->unit == MstGreigeGroup::UNIT_YARD){
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
    InspectingItem::GRADE_PUTIH => 'Total Grade Putih',
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
            <td rowspan="9">&nbsp;</td>
            <td rowspan="8">&nbsp;</td>
            <td rowspan="9">&nbsp;</td>
            <td rowspan="8">&nbsp;</td>
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
