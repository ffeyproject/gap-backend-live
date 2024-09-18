<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnSc;
use common\models\ar\TrnScAgen;
use common\models\ar\TrnScGreige;
use yii\i18n\Formatter;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnScAgen */
/* @var $formatter Formatter */
/* @var $sc TrnSc */
?>

<div class="row">
    <div class="col-xs-12 text-center">
        <h4>PT. GAJAH ANGKASA PERKASA</h4>
        <strong>No. 823 Jl. Jend. Sudirman, Bandung 40213 <br>Phone: (022) 6031030 (4 Lines), Fax: 6030849</strong>
    </div>
</div>

<div class="row">
    <div class="col-xs-6">
        <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table detail-view small'],
            'template' => '<tr><th style="width: 25%;">{label}</th><td{contentOptions}>: {value}</td></tr>',
            'attributes' => [
                [
                    'label' => 'NAME',
                    'attribute' => 'nama_agen'
                ],
                [
                    'label' => 'ATTN',
                    'attribute' => 'attention'
                ],
                [
                    'label' => 'RE',
                    'value' => 'TRADE DISCOUNT'
                ]
            ],
        ]) ?>
    </div>

    <div class="col-xs-6">
        <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table detail-view small'],
            'template' => '<tr><th style="width: 25%;">{label}</th><td{contentOptions}>: {value}</td></tr>',
            'attributes' => [
                [
                    'label' => 'DATE',
                    'value' => $formatter->asDate($model->sc->date, 'long')
                ],
                [
                    'label' => 'SP NO',
                    'value' => $model->no
                ]
            ],
        ]) ?>
    </div>
</div>

<p class="text-center"><strong>LETTER OF AGREEMENT</strong></p>

<p>DEAR SIRS,<br>WE AGREE THAT YOUR TRADE DISCOUNT IS</p>

<table class="table table-bordered small">
    <tr>
        <th rowspan="2" class="text-center" style="vertical-align: middle;">DATE SC</th>
        <th rowspan="2" class="text-center" style="vertical-align: middle;">SC NO</th>
        <th colspan="3" class="text-center" style="vertical-align: middle;">DESCRIPTION</th>
        <th rowspan="2" colspan="2" class="text-center" style="vertical-align: middle;">QTY</th>
        <th rowspan="2" class="text-center" style="vertical-align: middle;">PRICE (<?=$sc::currencyOptions()[$sc->currency]?>)</th>
        <th rowspan="2" class="text-center" style="vertical-align: middle;">C</th>
        <th rowspan="2" class="text-center" style="vertical-align: middle;">AMOUNT (<?=$sc::currencyOptions()[$sc->currency]?>)</th>
    </tr>
    <tr>
        <th>PROCCESS</th>
        <th>ITEM</th>
        <th>FEATURE</th>
    </tr>
    <?php foreach ($model->trnScKomisis as $komisi):?>
        <tr>
            <td><?=$sc->date?></td>
            <td><?=$sc->no?></td>
            <td><?=TrnScGreige::processOptions()[$komisi->scGreige->process]?></td>
            <td><?=$komisi->scGreige->greigeGroup->nama_kain?></td>
            <td class="text-center"><?=TrnScGreige::lebarKainOptions()[$komisi->scGreige->lebar_kain].'"'?></td>
            <td class="text-right">
                <?php
                echo $formatter->asDecimal($komisi->scGreige->qtyFinish);
                ?>
            </td>
            <td class="text-center">M</td>
            <td  class="text-right"><?=$formatter->asDecimal($komisi->scGreige->totalPrice)?></td>
            <td class="text-center">
                <?php
                switch ($komisi->tipe_komisi){
                    case $komisi::TIPE_KOMISI_PERSENTASE:
                        echo $formatter->asDecimal($komisi->komisi_amount).' %';
                        break;
                    default:
                        echo $sc->currencyName.' '.$formatter->asDecimal($komisi->komisi_amount);
                }
                ?>
            </td>
            <td class="text-right"><?=$formatter->asDecimal($komisi->komisiTotal)?></td>
        </tr>
    <?php endforeach;?>
</table>

<table class="table">
    <tr>
        <td style="width: 33.3%; text-align: center;">
            AGENT,<br><br><br><br><br><br>(<?=$model->attention?>)
        </td>
        <td style="width: 33.3%; text-align: center;">
            MENGETAHUI,<br><br><br><br><br><br>(<?=$sc->managerName?>)
        </td>
        <td style="text-align: center;">
            MARKETING,<br><br><br><br><br><br>(<?=$sc->marketingName?>)
        </td>
    </tr>
</table>