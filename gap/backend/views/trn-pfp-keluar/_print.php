<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnPfpKeluar */
/* @var $modelsItem common\models\ar\TrnPfpKeluarItem[] */
?>

<div class="box">
    <div class="box-header with-border">
        <!--<h3 class="box-title">Default Box Example</h3>-->
        <div class="box-tools pull-right">
            Ukuran Font: <input type="number" id="SizeText" min="1" max="99" step="1" value="11">
            <?=Html::button('<i class="fa fa-print" aria-hidden="true"></i>', ['class'=>'btn btn-default btn-sm', 'onclick'=>'printDiv("JualPfpPrint")'])?>
        </div>
    </div>

    <div class="box-body">
        <div id="JualPfpPrint">
            <table class="table" style="border-bottom: 1px solid black !important;">
                <tr>
                    <td style="width: 50%"><strong>PFP Keluar</strong></td>
                    <td style="width: 50%"><strong>ID: <?=$model->id?></strong></td>
                </tr>
            </table>

            <table class="table">
                <tr>
                    <td style="width: 25%;"></td>
                    <td style="width: 2%;"></td>
                    <td style="width: 23%;"></td>
                    <td style="width: 2%;"></td>
                    <td style="width: 23%;"></td>
                    <td style="width: 2%;"></td>
                    <td style="width: 23%;"></td>
                </tr>
                <tr>
                    <td>No</td>
                    <td>:</td>
                    <td colspan="4"><?=$model->no?></td>
                </tr>
                <tr>
                    <td>Jenis</td>
                    <td>:</td>
                    <td colspan="4"><?=$model->jenisName?></td>
                </tr>
                <tr>
                    <td>Destinasi</td>
                    <td>:</td>
                    <td><?=$model->destinasi?></td>
                    <td>&nbsp;</td>
                    <td>Diperintahkan Oleh</td>
                    <td>:</td>
                    <td><?=$model->approvalName?></td>
                </tr>
                <tr>
                    <td>No. Referensi</td>
                    <td>:</td>
                    <td><?=$model->no_referensi?></td>
                    <td>&nbsp;</td>
                    <td>Note</td>
                    <td>:</td>
                    <td><?=$model->note?></td>
                </tr>
            </table>

            <p>&nbsp;</p>

            <strong>Item List</strong>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Motif</th>
                    <th>Qty</th>
                    <th>Color</th>
                </tr>
                </thead>
                <tbody>
                <?php $totalPanjang=0; $i=1; foreach($modelsItem as $item): ?>
                    <?php
                    $panjang = $item->stockPfp->panjang_m;
                    ?>
                    <tr>
                        <td><?=$i?></td>
                        <td><?=$item->stockPfp->greige->nama_kain?></td>
                        <td><?=Yii::$app->formatter->asDecimal($panjang)?></td>
                        <td><?=$item->stockPfp->color?></td>
                    </tr>
                    <?php $i++;  $totalPanjang +=$panjang; endforeach; ?>
                <tr>
                    <td colspan="2">Total</td>
                    <td><?=Yii::$app->formatter->asDecimal($totalPanjang)?></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
