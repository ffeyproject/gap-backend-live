<?php

use common\models\ar\InspectingPrintingReject;
use yii\i18n\Formatter;

/* @var $this yii\web\View */
/* @var $model InspectingPrintingReject */

$formatter = Yii::$app->formatter;
?>

<div class="row">
    <div class="col-xs-12 text-center">
        <h4>PT. GAJAH ANGKASA PERKASA</h4>
        <strong>No. 823 Jl. Jend. Sudirman, Bandung 40213</strong>
    </div>
</div>

<p>&nbsp;</p>

<table style="width: 100%;">
    <tr>
        <td style="width: 20%">&nbsp;</td>
        <td style="width: 60%; text-align: center;"><STRONG>SURAT PENGANTAR</STRONG></td>
        <td style="width: 20%">&nbsp;</td>
    </tr>
</table>

<table style="width: 100%;">
    <tr>
        <td style="width: 15%">&nbsp;</td>
        <td style="width: 1%"></td>
        <td style="width: 26%">&nbsp;</td>
        <td style="width: 20%;">&nbsp;</td>
        <td style="width: 17%; text-align: right;">Tanggal</td>
        <td style="width: 1%; text-align: right;">:</td>
        <td style="width: 20%; text-align: right;"><?=$formatter->asDate($model->date)?></td>
    </tr>
    <tr>
        <td>Untuk Bagian</td>
        <td>:</td>
        <td><?=$model->untuk_bagian?></td>
        <td>&nbsp;</td>
        <td style="text-align: right;">Nomor</td>
        <td style="text-align: right;">:</td>
        <td style="text-align: right;"><?=$model->no?></td>
    </tr>
</table>

<table style="width: 100%;" class="bordered">
    <thead>
    <tr>
        <th style="text-align: center;">NO. WO</th>
        <th style="text-align: center;">NO. KARTU</th>
        <th style="text-align: center;">MOTIF</th>
        <th style="text-align: center;">YARD</th>
        <th style="text-align: center;">WARNA</th>
        <th style="text-align: center;">PCS</th>
        <th style="text-align: center;">KETERANGAN</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?=$model->kartuProses->wo->no?></td>
        <td><?=$model->kartuProses->no?></td>
        <td><?=$model->kartuProses->wo->greige->nama_kain?></td>
        <td>
            <?php
            $totalPanjang = 0;
            foreach ($model->kartuProses->trnKartuProsesPrintingItems as $trnKartuProsesPrintingItems) {
                $stockGreige = $trnKartuProsesPrintingItems->stock->toArray();
                $totalPanjang += $stockGreige['panjang_m'];
            }
            echo $formatter->asDecimal($totalPanjang);
            ?>
        </td>
        <td><?=$model->kartuProses->woColor->moColor->color?></td>
        <td><?=$formatter->asDecimal($model->pcs)?></td>
        <td><?=$model->keterangan?></td>
    </tr>
    </tbody>
</table>

<p>&nbsp;</p>

<table>
    <tr>
        <td style="width: 33.3%; text-align: center;">
            TANDA TERIMA,<br><br><br><br><br><br>(<?=$model->penerima?>)
        </td>
        <td style="width: 33.3%; text-align: center;">
            MENGETAHUI,<br><br><br><br><br><br>(<?=$model->mengetahui?>)
        </td>
        <td style="text-align: center;">
            DARI BAGIAN,<br><br><br><br><br><br>(<?=$model->pengirim?>)
        </td>
    </tr>
</table>