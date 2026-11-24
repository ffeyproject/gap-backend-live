<?php

use common\models\ar\TrnKirimBuyerHeader;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKirimBuyerHeaderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $tab string */
/* @var $countLokal int */
/* @var $countExport int */
/* @var $summary array */
/* @var $startDate string */
/* @var $endDate string */

$isExport = ($tab === 'export');
$tabTitle = $isExport ? 'Pengiriman Export' : 'Pengiriman Lokal';
$this->title = 'Pengiriman Ke Buyer - ' . $tabTitle;
$this->params['breadcrumbs'][] = ['label' => 'Pengiriman Ke Buyer', 'url' => ['index', 'tab' => $tab]];
$this->params['breadcrumbs'][] = $tabTitle;

$currSummary = $isExport ? $summary['export'] : $summary['lokal'];
$summaryLokal = $summary['lokal'];
$summaryExport = $summary['export'];
$summaryAll = $summary['all'];

$currEqYard = $currSummary['qty_yard'] + \backend\components\Converter::meterToYard($currSummary['qty_meter']);
$currEqMeter = $currSummary['qty_meter'] + \backend\components\Converter::yardToMeter($currSummary['qty_yard']);
?>
<div class="trn-kirim-buyer-header-index">

    <!-- Summary Total Pengiriman Bulan Ini (Tanggal 1 s/d Sekarang) Status Posted -->
    <div class="box box-solid" style="margin-bottom: 15px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div class="box-header with-border" style="background-color: #f8fafc; border-top: 3px solid <?= $isExport ? '#3c8dbc' : '#00a65a' ?>; padding: 10px 15px;">
            <div class="row" style="display: flex; align-items: center; flex-wrap: wrap;">
                <div class="col-md-7 col-sm-12">
                    <h4 style="margin: 0; font-weight: bold; color: #333; font-size: 16px;">
                        <i class="fa fa-truck text-green"></i> Total Pengiriman Posted Bulan Ini
                        <span class="label label-success" style="font-size: 11px; margin-left: 5px;">STATUS POSTED</span>
                    </h4>
                    <div style="color: #666; font-size: 13px; margin-top: 3px;">
                        <i class="fa fa-calendar text-muted"></i> Periode: <strong><?= Yii::$app->formatter->asDate($startDate, 'php:d F Y') ?></strong> s/d <strong><?= Yii::$app->formatter->asDate($endDate, 'php:d F Y') ?></strong> (Tanggal 1 s/d Hari ini)
                    </div>
                </div>
                <div class="col-md-5 col-sm-12 text-right" style="margin-top: 5px;">
                    <span style="font-size: 12px; color: #777; margin-right: 5px;">Rincian Tab Posted:</span>
                    <a href="<?= Url::to(['index', 'tab' => 'lokal']) ?>" class="btn btn-xs <?= !$isExport ? 'btn-success' : 'btn-default' ?>" style="font-weight: bold;">
                        <i class="fa fa-truck"></i> Lokal: <?= number_format($summaryLokal['total_sj'], 0, ',', '.') ?> SJ
                    </a>
                    <a href="<?= Url::to(['index', 'tab' => 'export']) ?>" class="btn btn-xs <?= $isExport ? 'btn-primary' : 'btn-default' ?>" style="font-weight: bold;">
                        <i class="fa fa-plane"></i> Export: <?= number_format($summaryExport['total_sj'], 0, ',', '.') ?> SJ
                    </a>
                    <span class="badge bg-purple" style="font-size: 12px; padding: 4px 8px; vertical-align: middle;">
                        Total: <?= number_format($summaryAll['total_sj'], 0, ',', '.') ?> SJ
                    </span>
                </div>
            </div>
        </div>
        <div class="box-body" style="padding: 15px 15px 0 15px; background: #fff;">
            <div class="row">
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-aqua">
                        <span class="info-box-icon"><i class="fa fa-file-text-o"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total SJ (<?= $tabTitle ?>)</span>
                            <span class="info-box-number" style="font-size: 22px;">
                                <?= number_format($currSummary['total_sj'], 0, ',', '.') ?> <small style="color: #fff; font-size: 14px;">SJ</small>
                            </span>
                            <div class="progress" style="height: 2px; margin: 5px 0; background: rgba(255,255,255,0.3);">
                                <div class="progress-bar" style="width: 100%; background: #fff;"></div>
                            </div>
                            <span class="progress-description" style="font-size: 11px;">
                                Semua Tab: <?= number_format($summaryAll['total_sj'], 0, ',', '.') ?> SJ (Lokal: <?= $summaryLokal['total_sj'] ?> | Export: <?= $summaryExport['total_sj'] ?>)
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-green">
                        <span class="info-box-icon"><i class="fa fa-cubes"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Roll / Pcs (<?= $tabTitle ?>)</span>
                            <span class="info-box-number" style="font-size: 22px;">
                                <?= number_format($currSummary['total_roll'], 0, ',', '.') ?> <small style="color: #fff; font-size: 14px;">Roll</small>
                            </span>
                            <div class="progress" style="height: 2px; margin: 5px 0; background: rgba(255,255,255,0.3);">
                                <div class="progress-bar" style="width: 100%; background: #fff;"></div>
                            </div>
                            <span class="progress-description" style="font-size: 11px;">
                                Semua Tab: <?= number_format($summaryAll['total_roll'], 0, ',', '.') ?> Roll (Lokal: <?= number_format($summaryLokal['total_roll'], 0, ',', '.') ?> | Export: <?= number_format($summaryExport['total_roll'], 0, ',', '.') ?>)
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-yellow">
                        <span class="info-box-icon"><i class="fa fa-arrows-h"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Yard (<?= $tabTitle ?>)</span>
                            <span class="info-box-number" style="font-size: 22px;">
                                <?= number_format($currSummary['qty_yard'], 2, ',', '.') ?> <small style="color: #fff; font-size: 14px;">Yard</small>
                            </span>
                            <div class="progress" style="height: 2px; margin: 5px 0; background: rgba(255,255,255,0.3);">
                                <div class="progress-bar" style="width: 100%; background: #fff;"></div>
                            </div>
                            <span class="progress-description" style="font-size: 11px;">
                                Semua Tab: <?= number_format($summaryAll['qty_yard'], 2, ',', '.') ?> Yard
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-purple">
                        <span class="info-box-icon"><i class="fa fa-tachometer"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Meter (<?= $tabTitle ?>)</span>
                            <span class="info-box-number" style="font-size: 22px;">
                                <?= number_format($currSummary['qty_meter'], 2, ',', '.') ?> <small style="color: #fff; font-size: 14px;">Meter</small>
                            </span>
                            <div class="progress" style="height: 2px; margin: 5px 0; background: rgba(255,255,255,0.3);">
                                <div class="progress-bar" style="width: 100%; background: #fff;"></div>
                            </div>
                            <span class="progress-description" style="font-size: 11px;">
                                Semua Tab: <?= number_format($summaryAll['qty_meter'], 2, ',', '.') ?> Meter
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($currSummary['qty_pcs'] > 0 || $currSummary['qty_kg'] > 0 || ($currSummary['qty_yard'] > 0 && $currSummary['qty_meter'] > 0)): ?>
                <div class="alert alert-info alert-dismissible" style="padding: 8px 15px; margin-bottom: 15px; font-size: 12px;">
                    <i class="fa fa-info-circle"></i>
                    <strong>Catatan Satuan & Ekuivalen (<?= $tabTitle ?>):</strong>
                    <?php if ($currSummary['qty_pcs'] > 0): ?>
                        <span class="label label-default" style="margin-left: 5px;">Pcs: <?= number_format($currSummary['qty_pcs'], 2, ',', '.') ?></span>
                    <?php endif; ?>
                    <?php if ($currSummary['qty_kg'] > 0): ?>
                        <span class="label label-default" style="margin-left: 5px;">Kg: <?= number_format($currSummary['qty_kg'], 2, ',', '.') ?></span>
                    <?php endif; ?>
                    <span style="margin-left: 10px;">
                        Total Ekuivalen Yard: <strong><?= number_format($currEqYard, 2, ',', '.') ?> Yard</strong> | Total Ekuivalen Meter: <strong><?= number_format($currEqMeter, 2, ',', '.') ?> Meter</strong>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="nav-tabs-custom" style="margin-bottom: 15px;">
        <ul class="nav nav-tabs">
            <li class="<?= !$isExport ? 'active' : '' ?>">
                <a href="<?= Url::to(['index', 'tab' => 'lokal']) ?>" style="font-weight: <?= !$isExport ? 'bold' : 'normal' ?>; font-size: 14px;">
                    <i class="fa fa-truck text-green"></i> Pengiriman Lokal
                    <span class="label <?= !$isExport ? 'label-success' : 'label-default' ?>" style="margin-left: 5px;"><?= number_format($countLokal, 0, ',', '.') ?></span>
                </a>
            </li>
            <li class="<?= $isExport ? 'active' : '' ?>">
                <a href="<?= Url::to(['index', 'tab' => 'export']) ?>" style="font-weight: <?= $isExport ? 'bold' : 'normal' ?>; font-size: 14px;">
                    <i class="fa fa-plane text-blue"></i> Pengiriman Export
                    <span class="label <?= $isExport ? 'label-primary' : 'label-default' ?>" style="margin-left: 5px;"><?= number_format($countExport, 0, ',', '.') ?></span>
                </a>
            </li>
        </ul>
        <div class="tab-content" style="padding: 10px 0 0 0;">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'responsiveWrap' => false,
                'panel' => [
                    'type' => $isExport ? 'primary' : 'success',
                    'heading' => '<i class="' . ($isExport ? 'fa fa-plane' : 'fa fa-truck') . '"></i> ' . ($isExport ? 'Daftar Pengiriman Ke Buyer (Export)' : 'Daftar Pengiriman Ke Buyer (Lokal)'),
                    'before' => Html::tag(
                        'div',
                        Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh', ['index', 'tab' => $tab], ['class' => 'btn btn-default', 'title' => 'Refresh']) . ' ' .
                        ($isExport
                            ? Html::a('<i class="glyphicon glyphicon-plus"></i> Tambah Pengiriman Export', ['create', 'is_export' => 1], ['class' => 'btn btn-primary', 'title' => 'Buat Pengiriman Export Baru'])
                            : Html::a('<i class="glyphicon glyphicon-plus"></i> Tambah Pengiriman Lokal', ['create', 'is_export' => 0], ['class' => 'btn btn-success', 'title' => 'Buat Pengiriman Lokal Baru'])
                        ),
                        ['class' => 'btn-group', 'role' => 'group']
                    ),
                ],
                'toolbar' => false,
                'columns' => [
                    ['class' => 'kartik\grid\SerialColumn'],
                    ['class' => 'kartik\grid\ActionColumn', 'template' => '{view}'],

                    [
                        'attribute' => 'no',
                        'label' => 'No. SJ',
                        'hAlign' => 'center',
                    ],
                    [
                        'label' => 'Buyer',
                        'attribute' => 'custName',
                        'value' => 'customer.name',
                    ],
                    [
                        'attribute' => 'nama_buyer',
                        'label' => 'Nama Buyer (SJ)',
                    ],
                    [
                        'attribute' => 'dateRange',
                        'label' => 'Tanggal',
                        'value' => 'date',
                        'format' => 'date',
                        'filterType' => GridView::FILTER_DATE_RANGE,
                        'filterWidgetOptions' => [
                            'convertFormat' => true,
                            'pluginOptions' => [
                                'locale' => [
                                    'format' => 'Y-m-d',
                                    'separator' => ' to ',
                                ],
                            ],
                        ],
                        'hAlign' => 'center',
                    ],
                    [
                        'label' => 'Total Roll',
                        'format' => 'raw',
                        'hAlign' => 'center',
                        'value' => function($data){
                            /* @var $data TrnKirimBuyerHeader */
                            $roll = $data->totalRoll;
                            return $roll > 0 ? '<strong>' . Yii::$app->formatter->asDecimal($roll, 0) . '</strong> <small class="text-muted">Roll</small>' : '-';
                        },
                    ],
                    [
                        'label' => 'Total Qty',
                        'format' => 'raw',
                        'hAlign' => 'right',
                        'value' => function($data){
                            /* @var $data TrnKirimBuyerHeader */
                            return $data->totalQtyFormatted;
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function($data){
                            /* @var $data TrnKirimBuyerHeader */
                            return TrnKirimBuyerHeader::statusOptions()[$data->status];
                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'data' => TrnKirimBuyerHeader::statusOptions(),
                            'options' => ['placeholder' => '...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ],
                        'contentOptions' => function($data){
                            return ['class' => $data->status == TrnKirimBuyerHeader::STATUS_POSTED ? 'text-success' : 'text-warning', 'style' => 'font-weight: bold; text-align: center;'];
                        },
                        'hAlign' => 'center',
                    ],
                    [
                        'attribute' => 'is_resmi',
                        'label' => 'Jenis SJ',
                        'visible' => !$isExport,
                        'format' => 'raw',
                        'value' => function($data){
                            return $data->is_resmi
                                ? '<span class="label label-primary">Resmi</span>'
                                : '<span class="label label-warning">Non Resmi</span>';
                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'data' => [1 => 'Resmi', 0 => 'Non Resmi'],
                            'options' => ['placeholder' => '...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ],
                        'hAlign' => 'center',
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'hAlign' => 'center',
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
