<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimMakloon */
/* @var $dataItems array*/
/* @var $formatter \yii\i18n\Formatter*/
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Items</h3>
        <div class="box-tools pull-right">
            <span class="label label-primary"></span>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>QTY</th>
            </tr>
            </thead>
            <tbody>
            <?php $total = 0; foreach ($dataItems as $index=>$dataItem):?>
                <tr>
                    <td><?=$index+1?></td>
                    <td><?=$dataItem['qty_fmt']?></td>
                </tr>
                <?php $total += $dataItem['qty']; endforeach; ?>

            <tr>
                <th>Total</th>
                <th><?=$formatter->asDecimal($total)?></th>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">SURAT PENGANTAR</h3>
        <div class="box-tools pull-right">
            <span class="label label-primary"></span>
        </div>
    </div>
    <div class="box-body">

    </div>
</div>
