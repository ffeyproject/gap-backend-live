<?php
use common\models\ar\TrnKartuProsesMaklon;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnKartuProsesMaklon */

$formatter = Yii::$app->formatter;
$unitName = \common\models\ar\MstGreigeGroup::unitOptions()[$model->scGreige->greigeGroup->unit];
?>

<div class="row">
    <div class="col-xs-12">
        PT. GAJAH ANGKASA PERKASA
        <br>
        Jl. Jend. Sudirman No. 823 Bandung 40213

        <p></p>

        <div class="text-center">
            <strong>SURAT PENGANTAR</strong>
        </div>

        <div class="row">
            <div class="col-xs-6">
                <table class="table" style="page-break-inside:avoid">
                    <tr>
                        <th>Kepada</th>
                        <td>:</td>
                        <td><?=$model->vendor->name?></td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>:</td>
                        <td><?=$model->vendor->address?></td>
                    </tr>
                </table>
            </div>

            <div class="col-xs-6">
                <table class="table" style="page-break-inside:avoid">
                    <tr>
                        <th>Nomor</th>
                        <td>:</td>
                        <td><?=$model->no?></td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>:</td>
                        <td><?=$formatter->asDate($model->date)?></td>
                    </tr>
                    <tr>
                        <th>Jenis</th>
                        <td>:</td>
                        <td><?=$model->wo->greigeNamaKain?></td>
                    </tr>
                </table>
            </div>
        </div>

        <table class="table table-bordered" style="page-break-inside:avoid">
            <tr>
                <th class="text-center" style="vertical-align: middle;">No</th>
                <th class="text-center" style="vertical-align: middle;">PANJANG</th>
                <th class="text-center" style="vertical-align: middle;">KETERANGAN</th>
            </tr>

            <?php $total = 0; foreach ($model->trnKartuProsesMaklonItems as $index=>$trnKartuProsesMaklonItem):?>
            <tr>
                <td><?=$index+1?></td>
                <td><?=$formatter->asDecimal($trnKartuProsesMaklonItem->qty)?> <?=$unitName?></td>
                <td><?=$trnKartuProsesMaklonItem->note?></td>
            </tr>
            <?php $total+= $trnKartuProsesMaklonItem->qty; endforeach; ?>
            <tr>
                <th>TOTAL</th>
                <th><?=$formatter->asDecimal($total)?> <?=$unitName?></th>
                <td></td>
            </tr>
        </table>

        <div class="row">
            <div class="col-xs-4 text-center">
                Penerima<br><br><br>
                (.......................)
            </div>

            <div class="col-xs-4 text-center">
                Mengetahui<br><br><br>

            </div>

            <div class="col-xs-4 text-center">Ka. Gudang</div>
        </div>
    </div>
</div>
