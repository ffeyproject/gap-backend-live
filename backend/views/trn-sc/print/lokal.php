<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnSc;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnSc */

$formatter = Yii::$app->formatter;
$bankAccount = $model->bankAcct;
?>

<div class="row">
    <div class="col-xs-12 text-center">
        <h4>KONTRAK PEMESANAN</h4>
        <strong>No. Kontrak: <?=$model->no?></strong>
    </div>
</div>

<br>

<p>Kontrak manufaktur dan pemesanan barang ini dibuat pada tanggal : <?=$formatter->asDate($model->date, 'long')?>.</p>

<p><?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table detail-view small'],
        'template' => '<tr><td style="width: 25%;">{label}</td><th{contentOptions}>: {value}</td></tr>',
        'attributes' => [
            [
                'label' => 'Dengan',
                'value' => $model->customerName
            ],
            [
                'label' => 'Tujuan Pengiriman',
                'value' => $model->destination
            ]
        ],
    ]) ?></p>

<p>Pabrik setuju untuk membuat dan menitipkan barang sebagai berikut :</p>

<table class="table table-bordered small">
    <tr>
        <th class="text-center">Jenis Proses</th>
        <th class="text-center">Item</th>
        <th class="text-center">Merek</th>
        <th class="text-center">Piece Length</th>
        <th class="text-center">Grade</th>
        <th class="text-center">Jumlah</th>
        <th class="text-center">Satuan</th>
        <th class="text-center">Harga Satuan (<?=$model::currencyOptions()[$model->currency]?>)</th>
    </tr>
    <?php
    $trnScGreiges = $model->trnScGreiges;
    ?>

    <?php foreach ($trnScGreiges as $trnScGreige):?>
        <tr>
            <td><?=$trnScGreige::processOptions()[$trnScGreige->process]?> <?=$trnScGreige::lebarKainOptions()[$trnScGreige->lebar_kain]?>"</td>
            <td><?=$trnScGreige->greigeGroup->nama_kain?></td>
            <td><?=$trnScGreige->merek?></td>
            <td style="text-align: center"><?=$trnScGreige->piece_length?></td>
            <td style="text-align: center"><?=$trnScGreige::gradeOptions()[$trnScGreige->grade]?></td>
            <td style="text-align: right">
                <?php
                switch ($trnScGreige->price_param){
                    case $trnScGreige::PRICE_PARAM_PER_YARD:
                        echo $formatter->asDecimal($trnScGreige->qtyFinishToYard);
                        break;
                    default:
                        echo $formatter->asDecimal($trnScGreige->qtyFinish);
                }
                ?>
            </td>
            <td style="text-align: center">
                <?php
                switch ($trnScGreige->price_param){
                    case $trnScGreige::PRICE_PARAM_PER_METER:
                        echo 'Meter';
                        break;
                    case $trnScGreige::PRICE_PARAM_PER_KILOGRAM:
                        echo 'Kilogram';
                        break;
                    case $trnScGreige::PRICE_PARAM_PER_YARD:
                        echo 'Yard';
                        break;
                    default:
                        echo '-';
                }
                ?>
            </td>
            <td style="text-align: right"><?=Yii::$app->formatter->asDecimal($trnScGreige->unit_price)?></td>
        </tr>
    <?php endforeach;?>
</table>

<strong>Catatan:</strong>
<?=$model->note?>

<p><strong>Ketentuan-Ketentuan:</strong></p>

<p>
    <strong>Grade</strong><br>
    Pabrik setuju memberikan diskon sebesar <?=Yii::$app->formatter->asDecimal($model->disc_grade_b)?>% apabila ada grade B yang dikirimkan kepada pemesan.
</p>

<p>
    <strong>Piece Kecil</strong><br>
    Pabrik setuju memberikan diskon sebesar <?=Yii::$app->formatter->asDecimal($model->disc_piece_kecil)?>% apabila ada piece kecil yang dikirimkan kepada pemesan.
</p>

<p>
    <strong>Pembayaran</strong><br>
    Pemesan setuju untuk melakukan pembayaran selambat-lambatnya <?=$model->pmt_term?> hari sejak barang titipan diterima.
</p>

<strong>Komplain/ Klaim</strong><br>
<ol>
    <li>Batas waktu komplain / klaim adalah maksimal 14 hari dari tanggal surat jalan.</li>
    <li>Komplain / Klaim Tidak Diterima apabila kain Sudah Dipotong.</li>
</ol>

<strong>Pembatalan Kontrak & Denda</strong><br>
Kontrak dinyatakan <strong>BATAL</strong> :
<ol>
    <li>Apabila pemesan tidak menurunkan order / memberikan keputusan mengenai warna, design, handling dan asesoris dalam waktu 30 hari.</li>
    <li>
        Setiap pembatalan kontrak akan dikenakan DENDA dengan ketentuan :
        <ul>
            <li>10% = Apabila benang sudah ditenun.</li>
            <li>20% = Apabila greige sudah siap.</li>
            <li>30% = Apabila kain sudah siap dikirim.</li>
        </ul>
    </li>
</ol>

<p>
    <strong>Pengiriman</strong><br>
    Ongkos angkut pengiriman sepenuhnya ditanggung oleh pembeli.
</p>

<p>
    <strong>Ketentuan tambahan</strong><br>
    Kontrak ini harus ditandatangani, apabila tidak ditandatangani maka kontrak ini dinyatakan <strong>TIDAK BERLAKU</strong>.
</p>

<table class="table">
    <tr>
        <td style="width: 33.3%; text-align: center;">
            Pemesan,<br><br><br><br><br><br>(<?=$model->customerName?>)
        </td>
        <td style="width: 33.3%; text-align: center;">
            Manager,<br>
            <?= Html::img($model->manager->signatureUrl, ['style'=>'height:100px;'])?><br>
            (<?=$model->managerName?>)
        </td>
        <td style="text-align: center;">
            Marketing,<br>
            <?= Html::img($model->marketing->signatureUrl, ['style'=>'height:100px;'])?><br>
            (<?=$model->marketingName?>)
        </td>
    </tr>
</table>