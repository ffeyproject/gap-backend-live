<?php
use common\models\ar\{ MstGreigeGroup, TrnGudangJadi, TrnScGreige, TrnStockGreige, MstSubLocation };
use yii\helpers\{ Html, Url };
use yii\web\{ JsExpression, View };
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\grid\GridView;
use kartik\widgets\Select2;
use backend\components\Converter;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnGudangJadiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

\backend\assets\DataTablesAsset::register($this);

$this->title = 'Print Stock';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="trn-print-stock-index">
    <div class="box">
        <div class="box-body">
            <?php
                $form = ActiveForm::begin(['method' => 'get', 'action' => ['trn-print-stock/index']]);
                $sub_location = Yii::$app->request->get('sub_location');
                $sumber_data = Yii::$app->request->get('sumber_data', $sumberData ?? 'auto');
            ?>
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <?php
                            echo '<label>Pilih Sub Location (Palet)</label>';
                            echo Select2::widget([
                                'name' => 'sub_location',
                                'value' => $sub_location,
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                                'data' => MstSubLocation::optionList(),
                                'options' => ['multiple' => false, 'placeholder' => 'Pilih Sub Location / Palet ...']
                            ]); 
                        ?>
                    </div>
                    <div class="form-group col-md-4">
                        <?php
                            echo '<label>Sumber Data</label>';
                            echo Select2::widget([
                                'name' => 'sumber_data',
                                'value' => $sumber_data,
                                'pluginOptions' => [
                                    'allowClear' => false,
                                ],
                                'data' => [
                                    'auto' => 'Otomatis (Stok Opname / Stok Sistem)',
                                    'opname' => 'Hasil Stok Opname (Fisik / Opname Pcs)',
                                    'system' => 'Stok Gudang Jadi (Sistem)',
                                ],
                                'options' => ['multiple' => false]
                            ]); 
                        ?>
                    </div>
                    <div class="form-group col-md-12">
                        <?php
                            echo Html::submitButton('<i class="fa fa-search"></i> Tampilkan Lembar Palet', ['class' => 'btn btn-primary btn-block']);
                        ?>
                    </div>
                </div>
            <?php 
                ActiveForm::end();
            ?>
        </div>
    </div>

    <?php
        echo '<div class="text-right"><p>Ukuran Font: <input type="number" id="SizeText" min="1" max="99" step="1" value="11"></p></div>';
    ?>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-file-text-o"></i> PRATINJAU STOCK LIST (LEMBAR PALET)
                <?php if (!empty($title) && $title !== '-'): ?>
                    <?php if (($sumberDataUsed ?? '') === 'opname'): ?>
                        <span class="label label-success" style="font-size: 11px; margin-left: 10px;"><i class="fa fa-barcode"></i> Sumber: Hasil Stok Opname</span>
                    <?php else: ?>
                        <span class="label label-primary" style="font-size: 11px; margin-left: 10px;"><i class="fa fa-database"></i> Sumber: Stok Gudang Jadi Sistem</span>
                    <?php endif; ?>
                <?php endif; ?>
            </h3>
            <div class="box-tools pull-right">
                <?=Html::button('<i class="fa fa-print" aria-hidden="true"></i> Cetak Lembar Palet', ['class'=>'btn btn-primary btn-sm', 'onclick'=>'printDivPL("stockList")'])?>
            </div>
        </div>
        <div class="box-body" id="stockList">
            <style type="text/css">
                @media print {
                    .hidden-print, .main-footer { display: none !important; }
                    body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; margin: 0; padding: 0; }
                    @page { size: auto; margin: 0mm; }
                    .table-palet { page-break-inside: auto; }
                    .table-palet tr { page-break-inside: avoid; page-break-after: auto; }
                }
                .palet-wrapper {
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 11px;
                    color: #000;
                    max-width: 950px;
                    margin: 0 auto;
                }
                .palet-header-table {
                    width: 100%;
                    margin-bottom: 10px;
                }
                .palet-header-table td {
                    vertical-align: top;
                }
                .palet-title-box {
                    background-color: #777;
                    color: #fff;
                    font-weight: bold;
                    font-size: 16px;
                    text-align: center;
                    padding: 6px;
                    letter-spacing: 1px;
                }
                .table-palet {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 10px;
                }
                .table-palet th, .table-palet td {
                    border: 1px solid #444;
                    padding: 4px 5px;
                    font-size: 10px;
                }
                .table-palet th {
                    text-align: center;
                    background-color: #f2f2f2;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                .piece-cell {
                    width: 32px;
                    text-align: center;
                }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .summary-box {
                    width: 320px;
                    border-collapse: collapse;
                    margin-top: 10px;
                }
                .summary-box td {
                    padding: 2px 5px;
                    font-size: 11px;
                    border: none;
                }
            </style>

            <div class="palet-wrapper">
                <!-- Header Table -->
                <table class="palet-header-table">
                    <tr>
                        <td width="35%">
                            <strong>PALET NO :</strong> <?= Html::encode($title) ?><br>
                            <strong>CHECKER 1 :</strong> _____________ <br>
                            <strong>CHECKER 2 :</strong> _____________
                        </td>
                        <td width="40%" class="text-center">
                            <div class="palet-title-box">
                                PALET: <?= Html::encode($title) ?>
                            </div>
                        </td>
                        <td width="25%" class="text-right">
                            <span>Page 1 of 1</span><br>
                            <strong><?= date('d F Y') ?></strong>
                        </td>
                    </tr>
                </table>

                <!-- Main Items Table -->
                <table class="table-palet">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 22%;">NO DO / MOTIF</th>
                            <th rowspan="2" style="width: 12%;">COLOR</th>
                            <th colspan="10">PIECE LENGTH</th>
                            <th rowspan="2" style="width: 8%;">GRADE</th>
                            <th colspan="2">TOTAL</th>
                        </tr>
                        <tr>
                            <th class="piece-cell">1</th>
                            <th class="piece-cell">2</th>
                            <th class="piece-cell">3</th>
                            <th class="piece-cell">4</th>
                            <th class="piece-cell">5</th>
                            <th class="piece-cell">6</th>
                            <th class="piece-cell">7</th>
                            <th class="piece-cell">8</th>
                            <th class="piece-cell">9</th>
                            <th class="piece-cell">10</th>
                            <th style="width: 6%;">PCS</th>
                            <th style="width: 8%;">YARD</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($dataProvider->models) > 0): 
                            $countTotalPcs = 0;
                            $grandTotalYard = 0;
                            $totalQtyByGrade = [];
                            $stockNotes = !empty($outNotes) ? $outNotes : [];
                            
                            foreach ($dataProvider->models as $dP):
                                $no_wo = array_key_exists('no_wo', $dP) ? $dP['no_wo'] : '';
                                $design = array_key_exists('design', $dP) ? $dP['design'] : '';
                                $motifLabel = $no_wo . ($design ? ' / ' . $design : '');
                                
                                foreach ($dP['colors'] as $color => $colorData):
                                    $qtyData = $colorData['qty'];
                                    foreach ($qtyData as $item) {
                                        if (!empty($item['note'])) {
                                            $stockNotes[] = $item['note'];
                                        }
                                        if (!empty($item['hasil_pemotongan'])) {
                                            $stockNotes[] = 'Stok ini merupakan Hasil Pemotongan (Gudang Jadi ID #' . ($item['id'] ?? '') . ')';
                                        }
                                    }
                                    
                                    // Group qtyData by Grade inside this color
                                    $gradeGroups = [];
                                    foreach ($qtyData as $item) {
                                        $gName = TrnStockGreige::gradeOptions()[$item['grade']] ?? 'NG';
                                        if (!isset($gradeGroups[$gName])) {
                                            $gradeGroups[$gName] = [
                                                'grade_val' => $item['grade'],
                                                'items' => []
                                            ];
                                        }
                                        $gradeGroups[$gName]['items'][] = $item;
                                    }

                                    foreach ($gradeGroups as $gName => $gData):
                                        $items = $gData['items'];
                                        $itemsCount = count($items);
                                        $chunks = array_chunk($items, 10);
                                        $totalRows = count($chunks) ?: 1;
                                        $sumQtyYard = 0;
                                        
                                        foreach ($items as $it) {
                                            $qYard = $it['qty'];
                                            if ($it['unit'] == MstGreigeGroup::UNIT_METER) {
                                                $qYard = Converter::meterToYard($it['qty']);
                                            }
                                            $sumQtyYard += $qYard;
                                            
                                            $countTotalPcs++;
                                            $grandTotalYard += $qYard;

                                            $gVal = $it['grade'];
                                            if (!isset($totalQtyByGrade[$gVal])) {
                                                $totalQtyByGrade[$gVal] = ['pcs' => 0, 'total_qty' => 0];
                                            }
                                            $totalQtyByGrade[$gVal]['pcs']++;
                                            $totalQtyByGrade[$gVal]['total_qty'] += $qYard;
                                        }
                                        ?>
                                        
                                        <?php foreach ($chunks as $rowIdx => $chunk): ?>
                                            <tr>
                                                <?php if ($rowIdx === 0): ?>
                                                    <td rowspan="<?= $totalRows ?>">
                                                        <strong><?= Html::encode($motifLabel) ?></strong>
                                                    </td>
                                                    <td rowspan="<?= $totalRows ?>">
                                                        <?= Html::encode($color) ?>
                                                    </td>
                                                <?php endif; ?>

                                                <?php for ($i = 0; $i < 10; $i++): ?>
                                                    <td class="piece-cell">
                                                        <?= isset($chunk[$i]) ? (float)$chunk[$i]['qty'] : '' ?>
                                                    </td>
                                                <?php endfor; ?>

                                                <?php if ($rowIdx === 0): ?>
                                                    <td rowspan="<?= $totalRows ?>" class="text-center">
                                                        <?= Html::encode($gName) ?>
                                                    </td>
                                                    <td rowspan="<?= $totalRows ?>" class="text-center">
                                                        <strong><?= $itemsCount ?></strong>
                                                    </td>
                                                    <td rowspan="<?= $totalRows ?>" class="text-right">
                                                        <strong><?= number_format($sumQtyYard, 0) ?></strong>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="15" class="text-center">Belum ada data stok di lokasi ini!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Summary Box -->
                <?php if (count($dataProvider->models) > 0): ?>
                    <table class="summary-box" style="margin-top: 10px; width: 320px;">
                        <tr>
                            <td width="100"><strong>TOTAL :</strong></td>
                            <td width="50" class="text-right"><strong><?= number_format($countTotalPcs) ?></strong></td>
                            <td width="20" class="text-center">:</td>
                            <td class="text-right"><strong><?= number_format($grandTotalYard, 0) ?></strong></td>
                            <td><strong>YARD</strong></td>
                        </tr>
                        <?php
                            ksort($totalQtyByGrade);
                            foreach ($totalQtyByGrade as $gradeVal => $stat):
                        ?>
                            <tr>
                                <td>GRADE <?= Html::encode(TrnStockGreige::gradeOptions()[$gradeVal]) ?> :</td>
                                <td class="text-right"><?= number_format($stat['pcs']) ?></td>
                                <td class="text-center">:</td>
                                <td class="text-right"><?= number_format($stat['total_qty'], 0) ?></td>
                                <td>YARD</td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>

                <!-- Note / Keterangan Mutasi Stok (Hanya Muncul Jika Ada Data History) -->
                <?php
                    $allNotes = !empty($outNotes) ? $outNotes : [];
                    if (!empty($stockNotes)) {
                        $allNotes = array_merge($allNotes, $stockNotes);
                    }

                    $cleanNotes = [];
                    if (!empty($allNotes)) {
                        foreach (array_unique($allNotes) as $rawNote) {
                            $parts = explode('|', $rawNote);
                            foreach ($parts as $noteText) {
                                $noteText = trim($noteText);
                                if (empty($noteText)) continue;
                                
                                if (strpos($noteText, 'Hasil Pemotongan') !== false || strpos($noteText, 'Dari inspecting') !== false || strpos($noteText, 'Barang Keluar:') !== false) {
                                    continue;
                                }
                                if (strpos($noteText, 'Pemotongan ID:') !== false && strpos($noteText, 'dipotong') === false) {
                                    if (preg_match('/Pemotongan ID:\s*(\d+)/i', $noteText, $matches)) {
                                        $potongId = (int)$matches[1];
                                        $potongModel = \common\models\ar\TrnPotongStock::findOne($potongId);
                                        if ($potongModel && $potongModel->stock) {
                                            $initialQty = (float)$potongModel->stock->qty;
                                            $pItems = [];
                                            foreach ($potongModel->trnPotongStockItems as $pItem) {
                                                $pItems[] = (float)$pItem->qty;
                                            }
                                            $sumP = array_sum($pItems);
                                            $remP = $initialQty - $sumP;
                                            if ($remP > 0) {
                                                $pItems[] = (float)$remP;
                                            }
                                            $cleanNotes[] = 'Pemotongan ID: ' . $potongId . ' qty: ' . $initialQty . ' dipotong ' . implode(' dan ', $pItems);
                                        } else {
                                            $cleanNotes[] = $noteText;
                                        }
                                    } else {
                                        $cleanNotes[] = $noteText;
                                    }
                                } else {
                                    $cleanNotes[] = $noteText;
                                }
                            }
                        }
                        $cleanNotes = array_values(array_unique($cleanNotes));
                    }
                ?>
                <?php if (!empty($cleanNotes)): ?>
                    <table style="width: 100%; border: 1px dashed #666; padding: 6px; font-size: 10px; margin-top: 15px;">
                        <tr>
                            <td colspan="2" style="font-weight: bold; border-bottom: 1px dashed #ccc; padding-bottom: 4px; margin-bottom: 4px;">
                                Catatan / Keterangan Mutasi Stok Palet Ini:
                            </td>
                        </tr>
                        <?php foreach ($cleanNotes as $noteText): ?>
                            <tr>
                                <td width="15" style="vertical-align: top; font-weight: bold;">=</td>
                                <td style="padding-bottom: 3px;"><?= Html::encode($noteText) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
</div>
<?php
$js = <<<JS
JS;
$this->registerJs($js.$this->renderFile(__DIR__.'/js/index.js'), View::POS_END);
