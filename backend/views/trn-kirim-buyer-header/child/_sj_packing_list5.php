<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimBuyerHeader */
/* @var $dataProviderKirimBuyer \yii\data\ActiveDataProvider*/
/* @var $formatter \yii\i18n\Formatter */

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnKirimBuyer;
use common\models\ar\TrnStockGreige;
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
                    <strong><?=Yii::$app->params['company']['nama']?></strong>
                    <br><?=Yii::$app->params['company']['alamat']?>
                </td>
                <td width="20%"><strong>PACKING LIST</strong></td>
                <td width="40%" style="text-align: right;"><strong>GAP-FRM-GDJ-02</strong></td>
            </tr>
        </table>

        <p></p>

        <table width="100%">
            <tr>
                <td width="70%">
                    <table width="100%">
                        <tr>
                            <td width="10%">NAMA BUYER</td>
                            <td width="3%">:</td>
                            <td><?=$model->nama_buyer?></td>
                        </tr>
                    </table>
                </td>
                <td width="30%">
                    <table width="100%">
                        <tr>
                            <td>TANGGAL KIRIM</td>
                            <td>:</td>
                            <td><?=$formatter->asDate($model->date)?></td>
                        </tr>
                    </table>
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
                <th colspan="2" style="text-align: center;">JUMLAH</th>
                <th rowspan="2" colspan="10" style="text-align: center;">PIECE LENGTH (YARD / METER / KG )</th>
            </tr>
            <tr>
                <th width="5%" style="text-align: center;">PCS</th>
                <th style="text-align: center;">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php
                $groupedByNoBal = [];
                foreach ($dataProviderKirimBuyer->models as $kirimBuyerModel) {
                    foreach ($kirimBuyerModel->trnKirimBuyerItems as $pssk) {
                        $no_bal = $pssk['no_bal'] ?: ''; // Jika no_bal kosong, set ke string kosong
                        $no_wo = $kirimBuyerModel->wo->no ?? ''; // Ambil no_wo jika ada

                        if ($no_wo) {
                            // Cek apakah no_wo sudah ada dalam $groupedByNoBal berdasarkan no_bal
                            $exists = false;
                            if (isset($groupedByNoBal[$no_bal])) {
                                foreach ($groupedByNoBal[$no_bal] as &$item) {
                                    if ($item['no_wo'] === $no_wo) {
                                        // Jika no_wo sudah ada, tambahkan ke qty
                                        $item['qty'][] = [
                                            'value' => $pssk['qty'],
                                            'grade' => $pssk->stockGrade,
                                        ];
                                        $exists = true;
                                        break;
                                    }
                                }
                            }

                            // Jika tidak ada no_wo yang sama, buat entri baru
                            if (!$exists) {
                                $groupedByNoBal[$no_bal][] = [
                                    'no_wo' => $no_wo,
                                    'design' => $kirimBuyerModel->nama_kain_alias,
                                    'no_bal' => $no_bal,
                                    'qty' => [[
                                        'value' => $pssk['qty'],
                                        'grade' => $pssk->stockGrade,
                                    ]],
                                ];
                            }
                        }
                    }
                }

                ksort($groupedByNoBal);

                //log $groupedByNoBal
                //    print("<pre>".print_r($groupedByNoBal ,true)."</pre>");
                //     die;
            ?>
            <?php foreach ($groupedByNoBal as $gbnbs):?>
                <?php foreach ($gbnbs as $key => $gbnb):?>
                <?php
                    $count = count($gbnb['qty']);
                    $totalPcs = 0;
                    $totalPcsPerGrade = [];
                    foreach ($gbnb['qty'] as $qty) {
                        if($qty['grade'] !== TrnStockGreige::GRADE_E) {
                            $totalPcs ++;
                        }
                        if (!isset($totalPcsPerGrade[$qty['grade']])) {
                            $totalPcsPerGrade[$qty['grade']] = [
                                'qty' => 0,
                                'totalQty' => 0, 
                            ];
                        }
                        $totalPcsPerGrade[$qty['grade']]['qty']++;
                        $totalPcsPerGrade[$qty['grade']]['totalQty'] += $qty['value'];
                    }
                    $itemsPerRow = 10;
                    $totalRows = ceil($count / $itemsPerRow);
                    $rowCounter = 0; // Initialize row counter
                ?>
                <tr>
                    <td rowspan="<?= ($totalRows + 2) ?>" style="text-align: center;"><?=$gbnb['no_bal'] ? $gbnb['no_bal'] : '' ?></td>
                    <td rowspan="<?= ($totalRows + 2) ?>" style="text-align: center;"><?=$gbnb['no_wo'] ? $gbnb['no_wo'] : '' ?></td>
                    <td rowspan="<?= ($totalRows + 2) ?>" style="text-align: center;"><?=$gbnb['design'] ? $gbnb['design'] : '' ?></td>
                    <td rowspan="<?= ($totalRows + 2) ?>" style="text-align: center;"><?= $totalPcs.' Pcs'?></td>
                    <td rowspan="<?= ($totalRows + 2) ?>" style="text-align: center;">
                        <?php
                            foreach ($totalPcsPerGrade as $key => $value) {
                                if ($key !== TrnStockGreige::GRADE_E) {
                                    echo $value['qty'].' Pcs ('.TrnStockGreige::gradeOptions()[$key].') = '.$value['totalQty'].' Yard,<br>';
                                }
                            }
                        ?>
                    </td>
                    <?php
                        for ($row = 0; $row < $totalRows; $row++) {
                            $rowCounter++; // Increment row counter for each new row
                            $sample = 0;
                            echo '<tr>';
                            for ($col = 0; $col < $itemsPerRow; $col++) {
                                $index = $row * $itemsPerRow + $col;
                                    if ($index < $count) {
                                        if ($gbnb['qty'][$index]['grade'] !== TrnStockGreige::GRADE_E) {
                                            echo '<td style="text-align: center; width: 50px;">'.$gbnb['qty'][$index]['value'].'</td>';
                                        }else {
                                            $sample = $gbnb['qty'][$index]['value'];
                                        }
                                    } else {
                                        // Echo an empty cell if no more data
                                        echo '<td style="width: 50px;"></td>';
                                    }
                            }
                            echo '</tr>';
                        }
                        echo '<tr>';
                            echo '<td colspan="10" style="text-align: center;">' . $sample . ' Yard</td>';
                        echo '</tr>';
                    ?>
                </tr>
                <?php endforeach;?>
            <?php endforeach;?>
            </tbody>
        </table>

        <p></p>
    </div>
</div>
