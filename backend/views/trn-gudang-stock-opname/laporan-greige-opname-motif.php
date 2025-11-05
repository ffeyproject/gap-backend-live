<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;

/**
 * Laporan Rekap Greige Stock Opname - Per Motif
 */

$this->title = 'Laporan Rekap Stock Opname Greige per Motif';
$this->params['breadcrumbs'][] = 'Gudang Greige > ' . $this->title;

// --- Ambil parameter pencarian ---
$request = Yii::$app->request;
$tanggalRange = $request->get('tanggalRange');
$namaMotif = $request->get('namaMotif');

// --- Default tanggal jika belum ada input ---
if (empty($tanggalRange)) {
    $tanggalRange = '2025-11-03 to ' . date('Y-m-d');
}

// --- Query utama (group by nama kain) ---
$query = (new \yii\db\Query())
    ->select([
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

// --- Grouping & Sorting ---
$query->groupBy(['mst_greige.nama_kain'])
      ->orderBy(['mst_greige.nama_kain' => SORT_ASC]);

$data = $query->all();

// --- Data provider ---
$dataProvider = new \yii\data\ArrayDataProvider([
    'allModels' => $data,
    'pagination' => [
        'pageSize' => 50,
    ],
]);
?>

<div class="laporan-greige-opname-per-motif container-fluid">

    <div class="row">
        <div class="col-md-12">
            <h2 class="text-primary">
                <i class="glyphicon glyphicon-list-alt"></i> <?= Html::encode($this->title) ?>
            </h2>
            <hr style="margin-top: 10px; margin-bottom: 20px;">
        </div>
    </div>

    <!-- 🔍 Filter -->
    <div class="panel panel-info" style="border-radius: 8px;">
        <div class="panel-heading" style="font-weight: bold;">
            <i class="glyphicon glyphicon-search"></i> Filter Laporan
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['laporan-greige-opname-motif'],
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
                    <?= Html::a('<i class="glyphicon glyphicon-refresh"></i> Reset', ['laporan-greige-opname-motif'], ['class' => 'btn btn-default']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?php Pjax::begin(['id' => 'laporanMotifPjax']); ?>

    <?php
    // tampilkan info range tanggal
    if (!empty($tanggalRange)) {
        $range = explode(' to ', $tanggalRange);
        $start = Yii::$app->formatter->asDate(trim($range[0]), 'php:d M Y');
        $end = Yii::$app->formatter->asDate(trim($range[1]), 'php:d M Y');
        echo Html::tag('div',
            "<strong>Hasil Opname per Motif dari tanggal {$start} sampai {$end}</strong>",
            ['class' => 'alert alert-info text-center', 'style' => 'margin-bottom:15px; font-size:16px;']
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
    'attribute' => 'nama_kain',
    'label' => 'Motif / Nama Kain',
    'format' => 'raw',
    'value' => function($model) {
        return Html::a(
            Html::encode($model['nama_kain']),
            '#',
            [
                'class' => 'show-history',
                'data-nama' => $model['nama_kain']
            ]
        );
    },
],
            [
                'attribute' => 'total_panjang',
                'label' => 'Total',
                'format' => ['decimal', 2],
                'hAlign' => 'right',
                'width' => '150px',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM,
            ],
            [
                'attribute' => 'total_valid',
                'label' => 'Valid',
                'format' => ['decimal', 2],
                'hAlign' => 'right',
                'width' => '150px',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM,
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
    <!-- Modal History -->
    <div class="modal fade" id="modal-history" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">History Opname per Hari</h4>
                </div>
                <div class="modal-body" id="modal-history-content">
                    <div class="text-center text-muted">Memuat data...</div>
                </div>
            </div>
        </div>
    </div>

    <?php
$urlHistory = \yii\helpers\Url::to(['trn-gudang-stock-opname/history-motif']);
$script = <<<JS
$(document).on('click', '.show-history', function(e){
    e.preventDefault();
    var nama = $(this).data('nama');
    $('#modal-history').modal('show');
    $('#modal-history-content').html('<div class="text-center text-muted">Memuat data...</div>');
    $.get('$urlHistory', {nama: nama}, function(data){
        $('#modal-history-content').html(data);
    });
});
JS;
$this->registerJs($script);
?>

</div>