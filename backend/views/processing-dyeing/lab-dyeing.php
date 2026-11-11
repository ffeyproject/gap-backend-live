<?php

use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWo;
use common\models\ar\TrnWoColor;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $tab string */
/* @var $woCount int */
/* @var $kartuProsesCount int */
/* @var $searchModelWo common\models\ar\TrnWoColorSearch */
/* @var $dataProviderWo yii\data\ActiveDataProvider|null */
/* @var $searchModelKp common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $dataProviderKp yii\data\ActiveDataProvider|null */

$isKartuProses = ($tab === 'kartu-proses');
$tabTitle = $isKartuProses ? 'Kartu Proses Dyeing' : 'WO';

$this->title = 'Lab Dyeing - ' . $tabTitle . ' (Ready Color)';
$this->params['breadcrumbs'][] = ['label' => 'Processing', 'url' => '#'];
$this->params['breadcrumbs'][] = ['label' => 'Lab Dyeing', 'url' => ['lab-dyeing', 'tab' => $tab]];
$this->params['breadcrumbs'][] = $tabTitle;
?>

<div class="lab-dyeing-index">
    <div class="nav-tabs-custom" style="margin-bottom: 15px;">
        <ul class="nav nav-tabs">
            <li class="<?= !$isKartuProses ? 'active' : '' ?>">
                <a href="<?= Url::to(['lab-dyeing', 'tab' => 'wo']) ?>" style="font-weight: <?= !$isKartuProses ? 'bold' : 'normal' ?>; font-size: 14px;">
                    <i class="fa fa-file-text-o text-blue"></i> WO (Ready Color)
                    <span class="label <?= !$isKartuProses ? 'label-primary' : 'label-default' ?>" style="margin-left: 5px;"><?= number_format($woCount, 0, ',', '.') ?></span>
                </a>
            </li>
            <li class="<?= $isKartuProses ? 'active' : '' ?>">
                <a href="<?= Url::to(['lab-dyeing', 'tab' => 'kartu-proses']) ?>" style="font-weight: <?= $isKartuProses ? 'bold' : 'normal' ?>; font-size: 14px;">
                    <i class="fa fa-list-alt text-green"></i> Kartu Proses Dyeing (Ready Color)
                    <span class="label <?= $isKartuProses ? 'label-success' : 'label-default' ?>" style="margin-left: 5px;"><?= number_format($kartuProsesCount, 0, ',', '.') ?></span>
                </a>
            </li>
        </ul>

        <div class="tab-content" style="padding: 10px 0 0 0;">
            <?php if (!$isKartuProses): ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProviderWo,
                    'filterModel' => $searchModelWo,
                    'responsive' => false,
                    'responsiveWrap' => false,
                    'hover' => true,
                    'striped' => true,
                    'condensed' => true,
                    'showPageSummary' => true,
                    'tableOptions' => ['style' => 'white-space: nowrap;'],
                    'panel' => [
                        'type' => GridView::TYPE_PRIMARY,
                        'heading' => '<i class="fa fa-flask"></i> Daftar WO Ready Color (Dyeing)',
                        'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh', ['lab-dyeing', 'tab' => 'wo'], ['class' => 'btn btn-default']),
                    ],
                    'toolbar' => [
                        '{export}',
                        '{toggleData}',
                    ],
                    'columns' => [
                        ['class' => 'kartik\grid\SerialColumn'],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'template' => '{view}',
                            'buttons' => [
                                'view' => function ($url, $model) {
                                    /** @var $model TrnWoColor */
                                    if ($model->wo_id) {
                                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/trn-wo/view', 'id' => $model->wo_id], [
                                            'title' => 'Lihat Detail WO',
                                            'target' => '_blank',
                                            'data-pjax' => '0',
                                        ]);
                                    }
                                    return '';
                                },
                            ],
                        ],
                        [
                            'attribute' => 'woNo',
                            'label' => 'No. WO',
                            'format' => 'raw',
                            'value' => function ($model) {
                                /** @var $model TrnWoColor */
                                if ($model->wo && !empty($model->wo->no)) {
                                    return Html::a(Html::encode($model->wo->no), ['/trn-wo/view', 'id' => $model->wo->id], [
                                        'title' => 'Lihat detail WO ' . $model->wo->no,
                                        'target' => '_blank',
                                        'data-pjax' => '0',
                                    ]);
                                }
                                return '-';
                            },
                        ],
                        [
                            'attribute' => 'dateRangeWo',
                            'label' => 'Tanggal WO',
                            'value' => function ($model) {
                                /** @var $model TrnWoColor */
                                return $model->wo ? $model->wo->date : null;
                            },
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
                        ],
                        [
                            'attribute' => 'woStatus',
                            'label' => 'Status WO',
                            'value' => function ($model) {
                                /** @var $model TrnWoColor */
                                if ($model->wo && isset(TrnWo::statusOptions()[$model->wo->status])) {
                                    return TrnWo::statusOptions()[$model->wo->status];
                                }
                                return '-';
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filterWidgetOptions' => [
                                'data' => TrnWo::statusOptions(),
                                'options' => ['placeholder' => '...'],
                                'pluginOptions' => ['allowClear' => true],
                            ],
                        ],
                        [
                            'attribute' => 'customerName',
                            'label' => 'Customer',
                            'value' => function ($model) {
                                /** @var $model TrnWoColor */
                                return ($model->wo && $model->wo->sc && $model->wo->sc->cust) ? $model->wo->sc->cust->name : '-';
                            },
                        ],
                        [
                            'attribute' => 'marketingName',
                            'label' => 'Marketing',
                            'value' => function ($model) {
                                /** @var $model TrnWoColor */
                                return ($model->wo && $model->wo->sc && $model->wo->sc->marketing) ? $model->wo->sc->marketing->full_name : '-';
                            },
                        ],
                        [
                            'attribute' => 'greigeName',
                            'label' => 'Motif / Greige',
                            'value' => function ($model) {
                                /** @var $model TrnWoColor */
                                return $model->greige ? $model->greige->nama_kain : '-';
                            },
                        ],
                        [
                            'attribute' => 'colorName',
                            'label' => 'Warna',
                            'value' => function ($model) {
                                /** @var $model TrnWoColor */
                                return $model->moColor ? $model->moColor->color : '-';
                            },
                        ],
                        [
                            'attribute' => 'noLabDip',
                            'label' => 'No. Lab Dip',
                            'value' => function ($model) {
                                /** @var $model TrnWoColor */
                                return ($model->mo && !empty($model->mo->no_lab_dip)) ? $model->mo->no_lab_dip : '-';
                            },
                        ],
                        [
                            'attribute' => 'qty',
                            'label' => 'Qty (Batch)',
                            'format' => ['decimal', 2],
                            'hAlign' => 'right',
                            'pageSummary' => true,
                        ],
                        [
                            'attribute' => 'dateRangeReadyColour',
                            'label' => 'Tanggal Ready Color',
                            'value' => function ($model) {
                                /** @var $model TrnWoColor */
                                return !empty($model->date_ready_colour) ? date('Y-m-d', $model->date_ready_colour) : null;
                            },
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
                        ],
                        [
                            'attribute' => 'hasKartuProses',
                            'label' => 'Kartu Proses',
                            'format' => 'raw',
                            'hAlign' => 'center',
                            'value' => function ($model) {
                                /** @var $model TrnWoColor */
                                $activeCards = array_filter($model->kartuProsesDyeings, function ($kp) {
                                    return (int)$kp->status !== TrnKartuProsesDyeing::STATUS_BATAL;
                                });
                                $count = count($activeCards);
                                return $count > 0
                                    ? Html::tag('span', $count . ' Kartu', ['class' => 'label label-success'])
                                    : Html::tag('span', 'Belum Ada', ['class' => 'label label-default']);
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filterWidgetOptions' => [
                                'data' => [
                                    '1' => 'Sudah Ada Kartu Proses',
                                    '0' => 'Belum Ada Kartu Proses',
                                ],
                                'options' => ['placeholder' => '...'],
                                'pluginOptions' => ['allowClear' => true],
                            ],
                        ],
                        [
                            'attribute' => 'note',
                            'label' => 'Catatan',
                            'format' => 'ntext',
                        ],
                    ],
                ]) ?>
            <?php else: ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProviderKp,
                    'filterModel' => $searchModelKp,
                    'responsive' => false,
                    'responsiveWrap' => false,
                    'hover' => true,
                    'striped' => true,
                    'condensed' => true,
                    'showPageSummary' => true,
                    'tableOptions' => ['style' => 'white-space: nowrap;'],
                    'panel' => [
                        'type' => GridView::TYPE_SUCCESS,
                        'heading' => '<i class="fa fa-flask"></i> Daftar Kartu Proses Dyeing Ready Color',
                        'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh', ['lab-dyeing', 'tab' => 'kartu-proses'], ['class' => 'btn btn-default']),
                    ],
                    'toolbar' => [
                        '{export}',
                        '{toggleData}',
                    ],
                    'columns' => [
                        ['class' => 'kartik\grid\SerialColumn'],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'template' => '{view}',
                            'buttons' => [
                                'view' => function ($url, $model) {
                                    /** @var $model TrnKartuProsesDyeing */
                                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/processing-dyeing/view', 'id' => $model->id], [
                                        'title' => 'Lihat Kartu Proses Dyeing',
                                        'target' => '_blank',
                                        'data-pjax' => '0',
                                    ]);
                                },
                            ],
                        ],
                        'id',
                        [
                            'attribute' => 'woNo',
                            'label' => 'No. WO',
                            'format' => 'raw',
                            'value' => function ($model) {
                                /** @var $model TrnKartuProsesDyeing */
                                if ($model->wo && !empty($model->wo->no)) {
                                    return Html::a(Html::encode($model->wo->no), ['/trn-wo/view', 'id' => $model->wo->id], [
                                        'title' => 'Lihat detail WO ' . $model->wo->no,
                                        'target' => '_blank',
                                        'data-pjax' => '0',
                                    ]);
                                }
                                return '-';
                            },
                        ],
                        'no',
                        'nomor_kartu',
                        [
                            'attribute' => 'customerName',
                            'label' => 'Customer',
                            'value' => function ($model) {
                                /** @var $model TrnKartuProsesDyeing */
                                return ($model->sc && $model->sc->cust) ? $model->sc->cust->cust_no : '-';
                            },
                        ],
                        [
                            'attribute' => 'motif',
                            'label' => 'Motif / Greige',
                            'value' => function ($model) {
                                /** @var $model TrnKartuProsesDyeing */
                                return ($model->wo && $model->wo->greige) ? $model->wo->greige->nama_kain : '-';
                            },
                        ],
                        [
                            'attribute' => 'warna',
                            'label' => 'Warna',
                            'value' => function ($model) {
                                /** @var $model TrnKartuProsesDyeing */
                                return ($model->woColor && $model->woColor->moColor) ? $model->woColor->moColor->color : '-';
                            },
                        ],
                        [
                            'attribute' => 'noLabDip',
                            'label' => 'No. Lab Dip',
                            'value' => function ($model) {
                                /** @var $model TrnKartuProsesDyeing */
                                return ($model->mo && !empty($model->mo->no_lab_dip)) ? $model->mo->no_lab_dip : '-';
                            },
                        ],
                        [
                            'attribute' => 'asal_greige',
                            'value' => function ($model) {
                                /** @var $model TrnKartuProsesDyeing */
                                $options = TrnStockGreige::asalGreigeOptions();
                                return isset($options[$model->asal_greige]) ? $options[$model->asal_greige] : '-';
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filterWidgetOptions' => [
                                'data' => TrnStockGreige::asalGreigeOptions(),
                                'options' => ['placeholder' => '...'],
                                'pluginOptions' => ['allowClear' => true],
                            ],
                        ],
                        [
                            'label' => 'Panjang (m)',
                            'value' => function ($model) {
                                /** @var $model TrnKartuProsesDyeing */
                                $totalPanjang = 0;
                                foreach ($model->trnKartuProsesDyeingItems as $item) {
                                    $totalPanjang += (float)$item->panjang_m;
                                }
                                return $totalPanjang;
                            },
                            'format' => 'decimal',
                            'pageSummary' => true,
                            'hAlign' => 'right',
                        ],
                        [
                            'attribute' => 'status',
                            'value' => function ($model) {
                                /** @var $model TrnKartuProsesDyeing */
                                $options = TrnKartuProsesDyeing::statusOptions();
                                return isset($options[$model->status]) ? $options[$model->status] : '-';
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filterWidgetOptions' => [
                                'data' => TrnKartuProsesDyeing::statusOptions(),
                                'options' => ['placeholder' => '...'],
                                'pluginOptions' => ['allowClear' => true],
                            ],
                        ],
                        [
                            'attribute' => 'dateRange',
                            'label' => 'Tanggal Kartu',
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
                        ],
                        [
                            'attribute' => 'dateRangeReadyColour',
                            'label' => 'Tanggal Ready Color',
                            'value' => function ($model) {
                                /** @var $model TrnKartuProsesDyeing */
                                return ($model->woColor && !empty($model->woColor->date_ready_colour))
                                    ? date('Y-m-d', $model->woColor->date_ready_colour)
                                    : null;
                            },
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
                        ],
                        [
                            'attribute' => 'toping_matching',
                            'label' => 'Toping Matching',
                            'value' => function ($model) {
                                /** @var $model TrnKartuProsesDyeing */
                                $topingMatching = $model->toping_matching ? 'Ya' : 'Tidak';
                                $dateTopingMatching = $model->date_toping_matching;
                                return $topingMatching . ($dateTopingMatching !== null ? ' / ' . date('Y-m-d', $dateTopingMatching) : '');
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filterWidgetOptions' => [
                                'data' => [1 => 'Ya', 0 => 'Tidak'],
                                'options' => ['placeholder' => '...'],
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'hAlign' => 'center',
                        ],
                    ],
                ]) ?>
            <?php endif; ?>
        </div>
    </div>
</div>

