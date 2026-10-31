<?php

use backend\components\ajax_modal\AjaxModal;
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inspect Kartu Proses Dyeing';
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

$formatter = Yii::$app->formatter;

$this->registerCss(<<<CSS
.kpd-inspect { --kpd-border: #e3e7ec; --kpd-muted: #7a8694; }
.kpd-inspect .kpd-hero { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
.kpd-inspect .kpd-hero h2 { margin: 0; font-size: 22px; font-weight: 700; color: #2c3e50; }
.kpd-inspect .kpd-hero p { margin: 4px 0 0; color: var(--kpd-muted); }

.kpd-inspect .panel { border-radius: 8px; border-color: var(--kpd-border); box-shadow: none; overflow: hidden; }
.kpd-inspect .panel-heading { background: #fff !important; border-bottom: 1px solid var(--kpd-border); }
.kpd-inspect .kv-panel-before { background: #fafbfc; border-bottom: 1px solid var(--kpd-border); }
.kpd-inspect .kv-grid-table thead th { background: #f7f9fb; color: #55606c; font-size: 12px; text-transform: uppercase; letter-spacing: .3px; border-bottom-width: 1px !important; white-space: nowrap; }
.kpd-inspect .kv-grid-table tbody td { vertical-align: middle !important; }
.kpd-inspect .kv-grid-table tbody tr { transition: background-color .12s ease; }
.kpd-inspect .kv-grid-table tbody tr:hover { background: #f4f8fb; }

.kpd-inspect .kpd-card-no { font-weight: 700; font-size: 14px; color: #2c3e50; }
.kpd-inspect .kpd-sub { display: block; font-size: 11px; color: var(--kpd-muted); }
.kpd-inspect .kpd-roll { font-weight: 700; }
.kpd-inspect .kpd-status { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; white-space: nowrap; }
.kpd-inspect .kpd-status-approved { background: #fff4e0; color: #c87f0a; }
.kpd-inspect .kpd-status-inspected { background: #e6f6ee; color: #008d4c; }
.kpd-inspect .kpd-actions { white-space: nowrap; }
.kpd-inspect .kpd-actions .btn { border-radius: 6px; margin: 1px; }
CSS
);
?>
<div class="kpd-inspect">

    <div class="kpd-hero">
        <div>
            <h2><span class="glyphicon glyphicon-search"></span> <?= Html::encode($this->title) ?></h2>
            <p>Kartu proses yang sudah masuk verpacking dan siap diinspect.</p>
        </div>
        <?= Html::a('<span class="glyphicon glyphicon-refresh"></span> Muat Ulang', ['kartu-proses-dyeing'], ['class' => 'btn btn-default']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'heading' => '<strong>Daftar Kartu Proses</strong>',
        ],
        'toolbar' => false,
        'striped' => false,
        'hover' => true,
        'condensed' => true,
        'responsiveWrap' => false,
        'pjax' => false,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'attribute' => 'nomor_kartu',
                'label' => 'Kartu Proses',
                'format' => 'raw',
                'value' => function ($data) {
                    /* @var $data TrnKartuProsesDyeing */
                    return '<span class="kpd-card-no">' . Html::encode($data->nomor_kartu) . '</span>'
                        . '<span class="kpd-sub">' . Html::encode($data->no) . '</span>';
                },
            ],
            [
                'attribute' => 'woNo',
                'label' => 'Nomor WO',
                'format' => 'raw',
                'value' => function ($data) {
                    /* @var $data TrnKartuProsesDyeing */
                    return '<strong>' . Html::encode($data->wo->no ?? '-') . '</strong>'
                        . '<span class="kpd-sub">' . Html::encode($data->wo->greige->nama_kain ?? '') . '</span>';
                },
            ],
            [
                'attribute' => 'warna',
                'label' => 'Warna',
                'value' => function ($data) {
                    /* @var $data TrnKartuProsesDyeing */
                    return $data->woColor->moColor->color ?? '-';
                },
            ],
            [
                'attribute' => 'asal_greige',
                'value' => function ($data) {
                    /* @var $data TrnKartuProsesDyeing */
                    return TrnStockGreige::asalGreigeOptions()[$data->asal_greige] ?? '-';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::asalGreigeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            'dikerjakan_oleh',
            'lusi',
            'pakan',
            [
                'label' => 'Roll / Qty',
                'format' => 'raw',
                'hAlign' => 'right',
                'value' => function ($data) use ($formatter) {
                    /* @var $data TrnKartuProsesDyeing */
                    $qty = 0;
                    foreach ($data->trnKartuProsesDyeingItems as $item) {
                        $qty += (float)($item->panjang_m ?? ($item->stock->panjang_m ?? 0));
                    }
                    return '<span class="kpd-roll">' . count($data->trnKartuProsesDyeingItems) . ' roll</span>'
                        . '<span class="kpd-sub">' . $formatter->asDecimal($qty) . '</span>';
                },
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'hAlign' => 'center',
                'value' => function ($data) {
                    /* @var $data TrnKartuProsesDyeing */
                    $class = $data->status == TrnKartuProsesDyeing::STATUS_INSPECTED ? 'kpd-status-inspected' : 'kpd-status-approved';
                    return '<span class="kpd-status ' . $class . '">' . Html::encode($data::statusOptions()[$data->status] ?? '-') . '</span>';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => [
                        TrnKartuProsesDyeing::STATUS_APPROVED => TrnKartuProsesDyeing::statusOptions()[TrnKartuProsesDyeing::STATUS_APPROVED],
                        TrnKartuProsesDyeing::STATUS_INSPECTED => TrnKartuProsesDyeing::statusOptions()[TrnKartuProsesDyeing::STATUS_INSPECTED],
                    ],
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => ['allowClear' => true],
                ],
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
                        ]
                    ]
                ],
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'header' => 'Aksi',
                'template' => '{view} {inspect} {reject}',
                'contentOptions' => ['class' => 'kpd-actions'],
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/trn-kartu-proses-dyeing/view', 'id' => $model->id], [
                            'class' => 'btn btn-default btn-xs',
                            'title' => 'Detail Kartu Proses',
                            'target' => '_blank',
                        ]);
                    },
                    'inspect' => function ($url, $model, $key) {
                        /* @var $model TrnKartuProsesDyeing */
                        return Html::a('<span class="glyphicon glyphicon-plus"></span> Inspect', ['create', 'process' => \common\models\ar\TrnScGreige::PROCESS_DYEING, 'processId' => $model->id], [
                            'class' => 'btn btn-primary btn-xs',
                            'title' => 'Buat Inspecting',
                        ]);
                    },
                    'reject' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-share-alt"></span> Kembalikan', ['/inspecting-dyeing-reject/create', 'kartu_proses_id' => $key], [
                            'class' => 'btn btn-danger btn-xs',
                            'title' => 'Kembalikan Kartu Proses Dyeing',
                            'data-toggle' => 'modal',
                            'data-target' => '#trnInspectingModal',
                            'data-title' => 'Kembalikan Kartu Proses Dyeing',
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>
</div>

<?php
echo AjaxModal::widget([
    'id' => 'trnInspectingModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);
