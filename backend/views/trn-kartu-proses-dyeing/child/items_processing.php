<?php
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnKartuProsesDyeingItem;
use common\models\ar\TrnStockGreige;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrnKartuProsesDyeing */

$wo = $model->wo;
$greige = $wo->greige;
$greigeGroup = $greige->group;

// ===== CONTROL LOGIC =====
$canCreateItem = in_array($model->status, [
    TrnKartuProsesDyeing::STATUS_DELIVERED,
]);

$canDeleteItem = in_array($model->status, [
    TrnKartuProsesDyeing::STATUS_DELIVERED,
    TrnKartuProsesDyeing::STATUS_APPROVED,
]);

// ===== DATA PROVIDERS =====
$dataProviderTubeKiri = new ActiveDataProvider([
    'query' => $model->getTrnKartuProsesDyeingItems()
        ->where(['tube' => TrnKartuProsesDyeingItem::TUBE_KIRI]),
    'pagination' => false,
    'sort' => false
]);

$dataProviderTubeKanan = new ActiveDataProvider([
    'query' => $model->getTrnKartuProsesDyeingItems()
        ->where(['tube' => TrnKartuProsesDyeingItem::TUBE_KANAN]),
    'pagination' => false,
    'sort' => false
]);
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">ITEMS</h3>
        <div class="box-tools pull-right">
            <?php if ($canCreateItem): ?>
            <?= 
                Html::a('<i class="glyphicon glyphicon-plus"></i>', 
                    ['/trn-kartu-proses-dyeing-item/add-create', 'processId' => $model->id],
                    [
                        'class' => 'btn btn-xs btn-success',
                        'title' => 'Tambah Roll',
                        'data-toggle' => "modal",
                        'data-target' => "#kartuProsesDyeingModal",
                        'data-title' => 'Tambah Roll Baru'
                    ]
                )
            ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <p>
            <strong>
                Greige: <?= Html::encode($greige->nama_kain) ?> -
                Per Batch: <?= Yii::$app->formatter->asDecimal($greigeGroup->qty_per_batch) ?>
                <?= $greigeGroup::unitOptions()[$greigeGroup->unit] ?>
            </strong>
        </p>

        <div class="row">
            <!-- ==================== TUBE KIRI ==================== -->
            <div class="col-md-6">
                <?= GridView::widget([
                    'dataProvider' => $dataProviderTubeKiri,
                    'id' => 'KartuProsesDyeingItemsGridTubeKiri',
                    'pjax' => true,
                    'responsiveWrap' => false,
                    'resizableColumns' => false,
                    'showPageSummary' => true,
                    'toolbar' => false,
                    'panel' => [
                        'heading' => 'Tube Kiri',
                        'type' => GridView::TYPE_DEFAULT,
                        'before' => false,
                        'after' => false,
                        'footer' => false
                    ],
                    'columns' => [
                        ['class' => 'kartik\grid\SerialColumn'],
                        'date:date',
                        [
                            'attribute' => 'panjang_m',
                            'label' => 'Qty',
                            'format' => ['decimal', 2],
                            'hAlign' => 'right',
                            'pageSummary' => true,
                            'pageSummaryFunc' => GridView::F_SUM,
                            'content' => function ($data) {
                                return Html::a(
                                    Yii::$app->formatter->asDecimal($data->panjang_m, 2),
                                    ['/trn-kartu-proses-dyeing-item/edit-qty', 'id' => $data->id],
                                    [
                                        'data-toggle' => "modal",
                                        'data-target' => "#kartuProsesDyeingModal",
                                        'data-title' => 'Edit Qty Roll ID: ' . $data->id,
                                        'title' => 'Klik untuk edit Qty',
                                    ]
                                );
                            },
                        ],
                        [
                            'attribute' => 'mesin',
                            'label' => 'Mesin',
                            'content' => function ($data) {
                                return Html::a(
                                    $data->mesin,
                                    ['/trn-kartu-proses-dyeing-item/edit-mesin', 'id' => $data->id],
                                    [
                                        'data-toggle' => "modal",
                                        'data-target' => "#kartuProsesDyeingModal",
                                        'data-title' => 'Edit Mesin Roll ID: ' . $data->id,
                                        'title' => 'Klik untuk edit Mesin',
                                    ]
                                );
                            }
                        ],
                        [
                            'label' => 'Unit',
                            'value' => function ($data) {
                                return $data->stock->greige->group->unitName ?? '';
                            },
                        ],
                        [
                            'label' => 'Grade',
                            'value' => function ($data) {
                                return isset($data->stock->grade)
                                    ? TrnStockGreige::gradeOptions()[$data->stock->grade]
                                    : '';
                            },
                        ],
                        [
                            'attribute' => 'tube',
                            'value' => function ($data) {
                                return $data::tubeOptions()[$data->tube];
                            },
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'template' => '{delete-item}',
                            'buttons' => [
                                'delete-item' => function ($url, $itemModel) use ($canDeleteItem) {
                                    if ($canDeleteItem) {
                                        return Html::a(
                                            '<i class="glyphicon glyphicon-minus"></i>',
                                            ['/trn-kartu-proses-dyeing-item/delete-item', 'id' => $itemModel->id],
                                            [
                                                'title' => 'Hapus Roll',
                                                'class' => 'btn btn-xs btn-danger',
                                                'data-toggle' => "modal",
                                                'data-target' => "#kartuProsesDyeingModal",
                                                'data-title' => 'Hapus Roll ID: ' . $itemModel->id,
                                            ]
                                        );
                                    }
                                    return '';
                                },
                            ],
                        ],
                    ],
                ]) ?>
            </div>

            <!-- ==================== TUBE KANAN ==================== -->
            <div class="col-md-6">
                <?= GridView::widget([
                    'dataProvider' => $dataProviderTubeKanan,
                    'id' => 'KartuProsesDyeingItemsGridTubeKanan',
                    'pjax' => true,
                    'responsiveWrap' => false,
                    'resizableColumns' => false,
                    'showPageSummary' => true,
                    'toolbar' => false,
                    'panel' => [
                        'heading' => 'Tube Kanan',
                        'type' => GridView::TYPE_DEFAULT,
                        'before' => false,
                        'after' => false,
                        'footer' => false
                    ],
                    'columns' => [
                        ['class' => 'kartik\grid\SerialColumn'],
                        'date:date',
                        [
                            'attribute' => 'panjang_m',
                            'label' => 'Qty',
                            'format' => ['decimal', 2],
                            'hAlign' => 'right',
                            'pageSummary' => true,
                            'pageSummaryFunc' => GridView::F_SUM,
                            'content' => function ($data) {
                                return Html::a(
                                    Yii::$app->formatter->asDecimal($data->panjang_m, 2),
                                    ['/trn-kartu-proses-dyeing-item/edit-qty', 'id' => $data->id],
                                    [
                                        'data-toggle' => "modal",
                                        'data-target' => "#kartuProsesDyeingModal",
                                        'data-title' => 'Edit Qty Roll ID: ' . $data->id,
                                        'title' => 'Klik untuk edit Qty',
                                    ]
                                );
                            },
                        ],
                        [
                            'attribute' => 'mesin',
                            'label' => 'Mesin',
                            'content' => function ($data) {
                                return Html::a(
                                    $data->mesin,
                                    ['/trn-kartu-proses-dyeing-item/edit-mesin', 'id' => $data->id],
                                    [
                                        'data-toggle' => "modal",
                                        'data-target' => "#kartuProsesDyeingModal",
                                        'data-title' => 'Edit Mesin Roll ID: ' . $data->id,
                                        'title' => 'Klik untuk edit Mesin',
                                    ]
                                );
                            }
                        ],
                        [
                            'label' => 'Unit',
                            'value' => function ($data) {
                                return $data->stock->greige->group->unitName ?? '';
                            },
                        ],
                        [
                            'label' => 'Grade',
                            'value' => function ($data) {
                                return isset($data->stock->grade)
                                    ? TrnStockGreige::gradeOptions()[$data->stock->grade]
                                    : '';
                            },
                        ],
                        [
                            'attribute' => 'tube',
                            'value' => function ($data) {
                                return $data::tubeOptions()[$data->tube];
                            },
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'template' => '{delete-item}',
                            'buttons' => [
                                'delete-item' => function ($url, $itemModel) use ($canDeleteItem) {
                                    if ($canDeleteItem) {
                                        return Html::a(
                                            '<i class="glyphicon glyphicon-minus"></i>',
                                            ['/trn-kartu-proses-dyeing-item/delete-item', 'id' => $itemModel->id],
                                            [
                                                'title' => 'Hapus Roll',
                                                'class' => 'btn btn-xs btn-danger',
                                                'data-toggle' => "modal",
                                                'data-target' => "#kartuProsesDyeingModal",
                                                'data-title' => 'Hapus Roll ID: ' . $itemModel->id,
                                            ]
                                        );
                                    }
                                    return '';
                                },
                            ],
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>