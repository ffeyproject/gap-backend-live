<?php
use common\models\ar\TrnKirimBuyer;
use common\models\ar\TrnKirimBuyerHeader;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnScGreige */
/* @var $formatter \yii\i18n\Formatter*/

?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">MARKETING ORDERS</h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>NOMOR MO</th>
                <th>TANGGAL MO</th>
                <th>QTY BATCH</th>
                <th>QTY UNIT</th>
                <th>QTY FINISH</th>
                <th>QTY FINISH (Y)</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $moColorQty = 0;
            $moColorQtyBatchToUnit = 0;
            $moColorQtyFinish = 0;
            $moColorQtyFinishToYard = 0;
            foreach ($model->trnMosAktif as $i=>$mo):?>
                <tr>
                    <td><?=$i+1?></td>
                    <td><?=$mo->no?></td>
                    <td><?=$formatter->asDate($mo->date)?></td>
                    <td>
                        <?php
                        $qty = $mo->colorQty;
                        $moColorQty += $qty;
                        echo $formatter->asDecimal($qty);
                        ?>
                    </td>
                    <td>
                        <?php
                        $qty = $mo->colorQtyBatchToUnit;
                        $moColorQtyBatchToUnit += $qty;
                        echo $formatter->asDecimal($qty);
                        ?>
                    </td>
                    <td>
                        <?php
                        $qty = $mo->colorQtyFinish;
                        $moColorQtyFinish += $qty;
                        echo $formatter->asDecimal($qty);
                        ?>
                    </td>
                    <td>
                        <?php
                        $qty = $mo->colorQtyFinishToYard;
                        $moColorQtyFinishToYard += $qty;
                        echo $formatter->asDecimal($qty);
                        ?>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">WORKING ORDERS</h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>NOMOR MO</th>
                <th>NOMOR WO</th>
                <th>TANGGAL WO</th>
                <th>QTY BATCH</th>
                <th>QTY UNIT</th>
                <th>QTY FINISH</th>
                <th>QTY FINISH (Y)</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $woColorQty = 0;
            $woColorQtyBatchToUnit = 0;
            $woColorQtyFinish = 0;
            $woColorQtyFinishToYard = 0;
            foreach ($model->trnWos as $i=>$wo):?>
                <tr>
                    <td><?=$i+1?></td>
                    <td><?=$wo->mo->no?></td>
                    <td><?=$wo->no?></td>
                    <td><?=$formatter->asDate($wo->date)?></td>
                    <td>
                        <?php
                        $qty = $wo->colorQty;
                        $woColorQty += $qty;
                        echo $formatter->asDecimal($qty);
                        ?>
                    </td>
                    <td>
                        <?php
                        $qty = $wo->colorQtyBatchToUnit;
                        $woColorQtyBatchToUnit += $qty;
                        echo $formatter->asDecimal($qty);
                        ?>
                    </td>
                    <td>
                        <?php
                        $qty = $wo->colorQtyFinish;
                        $woColorQtyFinish += $qty;
                        echo $formatter->asDecimal($qty);
                        ?>
                    </td>
                    <td>
                        <?php
                        $qty = $wo->colorQtyFinishToYard;
                        $woColorQtyFinishToYard += $qty;
                        echo $formatter->asDecimal($qty);
                        ?>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">KIRIM</h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>NOMOR PENGIRIMAN</th>
                <th>TANGGAL PENGIRIMAN</th>
                <th>QTY</th>
                <th>QTY (M)</th>
                <th>QTY (Y)</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $qtyKirim = 0;
            $qtyKirimToMeter = 0;
            $qtyKirimToYard = 0;
            foreach ($model->trnKirimBuyerPosted as $i=>$kirim):
                ?>
                <tr>
                    <td><?=$i+1?></td>
                    <td><?=$kirim->header->no?></td>
                    <td><?=$formatter->asDate($kirim->header->date)?></td>
                    <td>
                        <?php
                        $qty = $kirim->qtyKirim;
                        echo $formatter->asDecimal($qty);
                        $qtyKirim += $qty;
                        ?>
                    </td>
                    <td>
                        <?php
                        $qty = $kirim->qtyKirimToMeter;
                        echo $formatter->asDecimal($qty);
                        $qtyKirimToMeter+=$qty;
                        ?>
                    </td>
                    <td>
                        <?php
                        $qty = $kirim->qtyKirimToYard;
                        echo $formatter->asDecimal($qty);
                        $qtyKirimToYard+=$qty;
                        ?>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>

<table class="table table-bordered table-striped table-hover">
    <thead>
    <tr>
        <th colspan="4">MO</th>
        <th colspan="4">WO</th>
        <th colspan="3">KIRIM</th>
    </tr>
    <tr>
        <th>BATCH</th>
        <th>GREIGE</th>
        <th>FINISH</th>
        <th>FINISH (Y)</th>
        <th>BATCH</th>
        <th>GREIGE</th>
        <th>FINISH</th>
        <th>FINISH (Y)</th>
        <th>GREIGE</th>
        <th>GREIGE (M)</th>
        <th>GREIGE (Y)</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?=$formatter->asDecimal($moColorQty)?></td>
        <td><?=$formatter->asDecimal($moColorQtyBatchToUnit)?></td>
        <td><?=$formatter->asDecimal($moColorQtyFinish)?></td>
        <td><?=$formatter->asDecimal($moColorQtyFinishToYard)?></td>

        <td><?=$formatter->asDecimal($woColorQty)?></td>
        <td><?=$formatter->asDecimal($woColorQtyBatchToUnit)?></td>
        <td><?=$formatter->asDecimal($woColorQtyFinish)?></td>
        <td><?=$formatter->asDecimal($woColorQtyFinishToYard)?></td>

        <td><?=$formatter->asDecimal($qtyKirim)?></td>
        <td><?=$formatter->asDecimal($qtyKirimToMeter)?></td>
        <td><?=$formatter->asDecimal($qtyKirimToYard)?></td>
    </tr>
    </tbody>
</table>