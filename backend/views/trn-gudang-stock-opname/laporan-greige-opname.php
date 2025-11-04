<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;

/**
 * Laporan Rekap Greige Stock Opname
 * - Rekap per tanggal dan motif
 * - Fitur pencarian tanggal dan nama motif
 */

$this->title = 'Laporan Rekap Stock Opname Greige';
$this->params['breadcrumbs'][] = 'Gudang Greige > ' . $this->title;

// --- Ambil parameter pencarian ---
$request = Yii::$app->request;
$tanggalRange = $request->get('tanggalRange');
$namaMotif = $request->get('namaMotif');

// --- Query utama ---
$query = (new \yii\db\Query())
    ->select([
        'trn_stock_greige_opname.date',
        'mst_greige.nama_kain AS nama_kain',
        'SUM(trn_stock_greige_opname.panjang_m) AS total_panjang',
        'SUM(CASE WHEN trn_stock_greige_opname.status = 2 THEN trn_stock_greige_opname.panjang_m ELSE 0 END) AS total_valid'
    ])
    ->from('trn_stock_greige_opname')
    ->leftJoin('mst_greige', 'mst_greige.id = trn_stock_greige_opname.greige_id');

// --- Filter berdasarkan input ---
if (!empty($tanggalRange)) {
    $range = explode(' to ', $tanggalRange);
    if (count($range) === 2) {
        $query->andWhere(['between', 'trn_stock_greige_opname.date', trim($range[0]), trim($range[1])]);
    }
}
if (!empty($namaMotif)) {
    $query->andWhere(['like', 'mst_greige.nama_kain', $namaMotif]);
}

$query->groupBy(['trn_stock_greige_opname.date', 'mst_greige.nama_kain'])
      ->orderBy(['trn_stock_greige_opname.date' => SORT_ASC]);

$data = $query->all();

// --- Data provider ---
$dataProvider = new \yii\data\ArrayDataProvider([
    'allModels' => $data,
    'pagination' => ['pageSize' => 50],
]);
?>

<div class="laporan-greige-opname-rekap container-fluid">

    <div class="row">
        <div class="col-md-12">
            <h2 class="text-primary">
                <i class="glyphicon glyphicon-list-alt"></i> <?= Html::encode($this->title) ?>
            </h2>
            <hr style="margin-top: 10px; margin-bottom: 20px;">
        </div>
    </div>

    <!-- 🔍 Form Pencarian -->
    <div class="panel panel-info" style="border-radius: 8px;">
        <div class="panel-heading" style="font-weight: bold;">
            <i class="glyphicon glyphicon-search"></i> Filter Laporan
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['laporan-greige-opname'],
                'options' => ['data-pjax' => 1],
            ]); ?>

            <div class="row">
                <div class="col-md-4">
                    <?= Html::label('Tanggal Opname', 'tanggalRange', ['class' => 'control-label']) ?>
                    <?= DateRangePicker::widget([
                        'name' => 'tanggalRange',
                        'value' => $tanggalRange,
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'locale' => ['format' => 'Y-m-d', 'separator' => ' to '],
                            'autoclose' => true,
                        ],
                        'options' => ['placeholder' => 'Pilih rentang tanggal...', 'class' => 'form-control'],
                    ]) ?>
                </div>

                <div class="col-md-4">
                    <?= Html::label('Nama Motif / Kain', 'namaMotif', ['class' => 'control-label']) ?>
                    <?= Html::textInput('namaMotif', $namaMotif, [
                        'class' => 'form-control',
                        'placeholder' => 'Masukkan nama motif...',
                    ]) ?>
                </div>

                <div class="col-md-4" style="margin-top:25px;">
                    <?= Html::submitButton('<i class="glyphicon glyphicon-search"></i> Cari', ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('<i class="glyphicon glyphicon-refresh"></i> Reset', ['laporan-greige-opname'], ['class' => 'btn btn-default']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?php Pjax::begin(['id' => 'laporanRekapPjax']); ?>

    <?php
        if (!empty($tanggalRange)) {
            $range = explode(' to ', $tanggalRange);
            $start = isset($range[0]) ? Yii::$app->formatter->asDate(trim($range[0]), 'php:d M Y') : '';
            $end = isset($range[1]) ? Yii::$app->formatter->asDate(trim($range[1]), 'php:d M Y') : '';
            echo Html::tag('div',
                "<strong>Hasil Opname dari tanggal {$start}" . (!empty($end) ? " sampai {$end}" : '') . "</strong>",
                ['class' => 'alert alert-warning text-center', 'style' => 'margin-bottom:15px; font-size:16px;']
            );
        }
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'responsiveWrap' => false,
        'hover' => true,
        'condensed' => true,
        'pjax' => true,
        'showPageSummary' => true,
        'bordered' => true,
        'striped' => true,
        'panel' => [
            'type' => 'primary',
            'heading' => false,
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn', 'header' => '#'],

            [
                'attribute' => 'date',
                'label' => 'Tanggal',
                'format' => ['date', 'php:Y-m-d'],
                'hAlign' => 'center',
                'width' => '150px',
            ],
            [
                'attribute' => 'nama_kain',
                'label' => 'Motif / Nama Kain',
                'hAlign' => 'left',
            ],
            [
                'attribute' => 'total_panjang',
                'label' => 'Total',
                'format' => ['decimal', 2],
                'hAlign' => 'right',
                'width' => '120px',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM,
            ],
            [
                'attribute' => 'total_valid',
                'label' => 'Valid',
                'format' => ['decimal', 2],
                'hAlign' => 'right',
                'width' => '120px',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM,
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>

<style>
.laporan-greige-opname-rekap-index {
    background: #f8faff;
    padding: 20px 25px;
    border-radius: 10px;
}

.panel-info {
    border-color: #bce8f1;
    margin-bottom: 25px;
}

.panel-heading {
    background-color: #d9edf7 !important;
    color: #31708f !important;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}

h2.text-primary {
    font-weight: 600;
}

.btn {
    border-radius: 6px;
}
</style>