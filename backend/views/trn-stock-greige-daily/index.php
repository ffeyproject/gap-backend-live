<?php

use common\models\ar\MstGreige;
use common\models\ar\TrnStockGreigeDaily;
use common\models\ar\TrnStockGreigeDailySearch;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnStockGreigeDailySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $availableDates array */

$this->title = 'Rekap Stock Greige Harian & Perubahan';
$this->params['breadcrumbs'][] = ['label' => 'Stock Greige', 'url' => ['/trn-stock-greige/index']];
$this->params['breadcrumbs'][] = $this->title;

$greigeList = ArrayHelper::map(
    MstGreige::find()->select(['id', 'nama_kain'])->orderBy('nama_kain ASC')->asArray()->all(),
    'id',
    'nama_kain'
);

$currentDateFilter = $searchModel->dateRange ?: $searchModel->date;
?>

<div class="trn-stock-greige-daily-index">

    <?= $this->render('_save_modal') ?>

    <!-- Action Bar -->
    <div class="row" style="margin-bottom: 12px;">
        <div class="col-md-12">
            <div class="btn-group pull-left" role="group">
                <?= Html::a('<i class="glyphicon glyphicon-camera"></i> Simpan Snapshot Stock Harian', '#', [
                    'class' => 'btn btn-success',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal-save-daily-stock',
                    'title' => 'Simpan snapshot stock harian per motif dari data Packing List Greige'
                ]) ?>
                <?= Html::a('<i class="glyphicon glyphicon-th"></i> Mode Matrix / Pivot Harian', ['matrix'], [
                    'class' => 'btn btn-primary',
                    'title' => 'Lihat perbandingan stock antar tanggal dalam bentuk matrix'
                ]) ?>
                <?= Html::a('<i class="glyphicon glyphicon-download-alt"></i> Export Excel (CSV)', array_merge(['export-excel'], Yii::$app->request->queryParams), [
                    'class' => 'btn btn-info',
                    'title' => 'Download data rekap stock harian'
                ]) ?>
            </div>

            <div class="btn-group pull-right" role="group">
                <?= Html::a('<i class="glyphicon glyphicon-list-alt"></i> Packing List Greige', ['/trn-stock-greige/index'], [
                    'class' => 'btn btn-default',
                    'title' => 'Kembali ke halaman stock packing list fresh'
                ]) ?>
            </div>
        </div>
    </div>

    <!-- Quick Day Selector & DatePicker Bar -->
    <?php
    $selectedDate = '';
    if (!empty($searchModel->dateRange)) {
        $selectedDate = substr($searchModel->dateRange, 0, 10);
    } elseif (!empty($searchModel->date)) {
        $selectedDate = $searchModel->date;
    }
    $displayDate = !empty($selectedDate) ? $selectedDate : date('Y-m-d');
    $currentDayName = TrnStockGreigeDaily::getIndonesianDayName($displayDate);
    $currentFormatted = TrnStockGreigeDaily::formatIndonesianDate($displayDate, true);
    $prevDate = date('Y-m-d', strtotime($displayDate . ' -1 day'));
    $nextDate = date('Y-m-d', strtotime($displayDate . ' +1 day'));
    ?>
    <div class="panel panel-info" style="margin-bottom: 15px; border-color: #bce8f1;">
        <div class="panel-heading" style="padding: 8px 15px; background-color: #d9edf7;">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="panel-title" style="font-size: 14px; font-weight: bold; color: #31708f; line-height: 24px;">
                        <i class="glyphicon glyphicon-calendar"></i> PILIH & CEK LANGSUNG PER HARI
                    </h4>
                </div>
                <div class="col-md-6 text-right">
                    <?php if (!empty($selectedDate)): ?>
                        <span class="label label-primary" style="font-size: 12px; padding: 4px 10px;">
                            <i class="glyphicon glyphicon-eye-open"></i> Aktif: <strong><?= $currentFormatted ?></strong>
                        </span>
                    <?php else: ?>
                        <span class="label label-default" style="font-size: 12px; padding: 4px 10px;">
                            <i class="glyphicon glyphicon-list"></i> Menampilkan: <strong>Semua Tanggal</strong>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="panel-body" style="padding: 12px 15px; background-color: #fcfcfc;">
            <div class="row">
                <div class="col-md-4 col-sm-6">
                    <label class="control-label" style="font-size: 12px; color: #555;">Pilih Tanggal Tertentu:</label>
                    <div class="input-group">
                        <?= \kartik\widgets\DatePicker::widget([
                            'name' => 'quick_date_picker',
                            'value' => $selectedDate,
                            'id' => 'quick-day-picker',
                            'options' => [
                                'placeholder' => 'Pilih tanggal untuk cek...',
                                'class' => 'form-control input-sm',
                                'style' => 'font-weight:bold; color:#1e3799;',
                            ],
                            'pluginOptions' => [
                                'format' => 'yyyy-mm-dd',
                                'autoclose' => true,
                                'todayHighlight' => true,
                            ],
                            'pluginEvents' => [
                                'changeDate' => "function(e) {
                                    var picked = $('#quick-day-picker').val();
                                    if(picked) {
                                        window.location.href = '" . Url::to(['index']) . "?TrnStockGreigeDailySearch[dateRange]=' + picked + ' to ' + picked;
                                    }
                                }",
                            ]
                        ]); ?>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-primary btn-sm" onclick="
                                var picked = $('#quick-day-picker').val();
                                if(picked) {
                                    window.location.href = '<?= Url::to(['index']) ?>?TrnStockGreigeDailySearch[dateRange]=' + picked + ' to ' + picked;
                                } else {
                                    alert('Silakan pilih tanggal terlebih dahulu.');
                                }
                            ">
                                <i class="glyphicon glyphicon-search"></i> Cek Hari
                            </button>
                        </span>
                    </div>
                </div>

                <div class="col-md-8 col-sm-6">
                    <label class="control-label" style="font-size: 12px; color: #555;">Navigasi Cepat Hari:</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                        <?= Html::a('<i class="glyphicon glyphicon-chevron-left"></i> ' . TrnStockGreigeDaily::getIndonesianDayName($prevDate), [
                            'index',
                            'TrnStockGreigeDailySearch[dateRange]' => "{$prevDate} to {$prevDate}"
                        ], [
                            'class' => 'btn btn-default btn-sm',
                            'title' => 'Cek hari sebelumnya: ' . TrnStockGreigeDaily::formatIndonesianDate($prevDate, true),
                        ]) ?>

                        <?= Html::a('<i class="glyphicon glyphicon-calendar"></i> Hari Ini (' . TrnStockGreigeDaily::getIndonesianDayName(date('Y-m-d')) . ')', [
                            'index',
                            'TrnStockGreigeDailySearch[dateRange]' => date('Y-m-d') . ' to ' . date('Y-m-d')
                        ], [
                            'class' => 'btn ' . ($selectedDate === date('Y-m-d') ? 'btn-success' : 'btn-default') . ' btn-sm',
                            'style' => 'font-weight: bold;',
                            'title' => 'Cek stock hari ini: ' . TrnStockGreigeDaily::formatIndonesianDate(date('Y-m-d'), true),
                        ]) ?>

                        <?= Html::a(TrnStockGreigeDaily::getIndonesianDayName($nextDate) . ' <i class="glyphicon glyphicon-chevron-right"></i>', [
                            'index',
                            'TrnStockGreigeDailySearch[dateRange]' => "{$nextDate} to {$nextDate}"
                        ], [
                            'class' => 'btn btn-default btn-sm',
                            'title' => 'Cek hari berikutnya: ' . TrnStockGreigeDaily::formatIndonesianDate($nextDate, true),
                        ]) ?>

                        <?= Html::a('<i class="glyphicon glyphicon-th-list"></i> Tampilkan Semua Tanggal', ['index'], [
                            'class' => 'btn ' . (empty($selectedDate) ? 'btn-primary' : 'btn-default') . ' btn-sm',
                            'style' => (empty($selectedDate) ? 'font-weight: bold;' : ''),
                            'title' => 'Tampilkan seluruh data tanpa filter tanggal',
                        ]) ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($availableDates) && count($availableDates) > 1): ?>
            <div class="row" style="margin-top: 10px; padding-top: 8px; border-top: 1px dashed #ddd;">
                <div class="col-md-12">
                    <span style="font-size: 11px; color: #777; margin-right: 6px;">Riwayat Tanggal Tersimpan:</span>
                    <?php foreach ($availableDates as $aDate): ?>
                        <?php 
                        $dayName = TrnStockGreigeDaily::getIndonesianDayName($aDate);
                        $formattedShort = TrnStockGreigeDaily::formatIndonesianDate($aDate, false);
                        $isActive = ($selectedDate === $aDate);
                        $isSunday = (date('w', strtotime($aDate)) == 0);
                        $isSaturday = (date('w', strtotime($aDate)) == 6);
                        $badgeColor = $isActive ? '#2980b9' : ($isSunday ? '#c0392b' : ($isSaturday ? '#d35400' : '#7f8c8d'));
                        ?>
                        <?= Html::a(
                            "{$dayName}, {$formattedShort}",
                            ['index', 'TrnStockGreigeDailySearch[dateRange]' => "{$aDate} to {$aDate}"],
                            [
                                'class' => 'label',
                                'style' => "background-color: {$badgeColor}; font-size: 11px; padding: 3px 7px; display: inline-block; margin-right: 4px; margin-bottom: 2px; text-decoration: none;",
                                'title' => "Klik untuk cek stock {$dayName}, {$formattedShort}",
                            ]
                        ) ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'StockGreigeDailyGrid',
        'responsiveWrap' => false,
        'pjax' => true,
        'panel' => [
            'type' => 'primary',
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-calendar"></i> Catatan Stock Harian & Tambahan/Perubahan Antar Hari</h3>',
            'before' => Html::tag('div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh', ['index'], ['class' => 'btn btn-default btn-sm', 'style' => 'margin-right:5px;']) .
                '<span class="text-muted" style="margin-left: 10px;">
                    <i class="glyphicon glyphicon-info-sign"></i> Setiap baris menampilkan nama hari lengkap dan perbandingan tambahan stock dengan snapshot sebelumnya.
                </span>',
                ['style' => 'padding: 5px 0;']
            ),
            'after' => false,
        ],
        'showPageSummary' => true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            [
                'attribute' => 'dateRange',
                'label' => 'HARI & TANGGAL',
                'value' => function ($model) {
                    /** @var TrnStockGreigeDaily $model */
                    $dayName = $model->dayName;
                    $dateStr = Yii::$app->formatter->asDate($model->date, 'php:d/m/Y');
                    $isSunday = (date('w', strtotime($model->date)) == 0);
                    $isSaturday = (date('w', strtotime($model->date)) == 6);

                    $badgeColor = $isSunday ? '#e74c3c' : ($isSaturday ? '#e67e22' : '#2980b9');

                    return "<div style='text-align:center;'>
                        <span class='label' style='background-color:{$badgeColor}; font-size:11px; padding:2px 6px; display:inline-block; margin-bottom:2px;'>{$dayName}</span>
                        <div style='font-size:12px; font-weight:bold; color:#333;'>{$dateStr}</div>
                    </div>";
                },
                'format' => 'raw',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'Y-m-d',
                            'separator' => ' to ',
                        ]
                    ]
                ],
                'headerOptions' => ['style' => 'width: 140px; text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center;'],
            ],

            [
                'attribute' => 'greige_id',
                'label' => 'Motif / Nama Greige',
                'value' => function ($model) {
                    /** @var TrnStockGreigeDaily $model */
                    return $model->greige ? $model->greige->nama_kain : '-';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $greigeList,
                    'options' => ['placeholder' => 'Pilih Motif...'],
                    'pluginOptions' => ['allowClear' => true],
                ],
                'contentOptions' => ['style' => 'font-weight: bold; color: #1e3799;'],
            ],

            [
                'label' => 'Group Greige',
                'value' => function ($model) {
                    /** @var TrnStockGreigeDaily $model */
                    return $model->greigeGroup ? $model->greigeGroup->nama_kain : '-';
                },
                'contentOptions' => ['style' => 'color: #555;'],
            ],

            [
                'attribute' => 'asal_greige',
                'label' => 'Asal Greige',
                'value' => function ($model) {
                    /** @var TrnStockGreigeDaily $model */
                    return $model->asalGreigeName;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\TrnStockGreige::asalGreigeOptions(),
                    'options' => ['placeholder' => 'Pilih Asal Greige...'],
                    'pluginOptions' => ['allowClear' => true],
                ],
                'headerOptions' => ['style' => 'width: 140px;'],
                'contentOptions' => ['style' => 'font-size: 12px;'],
            ],

            [
                'attribute' => 'total_panjang',
                'label' => 'Stock Hari Ini (m)',
                'format' => 'decimal',
                'pageSummary' => true,
                'headerOptions' => ['style' => 'text-align: right; background-color: #e8f4f8;'],
                'contentOptions' => ['style' => 'text-align: right; font-weight: bold; background-color: #f9fbfd;'],
            ],

            [
                'attribute' => 'total_roll',
                'label' => 'Roll Hari Ini',
                'format' => 'integer',
                'pageSummary' => true,
                'headerOptions' => ['style' => 'text-align: right; width: 95px;'],
                'contentOptions' => ['style' => 'text-align: right;'],
            ],

            [
                'attribute' => 'prev_total_panjang',
                'label' => 'Stock Sebelumnya (m)',
                'value' => function ($model) {
                    /** @var TrnStockGreigeDaily $model */
                    if (empty($model->prev_date)) {
                        return '<span class="text-muted"><em>(Awal)</em></span>';
                    }
                    $prevDay = TrnStockGreigeDaily::getIndonesianDayName($model->prev_date);
                    $prevDateFmt = Yii::$app->formatter->asDate($model->prev_date, 'php:d/m');
                    return Yii::$app->formatter->asDecimal($model->prev_total_panjang) . 
                        " <br><small class='text-muted' style='font-size:10px;'>({$prevDay}, {$prevDateFmt})</small>";
                },
                'format' => 'raw',
                'headerOptions' => ['style' => 'text-align: right;'],
                'contentOptions' => ['style' => 'text-align: right; color: #666;'],
            ],

            [
                'attribute' => 'diff_panjang',
                'label' => 'Tambahan / Selisih (m)',
                'value' => function ($model) {
                    /** @var TrnStockGreigeDaily $model */
                    $diff = (float)$model->diff_panjang;
                    if (empty($model->prev_date)) {
                        return '<span class="label label-info" style="font-size:11px; display:inline-block; padding: 4px 7px;">' .
                            '<i class="glyphicon glyphicon-star"></i> Awal: ' . Yii::$app->formatter->asDecimal($model->total_panjang) . ' m</span>';
                    }

                    if ($diff > 0) {
                        return '<span class="label label-success" style="font-size:11px; display:inline-block; padding: 4px 7px;">' .
                            '<i class="glyphicon glyphicon-arrow-up"></i> +' . Yii::$app->formatter->asDecimal($diff) . ' m</span>';
                    } elseif ($diff < 0) {
                        return '<span class="label label-danger" style="font-size:11px; display:inline-block; padding: 4px 7px;">' .
                            '<i class="glyphicon glyphicon-arrow-down"></i> ' . Yii::$app->formatter->asDecimal($diff) . ' m</span>';
                    } else {
                        return '<span class="label label-default" style="font-size:11px; display:inline-block; padding: 4px 7px;">0.00 m</span>';
                    }
                },
                'format' => 'raw',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => TrnStockGreigeDailySearch::diffStatusOptions(),
                'filterWidgetOptions' => [
                    'options' => ['placeholder' => 'Semua Perubahan...'],
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterAttribute' => 'diffStatus',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM,
                'headerOptions' => ['style' => 'text-align: center; width: 170px;'],
                'contentOptions' => ['style' => 'text-align: center;'],
            ],

            [
                'attribute' => 'diff_roll',
                'label' => 'Tambahan Roll',
                'value' => function ($model) {
                    /** @var TrnStockGreigeDaily $model */
                    $diff = (int)$model->diff_roll;
                    if (empty($model->prev_date)) {
                        return '<span class="text-muted">' . $model->total_roll . ' roll</span>';
                    }
                    if ($diff > 0) {
                        return '<strong class="text-success">+' . $diff . '</strong>';
                    } elseif ($diff < 0) {
                        return '<strong class="text-danger">' . $diff . '</strong>';
                    } else {
                        return '<span class="text-muted">0</span>';
                    }
                },
                'format' => 'raw',
                'headerOptions' => ['style' => 'text-align: right; width: 100px;'],
                'contentOptions' => ['style' => 'text-align: right;'],
            ],

            [
                'attribute' => 'grade_a',
                'label' => 'Grade A (m)',
                'format' => 'decimal',
                'pageSummary' => true,
                'headerOptions' => ['style' => 'text-align: right; background-color: #e8f8f5;'],
                'contentOptions' => ['style' => 'text-align: right; font-size: 12px;'],
            ],

            [
                'attribute' => 'grade_b',
                'label' => 'Grade B (m)',
                'format' => 'decimal',
                'pageSummary' => true,
                'headerOptions' => ['style' => 'text-align: right; background-color: #e8f8f5;'],
                'contentOptions' => ['style' => 'text-align: right; font-size: 12px;'],
            ],

            [
                'attribute' => 'grade_c',
                'label' => 'Grade C (m)',
                'format' => 'decimal',
                'pageSummary' => true,
                'headerOptions' => ['style' => 'text-align: right; background-color: #e8f8f5;'],
                'contentOptions' => ['style' => 'text-align: right; font-size: 12px;'],
            ],

            [
                'attribute' => 'grade_d',
                'label' => 'Grade D (m)',
                'format' => 'decimal',
                'pageSummary' => true,
                'headerOptions' => ['style' => 'text-align: right; background-color: #e8f8f5;'],
                'contentOptions' => ['style' => 'text-align: right; font-size: 12px;'],
            ],

            [
                'attribute' => 'grade_e',
                'label' => 'Grade E (m)',
                'format' => 'decimal',
                'pageSummary' => true,
                'headerOptions' => ['style' => 'text-align: right; background-color: #e8f8f5;'],
                'contentOptions' => ['style' => 'text-align: right; font-size: 12px;'],
            ],

            [
                'attribute' => 'grade_ng',
                'label' => 'Grade NG (m)',
                'format' => 'decimal',
                'pageSummary' => true,
                'headerOptions' => ['style' => 'text-align: right; background-color: #fdebd0;'],
                'contentOptions' => ['style' => 'text-align: right; font-size: 12px; color: #c0392b;'],
            ],

            [
                'attribute' => 'grade_lain',
                'label' => 'Grade Lain (m)',
                'format' => 'decimal',
                'pageSummary' => true,
                'headerOptions' => ['style' => 'text-align: right; background-color: #f2f4f4;'],
                'contentOptions' => ['style' => 'text-align: right; font-size: 12px; color: #7f8c8d;'],
            ],

            [
                'attribute' => 'created_at',
                'label' => 'Waktu Simpan',
                'value' => function ($model) {
                    /** @var TrnStockGreigeDaily $model */
                    return $model->created_at ? Yii::$app->formatter->asDatetime($model->created_at) : '-';
                },
                'headerOptions' => ['style' => 'font-size: 11px; width: 130px; text-align: center;'],
                'contentOptions' => ['style' => 'font-size: 11px; text-align: center; color: #777;'],
            ],

            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{delete}',
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-trash text-danger"></span>', ['delete', 'id' => $model->id], [
                            'title' => 'Hapus snapshot ini',
                            'data-confirm' => 'Yakin ingin menghapus catatan snapshot stock ini?',
                            'data-method' => 'post',
                        ]);
                    }
                ],
                'headerOptions' => ['style' => 'width: 45px; text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center;'],
            ],
        ],
    ]); ?>

</div>
