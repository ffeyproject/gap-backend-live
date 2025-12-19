<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesPfpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Laporan Persiapan PFP';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="kartu-proses-pfp-index">

    <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'responsiveWrap' => false,
    'showPageSummary' => true,

    'panel' => [
        'type' => 'default',
        'before' => Html::tag(
            'div',
            Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['persiapan-pfp'], ['class'=>'btn btn-default']),
            ['class'=>'btn-group']
        ),
    ],

    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],

        /** TANGGAL */
        [
            'attribute' => 'dateRange',
            'label' => 'Tanggal',
            'value' => function ($model) {
                return $model->date;
            },
            'format' => 'date',
            'filterType' => GridView::FILTER_DATE_RANGE,
            'filterWidgetOptions' => [
                'convertFormat'=>true,
                'pluginOptions'=>[
                    'locale'=>[
                        'format'=>'Y-m-d',
                        'separator'=>' to ',
                    ]
                ]
            ],
        ],

        /** ORDER PFP */
        [
            'attribute' => 'orderPfpNo',
            'label' => 'No Order PFP',
            'value' => 'orderPfp.no'
        ],

        [
            'label' => 'Motif',
            'value' => function ($model) {
                /* @var $model \common\models\ar\TrnKartuProsesPfp */

                $lusi  = $model->lusi ?? '';
                $motif = $model->greige->nama_kain ?? '';
                $pakan = $model->pakan ?? '';

                return trim($lusi . ' ' . $motif . ' ' . $pakan);
            }
        ],

        /** NOMOR KARTU */
        'nomor_kartu',

        [
            'label' => 'Panjang',
            'value' => function ($data) {
                /* @var $data \common\models\ar\TrnKartuProsesPfp */
                $panjangTotal = $data->getTrnKartuProsesPfpItems()->sum('panjang_m');
                return $panjangTotal === null ? 0 : $panjangTotal;
            },
            'format' => 'decimal',
            'pageSummary' => true,
        ],
        [
            'label' => 'Berat',
            'value' => function ($data) {
                return $data->berat === null ? 0 : (float)$data->berat;
            },
            'format' => ['decimal', 2],
        ],
        [
            'label' => 'Gul',
            'value' => function ($data) {
                /* @var $data \common\models\ar\TrnKartuProsesPfp */
                $jumlahRoll = $data->getTrnKartuProsesPfpItems()->count('id');
                return $jumlahRoll === null ? 0 : $jumlahRoll;
            },
            'format' => 'decimal',
            'pageSummary' => true,
        ],
        [
            'label' => 'Shift',
            'value' => function ($data) {
                /* @var $data \common\models\ar\TrnKartuProsesPfp */
                $model = $data->getKartuProcessPfpProcesses()
                    ->where(['process_id' => 1])
                    ->one();

                if ($model !== null) {
                    try {
                        $value = \yii\helpers\Json::decode($model->value);
                        return $value['shift_group'] ?? '-';
                    } catch (\Throwable $t) {
                        return '-';
                    }
                }

                return '-';
            }
        ],
        [
            'label' => 'MC',
            'value' => function ($data) {
                /* @var $data \common\models\ar\TrnKartuProsesPfp */
                $model = $data->getKartuProcessPfpProcesses()
                    ->where(['process_id' => 1])
                    ->one();

                if ($model !== null) {
                    try {
                        $value = \yii\helpers\Json::decode($model->value);
                        return $value['no_mesin'] ?? '-';
                    } catch (\Throwable $t) {
                        return '-';
                    }
                }

                return '-';
            }
        ],
    ],
]); ?>

</div>