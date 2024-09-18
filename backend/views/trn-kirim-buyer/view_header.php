<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadi;
use kartik\dialog\Dialog;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimBuyer */
/* @var $formatter \yii\i18n\Formatter*/
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">DETAIL</h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>BUYER</th>
                <th>NOMOR SC</th>
                <th>GREIGE GROUP</th>
                <th>NOMOR MO</th>
                <th>NOMOR WO</th>
                <th>NOMOR DOKUMEN
                <th>TANGGAL</th>
                <th>NAMA KAIN PABRIK / BUYER</th>
                <th>UNIT</th>
                <th>PENERIMA</th>
                <th>STATUS</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?=$model->sc->cust->name?></td>
                <td><?=$model->sc->no?></td>
                <td><?=$model->scGreige->greigeGroup->nama_kain?></td>
                <td><?=$model->mo->no?></td>
                <td><?=$model->wo->no?></td>
                <td><?=$formatter->asText($model->no)?></td>
                <td><?=$formatter->asDate($model->date)?></td>
                <td><?=$model->wo->greige->nama_kain?> / <?=$model->nama_kain_alias?></td>
                <td><?=\common\models\ar\MstGreigeGroup::unitOptions()[$model->unit]?></td>
                <td><?=$model->penerima?></td>
                <td><?=$model::statusOptions()[$model->status]?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
