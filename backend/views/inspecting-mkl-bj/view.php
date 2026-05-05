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
?>

<style>
#loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(5px);
    z-index: 10000;
    display: none;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    transition: all 0.3s ease;
}
.loader-content {
    text-align: center;
}
.premium-loader {
    width: 80px;
    height: 80px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #3498db;
    border-radius: 50%;
    animation: spin 1s cubic-bezier(0.68, -0.55, 0.27, 1.55) infinite;
    margin: 0 auto 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.loader-text {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 1.2rem;
    color: #2c3e50;
    font-weight: 600;
    letter-spacing: 1px;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<div id="loading-overlay">
    <div class="loader-content">
        <div class="premium-loader"></div>
        <div class="loader-text">MEMPROSES DATA...</div>
    </div>
</div>

<?php

$totalQtyGrade = [
    InspectingMklBjItems::GRADE_A => 0,
    InspectingMklBjItems::GRADE_B => 0,
    InspectingMklBjItems::GRADE_C => 0,
    InspectingMklBjItems::GRADE_PK => 0,
    InspectingMklBjItems::GRADE_SAMPLE => 0,
    InspectingMklBjItems::GRADE_A_PLUS => 0,
    InspectingMklBjItems::GRADE_A_ASTERISK => 0,
    InspectingMklBjItems::GRADE_PUTIH => 0,
    InspectingMklBjItems::GRADE_D => 0,
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
    InspectingMklBjItems::GRADE_D => 0,
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
    InspectingMklBjItems::GRADE_D => 0,
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
    InspectingMklBjItems::GRADE_D => [],
];

$no_wo = substr($model->wo->no, -1);
$defaultCheck = ($no_wo == 'L' ? true : false);
?>
<div class="inspecting-mkl-bj-view">

    <p>
        <?php
        $hasDraftItems = \common\models\ar\InspectingMklBjItems::find()->where(['inspecting_id' => $model->id, 'is_posted' => false])->exists();

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
                break;
        }

        if ($model->status == $model::STATUS_DRAFT || $model->status == $model::STATUS_POSTED || $model->status == $model::STATUS_POSTED_PARTIAL) {
            if ($hasDraftItems) {
                $isAnyPrinted = \common\models\ar\InspectingMklBjItems::find()->where(['inspecting_id' => $model->id, 'is_posted' => false])->andWhere(['not', ['qr_print_at' => null]])->exists();
                if($isAnyPrinted){
                    echo Html::textInput('postingDateVisible', $model->tgl_kirim, [
                        'type' => 'date', 
                        'id' => 'posting-date-input', 
                        'style' => 'width: 150px; display: inline-block; vertical-align: middle; margin-right: 5px;', 
                        'class' => 'form-control'
                    ]);
                    echo Html::button('Posting', [
                        'class' => 'btn btn-warning',
                        'onclick' => 'postingItems()'
                    ]).' ';
                }else{
                    echo Html::a('Posting', 'javascript:void(0)', [
                        'class' => 'btn btn-warning',
                        'disabled' => 'disabled',
                        'title' => 'Cetak QR Code terlebih dahulu untuk dapat melakukan posting.',
                        'onclick' => 'alert("Cetak QR Code terlebih dahulu untuk dapat melakukan posting."); return false;'
                    ]).' ';
                }
            }
        }

        if ($model->status == $model::STATUS_DRAFT) {
            echo Html::a('Hapus Semua Defect', ['hapus-semua-defect', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Apakah Anda yakin ingin menghapus semua defect dari inspeksi ini?',
                    'method' => 'post',
                ],
            ]);
        }

        echo ' '.Html::a('Print', ['print', 'id' => $model->id], ['class' => 'btn btn-default', 'target' => '_blank']);
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
    <?= Html::beginForm(['posting', 'id' => $model->id], 'post', ['id' => 'posting-form']) ?>
    <div class="box">
        <div class="box-header with-border">
            <div class="box-tools pull-left">
                <span><strong>Packing List</strong></span><br>
                <small><b>*checklist untuk menampilkan</b></small><br>
                <?= Html::checkbox('param3', $defaultCheck, ['label' => 'Made In Indonesia', 'id' => 'param3Checkbox']); ?>
                <?= Html::checkbox('param4', $defaultCheck, ['label' => 'Registrasi K3L', 'id' => 'param4Checkbox']); ?>
                <?= Html::checkbox('param5', $defaultCheck, ['label' => 'Aktifkan Pembulatan Decimal', 'id' => 'param5Checkbox']); ?>
                <?= Html::checkbox('param7', $defaultCheck, ['label' => '2 Satuan', 'id' => 'param7Checkbox']); ?>

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
                        <th>No Urut</th>
                        <th>Grade A</th>
                        <th>Grade B</th>
                        <th>Grade C</th>
                        <th>Grade D</th>
                        <th>Piece Kecil</th>
                        <th>Contoh</th>
                        <th>Grade A+</th>
                        <th>Grade A*</th>
                        <th>Grade Putih</th>
                        <th>No Lot</th>
                        <th>Defect Input</th>
                        <th>Defect</th>
                        <!-- <th>Nilai Point</th> -->
                        <th>Keterangan</th>
                        <th>QR Code</th>
                        <th>QR Code ID</th>
                        <th>ID Barang</th>
                        <th>Qr-Code Print at</th>
                        <th>Tgl Posting</th>
                        <th>Pilih <?= Html::checkbox('check_all_items', false, ['id' => 'check_all_items']) ?></th>
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
                            ->with(['defectInspectingItems.mstKodeDefect'])
                            ->orderBy($hasNoUrut ? 'no_urut ASC' : 'id ASC')
                            ->all();
                            
                        $receivedItemIds = \common\models\ar\TrnGudangJadi::find()
                            ->select('id_from')
                            ->where(['id_from' => \yii\helpers\ArrayHelper::getColumn($items, 'id'), 'trans_from' => 'MKL'])
                            ->column();

                        $joinPieceToHeadId = [];
                        $joinPieceHasReceived = [];
                        foreach ($items as $ii) {
                            if ($ii->is_head == 1 && !empty($ii->join_piece)) {
                                $joinPieceToHeadId[$ii->join_piece] = $ii->id;
                            }
                            if (!empty($ii->join_piece) && in_array($ii->id, $receivedItemIds)) {
                                $joinPieceHasReceived[$ii->join_piece] = true;
                            }
                        }
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
                        <td><?= $item->no_urut ?: '' ?></td>
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
                                    if($item['grade_up'] === InspectingMklBjItems::GRADE_D){
                                        echo $formatter->asDecimal($item['qty']);
                                    }else{
                                        echo '0';
                                    }
                                } else {
                                    if($item['grade'] === InspectingMklBjItems::GRADE_D){
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
                        <td><?=$item['note']?></td>
                        <td style="width: 100px;">
                            <?php 
                                $printed = $item['qr_print_at'] ? 'qrPrint btn btn-success center-block' : 'qrPrint btn btn-default center-block';
                                if ($item['is_head'] == 1) {
                                    echo ' '.Html::a('PRINT '.'<span><i class="fa fa-qrcode"></i></span>', ['qr', 'id' => $item['id']], ['class' => $printed, 'id' => 'qrPrint'.$item['id'], 'target' => '_blank']);
                                }
                            ?>
                        </td>
                        <td style="width: 100px;"><?=$item['is_head'] == 1 ? $item['qr_code'] : ''?></td>
                        <td><?= $item['id'] ?></td>
                        <td style="width: 100px;"><?=$item['qr_print_at'] ? $item['qr_print_at'] : '-'?></td>
                        <td><?=$item['posted_at'] ? $item['posted_at'] : '-'?></td>
                        <td>
                            <?php
                                $isReceived = in_array($item->id, $receivedItemIds) || $item->qty <= 0;
                                if (!$isReceived && !empty($item->join_piece) && isset($joinPieceHasReceived[$item->join_piece])) {
                                    $isReceived = true;
                                }

                                if ($isReceived) {
                                    echo '<span class="label label-success">Diterima Gudang Jadi</span>';
                                } else if ($item['is_posted']) {
                                    echo '<span class="label label-warning">Posted</span> ';
                                    if ($item['is_head'] == 1) {
                                        echo Html::a('<i class="fa fa-undo"></i> Unpost', ['unpost-item', 'id' => $item['id']], [
                                            'class' => 'btn btn-xs btn-danger',
                                            'data-confirm' => 'Apakah Anda yakin ingin membatalkan posting item ini?',
                                            'data-method' => 'post',
                                            'title' => 'Unpost Item'
                                        ]);
                                    }
                                } else if ($item['is_head'] == 1) {
                                    echo Html::checkbox('postedItemIds[]', false, [
                                        'value' => $item['id'], 
                                        'class' => 'check-item',
                                        'style' => $item['qr_print_at'] ? '' : 'display:none;'
                                    ]);
                                    if (!$item['qr_print_at']) {
                                        echo '<small class="text-muted label-print-dulu">Print QR dulu</small>';
                                    }
                                }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>

        <div class="box-footer with-border">
            <?php
            $totalPieces = $totalPiecesGrade[InspectingMklBjItems::GRADE_A] + $totalPiecesGrade[InspectingMklBjItems::GRADE_B] + $totalPiecesGrade[InspectingMklBjItems::GRADE_C] + $totalPiecesGrade[InspectingMklBjItems::GRADE_PK] + $totalPiecesGrade[InspectingMklBjItems::GRADE_SAMPLE] + $totalPiecesGrade[InspectingMklBjItems::GRADE_A_ASTERISK] + $totalPiecesGrade[InspectingMklBjItems::GRADE_A_PLUS] + $totalPiecesGrade[InspectingMklBjItems::GRADE_PUTIH] + $totalPiecesGrade[InspectingMklBjItems::GRADE_D];
            $totalQty = $totalQtyGrade[InspectingMklBjItems::GRADE_A] + $totalQtyGrade[InspectingMklBjItems::GRADE_B] + $totalQtyGrade[InspectingMklBjItems::GRADE_C] + $totalQtyGrade[InspectingMklBjItems::GRADE_PK] + $totalQtyGrade[InspectingMklBjItems::GRADE_SAMPLE] + $totalQtyGrade[InspectingMklBjItems::GRADE_A_ASTERISK] + $totalQtyGrade[InspectingMklBjItems::GRADE_A_PLUS] + $totalQtyGrade[InspectingMklBjItems::GRADE_PUTIH] + $totalQtyGrade[InspectingMklBjItems::GRADE_D];
            $totalRoll = $totalRollGrade[InspectingMklBjItems::GRADE_A] + $totalRollGrade[InspectingMklBjItems::GRADE_B] + $totalRollGrade[InspectingMklBjItems::GRADE_C] + $totalRollGrade[InspectingMklBjItems::GRADE_PK] + $totalRollGrade[InspectingMklBjItems::GRADE_SAMPLE] + $totalRollGrade[InspectingMklBjItems::GRADE_A_ASTERISK] + $totalRollGrade[InspectingMklBjItems::GRADE_A_PLUS] + $totalRollGrade[InspectingMklBjItems::GRADE_PUTIH] + $totalRollGrade[InspectingMklBjItems::GRADE_D];

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
    <?= Html::endForm() ?>
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
                    <?= Html::checkbox('param8', $defaultCheck, ['label' => '2 Satuan', 'id' => 'param8Checkbox']); ?>
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
                            <th>Total Grade D</th>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalPiecesGrade[InspectingMklBjItems::GRADE_D]);
                            ?>
                            </td>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalRollGrade[InspectingMklBjItems::GRADE_D]);
                            ?>
                            </td>
                            <td>
                                <?php
                            echo $formatter->asDecimal($totalQtyGrade[InspectingMklBjItems::GRADE_D]);
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
<?php
    $this->registerJsVar('inspectionId', $model->id);
    $this->registerJs('
    window.postingItems = function() {
        if ($(".check-item:checked").length === 0) {
            alert("Pilih setidaknya satu item untuk dikirim.");
            return;
        }
            if (confirm("Apakah Anda yakin ingin memposting item yang dipilih?")) {
                localStorage.removeItem("checked_items_" + inspectionId);
                
                var date = $("#posting-date-input").val();
                if(date){
                    $("#posting-form").append("<input type=\"hidden\" name=\"postingDate\" value=\"" + date + "\">");
                }
                
                $("#posting-form").submit();
            }
    };

    function updateLocalStorage() {
        var checkedIds = [];
        $(".check-item:checked").each(function() {
            checkedIds.push($(this).val());
        });
        localStorage.setItem("checked_items_" + inspectionId, JSON.stringify(checkedIds));
    }

    var saved = localStorage.getItem("checked_items_" + inspectionId);
    if (saved) {
        var ids = JSON.parse(saved);
        ids.forEach(function(id) {
            var cb = $(".check-item[value=\"" + id + "\"]");
            if(cb.length){
                cb.show().prop("checked", true);
                cb.closest("tr").find(".label-print-dulu").hide();
            }
        });
    }

    $(document).on("click", "#qrPrintLink", function(e) {
        e.preventDefault();

        $(".check-item").show().prop("checked", true);
        $(".label-print-dulu").hide();
        $("#check_all_items").prop("checked", true);
        updateLocalStorage();

        var param1Value = $("#param1Checkbox").is(":checked") ? "1" : "0";
        var param2Value = $("#param2Checkbox").is(":checked") ? "1" : "0";
        var param6Value = $("#param6Checkbox").is(":checked") ? "1" : "0";
        var param8Value = $("#param8Checkbox").is(":checked") ? "1" : "0";
        var theView = (param1Value == 0 && param2Value == 0) ? "qr-all-without-attribute" : "qr-all";
        var url = $(this).attr("href") + "&param1=" + param1Value + "&param2=" + param2Value + "&param6=" + param6Value + "&param8=" + param8Value;
        var replacedUrl = url.replace(/replace/, theView);
        window.open(replacedUrl, "_blank");
        $("#loading-overlay").css("display", "flex");
        location.reload();
    });

    $(document).on("click", ".qrPrint", function(e) {
        e.preventDefault();
        var row = $(this).closest("tr");
        row.find(".check-item").show().prop("checked", true);
        row.find(".label-print-dulu").hide();
        updateLocalStorage();
        var param3Value = $("#param3Checkbox").is(":checked") ? "1" : "0";
        var param4Value = $("#param4Checkbox").is(":checked") ? "1" : "0";
        var param5Value = $("#param5Checkbox").is(":checked") ? "1" : "0";
        var param7Value = $("#param7Checkbox").is(":checked") ? "1" : "0";
        var url = $(this).attr("href") + "&param3=" + param3Value + "&param4=" + param4Value + "&param5=" + param5Value + "&param7=" + param7Value;
        window.open(url, "_blank");
        $("#loading-overlay").css("display", "flex");
        location.reload();
    });

    $(document).on("change", ".check-item", function() {
        updateLocalStorage();
    });

    $(document).on("change", "#check_all_items", function() {
        var isChecked = $(this).is(":checked");
        $(".check-item").prop("checked", isChecked);
        updateLocalStorage();
    });
'); ?>