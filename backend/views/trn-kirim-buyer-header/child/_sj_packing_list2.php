<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimBuyerHeader */
/* @var $dataProviderKirimBuyer \yii\data\ActiveDataProvider*/
/* @var $formatter \yii\i18n\Formatter */

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnKirimBuyer;
use yii\helpers\Html;

?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">PACKING LIST</h3>
        <div class="box-tools pull-right">
            <?=Html::button('<i class="fa fa-print" aria-hidden="true"></i>', ['class'=>'btn btn-default btn-xs', 'onclick'=>'printDivPL("packingList")'])?>
        </div>
    </div>
    <div class="box-body" id="packingList">
        <table width="100%">
            <tr>
                <td width="40%">
                    <strong><?=Yii::$app->params['company']['nama']?></strong>
                    <br><?=Yii::$app->params['company']['alamat']?>
                </td>
                <td width="20%"><strong>PACKING LIST</strong></td>
                <td width="40%" style="text-align: right;"><strong>GAP-FRM-GDJ-02</strong></td>
            </tr>
        </table>

        <p></p>

        <table width="100%">
            <tr>
                <td width="70%">
                    <table width="100%">
                        <tr>
                            <td width="10%">NAMA BUYER</td>
                            <td width="3%">:</td>
                            <td><?=$model->nama_buyer?></td>
                        </tr>
                    </table>
                </td>
                <td width="30%">
                    <table width="100%">
                        <tr>
                            <td>TANGGAL KIRIM</td>
                            <td>:</td>
                            <td><?=$formatter->asDate($model->date)?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <p>&nbsp;</p>

        <table width="100%" border="1">
            <thead>
            <tr>
                <th width="3%" rowspan="2" style="text-align: center;">BAL</th>
                <th rowspan="2" style="text-align: center;">NO WO</th>
                <th rowspan="2" style="text-align: center;">DESIGN</th>
                <th colspan="2" style="text-align: center;">JUMLAH</th>
                <th rowspan="2" colspan="10" style="text-align: center;">PIECE LENGTH (YARD / METER / KG )</th>
            </tr>
            <tr>
                <th width="5%" style="text-align: center;">PCS</th>
                <th style="text-align: center;">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($dataProviderKirimBuyer->models as $kirimBuyerModel):?>
                <?php
                    /* @var $kirimBuyerModel TrnKirimBuyer*/
                    $modelsKirimBuyerItem = $kirimBuyerModel->trnKirimBuyerItems;
                    $providerStockSiapKirim = $kirimBuyerModel->trnKirimBuyerItems;
                    $count = count($providerStockSiapKirim);
                    $itemsPerRow = 10;
                    $totalRows = ceil($count / $itemsPerRow);
                    $rowCounter = 0; // Initialize row counter
                ?>
                <tr>
                    <td rowspan="<?= ($totalRows + 1) ?>" style="text-align: center;"><input class="balInput" style="border: 0;" type="text" name="bal[]" value="" placeholder="input bal disini..."></td>
                    <td rowspan="<?= ($totalRows + 1) ?>" style="text-align: center;"><?=$kirimBuyerModel->wo->no?></td>
                    <td rowspan="<?= ($totalRows + 1) ?>" style="text-align: center;"><?=$kirimBuyerModel->nama_kain_alias?></td>
                    <td rowspan="<?= ($totalRows + 1) ?>" style="text-align: center;"><?= $count.' Pcs'?></td>
                    <td rowspan="<?= ($totalRows + 1) ?>">&nbsp;</td>
                    <?php
                        for ($row = 0; $row < $totalRows; $row++) {
                            $rowCounter++; // Increment row counter for each new row
                            echo '<tr>';
                            for ($col = 0; $col < $itemsPerRow; $col++) {
                                $index = $row * $itemsPerRow + $col;
                                if ($index < $count) {
                                    echo '<td style="text-align: center;">' . $providerStockSiapKirim[$index]['qty'] . '</td>';
                                } else {
                                    // Echo an empty cell if no more data
                                    echo '<td></td>';
                                }
                            }
                            echo '</tr>';
                        }
                    ?>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>

        <p></p>
    </div>
</div>
