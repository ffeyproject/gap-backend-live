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
                $limit = Yii::$app->request->get('limit', 50);
            ?>
                <div class="form-row">
                <div class="form-group col-md-9">
                        <?php
                            echo '<label>Pilih Sub Location</label>';
                            echo Select2::widget([
                                'name' => 'sub_location',
                                'value' => $sub_location,
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                                'data' => MstSubLocation::optionList(),
                                'options' => ['multiple' => false, 'placeholder' => 'Select Sub Location ...']
                            ]); 
                        ?>
                    </div>
                    <div class="form-group col-md-3">
                        <?php
                            echo '<label>Limit Data Untuk Ditampilkan</label>';
                            echo Select2::widget([
                                'name' => 'limit',
                                'value' => $limit,
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                                'data' => [50 => 50, 100 => 100, 200 => 200, 500 => 500, 1000 => 1000, count($dataProvider->allModels) => count($dataProvider->allModels)],
                                'options' => ['multiple' => false, 'placeholder' => 'Select limit ...']
                            ]); 
                        ?>
                    </div>
                    <div class="form-group col-md-12">
                        <?php
                            echo '<div class="form-group">';
                            echo Html::submitButton('Search', ['class' => 'btn btn-primary btn-block']);
                            echo '</div>';
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
            <h3 class="box-title">STOCK LIST</h3>
            <div class="box-tools pull-right">
                <?=Html::button('<i class="fa fa-print" aria-hidden="true"></i>', ['class'=>'btn btn-default btn-xs', 'onclick'=>'printDivPL("stockList")'])?>
            </div>
        </div>
        <div class="box-body" id="stockList">
            <table width="100%">
                <tr>
                    <td width="50%" style="text-align: left;"><strong><?= $title ?></strong></td>
                    <td width="50%" style="text-align: right;"><strong><?= $timestamp ?></strong></td>
                </tr>
            </table>

            <p></p>
            <p>&nbsp;</p>

            <table width="100%" border="1">
                <thead>
                    <tr>
                        <th rowspan="2" style="text-align: center;">NO WO</th>
                        <th rowspan="2" style="text-align: center;">DESIGN</th>
                        <th rowspan="2" style="text-align: center;">COLOR</th>
                        <th colspan="3" style="text-align: center;">JUMLAH</th>
                        <th rowspan="2" colspan="10" style="text-align: center;">PIECE LENGTH (YARD / METER / KG)</th>
                    </tr>
                    <tr>
                        <th width="5%" style="text-align: center;">PCS</th>
                        <th width="5%" style="text-align: center;">TOTAL</th>
                        <th width="5%" style="text-align: center;">SATUAN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($dataProvider->models) > 0) {
                        $countTotalPcs = 0;
                        $totalQtyByGrade = [];
                        foreach ($dataProvider->models as $dP): ?>
                            <?php
                                $no_wo = array_key_exists('no_wo', $dP) ? $dP['no_wo'] : '';
                                $design = array_key_exists('design', $dP) ? $dP['design'] : '';
                                $unit = MstGreigeGroup::unitOptions()[$dP['unit']];
                                $firstColorRow = true; // Flag to indicate the first color row
                                $colorRowspan = 0;
                                
                                // Calculate total rowspan for NO WO and DESIGN
                                foreach ($dP['colors'] as $color => $colorData) {
                                    $colorRowspan += ceil(count($colorData['qty']) / 10) + 1; // +1 for the color row itself
                                }
                            ?>
                            <?php foreach ($dP['colors'] as $color => $colorData): ?>
                                <?php
                                    $qtyData = $colorData['qty'];
                                    $total_qty = $colorData['total_qty'];
                                    $count = count($qtyData);
                                    $countTotalPcs += $count;
                                    $itemsPerRow = 10;
                                    $totalRows = ceil($count / $itemsPerRow);
                                ?>
                                <?php if ($firstColorRow): ?>
                                    <tr>
                                        <td rowspan="<?= $colorRowspan ?>" style="text-align: center;"><?= $no_wo ?></td>
                                        <td rowspan="<?= $colorRowspan ?>" style="text-align: left;"><?= $design ?></td>
                                        <?php $firstColorRow = false; ?>
                                <?php endif; ?>
                                    <td rowspan="<?= ($totalRows + 1) ?>" style="text-align: center;"><?= $color ?></td>
                                    <td rowspan="<?= ($totalRows + 1) ?>" style="text-align: center;"><?= $count ?> Pcs</td>
                                    <td rowspan="<?= ($totalRows + 1) ?>" style="text-align: center;"><?= number_format($total_qty) ?></td>
                                    <td rowspan="<?= ($totalRows + 1) ?>" style="text-align: center;"><?= $unit ?></td>
                                </tr>
                                <?php
                                    for ($row = 0; $row < $totalRows; $row++) {
                                        echo '<tr>';
                                        for ($col = 0; $col < $itemsPerRow; $col++) {
                                            $index = $row * $itemsPerRow + $col;
                                            if ($index < $count) {
                                                $qty = $qtyData[$index]['qty'];
                                                $grade = $qtyData[$index]['grade'];
                                                $unitQty = $qtyData[$index]['unit'];
                                                if (isset($totalQtyByGrade[$grade])) {
                                                    $totalQtyByGrade[$grade]['qty'] += 1;
                                                    $totalQtyToYard = $qty;
                                                    if ($unitQty == MstGreigeGroup::UNIT_METER){
                                                        $totalQtyToYard = Converter::meterToYard($qty);
                                                    }
                                                    $totalQtyByGrade[$grade]['total_qty'] += $totalQtyToYard;
                                                    
                                                } else {

                                                    $totalQtyByGrade[$grade] = ['qty' => 1, 'total_qty' => $qty];
                                                }
                                                echo '<td style="text-align: center; width: 50px;">' . $qty .'</td>';
                                            } else {
                                                echo '<td style="width: 50px;"></td>';
                                            }
                                        }
                                        echo '</tr>';
                                    }
                                ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        </tbody>
                </table>
                <table width="100%" border="1">
                    <tbody>
                        <tr><td colspan="<?= (6 + $itemsPerRow) ?>" style="text-align: left;"><b>TOTAL : <?= number_format($countTotalPcs) ?></b></td></tr>
                            <?php
                            ksort($totalQtyByGrade);
                            foreach ($totalQtyByGrade as $grade => $qty): ?>
                                <tr><td colspan="<?= (6 + $itemsPerRow) ?>" style="text-align: left;"><b>Grade <?=TrnStockGreige::gradeOptions()[$grade] ?> : <?= number_format($qty['qty']) ?> : <?= number_format($qty['total_qty']) ?> Yard</b></td></tr>
                            <?php endforeach; ?>
                    </tbody>
                </table>
                    <?php } else {
                        echo '<tr><td style="text-align: center;" colspan="15">Belum ada data!</td></tr>';
                    } ?>
    



            <p>&nbsp;</p><br><br>

            <table width="100%">
                <tr>
                    <td style="text-align: center;" width="30%">
                        <p><strong>Checker</strong></p><br><br><br><br><br>
                        <?= "______________________" ?>
                    </td>
                    <td style="text-align: center;" width="40%">
                        <p><strong>Maker</strong></p><br><br><br><br><br>
                        <?= "______________________" ?>
                    </td>
                    <td style="text-align: center;" width="30%">
                        <p><strong>Assign</strong></p><br><br><br><br><br>
                        <?= "______________________" ?>
                    </td>
                </tr>
            </table>

        </div>
    </div>
    
</div>
<?php
$js = <<<JS
JS;
$this->registerJs($js.$this->renderFile(__DIR__.'/js/index.js'), View::POS_END);
