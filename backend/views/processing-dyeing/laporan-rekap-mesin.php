<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;

$this->title = 'Laporan Rekap Mesin';
$this->params['breadcrumbs'][] = ['label' => 'Processing Dyeing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$formatNumber = function($panjang, $count) {
    if ($count == 0) return '';
    if ($panjang == 0) return "({$count}x)";
    return number_format($panjang, 0, ',', '.') . " ({$count}x)";
};
?>
<style>
    .summary-box {
        background-color: #fcf8e3;
        border: 1px solid #faebcc;
        border-radius: 4px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .summary-box h4 { margin-top: 0; font-weight: bold; }
    .table-sticky-container {
        max-height: 500px;
        overflow-y: auto;
        overflow-x: auto;
        border: 1px solid #ddd;
        margin-bottom: 30px;
    }
    .table-sticky-container table {
        margin-bottom: 0;
        border-collapse: separate; /* Required for sticky */
        border-spacing: 0;
    }
    .table-sticky-container th, .table-sticky-container td {
        white-space: nowrap;
        background-color: #fff;
        border-bottom: 1px solid #ddd;
        border-right: 1px solid #ddd;
    }
    .table-sticky-container th {
        position: sticky;
        top: 0;
        z-index: 20;
        background-color: #f4f4f4 !important;
        text-align: center;
        vertical-align: middle !important;
    }
    .table-sticky-container tfoot th {
        position: sticky;
        bottom: 0;
        z-index: 20;
        background-color: #e8f5e9 !important;
    }
    .freeze-col-1 {
        position: sticky;
        left: 0;
        z-index: 10;
        background-color: #f9f9f9 !important;
    }
    .freeze-col-2 {
        position: sticky;
        left: 50px; /* Adjust based on # column width */
        z-index: 10;
        background-color: #f9f9f9 !important;
    }
    th.freeze-col-1, th.freeze-col-2 { z-index: 30; }
    tfoot th.freeze-col-1, tfoot th.freeze-col-2 { z-index: 30; }
</style>

<div class="box box-primary">
    <div class="box-body">
        <div class="row">
            <div class="col-md-8">
                <?php $form = ActiveForm::begin([
                    'method' => 'get',
                    'action' => ['laporan-rekap-mesin'],
                ]); ?>
                <div class="row">
                    <div class="col-md-6">
                        <?= Html::label('Pilih Jenis Mesin') ?>
                        <?= Select2::widget([
                            'name' => 'model_mesins',
                            'value' => $selectedModelMesins,
                            'data' => array_combine($modelMesinOptions, $modelMesinOptions),
                            'options' => ['multiple' => true, 'placeholder' => 'Pilih Mesin...'],
                            'pluginOptions' => ['allowClear' => true],
                        ]) ?>
                    </div>
                    <div class="col-md-4">
                        <?= Html::label('Rentang Tanggal') ?>
                        <?= DateRangePicker::widget([
                            'name' => 'date_range',
                            'value' => $dateRange,
                            'convertFormat' => true,
                            'pluginOptions' => [
                                'locale' => ['format' => 'Y-m-d'],
                                'separator' => ' to ',
                            ]
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= Html::label('&nbsp;', null, ['style' => 'display:block;']) ?>
                        <?= Html::submitButton('<i class="fa fa-search"></i> Tampilkan', ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            
            <div class="col-md-4">
                <div class="summary-box">
                    <strong>Batch:</strong> <?= $summary['batch'] ?><br>
                    <strong>Kartu:</strong> <?= $summary['kartu'] ?><br>
                    <strong>Jumbo:</strong> <?= $summary['jumbo'] ?><br>
                    <strong>Perbaikan:</strong> <?= $summary['perbaikan'] ?>
                </div>
            </div>
        </div>

        <?php if (!empty($selectedModelMesins)): ?>
            <hr>
            
            <!-- Table 1: Rekapitulasi Jumlah Semua Proses -->
            <h4><i class="fa fa-bar-chart"></i> Rekapitulasi Jumlah Semua Proses</h4>
            <div class="table-sticky-container">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="freeze-col-1" style="width: 50px;">#</th>
                            <th class="freeze-col-2" style="width: 250px;">Nama Proses</th>
                            <th>Jumlah Dyeing</th>
                            <th>Jumlah PFP</th>
                            <th>Jumlah Printing</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        $totDyeingP = 0; $totDyeingC = 0;
                        $totPfpP = 0; $totPfpC = 0;
                        $totPrintingP = 0; $totPrintingC = 0;
                        $totAllP = 0; $totAllC = 0;
                        
                        foreach ($allProsesNames as $proses): 
                            $data = $rekapProses[$proses];
                            $totDyeingP += $data['dyeing']['p']; $totDyeingC += $data['dyeing']['c'];
                            $totPfpP += $data['pfp']['p']; $totPfpC += $data['pfp']['c'];
                            $totPrintingP += $data['printing']['p']; $totPrintingC += $data['printing']['c'];
                            $totAllP += $data['total']['p']; $totAllC += $data['total']['c'];
                        ?>
                        <tr>
                            <td class="freeze-col-1 text-center"><?= $no++ ?></td>
                            <td class="freeze-col-2"><strong><?= Html::encode($proses) ?></strong></td>
                            <td class="text-right"><?= $formatNumber($data['dyeing']['p'], $data['dyeing']['c']) ?></td>
                            <td class="text-right"><?= $formatNumber($data['pfp']['p'], $data['pfp']['c']) ?></td>
                            <td class="text-right"><?= $formatNumber($data['printing']['p'], $data['printing']['c']) ?></td>
                            <td class="text-right" style="background-color: #f9f9f9; font-weight: bold;"><?= $formatNumber($data['total']['p'], $data['total']['c']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="freeze-col-1" colspan="2" style="text-align: right;">GRAND TOTAL</th>
                            <th class="text-right"><?= $formatNumber($totDyeingP, $totDyeingC) ?></th>
                            <th class="text-right"><?= $formatNumber($totPfpP, $totPfpC) ?></th>
                            <th class="text-right"><?= $formatNumber($totPrintingP, $totPrintingC) ?></th>
                            <th class="text-right"><?= $formatNumber($totAllP, $totAllC) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Table 2: Rekapitulasi Jumlah Per Mesin -->
            <h4><i class="fa fa-bar-chart"></i> Rekapitulasi Jumlah Per Mesin</h4>
            <div class="table-sticky-container">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="freeze-col-1" style="width: 50px;">#</th>
                            <th class="freeze-col-2" style="width: 250px;">Nama Mesin</th>
                            <?php foreach ($allProsesNames as $proses): ?>
                                <th><?= Html::encode($proses) ?></th>
                            <?php endforeach; ?>
                            <th style="background-color: #e8f5e9 !important;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        $totColsMesin = array_fill_keys($allProsesNames, ['p' => 0, 'c' => 0]);
                        $grandTotMesinP = 0; $grandTotMesinC = 0;
                        
                        foreach ($rekapMesin as $mesin => $data): 
                            $grandTotMesinP += $data['total']['p'];
                            $grandTotMesinC += $data['total']['c'];
                        ?>
                        <tr>
                            <td class="freeze-col-1 text-center"><?= $no++ ?></td>
                            <td class="freeze-col-2"><strong><?= Html::encode($mesin) ?></strong></td>
                            <?php foreach ($allProsesNames as $proses): 
                                $p = $data[$proses]['p'] ?? 0;
                                $c = $data[$proses]['c'] ?? 0;
                                $totColsMesin[$proses]['p'] += $p;
                                $totColsMesin[$proses]['c'] += $c;
                            ?>
                                <td class="text-right"><?= $formatNumber($p, $c) ?></td>
                            <?php endforeach; ?>
                            <td class="text-right" style="background-color: #f9f9f9; font-weight: bold;"><?= $formatNumber($data['total']['p'], $data['total']['c']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="freeze-col-1" colspan="2" style="text-align: right;">GRAND TOTAL</th>
                            <?php foreach ($allProsesNames as $proses): ?>
                                <th class="text-right"><?= $formatNumber($totColsMesin[$proses]['p'], $totColsMesin[$proses]['c']) ?></th>
                            <?php endforeach; ?>
                            <th class="text-right"><?= $formatNumber($grandTotMesinP, $grandTotMesinC) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Table 3: Rekapitulasi Jumlah Per Shift -->
            <h4><i class="fa fa-bar-chart"></i> Rekapitulasi Jumlah Per Shift</h4>
            <div class="table-sticky-container">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="freeze-col-1" style="width: 50px;">#</th>
                            <th class="freeze-col-2" style="width: 250px;">Shift</th>
                            <?php foreach ($allProsesNames as $proses): ?>
                                <th><?= Html::encode($proses) ?></th>
                            <?php endforeach; ?>
                            <th style="background-color: #e8f5e9 !important;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        $totColsShift = array_fill_keys($allProsesNames, ['p' => 0, 'c' => 0]);
                        $grandTotShiftP = 0; $grandTotShiftC = 0;
                        
                        foreach ($rekapShift as $shift => $data): 
                            $grandTotShiftP += $data['total']['p'];
                            $grandTotShiftC += $data['total']['c'];
                        ?>
                        <tr>
                            <td class="freeze-col-1 text-center"><?= $no++ ?></td>
                            <td class="freeze-col-2"><strong><?= Html::encode($shift) ?></strong></td>
                            <?php foreach ($allProsesNames as $proses): 
                                $p = $data[$proses]['p'] ?? 0;
                                $c = $data[$proses]['c'] ?? 0;
                                $totColsShift[$proses]['p'] += $p;
                                $totColsShift[$proses]['c'] += $c;
                            ?>
                                <td class="text-right"><?= $formatNumber($p, $c) ?></td>
                            <?php endforeach; ?>
                            <td class="text-right" style="background-color: #f9f9f9; font-weight: bold;"><?= $formatNumber($data['total']['p'], $data['total']['c']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="freeze-col-1" colspan="2" style="text-align: right;">GRAND TOTAL</th>
                            <?php foreach ($allProsesNames as $proses): ?>
                                <th class="text-right"><?= $formatNumber($totColsShift[$proses]['p'], $totColsShift[$proses]['c']) ?></th>
                            <?php endforeach; ?>
                            <th class="text-right"><?= $formatNumber($grandTotShiftP, $grandTotShiftC) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> Silakan pilih minimal 1 Jenis Mesin dan klik <strong>Tampilkan</strong>.
            </div>
        <?php endif; ?>
    </div>
</div>
