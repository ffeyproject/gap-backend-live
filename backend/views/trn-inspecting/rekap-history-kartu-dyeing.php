<?php
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = 'Rekap History Kartu Dyeing';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="rekap-history-kartu-dyeing-index">

    <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'panel' => [
        'type' => 'primary',
        'heading' => '<strong>Rekap History Kartu Dyeing</strong>',
    ],
    'toolbar' => [
        '{export}',
        '{toggleData}'
    ],
    'pjax' => true,
    'hover' => true,
    'striped' => true,
    'responsiveWrap' => false,

    /* --- PEMISAH GARIS PER WO --- */
    'rowOptions' => function ($model, $key, $index, $grid) {
        static $lastWO = null;
        $currentWO = $model->kartuProses->wo->no ?? '-';

        // Jika WO berbeda dari sebelumnya → kasih border-top
        if ($currentWO !== $lastWO) {
            $lastWO = $currentWO;
            return ['style' => 'border-top: 3px solid #000;'];
        }
        return [];
    },

    'columns' => [
        /* === NOMOR KARTU SAJA === */
        [
            'class' => 'kartik\grid\DataColumn',
            'header' => '#',
            'value' => function ($model, $key, $index, $widget) use (&$lastCard, &$counter) {
                static $lastCard = null;
                static $counter = 0;

                $currentCard = $model->kartuProses->no ?? '-';

                if ($currentCard !== $lastCard) {
                    $counter++;
                    $lastCard = $currentCard;
                    return $counter;
                }
                return '';
            },
            'group' => true,
            'subGroupOf' => 2,
            'hAlign' => 'center',
            'width' => '50px',
        ],

        // 🔹 Group utama per WO
        [
            'attribute' => 'woNo',
            'label' => 'Nomor WO',
            'value' => 'kartuProses.wo.no',
            'group' => true,
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => 'Cari WO...'],
            'contentOptions' => ['style' => 'font-weight:bold;']
        ],

        // 🔹 Sub-group berdasarkan warna
        [
            'attribute' => 'warna',
            'label' => 'Warna / Kombinasi',
            'value' => 'kartuProses.woColor.moColor.color',
            'group' => true,
            'subGroupOf' => 1,
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => 'Cari Warna...'],
            'contentOptions' => ['style' => 'font-style:italic;']
        ],

        // 🔹 Sub-group berdasarkan kartu dyeing
        [
            'attribute' => 'kartuNo',
            'label' => 'Kartu Dyeing',
            'value' => 'kartuProses.no',
            'group' => true,
            'subGroupOf' => 2,
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => 'Cari Kartu...'],
        ],

        // Kolom data detail log
        [
            'attribute' => 'created_at',
            'label' => 'Tanggal',
            'format' => ['datetime', 'php:d-m-Y H:i:s'],
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
            'attribute' => 'username',
            'label' => 'User',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => 'Cari User...'],
        ],
        [
            'attribute' => 'action_name',
            'label' => 'Aksi',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => 'Cari Aksi...'],
            'contentOptions' => ['style' => 'font-weight:bold;']
        ],
        [
            'attribute' => 'description',
            'label' => 'Keterangan',
            'format' => 'ntext',
            'filterInputOptions' => ['class' => 'form-control', 'placeholder' => 'Cari Keterangan...'],
            'contentOptions' => ['style' => 'max-width:400px; white-space:normal;']
        ],
    ],
]); ?>

</div>