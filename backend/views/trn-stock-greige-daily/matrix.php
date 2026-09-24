<?php

use common\models\ar\MstGreige;
use kartik\select2\Select2;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dates array */
/* @var $motifs array */
/* @var $matrixData array */
/* @var $startDate string */
/* @var $endDate string */
/* @var $greigeId int|null */

$this->title = 'Matrix Perbandingan Stock Greige Harian';
$this->params['breadcrumbs'][] = ['label' => 'Stock Greige', 'url' => ['/trn-stock-greige/index']];
$this->params['breadcrumbs'][] = ['label' => 'Rekap Harian', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$greigeList = ArrayHelper::map(
    MstGreige::find()->select(['id', 'nama_kain'])->orderBy('nama_kain ASC')->asArray()->all(),
    'id',
    'nama_kain'
);
?>

<div class="trn-stock-greige-daily-matrix">

    <?= $this->render('_save_modal') ?>

    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-12">
            <div class="btn-group pull-left" role="group">
                <?= Html::a('<i class="glyphicon glyphicon-list"></i> Tampilan List & Log', ['index'], [
                    'class' => 'btn btn-default',
                    'title' => 'Lihat dalam bentuk list tabel'
                ]) ?>
                <?= Html::a('<i class="glyphicon glyphicon-camera"></i> Simpan Snapshot Stock', '#', [
                    'class' => 'btn btn-success',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal-save-daily-stock',
                    'title' => 'Simpan snapshot stock harian per motif'
                ]) ?>
            </div>

            <div class="btn-group pull-right" role="group">
                <?= Html::a('<i class="glyphicon glyphicon-list-alt"></i> Packing List Greige', ['/trn-stock-greige/index'], [
                    'class' => 'btn btn-default',
                ]) ?>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="glyphicon glyphicon-filter"></i> Filter Periode & Motif</h3>
        </div>
        <div class="panel-body">
            <form method="get" action="<?= Url::to(['matrix']) ?>" class="form-inline">
                <div class="form-group" style="margin-right: 15px;">
                    <label style="margin-right: 5px;">Dari Tanggal:</label>
                    <?= DatePicker::widget([
                        'name' => 'startDate',
                        'value' => $startDate,
                        'options' => ['placeholder' => 'Dari tanggal...', 'style' => 'width:130px;'],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                    ]) ?>
                </div>

                <div class="form-group" style="margin-right: 15px;">
                    <label style="margin-right: 5px;">Sampai Tanggal:</label>
                    <?= DatePicker::widget([
                        'name' => 'endDate',
                        'value' => $endDate,
                        'options' => ['placeholder' => 'Sampai tanggal...', 'style' => 'width:130px;'],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                    ]) ?>
                </div>

                <div class="form-group" style="margin-right: 15px; min-width: 250px;">
                    <label style="margin-right: 5px;">Motif:</label>
                    <?= Select2::widget([
                        'name' => 'greige_id',
                        'value' => $greigeId,
                        'data' => $greigeList,
                        'options' => ['placeholder' => 'Semua Motif...'],
                        'pluginOptions' => ['allowClear' => true, 'width' => '220px'],
                    ]) ?>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="glyphicon glyphicon-search"></i> Tampilkan
                </button>
                <?= Html::a('<i class="glyphicon glyphicon-refresh"></i> Reset', ['matrix'], ['class' => 'btn btn-default', 'style' => 'margin-left: 5px;']) ?>
            </form>
        </div>
    </div>

    <!-- Matrix Table -->
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="glyphicon glyphicon-calendar"></i> 
                Matrix Pergerakan Stock Greige per Hari (<?= Yii::$app->formatter->asDate($startDate) ?> s/d <?= Yii::$app->formatter->asDate($endDate) ?>)
            </h3>
        </div>
        <div class="panel-body" style="padding: 0; overflow-x: auto;">
            <?php if (empty($dates) || empty($motifs)): ?>
                <div class="alert alert-warning" style="margin: 20px;">
                    <i class="glyphicon glyphicon-info-sign"></i> Belum ada data snapshot stock harian pada rentang tanggal yang dipilih.
                    Silakan klik tombol <strong>"Simpan Snapshot Stock"</strong> untuk menyimpan stock hari ini.
                </div>
            <?php else: ?>
                <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0; font-size: 13px;">
                    <thead>
                        <tr style="background-color: #2c3e50; color: #ffffff;">
                            <th style="width: 40px; text-align: center; vertical-align: middle;">No</th>
                            <th style="min-width: 220px; vertical-align: middle;">Motif / Nama Greige</th>
                            <th style="min-width: 140px; vertical-align: middle;">Group</th>
                            <?php foreach ($dates as $date): ?>
                                <?php 
                                $dayName = \common\models\ar\TrnStockGreigeDaily::getIndonesianDayName($date);
                                $isSunday = (date('w', strtotime($date)) == 0);
                                $isSaturday = (date('w', strtotime($date)) == 6);
                                $dayBg = $isSunday ? '#c0392b' : ($isSaturday ? '#d35400' : '#2980b9');
                                ?>
                                <th style="text-align: center; min-width: 130px; vertical-align: middle; background-color: <?= $dayBg ?>; color: #fff;">
                                    <div style="font-weight: bold; font-size: 13px;"><?= $dayName ?></div>
                                    <small style="color: #f1f2f6;"><?= Yii::$app->formatter->asDate($date, 'php:d/m/Y') ?></small>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        $columnTotals = array_fill_keys($dates, 0);
                        $columnRolls = array_fill_keys($dates, 0);
                        $columnDiffs = array_fill_keys($dates, 0);

                        foreach ($motifs as $gId => $mInfo): 
                        ?>
                            <tr>
                                <td style="text-align: center; font-weight: bold; vertical-align: middle;"><?= $no++ ?></td>
                                <td style="font-weight: bold; vertical-align: middle; color: #1e3799;">
                                    <?= Html::encode($mInfo['nama']) ?>
                                </td>
                                <td style="vertical-align: middle; color: #555;">
                                    <?= Html::encode($mInfo['group']) ?>
                                </td>
                                <?php foreach ($dates as $date): ?>
                                    <?php 
                                    $cell = $matrixData[$gId][$date] ?? null;
                                    if ($cell !== null) {
                                        $qty = (float)$cell['qty'];
                                        $roll = (int)$cell['roll'];
                                        $diff = (float)($cell['diff_qty'] ?? 0);

                                        $columnTotals[$date] += $qty;
                                        $columnRolls[$date] += $roll;
                                        $columnDiffs[$date] += $diff;
                                    }
                                    ?>
                                    <td style="text-align: right; vertical-align: middle; padding: 6px;">
                                        <?php if ($cell !== null): ?>
                                            <div style="font-weight: bold; font-size: 13px;">
                                                <?= Yii::$app->formatter->asDecimal($qty) ?> m
                                            </div>
                                            <div style="font-size: 11px; color: #777;">
                                                <?= $roll ?> roll
                                            </div>
                                            <?php if (isset($cell['diff_qty'])): ?>
                                                <?php if ($diff > 0): ?>
                                                    <div style="margin-top: 2px;">
                                                        <span class="badge" style="background-color: #27ae60; font-size: 10px; font-weight: normal; padding: 2px 5px;">
                                                            <i class="glyphicon glyphicon-arrow-up"></i> +<?= Yii::$app->formatter->asDecimal($diff) ?>
                                                        </span>
                                                    </div>
                                                <?php elseif ($diff < 0): ?>
                                                    <div style="margin-top: 2px;">
                                                        <span class="badge" style="background-color: #c0392b; font-size: 10px; font-weight: normal; padding: 2px 5px;">
                                                            <i class="glyphicon glyphicon-arrow-down"></i> <?= Yii::$app->formatter->asDecimal($diff) ?>
                                                        </span>
                                                    </div>
                                                <?php else: ?>
                                                    <div style="margin-top: 2px;">
                                                        <span class="badge" style="background-color: #95a5a6; font-size: 10px; font-weight: normal; padding: 2px 5px;">
                                                            0
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted" style="font-size: 11px;">-</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f1f2f6; font-weight: bold;">
                            <td colspan="3" style="text-align: right; vertical-align: middle; font-size: 14px; color: #2f3542;">
                                GRAND TOTAL PER HARI:
                            </td>
                            <?php foreach ($dates as $date): ?>
                                <td style="text-align: right; vertical-align: middle; background-color: #dfe4ea;">
                                    <div style="font-size: 14px; color: #1e3799;">
                                        <?= Yii::$app->formatter->asDecimal($columnTotals[$date]) ?> m
                                    </div>
                                    <div style="font-size: 11px; color: #555;">
                                        <?= $columnRolls[$date] ?> roll
                                    </div>
                                    <?php 
                                    $cDiff = $columnDiffs[$date];
                                    if ($cDiff > 0): ?>
                                        <div style="margin-top: 2px;">
                                            <span class="badge" style="background-color: #27ae60; font-size: 10px;">
                                                <i class="glyphicon glyphicon-arrow-up"></i> +<?= Yii::$app->formatter->asDecimal($cDiff) ?>
                                            </span>
                                        </div>
                                    <?php elseif ($cDiff < 0): ?>
                                        <div style="margin-top: 2px;">
                                            <span class="badge" style="background-color: #c0392b; font-size: 10px;">
                                                <i class="glyphicon glyphicon-arrow-down"></i> <?= Yii::$app->formatter->asDecimal($cDiff) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tfoot>
                </table>
            <?php endif; ?>
        </div>
    </div>

</div>
