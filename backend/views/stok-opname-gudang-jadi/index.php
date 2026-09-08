<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadiOpnamePcs;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TrnGudangJadiOpnamePcsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $totalPcs int */
/* @var $totalQty float */
/* @var $totalVerified int */
/* @var $totalQtyVerified float */
/* @var $totalStock int */
/* @var $totalQtyStock float */
/* @var $totalOut int */
/* @var $totalQtyOut float */

$this->title = 'Stok Opname Gudang Jadi';
$this->params['breadcrumbs'][] = ['label' => 'Gudang Jadi', 'url' => ['/trn-gudang-jadi/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stok-opname-gudang-jadi-index">

    <!-- Summary Info Box Cards -->
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-cubes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Roll / Pcs</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asInteger($totalPcs) ?> Roll (<?= Yii::$app->formatter->asDecimal($totalQty, 2) ?>)</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-green">
                <span class="info-box-icon"><i class="fa fa-dashboard"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Kuantitas</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asDecimal($totalQty, 2) ?></span>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-sm-6 col-xs-12">
            <div class="info-box bg-teal">
                <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Terverifikasi</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asInteger($totalVerified) ?> Roll (<?= Yii::$app->formatter->asDecimal($totalQtyVerified, 2) ?>)</span>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-sm-6 col-xs-12">
            <div class="info-box bg-yellow">
                <span class="info-box-icon"><i class="fa fa-cubes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Stock</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asInteger($totalStock) ?> Roll (<?= Yii::$app->formatter->asDecimal($totalQtyStock, 2) ?>)</span>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-sm-6 col-xs-12">
            <div class="info-box bg-red">
                <span class="info-box-icon"><i class="fa fa-times-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Out / Keluar</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asInteger($totalOut) ?> Roll (<?= Yii::$app->formatter->asDecimal($totalQtyOut, 2) ?>)</span>
                </div>
            </div>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'StokOpnameGdJadiGrid',
        'resizableColumns' => false,
        'responsiveWrap' => false,
        'pjax' => true,
        'showPageSummary' => true,
        'toolbar' => [
            '{toggleData}',
            '{export}'
        ],
        'panel' => [
            'type' => 'primary',
            'heading' => '<h3 class="panel-title"><i class="fa fa-list"></i> Data Stok Opname Gudang Jadi</h3>',
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh', ['index'], ['class' => 'btn btn-default']) . ' ' .
                        Html::a('<i class="fa fa-refresh"></i> Sync Status Out', ['sync-status-out'], [
                            'class' => 'btn btn-warning',
                            'data-confirm' => 'Apakah Anda yakin ingin menyinkronkan status stok opname dengan status fisik Gudang Jadi saat ini?',
                            'title' => 'Ubah status opname menjadi OUT jika stok di Gudang Jadi sudah bukan Stock',
                        ]) . ' ' .
                        Html::a('<i class="fa fa-plus-circle"></i> Sync & Tambah Stock Gudang Jadi', ['sync-stock-gudang-jadi'], [
                            'class' => 'btn btn-success',
                            'data-confirm' => 'Apakah Anda yakin ingin menyinkronkan data opname dan menambahkan stock ke Gudang Jadi yang belum memiliki ID Gudang Jadi?',
                            'title' => 'Buat & sinkronkan stock Gudang Jadi dari data opname yang ID Gudang Jadi-nya masih kosong',
                        ]) . ' ' .
                        Html::a('<i class="fa fa-pie-chart"></i> Rekap Stok Opname', ['rekap'], ['class' => 'btn btn-info']),
            'after' => false,
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'label' => 'ID Opname',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a('<strong>#' . $model->id . '</strong>', ['view', 'id' => $model->id], [
                        'title' => 'Lihat Detail',
                        'data-pjax' => '0',
                    ]);
                },
                'pageSummary' => 'TOTAL Halaman Ini',
            ],
            [
                'attribute' => 'id_trn_gudang_jadi',
                'label' => 'ID Gudang Jadi',
                'format' => 'raw',
                'value' => function($model) {
                    if ($model->id_trn_gudang_jadi) {
                        return Html::a('<span class="label label-info">#' . $model->id_trn_gudang_jadi . '</span>', ['/trn-gudang-jadi/view', 'id' => $model->id_trn_gudang_jadi], [
                            'title' => 'Lihat Stok Gudang Jadi',
                            'target' => '_blank',
                            'data-pjax' => '0',
                        ]);
                    }
                    return '<span class="label label-default">Kosong</span> ' .
                        Html::a('<i class="fa fa-plus"></i> Buat Stock', ['create-stock', 'id' => $model->id], [
                            'class' => 'btn btn-xs btn-success',
                            'data-confirm' => 'Buat dan sinkronkan stock gudang jadi untuk item opname #' . $model->id . ' ini?',
                            'title' => 'Buat & Hubungkan ke Stock Gudang Jadi',
                            'data-pjax' => '0',
                        ]);
                }
            ],
            'opname_code',
            'qr_code',
            [
                'attribute' => 'woNo',
                'label' => 'No. WO',
                'value' => function($model) {
                    return $model->woNo;
                }
            ],
            [
                'attribute' => 'scNo',
                'label' => 'No. SC',
                'value' => function($model) {
                    return $model->scNo;
                }
            ],
            [
                'attribute' => 'marketingName',
                'label' => 'Marketing',
                'value' => function($model) {
                    return $model->marketingName;
                }
            ],
            [
                'attribute' => 'customerName',
                'label' => 'Buyer',
                'value' => function($model) {
                    return $model->customerName;
                }
            ],
            [
                'attribute' => 'motif',
                'label' => 'Motif / Kain',
                'value' => function($model) {
                    return $model->motif;
                }
            ],
            [
                'attribute' => 'color',
                'label' => 'Color / Warna',
                'value' => function($model) {
                    return $model->color;
                }
            ],
            [
                'attribute' => 'qty',
                'value' => function($model) {
                    return Yii::$app->formatter->asDecimal($model->qty);
                },
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM,
            ],
            [
                'attribute' => 'unit',
                'label' => 'Satuan',
                'value' => function($model) {
                    return $model->unitName;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => MstGreigeGroup::unitOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'attribute' => 'grade',
                'value' => function($model) {
                    return $model->gradeName;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::gradeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            'locs_code',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    if ($model->status === TrnGudangJadiOpnamePcs::STATUS_VERIFIED) {
                        return '<span class="label label-success"><i class="fa fa-check"></i> ' . Html::encode($model->statusName) . '</span>';
                    } elseif ($model->status === TrnGudangJadiOpnamePcs::STATUS_OUT) {
                        return '<span class="label label-danger"><i class="fa fa-times-circle"></i> ' . Html::encode($model->statusName) . '</span>';
                    }
                    return '<span class="label label-warning"><i class="fa fa-cubes"></i> ' . Html::encode($model->statusName) . '</span>';
                },
                'format' => 'raw',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnGudangJadiOpnamePcs::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'attribute' => 'dateRange',
                'label' => 'Tgl. Opname',
                'value' => 'created_at',
                'format' => 'datetime',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'Y-m-d',
                            'separator' => ' to ',
                        ]
                    ]
                ]
            ],

            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view} {create-stock}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $model->id], [
                            'title' => 'Detail Stok Opname Pcs',
                            'class' => 'btn btn-xs btn-default',
                        ]);
                    },
                    'create-stock' => function($url, $model, $key) {
                        if (!$model->id_trn_gudang_jadi) {
                            return Html::a('<span class="glyphicon glyphicon-plus"></span>', ['create-stock', 'id' => $model->id], [
                                'title' => 'Buat & Hubungkan ke Stock Gudang Jadi',
                                'class' => 'btn btn-xs btn-success',
                                'data-confirm' => 'Buat stock gudang jadi untuk item opname #' . $model->id . ' ini?',
                                'data-pjax' => '0',
                            ]);
                        }
                        return '';
                    }
                ]
            ]
        ],
    ]); ?>
</div>
