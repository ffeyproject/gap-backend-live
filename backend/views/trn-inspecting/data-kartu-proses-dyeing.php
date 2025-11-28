<?php
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Data Kartu Proses Dyeing Inspecting';
$this->params['breadcrumbs'][] = $this->title;

/* === Ambil daftar tahun untuk dropdown modal === */
$tahunList = ArrayHelper::map(
    TrnKartuProsesDyeing::find()
        ->select(['EXTRACT(YEAR FROM "date") AS tahun'])
        ->groupBy('tahun')
        ->orderBy('tahun DESC')
        ->asArray()
        ->all(),
    'tahun',
    'tahun'
);
?>

<div class="data-kartu-proses-dyeing-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'panel' => [
            'type' => 'primary',
            'heading' => '<strong>Data Kartu Proses Dyeing Inspecting</strong>',
            'before' => 
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', 
                    ['data-kartu-proses-dyeing'], 
                    ['class' => 'btn btn-default']
                ) 
                . ' ' .
                Html::button(
                    'Terima Gudang Jadi Semua',
                    [
                        'class' => 'btn btn-success',
                        'data-toggle' => 'modal',
                        'data-target' => '#modalPilihTahun'
                    ]
                ),
        ],
        'toolbar'=>[
            '{export}',
            '{toggleData}'
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view} {history}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            ['trn-inspecting/view-kartu', 'id' => $model->id],
                            ['title' => 'Lihat Kartu Proses Dyeing']
                        );
                    },
                    'history' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-time"></span>',
                            ['trn-inspecting/history-kartu', 'id' => $model->id],
                            ['title' => 'Lihat Riwayat Kartu']
                        );
                    },
                ],
            ],

            'id',
            [
                'attribute'=>'woNo',
                'label'=>'Nomor WO',
                'value'=>'wo.no'
            ],
            'no',
            [
                'attribute'=>'asal_greige',
                'value'=>function($data){
                    return TrnStockGreige::asalGreigeOptions()[$data->asal_greige];
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
                'label'=>'Warna',
                'value'=>'woColor.moColor.color'
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($data){
                    $label = TrnKartuProsesDyeing::statusOptionsFiltered()[$data->status] ?? '-';
                    $color = TrnKartuProsesDyeing::statusColor($data->status);

                    if ($color === 'purple') {
                        return "<span class='badge badge-purple'>{$label}</span>";
                    }
                    return "<span class='badge badge-{$color}'>{$label}</span>";
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnKartuProsesDyeing::statusOptionsFiltered(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            [
                'attribute'=>'dateRange',
                'label'=>'Tanggal',
                'value'=>'date',
                'format'=>'date',
                'filterType'=>GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions'=>[
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        'locale'=>[
                            'format'=>'Y-m-d',
                            'separator'=>' to ',
                        ]
                    ]
                ]
            ],
            [
                'label'=>'Total Panjang',
                'value'=>function($data){
                    $total = 0;
                    foreach ($data->trnKartuProsesDyeingItems as $item) {
                        $total += $item->stock->panjang_m;
                    }
                    return $total;
                },
                'format'=>'decimal'
            ],
        ],
    ]); ?>

</div>

<!-- ========================= -->
<!--       MODAL TAHUN         -->
<!-- ========================= -->

<div class="modal fade" id="modalPilihTahun" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Pilih Tahun Proses</h4>
            </div>

            <div class="modal-body">
                <label>Pilih Tahun:</label>
                <select id="tahunPilihan" class="form-control">
                    <option value="">-- Pilih Tahun --</option>
                    <?php foreach($tahunList as $t): ?>
                    <option value="<?= $t ?>"><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" id="btnProsesGudangJadi" class="btn btn-success">Proses</button>
            </div>

        </div>
    </div>
</div>

<?php
$this->registerJs("
    $('#btnProsesGudangJadi').on('click', function() {

        var tahun = $('#tahunPilihan').val();

        if (!tahun) {
            alert('Silakan pilih tahun terlebih dahulu');
            return;
        }

        // redirect dengan parameter tahun
        window.location.href = '" . Url::to(['trn-inspecting/set-status-gudang-jadi-all']) . "?tahun=' + tahun;
    });
");

$this->registerCss("
.badge-success { background-color:#28a745; color:white; }
.badge-danger { background-color:#dc3545; color:white; }
.badge-warning { background-color:#ffc107; color:black; }
.badge-info { background-color:#17a2b8; color:white; }
.badge-purple { background-color:#6f42c1; color:white; }
");
?>