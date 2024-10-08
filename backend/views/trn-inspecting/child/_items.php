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

$no_wo = substr($model->wo->no, -1);
$defaultCheck = ($no_wo == 'L' ? true : false);
?>

<div class="box">
    <div class="box-header with-border">
        <div class="box-tools pull-left">
            <span><strong>Packing List</strong></span><br>
            <small><b>*checklist untuk menampilkan</b></small><br>
            <?= Html::checkbox('param3', $defaultCheck, ['label' => 'Made In Indonesia', 'id' => 'param3Checkbox']); ?>
            <?= Html::checkbox('param4', $defaultCheck, ['label' => 'Registrasi K3L', 'id' => 'param4Checkbox']); ?>
            <?= Html::checkbox('param5', true, ['label' => 'Aktifkan Pembulatan Decimal', 'id' => 'param5Checkbox']); ?>

        </div>

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
                <th>Grade A+</th>
                <th>Grade A*</th>
                <th>Lot No</th>
                <th>Defect</th>
                <th>Keterangan</th>
                <th>Qr-Code</th>
                <th>Qr-Data</th>
                <th>Qr-Code Print at</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model->getInspectingItems()->orderBy('id ASC')->all() as $index=>$item):?>
                <?php
                    /* @var $item InspectingItem*/

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
                    <td><?=($index+1).$item['join_piece']?></td>
                    <td>
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
                    <td>
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
                    <td>
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
                    <td>
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
                    <td>
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
                    <td>
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
                    <td>
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
                    <td><?=$item['lot_no']?></td>
                    <td><?=$item['defect']?></td>
                    <td><?=$item['note']?></td>
                    <td style="width: 100px;">
                        <?php 
                            $printed = $item['qr_print_at'] ? 'qrPrint btn btn-success center-block' : 'qrPrint btn btn-default center-block';
                            if ($item['is_head'] == 1) {
                                echo ' '.Html::a('PRINT '.'<span><i class="fa fa-qrcode"></i></span>', ['qr', 'id' => $item['id']], ['class' => $printed, 'id' => 'qrPrint'.$item['id']]);
                            }
                        ?>
                    </td>
                    <td style="width: 150px;"><?=$item['is_head'] == 1 ? $item['qr_code'] : ''?></td>
                    <td style="width: 200px;"><?=$item['qr_print_at'] ? $item['qr_print_at'] : '-'?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="box-footer with-border">
        <?php
        $totalPieces = $totalPiecesGrade[InspectingItem::GRADE_A] + $totalPiecesGrade[InspectingItem::GRADE_B] + $totalPiecesGrade[InspectingItem::GRADE_C] + $totalPiecesGrade[InspectingItem::GRADE_PK] + $totalPiecesGrade[InspectingItem::GRADE_SAMPLE] + $totalPiecesGrade[InspectingItem::GRADE_A_PLUS] + $totalPiecesGrade[InspectingItem::GRADE_A_ASTERISK];
        $totalQty = $totalQtyGrade[InspectingItem::GRADE_A] + $totalQtyGrade[InspectingItem::GRADE_B] + $totalQtyGrade[InspectingItem::GRADE_C] + $totalQtyGrade[InspectingItem::GRADE_PK] + $totalQtyGrade[InspectingItem::GRADE_SAMPLE] + $totalQtyGrade[InspectingItem::GRADE_A_PLUS] + $totalQtyGrade[InspectingItem::GRADE_A_ASTERISK];
        $totalRoll = $totalRollGrade[InspectingItem::GRADE_A] + $totalRollGrade[InspectingItem::GRADE_B] + $totalRollGrade[InspectingItem::GRADE_C] + $totalRollGrade[InspectingItem::GRADE_PK] + $totalRollGrade[InspectingItem::GRADE_SAMPLE] + $totalRollGrade[InspectingItem::GRADE_A_PLUS] + $totalRollGrade[InspectingItem::GRADE_A_ASTERISK];

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
            <div class="box-header with-border">
                <span><strong></strong></span>
                <div class="box-tools pull-left">
                    <?= Html::checkbox('param1', $defaultCheck, ['label' => 'Made In Indonesia', 'id' => 'param1Checkbox']); ?>
                    <?= Html::checkbox('param2', $defaultCheck, ['label' => 'Registrasi K3L', 'id' => 'param2Checkbox']); ?>
                    <?= Html::checkbox('param6', $defaultCheck, ['label' => 'Aktifkan Pembulatan Decimal', 'id' => 'param6Checkbox']); ?>
                    <p class="m-0"><small><b>*checklist untuk menampilkan</b></small></p>
                </div>
                <div class="box-tools pull-right">
                    <?= Html::a('PRINT '.'<span><i class="fa fa-qrcode"></i></span>', ['replace', 'id' => $model->id], ['class' => 'btn btn-default', 'id' => 'qrPrintLink']); ?>
                </div>
            </div>
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
                        <th>Total Grade A+</th>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingItem::GRADE_A_PLUS]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingItem::GRADE_A_PLUS]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingItem::GRADE_A_PLUS]);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Total Grade A*</th>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingItem::GRADE_A_ASTERISK]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingItem::GRADE_A_ASTERISK]);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingItem::GRADE_A_ASTERISK]);
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
<!-- JavaScript to handle the click event and append parameters -->
<?php $this->registerJs('
    $("#qrPrintLink").on("click", function(e) {
        e.preventDefault();

        // Get selected values from combo box or checkbox
        var param1Value = $("#param1Checkbox").is(":checked") ? "1" : "0"; // Use 1 for checked, 0 for unchecked
        var param2Value = $("#param2Checkbox").is(":checked") ? "1" : "0"; // Use 1 for checked, 0 for unchecked
        var param6Value = $("#param6Checkbox").is(":checked") ? "1" : "0"; // Use 1 for checked, 0 for unchecked

        // Determine the URL based on checkbox status
        var theView = (param1Value == 0 && param2Value == 0) ? "qr-all-without-attribute" : "qr-all";

        // Build the URL with the selected values
        var url = $(this).attr("href") + "&param1=" + param1Value + "&param2=" + param2Value + "&param6=" + param6Value;

        var replacedUrl = url.replace(/replace/, theView);

        // Redirect to the new URL
        window.location.href = replacedUrl;
    });

    $(".qrPrint").on("click", function(e) {
        e.preventDefault();

        // Get selected values from combo box or checkbox
        var param3Value = $("#param3Checkbox").is(":checked") ? "1" : "0"; // Use 1 for checked, 0 for unchecked
        var param4Value = $("#param4Checkbox").is(":checked") ? "1" : "0"; // Use 1 for checked, 0 for unchecked
        var param5Value = $("#param5Checkbox").is(":checked") ? "1" : "0"; // Use 1 for checked, 0 for unchecked

        // Build the URL with the selected values
        var url = $(this).attr("href") + "&param3=" + param3Value + "&param4=" + param4Value + "&param5=" + param5Value;

        // Redirect to the new URL
        window.location.href = url;
    });
'); ?>
