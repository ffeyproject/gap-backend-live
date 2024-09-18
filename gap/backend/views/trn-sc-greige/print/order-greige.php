<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnScGreige;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $model TrnScGreige*/

$sc = $model->sc;
?>

    <div class="row">
        <div class="col-xs-12 text-center">
            <h4>FORM ORDER GREIGE - PT. GAP</h4>
            <strong>No. Order: <?=$model->no_order_greige?></strong>
        </div>
    </div>

    <p><?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table detail-view small'],
            'template' => '<tr><th style="width: 20%;">{label}</th><th style="width: 2%;">:</th><td{contentOptions}>{value}</td></tr>',
            'attributes' => [
                [
                    'label' => 'Comodity',
                    'value' => $model->greigeGroup->nama_kain
                ],
                [
                    'label' => 'Jenis',
                    'value' => $model::processOptions()[$model->process]
                ],
                [
                    'label' => 'Quantity',
                    'value' => Yii::$app->formatter->asDecimal($model->qtyFinish).' '.MstGreigeGroup::unitOptions()[$model->greigeGroup->unit].' / '.Yii::$app->formatter->asDecimal($model->qtyFinishToYard).' Yard'
                ],
                [
                    'label' => 'Lebar Kain',
                    'value' => $model::lebarKainOptions()[$model->lebar_kain].'"'
                ],
                [
                    'label' => 'Woven Selvedge',
                    'value' => $model->woven_selvedge
                ],
                [
                    'label' => 'P/L',
                    'value' => ''
                ],
                [
                    'label' => 'Buyer',
                    'value' => $sc->cust->name
                ],
                [
                    'label' => 'Shipment',
                    'value' => ''
                ],
                [
                    'label' => 'Destination',
                    'value' => $sc->destination
                ],
            ],
        ]) ?></p>

    <p style="text-align: right">Bandung, <?=Yii::$app->formatter->asDate($sc->date, 'long')?></p>

    <table class="table">
        <tr>
            <td style="width: 25%; text-align: center;">
                Mengetahui,<br>
                <?= $model->order_grege_approved_dir ? Html::img($sc->direktur->signatureUrl, ['style'=>'height:100px;']) : '<br><br><br><br><br>'?><br>
                (<?=$model->order_grege_approved_dir ? $sc->direkturName : 'Belum Disetujui DIR'?>)
            </td>
            <td style="width: 25%; text-align: center;">
                Mengetahui,<br>
                <?= Html::img($sc->manager->signatureUrl, ['style'=>'height:100px;'])?><br>
                (<?=$sc->managerName?>)
            </td>
            <td style="width: 25%; text-align: center;">
                Marketing,<br>
                <?= Html::img($sc->marketing->signatureUrl, ['style'=>'height:100px;'])?><br>
                (<?=$sc->marketingName?>)
            </td>
            <td style="width: 25%; text-align: center;">
                PMC,<br>
                <?= $model->kabagPmc !== null ? Html::img($model->kabagPmc->signatureUrl, ['style'=>'height:100px;']) : '<br><br><br><br><br>'?><br>
                (<?=$model->kabagPmc !== null ? $model->kabagPmc->full_name : 'Belum Disetujui'?>)
            </td>
        </tr>
    </table>

    <br>

    <strong>Keterangan:</strong>
<br>
    <?=$model->sc->note?>