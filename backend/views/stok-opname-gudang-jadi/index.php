<?php

use common\models\ar\TrnGudangJadiOpnamePcs;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TrnGudangJadiOpnamePcsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $totalPcs int */

$this->title = 'Stok Opname Gudang Jadi';
$this->params['breadcrumbs'][] = ['label' => 'Gudang Jadi', 'url' => ['/trn-gudang-jadi/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stok-opname-gudang-jadi-index">

    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-barcode"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Item Pcs Opname</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asInteger($totalPcs) ?> Pcs / Roll</span>
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
        'toolbar' => [
            '{toggleData}',
            '{export}'
        ],
        'panel' => [
            'type' => 'primary',
            'heading' => '<h3 class="panel-title"><i class="fa fa-list"></i> Data Stok Opname Gudang Jadi</h3>',
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh', ['index'], ['class' => 'btn btn-default']),
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
                }
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
                    return '<span class="label label-default">Kosong</span>';
                }
            ],
            'opname_code',
            'qr_code',
            [
                'attribute' => 'woNo',
                'label' => 'No. WO',
                'value' => function($model) {
                    return $model->gudangJadi && $model->gudangJadi->wo ? $model->gudangJadi->wo->no : '-';
                }
            ],
            [
                'attribute' => 'scNo',
                'label' => 'No. SC',
                'value' => function($model) {
                    return $model->gudangJadi && $model->gudangJadi->wo && $model->gudangJadi->wo->mo && $model->gudangJadi->wo->mo->scGreige && $model->gudangJadi->wo->mo->scGreige->sc ? $model->gudangJadi->wo->mo->scGreige->sc->no : '-';
                }
            ],
            [
                'attribute' => 'marketingName',
                'label' => 'Marketing',
                'value' => function($model) {
                    return $model->gudangJadi && $model->gudangJadi->wo && $model->gudangJadi->wo->mo && $model->gudangJadi->wo->mo->scGreige && $model->gudangJadi->wo->mo->scGreige->sc && $model->gudangJadi->wo->mo->scGreige->sc->marketing ? $model->gudangJadi->wo->mo->scGreige->sc->marketing->full_name : '-';
                }
            ],
            [
                'attribute' => 'customerName',
                'label' => 'Buyer',
                'value' => function($model) {
                    return $model->gudangJadi && $model->gudangJadi->wo && $model->gudangJadi->wo->mo && $model->gudangJadi->wo->mo->scGreige && $model->gudangJadi->wo->mo->scGreige->sc && $model->gudangJadi->wo->mo->scGreige->sc->cust ? $model->gudangJadi->wo->mo->scGreige->sc->cust->name : '-';
                }
            ],
            [
                'attribute' => 'motif',
                'label' => 'Motif / Kain',
                'value' => function($model) {
                    if ($model->gudangJadi && $model->gudangJadi->wo) {
                        return $model->gudangJadi->wo->greigeNamaKain;
                    }
                    return !empty($model->qr_code_desc) ? $model->qr_code_desc : '-';
                }
            ],
            [
                'attribute' => 'color',
                'label' => 'Color / Warna',
                'value' => function($model) {
                    return $model->gudangJadi && !empty($model->gudangJadi->color) ? $model->gudangJadi->color : '-';
                }
            ],
            [
                'attribute' => 'qty',
                'value' => function($model) {
                    return Yii::$app->formatter->asDecimal($model->qty);
                }
            ],
            'unit',
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
                        return '<span class="label label-success">' . Html::encode($model->statusName) . '</span>';
                    }
                    return '<span class="label label-warning">' . Html::encode($model->statusName) . '</span>';
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
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $model->id], [
                            'title' => 'Detail Stok Opname Pcs',
                            'class' => 'btn btn-xs btn-default',
                        ]);
                    }
                ]
            ]
        ],
    ]); ?>
</div>
