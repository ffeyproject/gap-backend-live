<?php

use backend\components\Converter;
use common\models\ar\InspectingMklBjItems;
use common\models\ar\MstGreigeGroup;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\ar\TrnScGreige;

/* @var $this yii\web\View */
/* @var $model common\models\ar\InspectingMklBj */

$this->title = 'Inspecting Makloon Dan Barang Jadi - '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inspecting Makloon Dan Barang Jadi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

$perBatch = $model->wo->greige->group->qty_per_batch;
$formatter = Yii::$app->formatter;

$totalQtyGrade = [
    InspectingMklBjItems::GRADE_A => 0,
    InspectingMklBjItems::GRADE_B => 0,
    InspectingMklBjItems::GRADE_C => 0,
    InspectingMklBjItems::GRADE_PK => 0,
    InspectingMklBjItems::GRADE_SAMPLE => 0,
    InspectingMklBjItems::GRADE_A_PLUS => 0,
    InspectingMklBjItems::GRADE_A_ASTERISK => 0,
    InspectingMklBjItems::GRADE_PUTIH => 0,
];
$totalPiecesGrade = [
    InspectingMklBjItems::GRADE_A => 0,
    InspectingMklBjItems::GRADE_B => 0,
    InspectingMklBjItems::GRADE_C => 0,
    InspectingMklBjItems::GRADE_PK => 0,
    InspectingMklBjItems::GRADE_SAMPLE => 0,
    InspectingMklBjItems::GRADE_A_PLUS => 0,
    InspectingMklBjItems::GRADE_A_ASTERISK => 0,
    InspectingMklBjItems::GRADE_PUTIH => 0,
];
$totalRollGrade = [
    InspectingMklBjItems::GRADE_A => 0,
    InspectingMklBjItems::GRADE_B => 0,
    InspectingMklBjItems::GRADE_C => 0,
    InspectingMklBjItems::GRADE_PK => 0,
    InspectingMklBjItems::GRADE_SAMPLE => 0,
    InspectingMklBjItems::GRADE_A_PLUS => 0,
    InspectingMklBjItems::GRADE_A_ASTERISK => 0,
    InspectingMklBjItems::GRADE_PUTIH => 0,
];
$joinPieces = [
    InspectingMklBjItems::GRADE_A => [],
    InspectingMklBjItems::GRADE_B => [],
    InspectingMklBjItems::GRADE_C => [],
    InspectingMklBjItems::GRADE_PK => [],
    InspectingMklBjItems::GRADE_SAMPLE => [],
    InspectingMklBjItems::GRADE_A_PLUS => [],
    InspectingMklBjItems::GRADE_A_ASTERISK => [],
    InspectingMklBjItems::GRADE_PUTIH => [],
];

$no_wo = substr($model->wo->no, -1);
$defaultCheck = ($no_wo == 'L' ? true : false);
?>
<div class="inspecting-mkl-bj-view">

    <p>
        <?php
        switch ($model->status){
            case $model::STATUS_DRAFT:
                echo Html::a('Upgrade', ['upgrade', 'id' => $model->id], ['class' => 'btn btn-success']).' ';
                echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']).' ';
                echo Html::a('Delete', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]).' ';
                echo Html::a('Posting', ['posting', 'id' => $model->id], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => 'Are you sure you want to posting this item?',
                        'method' => 'post',
                    ],
                ]);
                break;
        }

        echo ' '.Html::a('Print', ['print', 'id' => $model->id], ['class' => 'btn btn-default']);
        ?>
    </p>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'no',
                            'woNo',
                            'colorName',
                            'tgl_inspeksi:date',
                            'tgl_kirim:date',
                            'no_lot',
                            'k3l_code',
                            'no_memo',
                        ],
                    ]) ?>
                </div>

                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'jenisName',
                            'jenisInspeksi',
                            'satuanName',
                            'created_at:datetime',
                            'created_by',
                            'updated_at:datetime',
                            'updated_by',
                            'statusName',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!--Items-->
    <div class="box">
        <div class="box-header with-border">
            <div class="box-tools pull-left">
                <span><strong>Packing List</strong></span><br>
                <small><b>*checklist untuk menampilkan</b></small><br>
                <?= Html::checkbox('param3', $defaultCheck, ['label' => 'Made In Indonesia', 'id' => 'param3Checkbox']); ?>
                <?= Html::checkbox('param4', $defaultCheck, ['label' => 'Registrasi K3L', 'id' => 'param4Checkbox']); ?>
                <?= Html::checkbox('param5', $defaultCheck, ['label' => 'Aktifkan Pembulatan Decimal', 'id' => 'param5Checkbox']); ?>

            </div>

            <div class="box-tools pull-right">
                <span class="label label-info">
                    <?='<strong>Greige: '.$model->wo->greige->nama_kain.' - Per Batch: '.Yii::$app->formatter->asDecimal($perBatch).' '.$model->satuanName.'</strong>'?>
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
                        <th>Grade Putih</th>
                        <th>No Lot</th>
                        <th>Defect Input</th>
                        <th>Defect</th>
                        <th>Nilai Point</th>
                        <th>Keterangan</th>
                        <th>QR Code</th>
                        <th>QR Code ID</th>
                        <th>Qr-Code Print at</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                        $itemsQuery = $model->getItems();
                        $inspectingId = $model->id;

                        $hasNoUrut = \common\models\ar\InspectingMklBjItems::find()
                            ->where(['inspecting_id' => $inspectingId])
                            ->andWhere(['IS NOT', 'no_urut', null])
                            ->exists();

                        $items = $itemsQuery
                            ->orderBy($hasNoUrut ? 'no_urut ASC' : 'id ASC')
                            ->all();
                    ?>
                    <?php foreach ($items as $index => $item): ?>
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
                        <td><?=($index+1).$item['join_piece']?></td>
                        <td>
                            <?php
                                if ($item['grade_up'] <> NULL) {
                                    if($item['grade_up'] === InspectingMklBjItems::GRADE_A){
                                        echo $formatter->asDecimal($item['qty']);
                                    }else{
                                        echo '0';
                                    }
                                } else {
                                    if($item['grade'] === InspectingMklBjItems::GRADE_A){
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
                                    if($item['grade_up'] === InspectingMklBjItems::GRADE_B){
                                        echo $formatter->asDecimal($item['qty']);
                                    }else{
                                        echo '0';
                                    }
                                } else {
                                    if($item['grade'] === InspectingMklBjItems::GRADE_B){
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
                                    if($item['grade_up'] === InspectingMklBjItems::GRADE_C){
                                        echo $formatter->asDecimal($item['qty']);
                                    }else{
                                        echo '0';
                                    }
                                } else {
                                    if($item['grade'] === InspectingMklBjItems::GRADE_C){
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
                                    if($item['grade_up'] === InspectingMklBjItems::GRADE_PK){
                                        echo $formatter->asDecimal($item['qty']);
                                    }else{
                                        echo '0';
                                    }
                                } else {
                                    if($item['grade'] === InspectingMklBjItems::GRADE_PK){
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
                                    if($item['grade_up'] === InspectingMklBjItems::GRADE_SAMPLE){
                                        echo $formatter->asDecimal($item['qty']);
                                    }else{
                                        echo '0';
                                    }
                                } else {
                                    if($item['grade'] === InspectingMklBjItems::GRADE_SAMPLE){
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
                                    if($item['grade_up'] === InspectingMklBjItems::GRADE_A_PLUS){
                                        echo $formatter->asDecimal($item['qty']);
                                    }else{
                                        echo '0';
                                    }
                                } else {
                                    if($item['grade'] === InspectingMklBjItems::GRADE_A_PLUS){
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
                                    if($item['grade_up'] === InspectingMklBjItems::GRADE_A_ASTERISK){
                                        echo $formatter->asDecimal($item['qty']);
                                    }else{
                                        echo '0';
                                    }
                                } else {
                                    if($item['grade'] === InspectingMklBjItems::GRADE_A_ASTERISK){
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
                                    if($item['grade_up'] === InspectingMklBjItems::GRADE_PUTIH){
                                        echo $formatter->asDecimal($item['qty']);
                                    }else{
                                        echo '0';
                                    }
                                } else {
                                    if($item['grade'] === InspectingMklBjItems::GRADE_PUTIH){
                                        echo $formatter->asDecimal($item['qty']);
                                    }else{
                                        echo '0';
                                    }
                                }
                            ?>
                        </td>
                        <td><?=$item['lot_no']?></td>
                        <td><?=$item['defect']?></td>
                        <td>
                            <?php
                        foreach ($item->defectInspectingItems as $defectItem) {
                            echo '<span class="primary" style="color: red;">(' . $defectItem->meterage . ')</span>' . 
                                ' / <span class="primary" style="color: blue;">' . $defectItem->mstKodeDefect->no_urut . '</span>' . 
                                ' - <span class="primary" style="color: blue;">' . $defectItem->mstKodeDefect->nama_defect . '</span>' . 
                                ' / <span class="primary" style="color: green;">(' . $defectItem->point . ')</span>' . 
                                '<br>';
                        }
                        ?>
                        </td>
                        <td>
                            <?php 
                            if ($item['is_head'] == 1) {
                                $options = $item->inspecting->wo->scGreige->lebar_kain;
                                switch ($options) {
                                    case TrnScGreige::LEBAR_KAIN_44:
                                        $lebarKain = 44;
                                        break;
                                    case TrnScGreige::LEBAR_KAIN_58:
                                        $lebarKain = 58;
                                        break;
                                    case TrnScGreige::LEBAR_KAIN_64:
                                        $lebarKain = 64;
                                        break;
                                    case TrnScGreige::LEBAR_KAIN_66:
                                        $lebarKain = 66;
                                        break;
                                    default:
                                        $lebarKain = 0;
                                }

                                $totalPoint = 0;

                                $inspectingId = $item->inspecting_id;
                                $joinPiece = $item->join_piece;
                                $totalPoint = $item->getTotalPoints($inspectingId, $joinPiece);

                                $qty = $item['qty_sum'];
                                 $satuan = MstGreigeGroup::unitOptions()[$model->satuan] ?? '';

                                if ($qty != 0 && $qty != null && $lebarKain != 0) {
                                    if ($satuan == 'Yard') {
                                        
                                        $result = ($totalPoint * 36 * 100) / ($lebarKain * $qty);
                                    } elseif ($satuan == 'Meter') {
                                         $result = ($totalPoint * 36 * 100) / ($lebarKain * $qty * 0.9144);
                                    } elseif ($satuan == 'Kilogram') {
                                       $result = ($totalPoint * 36 * 100) / ($lebarKain * $qty * 764.55486);
                                    }

                                    echo number_format($result, 2);
                                } else {
                                    echo '';
                                }
                            } else {
                                echo '';
                            }
                        ?>
                        </td>

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
            $totalPieces = $totalPiecesGrade[InspectingMklBjItems::GRADE_A] + $totalPiecesGrade[InspectingMklBjItems::GRADE_B] + $totalPiecesGrade[InspectingMklBjItems::GRADE_C] + $totalPiecesGrade[InspectingMklBjItems::GRADE_PK] + $totalPiecesGrade[InspectingMklBjItems::GRADE_SAMPLE] + $totalPiecesGrade[InspectingMklBjItems::GRADE_A_ASTERISK] + $totalPiecesGrade[InspectingMklBjItems::GRADE_A_PLUS] + $totalPiecesGrade[InspectingMklBjItems::GRADE_PUTIH];
            $totalQty = $totalQtyGrade[InspectingMklBjItems::GRADE_A] + $totalQtyGrade[InspectingMklBjItems::GRADE_B] + $totalQtyGrade[InspectingMklBjItems::GRADE_C] + $totalQtyGrade[InspectingMklBjItems::GRADE_PK] + $totalQtyGrade[InspectingMklBjItems::GRADE_SAMPLE] + $totalQtyGrade[InspectingMklBjItems::GRADE_A_ASTERISK] + $totalQtyGrade[InspectingMklBjItems::GRADE_A_PLUS] + $totalQtyGrade[InspectingMklBjItems::GRADE_PUTIH];
            $totalRoll = $totalRollGrade[InspectingMklBjItems::GRADE_A] + $totalRollGrade[InspectingMklBjItems::GRADE_B] + $totalRollGrade[InspectingMklBjItems::GRADE_C] + $totalRollGrade[InspectingMklBjItems::GRADE_PK] + $totalRollGrade[InspectingMklBjItems::GRADE_SAMPLE] + $totalRollGrade[InspectingMklBjItems::GRADE_A_ASTERISK] + $totalRollGrade[InspectingMklBjItems::GRADE_A_PLUS] + $totalRollGrade[InspectingMklBjItems::GRADE_PUTIH];

            if($model->satuan == MstGreigeGroup::UNIT_YARD){
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
    <!--Items-->
</div>

<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
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
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingMklBjItems::GRADE_A_PLUS]);
                            ?>
                            </td>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingMklBjItems::GRADE_A_PLUS]);
                            ?>
                            </td>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingMklBjItems::GRADE_A_PLUS]);
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Grade A*</th>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingMklBjItems::GRADE_A_ASTERISK]);
                            ?>
                            </td>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingMklBjItems::GRADE_A_ASTERISK]);
                            ?>
                            </td>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingMklBjItems::GRADE_A_ASTERISK]);
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Grade C</th>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingMklBjItems::GRADE_C]);
                            ?>
                            </td>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingMklBjItems::GRADE_C]);
                            ?>
                            </td>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingMklBjItems::GRADE_C]);
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Piece Kecil</th>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingMklBjItems::GRADE_PK]);
                            ?>
                            </td>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingMklBjItems::GRADE_PK]);
                            ?>
                            </td>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingMklBjItems::GRADE_PK]);
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Contoh</th>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingMklBjItems::GRADE_SAMPLE]);
                            ?>
                            </td>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingMklBjItems::GRADE_SAMPLE]);
                            ?>
                            </td>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingMklBjItems::GRADE_SAMPLE]);
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Grade A</th>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingMklBjItems::GRADE_A]);
                            ?>
                            </td>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingMklBjItems::GRADE_A]);
                            ?>
                            </td>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingMklBjItems::GRADE_A]);
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Grade B</th>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingMklBjItems::GRADE_B]);
                            ?>
                            </td>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingMklBjItems::GRADE_B]);
                            ?>
                            </td>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingMklBjItems::GRADE_B]);
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Grade Putih</th>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingMklBjItems::GRADE_PUTIH]);
                            ?>
                            </td>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingMklBjItems::GRADE_PUTIH]);
                            ?>
                            </td>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingMklBjItems::GRADE_PUTIH]);
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
                    if($model->delivery_reject_note !== null){
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