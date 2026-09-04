<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadiOpnamePcs;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $models common\models\ar\TrnGudangJadiOpnamePcs[] */
/* @var $groupInfo array */

$totalQty = 0;
foreach ($models as $m) {
    $totalQty += (float)$m->qty;
}
?>

<div class="pcs-list-modal-content">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">
                <strong>Kode Opname:</strong> <?= Html::encode($groupInfo['opname_code'] ?: '-') ?> | 
                <strong>Lokasi:</strong> <span class="label label-info"><?= Html::encode($groupInfo['locs_code'] ?: 'TRANSIT') ?></span> | 
                <strong>Motif:</strong> <?= Html::encode($groupInfo['motif']) ?> | 
                <strong>Warna:</strong> <?= Html::encode($groupInfo['color']) ?>
            </h5>
        </div>
        <div class="panel-body">
            <p>
                <strong>Total Roll / Pcs:</strong> <?= count($models) ?> Roll | 
                <strong>Total Kuantitas:</strong> <?= Yii::$app->formatter->asDecimal($totalQty, 2) ?>
            </p>
        </div>
    </div>

    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr class="bg-primary">
                    <th style="width: 40px;" class="text-center">No</th>
                    <th>ID Opname</th>
                    <th>QR Code / Barcode</th>
                    <th>Motif / Nama Kain</th>
                    <th>Color / Warna</th>
                    <th>No. WO</th>
                    <th>No. SC</th>
                    <th>Grade</th>
                    <th class="text-right">Qty</th>
                    <th>Satuan</th>
                    <th>Status</th>
                    <th style="width: 60px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($models)): ?>
                    <tr>
                        <td colspan="12" class="text-center text-muted">Tidak ada data pcs ditemukan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($models as $idx => $model): ?>
                        <?php 
                            $woNo = ($model->gudangJadi && $model->gudangJadi->wo) ? $model->gudangJadi->wo->no : '-';
                            $scNo = ($model->gudangJadi && $model->gudangJadi->wo && $model->gudangJadi->wo->mo && $model->gudangJadi->wo->mo->scGreige && $model->gudangJadi->wo->mo->scGreige->sc) ? $model->gudangJadi->wo->mo->scGreige->sc->no : '-';
                            $motifNama = ($model->gudangJadi && $model->gudangJadi->wo) ? $model->gudangJadi->wo->greigeNamaKain : (!empty($model->qr_code_desc) ? $model->qr_code_desc : '-');
                            $colorWarna = ($model->gudangJadi && !empty($model->gudangJadi->color)) ? $model->gudangJadi->color : '-';
                        ?>
                        <tr>
                            <td class="text-center"><?= $idx + 1 ?></td>
                            <td>
                                <?= Html::a('#' . $model->id, ['view', 'id' => $model->id], [
                                    'target' => '_blank',
                                    'data-pjax' => '0',
                                    'title' => 'Rincian Detail'
                                ]) ?>
                            </td>
                            <td><code><?= Html::encode($model->qr_code) ?></code></td>
                            <td><strong><?= Html::encode($motifNama) ?></strong></td>
                            <td><?= Html::encode($colorWarna) ?></td>
                            <td><?= Html::encode($woNo) ?></td>
                            <td><?= Html::encode($scNo) ?></td>
                            <td><?= Html::encode($model->gradeName) ?></td>
                            <td class="text-right"><strong><?= Yii::$app->formatter->asDecimal($model->qty, 2) ?></strong></td>
                            <td><?= Html::encode($model->unitName) ?></td>
                            <td>
                                <?php if ((int)$model->status === TrnGudangJadiOpnamePcs::STATUS_VERIFIED): ?>
                                    <span class="label label-success"><i class="fa fa-check"></i> <?= Html::encode($model->statusName) ?></span>
                                <?php elseif ((int)$model->status === TrnGudangJadiOpnamePcs::STATUS_OUT): ?>
                                    <span class="label label-danger"><i class="fa fa-times-circle"></i> <?= Html::encode($model->statusName) ?></span>
                                <?php else: ?>
                                    <span class="label label-warning"><i class="fa fa-cubes"></i> <?= Html::encode($model->statusName) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?= Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['view', 'id' => $model->id], [
                                    'class' => 'btn btn-xs btn-default',
                                    'title' => 'Lihat Detail Pcs',
                                    'target' => '_blank',
                                    'data-pjax' => '0',
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
    </div>

    <div class="modal-footer" style="margin-top: 15px; padding-bottom: 0;">
        <?= Html::a('<i class="fa fa-print"></i> Print Lembar Palet Lokasi', [
            'print-lokasi',
            'locs_code' => $groupInfo['locs_code'],
            'opname_code' => $groupInfo['opname_code'],
        ], [
            'class' => 'btn btn-warning pull-left',
            'target' => '_blank',
            'data-pjax' => '0',
            'title' => 'Cetak cetakan format Palet per Lokasi ini'
        ]) ?>
        <?= Html::a('<i class="fa fa-external-link"></i> Buka Semua Data Pcs (Filtered)', [
            'index',
            'TrnGudangJadiOpnamePcsSearch[opname_code]' => $groupInfo['opname_code'],
            'TrnGudangJadiOpnamePcsSearch[locs_code]' => $groupInfo['locs_code'],
            'TrnGudangJadiOpnamePcsSearch[motif]' => $groupInfo['motif'] !== '-' ? $groupInfo['motif'] : '',
            'TrnGudangJadiOpnamePcsSearch[color]' => $groupInfo['color'] !== '-' ? $groupInfo['color'] : '',
            'TrnGudangJadiOpnamePcsSearch[grade]' => $groupInfo['grade'],
            'TrnGudangJadiOpnamePcsSearch[status]' => $groupInfo['status'],
        ], [
            'class' => 'btn btn-primary pull-left',
            'target' => '_blank',
            'data-pjax' => '0',
            'title' => 'Lihat seluruh daftar pcs lokasi ini di halaman Data Stok Opname'
        ]) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
    </div>
</div>

