<?php
use yii\helpers\Html;

/* @var $models common\models\ar\TrnSc[] */
/* @var $month int */
/* @var $currentYear string */
/* @var $buyerName string|null */

$months = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
    7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
?>

<div class="sc-detail-by-month">
    <h4 class="text-primary" style="font-weight: bold; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 20px;">
        <i class="glyphicon glyphicon-list"></i> Daftar SC: <?= $months[$month] ?> <?= $currentYear ?>
        <?= $buyerName ? " (" . Html::encode($buyerName) . ")" : "" ?>
    </h4>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover small">
            <thead>
                <tr class="bg-primary">
                    <th>No. SC</th>
                    <th>Buyer</th>
                    <th class="text-right">T. Batch</th>
                    <th class="text-right">Qty Finish (Meter)</th>
                    <th class="text-right">Qty Finish (Yard)</th>
                    <th class="text-right">T. MO</th>
                    <th class="text-right">T. WO</th>
                    <th class="text-right">Inspek</th>
                    <th class="text-right">Kirim</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($models)): ?>
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data Sales Contract.</td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $totals = [
                        'batch' => 0, 'finishMeter' => 0, 'finishYard' => 0,
                        'mo' => 0, 'wo' => 0, 'inspek' => 0, 'kirim' => 0
                    ];
                    
                    foreach ($models as $model): 
                        $batch = $model->qtyScGreige;
                        $finishMeter = $model->qtyFinishToMeter;
                        $finishYard = $model->qtyFinishToYard;
                        $mo = $model->qtyMoColorNotBatal;
                        $wo = $model->qtyWoColorNotBatal;
                        $inspek = $model->qtyInspected;
                        $kirim = $model->qtyKirim;
                        $unitName = $model->scUnitName;
                        
                        $totals['batch'] += $batch;
                        $totals['finishMeter'] += $finishMeter;
                        $totals['finishYard'] += $finishYard;
                        $totals['mo'] += $mo;
                        $totals['wo'] += $wo;
                        $totals['inspek'] += $inspek;
                        $totals['kirim'] += $kirim;
                    ?>
                        <tr>
                            <td style="font-weight: bold;"><?= Html::encode($model->no) ?></td>
                            <td><?= Html::encode($model->cust->name) ?></td>
                            <td class="text-right">
                                <?= number_format($batch, 2) ?>
                                <small class="text-muted">Batch</small>
                            </td>
                            <td class="text-right">
                                <?= number_format($finishMeter, 2) ?>
                                <small class="text-muted">Meter</small>
                            </td>
                            <td class="text-right">
                                <?= number_format($finishYard, 2) ?>
                                <small class="text-muted">Yard</small>
                            </td>
                            <td class="text-right">
                                <?= number_format($mo, 2) ?>
                                <small class="text-muted">Batch</small>
                            </td>
                            <td class="text-right">
                                <?= number_format($wo, 2) ?>
                                <small class="text-muted">Batch</small>
                            </td>
                            <td class="text-right text-info">
                                <strong><?= number_format($inspek, 2) ?></strong>
                                <small class="text-muted"><?= Html::encode($unitName) ?></small>
                            </td>
                            <td class="text-right text-success">
                                <strong><?= number_format($kirim, 2) ?></strong>
                                <small class="text-muted"><?= Html::encode($unitName) ?></small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="info" style="font-weight: bold;">
                        <td colspan="2" class="text-right">TOTAL</td>
                        <td class="text-right"><?= number_format($totals['batch'], 2) ?></td>
                        <td class="text-right"><?= number_format($totals['finishMeter'], 2) ?></td>
                        <td class="text-right"><?= number_format($totals['finishYard'], 2) ?></td>
                        <td class="text-right"><?= number_format($totals['mo'], 2) ?></td>
                        <td class="text-right"><?= number_format($totals['wo'], 2) ?></td>
                        <td class="text-right"><?= number_format($totals['inspek'], 2) ?></td>
                        <td class="text-right"><?= number_format($totals['kirim'], 2) ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="modal-footer">
        <?= Html::a('<i class="glyphicon glyphicon-chevron-left"></i> Kembali', ['sc-by-month', 'buyerName' => $buyerName], [
            'class' => 'btn btn-primary pull-left ajax-modal-click',
            'data-title' => 'Kembali ke Ringkasan Bulanan',
        ]) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
    </div>
</div>
