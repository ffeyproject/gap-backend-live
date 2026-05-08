<?php
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rekap Processing Dyeing';
$this->params['breadcrumbs'][] = $this->title;

$currentYear = date('Y');
$woMonths = Yii::$app->db->createCommand("
    SELECT DISTINCT TO_CHAR(date, 'MM') AS m
    FROM trn_wo
    WHERE TO_CHAR(date, 'YYYY') = :year AND date IS NOT NULL
    ORDER BY m ASC
", [':year' => $currentYear])->queryColumn();

$indonesianMonths = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

$monthOptions = [];
foreach ($woMonths as $m) {
    if (isset($indonesianMonths[$m])) {
        $monthOptions[$m] = $indonesianMonths[$m];
    }
}

if (empty($monthOptions)) {
    $monthOptions = $indonesianMonths;
}

$queryParams = Yii::$app->request->queryParams;

$queryParamsOnProcess = array_merge(['rekap'], $queryParams, ['status_rekap' => 'on_process']);
unset($queryParamsOnProcess['page']);

$queryParamsSelesai = array_merge(['rekap'], $queryParams, ['status_rekap' => 'selesai']);
unset($queryParamsSelesai['page']);

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'id',
        'group' => true,
        'subGroupOf' => 3,
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'dateRange',
        'label' => 'Tanggal',
        'value' => 'date',
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
        'group' => true,
        'subGroupOf' => 3,
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label'=>'Buyer',
        'value'=>function($data){
            return $data->sc->customerName;
        },
        'group' => true,
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'woNo',
        'label'=>'No. WO',
        'value'=>function($data){
            return $data->wo->no;
        },
        'group' => true,
        'subGroupOf' => 3,
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label'=>'Motif',
        'value'=>function($data){
            return $data->wo->greigeNamaKain;
        },
        'group' => true,
        'subGroupOf' => 3,
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label'=>'Tgl. Kirim',
        'value'=>function($data){
            return $data->wo->tgl_kirim;
        },
        'format'=>'date',
        'group' => true,
        'subGroupOf' => 3,
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label'=>'Hand',
        'value'=>function($data){
            return $data->wo->handling->name;
        },
        'group' => true,
        'subGroupOf' => 3,
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label'=>'Note',
        'value'=>function($data){
            return $data->wo->note;
        },
        'format'=>'html',
        'group' => true,
        'subGroupOf' => 3,
    ],
    [
        'label'=>'T. Finish',
        'value'=>function($data){
            return Yii::$app->formatter->asDecimal($data->wo->colorQtyFinish) .'M / '. Yii::$app->formatter->asDecimal($data->wo->colorQtyFinishToYard).'Y';
        },
        'group' => true,
        'subGroupOf' => 3,
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label'=>'Warna',
        'value'=>function($data){
            return $data->woColor->moColor->color;
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'nomor_kartu',
        'label'=>'NK',
        'value'=>function($data){
            return $data->nomor_kartu;
        },
        'format'=>'html',
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label'=>'Panjang',
        'value'=>function($data){
            return $data->wo->colorQtyBatchToMeter;
        },
        'format'=>'decimal'
    ],
    [
        'label'=>'Panjang Greige',
        'value'=>function($data){
            return $data->getTrnKartuProsesDyeingItems()->sum('panjang_m');
        },
        'format'=>'decimal'
    ],
    [
        'label'=>'Greige',
        'value'=>function($data){
            return $data->wo->greigeNamaKain;
        },
    ],
    [
        'label'=>'Berat Greige',
        'value'=>function($data){
            return $data->berat;
        },
    ],
    [
        'label'=>'Pcs',
        'value'=>function($data){
            return $data->getTrnKartuProsesDyeingItems()->count();
        },
        'format'=>'decimal'
    ],
    [
        'label' => 'Terakhir Proses',
        'value' => function($data) {
            $lastProcess = (new \yii\db\Query())
                ->select(['m.nama_proses'])
                ->from('kartu_process_dyeing_process k')
                ->innerJoin('mst_process_dyeing m', 'k.process_id = m.id')
                ->where(['k.kartu_process_id' => $data->id])
                ->andWhere(['is not', 'k.value', null])
                ->andWhere(['<>', 'k.value', ''])
                ->orderBy(['m.order' => SORT_DESC])
                ->one();
            return $lastProcess !== false ? $lastProcess['nama_proses'] : '-';
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label'=>'Tgl. Buka / Shift',
        'value'=>function($data){
            $tg = '-'; $sh = '-';
            $pc = (new \yii\db\Query())->from(\common\models\ar\KartuProcessDyeingProcess::tableName())->where(['kartu_process_id'=>$data->id, 'process_id'=>1])->one();
            if($pc !== false){
                $v = \yii\helpers\Json::decode($pc['value']);
                if(isset($v['tanggal'])){ $tg = $v['tanggal']; }
                if(isset($v['shift_group'])){ $sh = $v['shift_group']; }
            }
            return $tg.' / '.$sh;
        },
    ],
    [
        'label'=>'Washing / Shift',
        'value'=>function($data){
            $tg = '-'; $sh = '-';
            $pc = (new \yii\db\Query())->from(\common\models\ar\KartuProcessDyeingProcess::tableName())->where(['kartu_process_id'=>$data->id, 'process_id'=>2])->one();
            if($pc !== false){
                $v = \yii\helpers\Json::decode($pc['value']);
                if(isset($v['tanggal'])){ $tg = $v['tanggal']; }
                if(isset($v['shift_group'])){ $sh = $v['shift_group']; }
            }
            return $tg.' / '.$sh;
        },
    ],
    [
        'label'=>'Relaxing / Shift',
        'value'=>function($data){
            $tg = '-'; $sh = '-';
            $pc = (new \yii\db\Query())->from(\common\models\ar\KartuProcessDyeingProcess::tableName())->where(['kartu_process_id'=>$data->id, 'process_id'=>3])->one();
            if($pc !== false){
                $v = \yii\helpers\Json::decode($pc['value']);
                if(isset($v['tanggal'])){ $tg = $v['tanggal']; }
                if(isset($v['shift_group'])){ $sh = $v['shift_group']; }
            }
            return $tg.' / '.$sh;
        },
    ],
    [
        'label'=>'Scutcher Relaxing / Shift',
        'value'=>function($data){
            $tg = '-'; $sh = '-';
            $pc = (new \yii\db\Query())->from(\common\models\ar\KartuProcessDyeingProcess::tableName())->where(['kartu_process_id'=>$data->id, 'process_id'=>4])->one();
            if($pc !== false){
                $v = \yii\helpers\Json::decode($pc['value']);
                if(isset($v['tanggal'])){ $tg = $v['tanggal']; }
                if(isset($v['shift_group'])){ $sh = $v['shift_group']; }
            }
            return $tg.' / '.$sh;
        },
    ],
    [
        'label'=>'Preset / Shift',
        'value'=>function($data){
            $tg = '-'; $sh = '-';
            $pc = (new \yii\db\Query())->from(\common\models\ar\KartuProcessDyeingProcess::tableName())->where(['kartu_process_id'=>$data->id, 'process_id'=>5])->one();
            if($pc !== false){
                $v = \yii\helpers\Json::decode($pc['value']);
                if(isset($v['tanggal'])){ $tg = $v['tanggal']; }
                if(isset($v['shift_group'])){ $sh = $v['shift_group']; }
            }
            return $tg.' / '.$sh;
        },
    ],
    [
        'label'=>'WR / Shift',
        'value'=>function($data){
            $tg = '-'; $sh = '-';
            $pc = (new \yii\db\Query())->from(\common\models\ar\KartuProcessDyeingProcess::tableName())->where(['kartu_process_id'=>$data->id, 'process_id'=>6])->one();
            if($pc !== false){
                $v = \yii\helpers\Json::decode($pc['value']);
                if(isset($v['tanggal'])){ $tg = $v['tanggal']; }
                if(isset($v['shift_group'])){ $sh = $v['shift_group']; }
            }
            return $tg.' / '.$sh;
        },
    ],
    [
        'label'=>'C WR / Shift',
        'value'=>function($data){
            $tg = '-'; $sh = '-';
            $pc = (new \yii\db\Query())->from(\common\models\ar\KartuProcessDyeingProcess::tableName())->where(['kartu_process_id'=>$data->id, 'process_id'=>7])->one();
            if($pc !== false){
                $v = \yii\helpers\Json::decode($pc['value']);
                if(isset($v['tanggal'])){ $tg = $v['tanggal']; }
                if(isset($v['shift_group'])){ $sh = $v['shift_group']; }
            }
            return $tg.' / '.$sh;
        },
    ],
    [
        'label'=>'DYEING / Shift',
        'value'=>function($data){
            $tg = '-'; $sh = '-';
            $pc = (new \yii\db\Query())->from(\common\models\ar\KartuProcessDyeingProcess::tableName())->where(['kartu_process_id'=>$data->id, 'process_id'=>8])->one();
            if($pc !== false){
                $v = \yii\helpers\Json::decode($pc['value']);
                if(isset($v['tanggal'])){ $tg = $v['tanggal']; }
                if(isset($v['shift_group'])){ $sh = $v['shift_group']; }
            }
            return $tg.' / '.$sh;
        },
    ],
    [
        'label'=>'SCUTCHER DYEING / Shift',
        'value'=>function($data){
            $tg = '-'; $sh = '-';
            $pc = (new \yii\db\Query())->from(\common\models\ar\KartuProcessDyeingProcess::tableName())->where(['kartu_process_id'=>$data->id, 'process_id'=>9])->one();
            if($pc !== false){
                $v = \yii\helpers\Json::decode($pc['value']);
                if(isset($v['tanggal'])){ $tg = $v['tanggal']; }
                if(isset($v['shift_group'])){ $sh = $v['shift_group']; }
            }
            return $tg.' / '.$sh;
        },
    ],
    [
        'label'=>'SETTING / Shift',
        'value'=>function($data){
            $tg = '-'; $sh = '-';
            $pc = (new \yii\db\Query())->from(\common\models\ar\KartuProcessDyeingProcess::tableName())->where(['kartu_process_id'=>$data->id, 'process_id'=>10])->one();
            if($pc !== false){
                $v = \yii\helpers\Json::decode($pc['value']);
                if(isset($v['tanggal'])){ $tg = $v['tanggal']; }
                if(isset($v['shift_group'])){ $sh = $v['shift_group']; }
            }
            return $tg.' / '.$sh;
        },
    ],
    [
        'label'=>'RESIN FINISH / Shift',
        'value'=>function($data){
            $tg = '-'; $sh = '-';
            $pc = (new \yii\db\Query())->from(\common\models\ar\KartuProcessDyeingProcess::tableName())->where(['kartu_process_id'=>$data->id, 'process_id'=>11])->one();
            if($pc !== false){
                $v = \yii\helpers\Json::decode($pc['value']);
                if(isset($v['tanggal'])){ $tg = $v['tanggal']; }
                if(isset($v['shift_group'])){ $sh = $v['shift_group']; }
            }
            return $tg.' / '.$sh;
        },
    ],
];

// Query and append additional processes (ID > 11) dynamically
$additionalProcesses = \common\models\ar\MstProcessDyeing::find()
    ->where(['>', 'id', 11])
    ->orderBy('order')
    ->all();

foreach ($additionalProcesses as $proc) {
    $gridColumns[] = [
        'label' => $proc->nama_proses . ' / Shift',
        'value' => function($data) use ($proc) {
            $tg = '-';
            $sh = '-';
            $pc = (new \yii\db\Query())
                ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                ->where(['kartu_process_id' => $data->id, 'process_id' => $proc->id])
                ->one();

            if ($pc !== false) {
                $v = \yii\helpers\Json::decode($pc['value']);
                if (isset($v['tanggal'])) {
                    $tg = $v['tanggal'];
                }
                if (isset($v['shift_group'])) {
                    $sh = $v['shift_group'];
                }
            }

            return $tg . ' / ' . $sh;
        }
    ];
}

// Append final columns (Panjang Jadi, Pack)
$gridColumns[] = [
    'label' => 'Panjang Jadi',
    'value' => function($data) {
        $r = 0;
        $pc = (new \yii\db\Query())
            ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
            ->where(['kartu_process_id' => $data->id, 'process_id' => 11])
            ->one();
        if ($pc !== false) {
            $v = \yii\helpers\Json::decode($pc['value']);
            if (isset($v['panjang_jadi'])) {
                $r = $v['panjang_jadi'];
            }
        }
        return $r;
    },
    'format' => 'decimal'
];

$gridColumns[] = [
    'label' => 'Pack',
    'value' => function($data) {
        $packDates = [];
        
        // Ambil riwayat dari ActionLogKartuDyeing untuk mencakup semua data historis di masa lalu
        $logs = \common\models\ar\ActionLogKartuDyeing::find()
            ->where(['kartu_proses_id' => $data->id, 'action_name' => 'masuk_verpacking'])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
            
        foreach ($logs as $index => $log) {
            $dateFormatted = Yii::$app->formatter->asDate($log->created_at);
            $packDates[] = 'Persetujuan Ke-' . ($index + 1) . ': ' . $dateFormatted;
        }
        
        // Jika karena suatu hal log kosong, gunakan kolom approved_history atau approved_at
        if (empty($packDates)) {
            if (!empty($data->approved_history)) {
                $history = \yii\helpers\Json::decode($data->approved_history);
                if (is_array($history)) {
                    foreach ($history as $index => $h) {
                        if (isset($h['time'])) {
                            $dateFormatted = Yii::$app->formatter->asDate($h['time']);
                            $packDates[] = 'Persetujuan Ke-' . ($index + 1) . ': ' . $dateFormatted;
                        }
                    }
                }
            }
        }
        
        if (empty($packDates) && !empty($data->approved_at)) {
            $packDates[] = 'Persetujuan Ke-1: ' . Yii::$app->formatter->asDate($data->approved_at);
        }
        
        return implode('<br>', $packDates);
    },
    'format' => 'raw'
];
?>
<div class="kartu-proses-dyeing-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['rekap'], ['class' => 'btn btn-default']) . ' ' .
                        Html::dropDownList(
                            'woMonthDropdown',
                            $searchModel->woMonth,
                            $monthOptions,
                            [
                                'prompt' => '-- Pilih Bulan WO (' . date('Y') . ') --',
                                'class' => 'form-control',
                                'style' => 'display: inline-block; width: auto; margin-left: 10px; vertical-align: middle;',
                                'onchange' => 'filterWoMonth(this);'
                            ]
                        ) . (!empty($searchModel->woMonth) ? ' ' . Html::a('<i class="glyphicon glyphicon-file"></i> Export Excel', ['export-excel', 'woMonth' => $searchModel->woMonth, 'status_rekap' => $statusRekap], ['class' => 'btn btn-success', 'style' => 'vertical-align: middle; margin-left: 10px;']) : '') . '<br><br>' .
                        \yii\bootstrap\Nav::widget([
                            'options' => ['class' => 'nav nav-tabs', 'style' => 'margin-top: 5px; border-bottom: 2px solid #ddd;'],
                            'items' => [
                                [
                                    'label' => 'On Process',
                                    'url' => $queryParamsOnProcess,
                                    'active' => ($statusRekap === 'on_process'),
                                ],
                                [
                                    'label' => 'Selesai',
                                    'url' => $queryParamsSelesai,
                                    'active' => ($statusRekap === 'selesai'),
                                ],
                            ],
                        ]),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar'=>[
            '{export}',
            //'{toggleData}'
        ],
        'showPageSummary'=>true,
        'bordered' => true,
        'striped' => false,
        'condensed' => true,
        'hover' => true,
        'rowOptions' => function($model, $key, $index, $grid) {
            $models = $grid->dataProvider->getModels();
            $nextModel = isset($models[$index + 1]) ? $models[$index + 1] : null;
            
            $currentBuyer = $model->sc ? $model->sc->customerName : null;
            $nextBuyer = ($nextModel && $nextModel->sc) ? $nextModel->sc->customerName : null;
            
            if ($currentBuyer !== $nextBuyer) {
                return ['class' => 'group-end-row'];
            }
            return [];
        },
        'columns' => $gridColumns,
    ]); ?>
</div>

<style>
/* CSS premium untuk merapikan garis kisi (gridlines) tabel rekap agar bersih, rapi, dan seimbang seperti Excel */

/* 1. Paksa semua border luar dan dalam tampil tegas, hilangkan efek hilangnya garis vertikal akibat grouping Kartik */
.kartu-proses-dyeing-index .table-bordered {
    border: 1.5px solid #666666 !important;
    border-collapse: collapse !important;
}

.kartu-proses-dyeing-index .table-bordered > thead > tr > th {
    border: 1px solid #777777 !important;
    background-color: #ecf0f5 !important; /* Abu-abu terang profesional */
    color: #222222 !important;
    font-weight: bold;
    text-align: center;
    vertical-align: middle !important;
}

/* Kotak pencarian filter di bagian atas tabel */
.kartu-proses-dyeing-index .table-bordered .filters td {
    border: 1px solid #888888 !important;
    background-color: #f9f9f9 !important;
}

/* 2. Paksa semua sel body memiliki garis tepi (top, bottom, left, right) yang lengkap, tegas, dan berwarna abu-abu solid */
.kartu-proses-dyeing-index .table-bordered > tbody > tr > td {
    border: 1px solid #b0b0b0 !important;
    background-color: #ffffff !important; /* Latar belakang putih bersih, hilangkan warna lavender/ungu yang tidak rapi */
    color: #333333 !important;
    padding: 8px !important;
}

/* 3. Garis pembatas yang tebal, solid, dan menyatu (continuous) saat berganti Buyer */
.kartu-proses-dyeing-index .table tbody tr.group-end-row td {
    border-bottom: 2.5px solid #222222 !important; /* Garis hitam tebal menyatu dari ujung kiri ke kanan */
}

/* 4. Efek hover baris yang lembut */
.kartu-proses-dyeing-index .table tbody tr:hover td {
    background-color: #f5f8fa !important;
}
</style>

<script>
function filterWoMonth(selectElement) {
    var val = selectElement.value;
    var searchKey = "TrnKartuProsesDyeingSearch[woMonth]";
    var url = window.location.href;
    var urlParts = url.split("?");
    var newUrl = urlParts[0];
    
    if (urlParts.length > 1) {
        var params = urlParts[1].split("&");
        var newParams = [];
        for (var i = 0; i < params.length; i++) {
            var p = params[i].split("=");
            var decodedKey = decodeURIComponent(p[0]);
            if (decodedKey !== searchKey && decodedKey !== "page" && decodedKey !== "_pjax") {
                newParams.push(params[i]);
            }
        }
        if (val) {
            newParams.push(encodeURIComponent(searchKey) + "=" + val);
        }
        if (newParams.length > 0) {
            newUrl += "?" + newParams.join("&");
        }
    } else {
        if (val) {
            newUrl += "?" + encodeURIComponent(searchKey) + "=" + val;
        }
    }
    
    window.location.href = newUrl;
}
</script>
