<?php
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rekap Dyeing By Process';
$this->params['breadcrumbs'][] = $this->title;

$totalPanjangByUnit = [];
$totalJumlahRoll = 0;
$totalBerat = 0;

$summaryData = [];
if (!empty($searchModel->dateRange)) {
    $summaryQuery = clone $dataProvider->query;
    $summaryQuery->limit(null)->offset(null)->orderBy(null);
    $summaryQuery->with(['kartuProcess.trnKartuProsesDyeingItems']);
    $summaryModels = $summaryQuery->all();
    
    foreach ($summaryModels as $processModel) {
        $processName = $processModel->process ? $processModel->process->nama_proses : 'Unknown';
        $kartuProcess = $processModel->kartuProcess;
        
        if (!isset($summaryData[$processName])) {
            $summaryData[$processName] = [
                'count' => 0,
                'panjang' => 0,
                'roll' => 0,
                'berat' => 0,
            ];
        }
        
        $summaryData[$processName]['count']++;
        if ($kartuProcess) {
            $items = $kartuProcess->trnKartuProsesDyeingItems;
            $panjang = 0;
            $rolls = count($items);
            foreach ($items as $item) {
                $panjang += $item->panjang_m;
            }
            $summaryData[$processName]['panjang'] += $panjang;
            $summaryData[$processName]['roll'] += $rolls;
            $summaryData[$processName]['berat'] += $kartuProcess->berat ?: 0;
        }
    }
    
    ksort($summaryData);
}
?>
<div class="kartu-proses-dyeing-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before' => Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['rekap-by-process'], ['class' => 'btn btn-default']) .
                Html::button('<i class="glyphicon glyphicon-stats"></i> Rekap Proses', [
                    'class' => 'btn btn-info',
                    'data-toggle' => 'modal',
                    'data-target' => '#summary-proses-modal',
                ]),
                ['class' => 'btn-group', 'role' => 'group']
            ),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar'=>[
            '{export}',
            '{toggleData}'
        ],
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $model->kartuProcess->id], [
                            'title' => Yii::t('app', 'view'),
                            'target' => '_blank'
                        ]);
                    },
                ],
            ],
            [
                'attribute'=>'woNo',
                'label'=>'Nomor WO',
                'value'=>'kartuProcess.wo.no'
            ],
            [   
                'attribute'=>'no_kartu',
                'label'=>'No Kartu',
                'value'=>'kartuProcess.no'
            ],
            [
                'attribute' => 'nama_proses',
                'label' => 'Nama Proses',
                'value' => 'process.nama_proses',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\ar\MstProcessDyeing::find()->all(), 'id', 'nama_proses'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Pilih Nama Proses'],
            ],
            [   
                'attribute'=>'warna',
                'label'=>'Warna',
                'value'=>'kartuProcess.woColor.moColor.color'
            ],
            [
                'attribute' => 'dateRange',
                'label' => 'TANGGAL',
                'value' => function ($model) {
                    $data = json_decode($model->value, true); // Decode JSON
                    $tanggal = $data['tanggal'] ?? null; // Ambil nilai no_mesin
                    return $tanggal;
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
            [   
                'attribute'=>'no_mesin',
                'label' => 'No Mesin',
                'value' => function ($model) {
                    $data = json_decode($model->value, true); // Decode JSON
                    $noMesin = $data['no_mesin'] ?? null; // Ambil nilai no_mesin
                    return $noMesin;
                },
            ],
            [   
                'attribute'=>'shift_group',
                'label' => 'Shift Group',
                'value' => function ($model) { 
                    $data = json_decode($model->value, true); // Decode JSON
                    $shiftGroup = $data['shift_group'] ?? null; // Ambil nilai shift_group
                    return $shiftGroup;
                },
            ],
            [   
                'attribute'=>'panjang',
                'label' => 'Panjang',
                'value' => function ($model) use (&$totalPanjangByUnit) { 
                    $kartuProcess = $model->kartuProcess;
                    $greige = $kartuProcess->wo->greige;
                    $greigeGroup = $greige->group;
                    $panjangTotal = $kartuProcess->getTrnKartuProsesDyeingItems()->sum('panjang_m');
                    $panjangTotal = $panjangTotal === null ? 0 : $panjangTotal;
                    
                    $unitName = $greigeGroup ? $greigeGroup->unitName : '';
                    if (!isset($totalPanjangByUnit[$unitName])) {
                        $totalPanjangByUnit[$unitName] = 0;
                    }
                    $totalPanjangByUnit[$unitName] += $panjangTotal;

                    return Yii::$app->formatter->asDecimal($panjangTotal) . ' ' . $unitName;
                },
                'pageSummary' => function () use (&$totalPanjangByUnit) {
                    $total = array_sum($totalPanjangByUnit);
                    return Yii::$app->formatter->asDecimal($total);
                },
                'hAlign' => 'right',
            ],
            [   
                'attribute'=>'jumlah_roll',
                'label' => 'Jml. Roll',
                'value' => function ($model) use (&$totalJumlahRoll) { 
                    $kartuProcess = $model->kartuProcess;
                    $jumlahRoll = $kartuProcess->getTrnKartuProsesDyeingItems()->count('id');
                    $jumlahRoll = $jumlahRoll === null ? 0 : $jumlahRoll;
                    $totalJumlahRoll += $jumlahRoll;
                    return $jumlahRoll;
                },
                'pageSummary' => function () use (&$totalJumlahRoll) {
                    return Yii::$app->formatter->asInteger($totalJumlahRoll);
                },
                'hAlign' => 'right',
            ],
            [   
                'attribute'=>'berat',
                'label' => 'Berat',
                'value' => function ($model) use (&$totalBerat) { 
                    $kartuProcess = $model->kartuProcess;
                    $berat = $kartuProcess->berat;
                    $totalBerat += $berat;
                    return $berat;
                },
                'pageSummary' => function () use (&$totalBerat) {
                    return Yii::$app->formatter->asDecimal($totalBerat);
                },
                'hAlign' => 'right',
            ],
            //'note:ntext',
            //'created_at:datetime',
            //'created_by',
            //'updated_at:datetime',
            //'updated_by',
        ],
    ]); ?>


</div>

<?php
Modal::begin([
    'id' => 'summary-proses-modal',
    'header' => '<h4><i class="glyphicon glyphicon-stats"></i> Rekapitulasi Jumlah Semua Proses</h4>',
    'size' => Modal::SIZE_LARGE,
]);
?>

<div class="table-responsive">
    <?php if (empty($searchModel->dateRange)): ?>
        <div class="alert alert-warning text-center" style="margin: 0; font-size: 14px;">
            <i class="glyphicon glyphicon-exclamation-sign"></i> 
            <strong>Perhatian!</strong> Silakan pilih filter <strong>TANGGAL</strong> terlebih dahulu untuk melihat rekapitulasi jumlah semua proses.
        </div>
    <?php else: ?>
        <p class="text-muted"><strong>Periode:</strong> <?= Html::encode($searchModel->dateRange) ?></p>
        <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
            <thead>
                <tr class="info">
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>Nama Proses</th>
                    <th class="text-right">Jumlah Kartu</th>
                    <th class="text-right">Total Panjang</th>
                    <th class="text-right">Total Roll</th>
                    <th class="text-right">Total Berat</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                $grandTotalCount = 0;
                $grandTotalPanjang = 0;
                $grandTotalRoll = 0;
                $grandTotalBerat = 0;
                
                foreach ($summaryData as $processName => $totals): 
                    $grandTotalCount += $totals['count'];
                    $grandTotalPanjang += $totals['panjang'];
                    $grandTotalRoll += $totals['roll'];
                    $grandTotalBerat += $totals['berat'];
                ?>
                    <tr>
                        <td class="text-center"><?= $i++ ?></td>
                        <td><strong><?= Html::encode($processName) ?></strong></td>
                        <td class="text-right"><?= Yii::$app->formatter->asInteger($totals['count']) ?></td>
                        <td class="text-right"><?= Yii::$app->formatter->asDecimal($totals['panjang']) ?></td>
                        <td class="text-right"><?= Yii::$app->formatter->asInteger($totals['roll']) ?></td>
                        <td class="text-right"><?= Yii::$app->formatter->asDecimal($totals['berat']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($summaryData)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">Tidak ada data proses untuk periode ini.</td>
                    </tr>
                <?php else: ?>
                    <tr class="success" style="font-weight: bold; background-color: #dff0d8;">
                        <td colspan="2" class="text-center">GRAND TOTAL</td>
                        <td class="text-right"><?= Yii::$app->formatter->asInteger($grandTotalCount) ?></td>
                        <td class="text-right"><?= Yii::$app->formatter->asDecimal($grandTotalPanjang) ?></td>
                        <td class="text-right"><?= Yii::$app->formatter->asInteger($grandTotalRoll) ?></td>
                        <td class="text-right"><?= Yii::$app->formatter->asDecimal($grandTotalBerat) ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php Modal::end(); ?>
