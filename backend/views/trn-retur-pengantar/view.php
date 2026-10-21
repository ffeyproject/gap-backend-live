<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnReturPengantar;
use common\models\ar\TrnStockGreige;
use common\models\User;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnReturPengantar */

$this->title = $model->no ?: $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Surat Pengantar Retur', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$units = MstGreigeGroup::unitOptions();
$grades = TrnStockGreige::gradeOptions();
$items = $model->getTrnReturPengantarItems()->with(['gudangJadi.wo.greige', 'gudangJadi.returFrom', 'stockGreige'])->orderBy('id')->all();
$isProcessing = $model->bagian == TrnReturPengantar::BAGIAN_PROCESSING;
?>
<div class="trn-retur-pengantar-view">
    <p>
        <?php if ($model->status == $model::STATUS_DRAFT): ?>
            <?= Html::a('Ubah', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Hapus', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Hapus surat pengantar ini?', 'method' => 'post'],
            ]) ?>
            <?= Html::a('Posting', ['posting', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => $isProcessing
                        ? 'Posting surat pengantar ke Processing? Stok retur akan dipindah ke Gudang Mutasi dan bisa dipakai di Kartu Proses Mutasi.'
                        : 'Posting surat pengantar ke '.$model->bagianName.'? Stok retur akan keluar (Out) dari gudang jadi.',
                    'method' => 'post',
                ],
            ]) ?>
        <?php elseif ($isProcessing): ?>
            <?= Html::a('Buat Kartu Proses Dyeing', ['/trn-kartu-proses-mutasi/create-dyeing'], ['class' => 'btn btn-info']) ?>
            <?= Html::a('Buat Kartu Proses Printing', ['/trn-kartu-proses-mutasi/create-printing'], ['class' => 'btn btn-info']) ?>
        <?php endif; ?>
        <?= Html::a('Buat Baru', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'no',
                            'date:date',
                            'bagianName',
                            'penerima',
                            [
                                'attribute' => 'status',
                                'value' => $model::statusOptions()[$model->status],
                            ],
                            'note:ntext',
                        ],
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'created_at:datetime',
                            [
                                'attribute' => 'created_by',
                                'value' => ($u = User::findOne($model->created_by)) !== null ? $u->full_name : $model->created_by,
                            ],
                            'posted_at:datetime',
                            [
                                'attribute' => 'posted_by',
                                'value' => ($u = User::findOne($model->posted_by)) !== null ? $u->full_name : $model->posted_by,
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">ITEMS</h3>
        </div>
        <div class="box-body">
            <?= GridView::widget([
                'dataProvider' => new ActiveDataProvider(['query' => $model->getTrnReturPengantarItems()->with(['gudangJadi.wo', 'gudangJadi.returFrom', 'stockGreige'])->orderBy('id'), 'pagination' => false, 'sort' => false]),
                'responsiveWrap' => false,
                'showPageSummary' => true,
                'toolbar' => false,
                'panel' => false,
                'columns' => [
                    ['class' => 'kartik\grid\SerialColumn'],
                    [
                        'label' => 'Stok Retur',
                        'format' => 'raw',
                        'value' => function($data){
                            return Html::a('#'.$data->gudang_jadi_id, ['/trn-gudang-jadi/view', 'id' => $data->gudang_jadi_id], ['target' => '_blank']);
                        }
                    ],
                    'gudangJadi.source_ref:text:No Retur',
                    'gudangJadi.wo.no:text:WO',
                    [
                        'label' => 'Warna',
                        'value' => function($data){
                            return $data->gudangJadi->getColorResolved();
                        }
                    ],
                    [
                        'attribute' => 'qty',
                        'format' => 'decimal',
                        'pageSummary' => true,
                        'hAlign' => 'right',
                    ],
                    [
                        'label' => 'Unit',
                        'value' => function($data) use ($units){
                            return $units[$data->gudangJadi->unit] ?? '-';
                        }
                    ],
                    [
                        'label' => 'Grade',
                        'value' => function($data) use ($grades){
                            return $grades[$data->gudangJadi->grade] ?? '-';
                        }
                    ],
                    [
                        'label' => 'Roll Asal',
                        'format' => 'raw',
                        'value' => function($data){
                            $asal = $data->gudangJadi->returFrom;
                            return $asal !== null ? Html::encode('#'.$asal->id.($asal->qr_code ? ' | '.$asal->qr_code : '')) : '<span class="text-muted">Tidak diketahui</span>';
                        }
                    ],
                    [
                        'label' => 'Status Stok',
                        'value' => function($data){
                            return TrnGudangJadi::statusOptions()[$data->gudangJadi->status] ?? '-';
                        }
                    ],
                    [
                        'label' => 'Stok Gudang Mutasi',
                        'visible' => $isProcessing,
                        'format' => 'raw',
                        'value' => function($data){
                            if ($data->stockGreige === null) {
                                return '<span class="text-muted">-</span>';
                            }
                            // status berubah ke ON_PROCESS_CARD saat kartu proses diposting
                            $dipakai = $data->stockGreige->status == TrnStockGreige::STATUS_ON_PROCESS_CARD;
                            return '#'.$data->stock_greige_id.' ('.Yii::$app->formatter->asDecimal($data->stockGreige->panjang_m).' M) '
                                .($dipakai ? '<span class="label label-default">Sudah di kartu proses</span>' : '<span class="label label-success">Tersedia untuk kartu proses</span>');
                        }
                    ],
                ],
            ]) ?>
        </div>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">SURAT PENGANTAR</h3>
            <div class="box-tools pull-right">
                <input type="number" id="SizeText" min="1" max="99" step="1" value="11">
                <?= Html::button('<i class="fa fa-print" aria-hidden="true"></i>', ['class' => 'btn btn-default btn-xs', 'onclick' => 'printDiv()']) ?>
            </div>
        </div>
        <div class="box-body" id="GFG">
            <div style="text-align: center;">
                <h2>SURAT PENGANTAR BARANG RETUR</h2>
            </div>

            <table width="100%">
                <tr>
                    <td width="15%">Nomor</td><td width="2%">:</td><td><?= Html::encode($model->no ?: '(draft)') ?></td>
                </tr>
                <tr>
                    <td>Tanggal</td><td>:</td><td><?= Yii::$app->formatter->asDate($model->date) ?></td>
                </tr>
                <tr>
                    <td>Dari</td><td>:</td><td>Gudang Jadi</td>
                </tr>
                <tr>
                    <td>Kepada Bagian</td><td>:</td><td><?= Html::encode($model->bagianName) ?></td>
                </tr>
            </table>

            <p></p>

            <table width="100%" border="1">
                <thead>
                <tr>
                    <th class="text-center">NO</th>
                    <th class="text-center">NO RETUR</th>
                    <th class="text-center">WO</th>
                    <th class="text-center">NAMA BARANG</th>
                    <th class="text-center">WARNA</th>
                    <th class="text-center">GRADE</th>
                    <th class="text-center">QTY</th>
                </tr>
                </thead>
                <tbody>
                <?php $total = 0; foreach ($items as $i => $item): $gj = $item->gudangJadi; $total += $item->qty; ?>
                    <tr>
                        <td class="text-center"><?= $i + 1 ?></td>
                        <td><?= Html::encode($gj->source_ref) ?></td>
                        <td><?= Html::encode($gj->wo->no) ?></td>
                        <td><?= Html::encode($gj->wo->greige->nama_kain ?? '-') ?></td>
                        <td><?= Html::encode($gj->getColorResolved()) ?></td>
                        <td class="text-center"><?= $grades[$gj->grade] ?? '-' ?></td>
                        <td class="text-right"><?= Yii::$app->formatter->asDecimal($item->qty).' '.($units[$gj->unit] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="6" class="text-right">TOTAL (<?= count($items) ?> roll)</th>
                    <th class="text-right"><?= Yii::$app->formatter->asDecimal($total) ?></th>
                </tr>
                </tbody>
            </table>

            <p>Keterangan: <?= Html::encode($model->note) ?></p>

            <table width="100%">
                <tr>
                    <td style="text-align: center;" width="50%">
                        <p><strong>Yang Menyerahkan (Gudang Jadi)</strong></p><br><br><br>
                        <?= ($u = User::findOne($model->created_by)) !== null ? Html::encode($u->full_name) : '' ?>
                    </td>
                    <td style="text-align: center;" width="50%">
                        <p><strong>Yang Menerima (<?= Html::encode($model->bagianName) ?>)</strong></p><br><br><br>
                        <?= Html::encode($model->penerima) ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<?php
$js = <<<JS
function printDiv() {
    var fontSize = document.getElementById("SizeText").value;
    var divContents = document.getElementById("GFG").innerHTML;
    var a = window.open('', '');
    a.document.write('<html><head><style type="text/css">');
    a.document.write('body{font-size:' + fontSize + 'px;} table {font-size:' + fontSize + 'px; border-spacing: 0;} th, td {padding: 0.3em 0.6em;} .text-center{text-align:center} .text-right{text-align:right}');
    a.document.write('</style></head><body>');
    a.document.write(divContents);
    a.document.write('</body></html>');
    a.document.close();
    a.print();
}
JS;
$this->registerJs($js, \yii\web\View::POS_END);
?>
