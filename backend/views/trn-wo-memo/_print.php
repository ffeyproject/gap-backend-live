<?php

use common\models\ar\TrnWo;
use common\models\ar\TrnWoMemo;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnWoMemo */
/* @var $wo TrnWo */

$mo = $wo->mo;
?>

<div class="row">
    <div class="col-xs-4">
        <strong>PT. GAJAH ANGKASA PERKASA</strong>
    </div>

    <div class="col-xs-4">&nbsp;</div>

    <div class="col-xs-4" style="text-align: right;">
        <strong>NO: <?=$model->no?></strong>
    </div>
</div>

<br>

<div class="row">
    <div class="col-xs-12 text-center">
        <strong>MEMO PEMBERITAHUAN</strong>
    </div>
</div>

<br>

<p><?=$model->memo?></p>

<br>

<div class="row">
    <div class="col-xs-4 text-center">
        Marketing<br><br><br>
        <?=$wo->marketingName?>
    </div>

    <div class="col-xs-4 text-center">
        Mengetahui<br><br><br>
        <?php //echo $wo->mengetahuiName;?>
    </div>

    <div class="col-xs-4 text-center">
        Bandung, <?=Yii::$app->formatter->asDate($model->created_at)?><br>Mengetahui,<br><br>
        <?php //echo $wo->mengetahuiName;?>
    </div>
</div>