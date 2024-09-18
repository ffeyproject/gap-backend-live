<?php
use backend\components\Converter;
use common\models\ar\InspectingItem;
use common\models\ar\MstGreige;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnInspecting;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrnInspecting */
/* @var $greige MstGreige */
/* @var $formatter \yii\i18n\Formatter*/

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

<div class="box">
    <div class="box-header with-border">
        <span><strong>Packing List</strong></span>

        <div class="box-tools pull-right">
                <span class="label label-info">
                    <?='<strong>Greige: '.$greige->nama_kain.' - Per Batch: '.Yii::$app->formatter->asDecimal($perBatch).' '.MstGreigeGroup::unitOptions()[$greige->group->unit].'</strong>'?>
                </span>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped">
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
            <?php foreach ($model->getInspectingItems()->orderBy('id ASC')->all() as $index=>$item):?>
                <?php
                /* @var $item InspectingItem*/

                if($item['qty'] > 0){
                    // akumulasi hanya berlaku jika qty > 0
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
                }
                ?>
                <tr>
                    <td><?=($index+1).$item['join_piece']?></td>
                    <td>
                        <?php
                        if($item['grade'] === InspectingItem::GRADE_A){
                            echo $formatter->asDecimal($item['qty']);
                        }else{
                            echo '0';
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if($item['grade'] === InspectingItem::GRADE_B){
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
    </div>
    <div class="box-footer with-border">
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

        <p><strong>Total: <?=Yii::$app->formatter->asDecimal($totalM)?> M</strong></p>
        <p><strong>Susut: <?=Yii::$app->formatter->asDecimal($susutM)?> M (<?=$susutPcnt?>%)</strong></p>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Total</th>
                        <th>Total Pieces</th>
                        <th>Total Roll</th>
                        <th>Total Ukuran</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th>Total Grade A</th>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingItem::GRADE_A]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingItem::GRADE_A]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingItem::GRADE_A]);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Total Grade B</th>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingItem::GRADE_B]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingItem::GRADE_B]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingItem::GRADE_B]);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Total Grade C</th>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingItem::GRADE_C]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingItem::GRADE_C]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingItem::GRADE_C]);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Total Piece Kecil</th>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingItem::GRADE_PK]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingItem::GRADE_PK]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingItem::GRADE_PK]);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Total Contoh</th>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingItem::GRADE_SAMPLE]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingItem::GRADE_SAMPLE]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingItem::GRADE_SAMPLE]);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Grand Total</th>
                        <th><?=$formatter->asDecimal($totalPieces)?></th>
                        <th><?=$formatter->asDecimal($totalRoll)?></th>
                        <th><?=$formatter->asDecimal($totalQty)?></th>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <span><strong>Catatan Penolakan</strong></span>
            </div>

            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pesan</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(!empty($model->delivery_reject_note)){
                        $notes = \yii\helpers\Json::decode($model->delivery_reject_note);
                        foreach ($notes as $note) {
                            echo Html::tag(
                                'tr',
                                Html::tag('td', $note['date_time'])
                                .Html::tag('td', $note['note'])
                            );
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
