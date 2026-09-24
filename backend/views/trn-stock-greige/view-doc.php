<?php

use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $noDoc string */
/* @var $header TrnStockGreige */
/* @var $models TrnStockGreige[] */
/* @var $totalRoll int */
/* @var $totalMeter float */
/* @var $gradeBreakdown array */

$this->title = 'Packing List Greige: ' . $noDoc;
$this->params['breadcrumbs'][] = ['label' => 'Packing List Greige', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$isPending = ($header->status == TrnStockGreige::STATUS_PENDING);
?>
<div class="trn-stock-greige-view-doc">

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">
                Dokumen: <strong><?= Html::encode($noDoc) ?></strong>
                <?php if ($isPending): ?>
                    <span class="label label-warning" style="font-size: 13px; margin-left: 10px;">DRAFT / PENDING</span>
                <?php elseif ($header->status == TrnStockGreige::STATUS_VALID): ?>
                    <span class="label label-success" style="font-size: 13px; margin-left: 10px;">POSTED / VALID</span>
                <?php else: ?>
                    <span class="label label-default" style="font-size: 13px; margin-left: 10px;">Status: <?= $header->status ?></span>
                <?php endif; ?>
            </h3>
            <div class="box-tools pull-right">
                <?= Html::a('<i class="glyphicon glyphicon-arrow-left"></i> Kembali ke Index', ['index'], ['class' => 'btn btn-default btn-sm']) ?>
                <?= Html::a('<i class="glyphicon glyphicon-plus"></i> Buat Baru', ['create-dua'], ['class' => 'btn btn-info btn-sm']) ?>
                <?php if ($isPending): ?>
                    <?= Html::a('<i class="glyphicon glyphicon-edit"></i> Ubah (Edit)', ['update-doc', 'no_doc' => $noDoc], ['class' => 'btn btn-warning btn-sm']) ?>
                    <?= Html::a('<i class="glyphicon glyphicon-trash"></i> Hapus', ['delete-doc', 'no_doc' => $noDoc], [
                        'class' => 'btn btn-danger btn-sm',
                        'data' => [
                            'confirm' => 'Apakah Anda yakin ingin menghapus draft dokumen ini? Seluruh roll pada dokumen ini akan dihapus.',
                            'method' => 'post',
                        ],
                    ]) ?>
                    <?= Html::a('<i class="glyphicon glyphicon-ok"></i> <strong>Posting ke Stock</strong>', ['posting-doc', 'no_doc' => $noDoc], [
                        'class' => 'btn btn-primary btn-sm',
                        'data' => [
                            'confirm' => 'Apakah Anda yakin ingin mem-posting dokumen ini ke database stock? Stock greige akan otomatis bertambah dan data tidak dapat diubah lagi.',
                            'method' => 'post',
                        ],
                    ]) ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th style="width: 35%;">No. Document</th>
                            <td><?= Html::encode($header->no_document) ?></td>
                        </tr>
                        <tr>
                            <th>Motif / Greige</th>
                            <td><strong><?= $header->greige ? Html::encode($header->greige->nama_kain) : '-' ?></strong></td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td><?= Yii::$app->formatter->asDate($header->date) ?></td>
                        </tr>
                        <tr>
                            <th>Asal Greige</th>
                            <td><?= isset(TrnStockGreige::asalGreigeOptions()[$header->asal_greige]) ? TrnStockGreige::asalGreigeOptions()[$header->asal_greige] : '-' ?></td>
                        </tr>
                        <tr>
                            <th>No. Lapak</th>
                            <td><?= Html::encode($header->no_lapak) ?></td>
                        </tr>
                        <tr>
                            <th>Hasil Setting</th>
                            <td>
                                <?= $header->is_hasil_setting 
                                    ? '<span class="label label-primary">Ya</span>' 
                                    : '<span class="label label-default">Tidak</span>' ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th style="width: 35%;">Lot Lusi / Pakan</th>
                            <td><?= Html::encode($header->lot_lusi) ?> / <?= Html::encode($header->lot_pakan) ?></td>
                        </tr>
                        <tr>
                            <th>Status Ket. Weaving</th>
                            <td><?= isset(TrnStockGreige::tsdOptions()[$header->status_tsd]) ? TrnStockGreige::tsdOptions()[$header->status_tsd] : '-' ?></td>
                        </tr>
                        <tr>
                            <th>Pengirim</th>
                            <td><?= Html::encode($header->pengirim) ?></td>
                        </tr>
                        <tr>
                            <th>Mengetahui</th>
                            <td><?= Html::encode($header->mengetahui) ?></td>
                        </tr>
                        <tr>
                            <th>Note</th>
                            <td><?= Html::encode($header->note) ?></td>
                        </tr>
                        <tr>
                            <th>Status Stock</th>
                            <td>
                                <?php if ($isPending): ?>
                                    <span class="text-warning font-weight-bold"><i class="fa fa-clock-o"></i> Belum Diposting (Belum masuk stock)</span>
                                <?php else: ?>
                                    <span class="text-success font-weight-bold"><i class="fa fa-check-circle"></i> Sudah Diposting (Stock Aktif)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Summary Box -->
            <div class="well well-sm" style="background: #f9f9f9; border-left: 4px solid #3c8dbc; margin-top: 10px; margin-bottom: 20px; padding: 12px 20px;">
                <div class="row">
                    <div class="col-md-3">
                        <span style="font-size: 14px; color: #666;">Total Roll:</span><br>
                        <strong style="font-size: 18px; color: #333;"><?= number_format($totalRoll) ?> Roll</strong>
                    </div>
                    <div class="col-md-4">
                        <span style="font-size: 14px; color: #666;">Total Qty (Meter):</span><br>
                        <strong style="font-size: 18px; color: #0073b7;"><?= Yii::$app->formatter->asDecimal($totalMeter) ?> m</strong>
                    </div>
                    <div class="col-md-5">
                        <span style="font-size: 14px; color: #666;">Rincian Per Grade:</span><br>
                        <div style="margin-top: 4px;">
                            <?php foreach ($gradeBreakdown as $gradeName => $gData): ?>
                                <span class="label label-default" style="font-size: 13px; font-weight: normal; margin-right: 6px; display: inline-block; margin-bottom: 4px; color: #333; background: #e8e8e8; border: 1px solid #ccc; padding: 4px 8px;">
                                    <?= Html::encode($gradeName) ?>: <strong><?= Yii::$app->formatter->asDecimal($gData['qty']) ?> m</strong> (<?= $gData['roll'] ?> roll)
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr class="active">
                            <th style="width: 50px; text-align: center;">No</th>
                            <th style="width: 120px;">Grade</th>
                            <th>No. MC Weaving / No Set Lusi</th>
                            <th style="text-align: right; width: 180px;">Qty (m)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $gradeOptions = \backend\models\ar\StockGreige::gradeOptions();
                        foreach ($models as $idx => $item): 
                            $gradeLabel = isset($gradeOptions[$item->grade]) ? $gradeOptions[$item->grade] : ($item->grade ?: '-');
                        ?>
                            <tr>
                                <td style="text-align: center;"><?= $idx + 1 ?></td>
                                <td><span class="label label-info" style="font-size: 12px;"><?= Html::encode($gradeLabel) ?></span></td>
                                <td><?= Html::encode($item->no_set_lusi) ?></td>
                                <td style="text-align: right; font-weight: bold;"><?= Yii::$app->formatter->asDecimal($item->panjang_m) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="warning" style="font-weight: bold; font-size: 15px;">
                            <td colspan="3" style="text-align: right;">GRAND TOTAL:</td>
                            <td style="text-align: right; color: #0073b7;"><?= Yii::$app->formatter->asDecimal($totalMeter) ?> m</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <?php if ($isPending): ?>
            <div class="box-footer">
                <div class="pull-right">
                    <?= Html::a('<i class="glyphicon glyphicon-edit"></i> Ubah (Edit)', ['update-doc', 'no_doc' => $noDoc], ['class' => 'btn btn-warning']) ?>
                    <?= Html::a('<i class="glyphicon glyphicon-trash"></i> Hapus', ['delete-doc', 'no_doc' => $noDoc], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Apakah Anda yakin ingin menghapus draft dokumen ini? Seluruh roll pada dokumen ini akan dihapus.',
                            'method' => 'post',
                        ],
                    ]) ?>
                    <?= Html::a('<i class="glyphicon glyphicon-ok"></i> <strong>Posting ke Stock</strong>', ['posting-doc', 'no_doc' => $noDoc], [
                        'class' => 'btn btn-primary',
                        'data' => [
                            'confirm' => 'Apakah Anda yakin ingin mem-posting dokumen ini ke database stock? Stock greige akan otomatis bertambah dan data tidak dapat diubah lagi.',
                            'method' => 'post',
                        ],
                    ]) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>
