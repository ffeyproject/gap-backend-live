<?php
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnMo;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use yii\helpers\Html;
use yii\i18n\Formatter;

/* @var $this yii\web\View */
/* @var $model TrnMo */
/* @var $scGreige TrnScGreige */
/* @var $sc TrnSc */
/* @var $formatter Formatter */
/* @var $greigeGroup MstGreigeGroup */
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">SC & Greige Info</h3>
    </div>
    <div class="box-body">
        <?='<strong>SC No:</strong> '. Html::a($sc->no, ['trn-sc/view', 'id'=>$sc->id], ['target'=>'blank'])?>

        <table class="table table-bordered">
            <tr>
                <th class="text-center">PROCCESS</th>
                <th class="text-center">MOTIF GREIGE</th>
                <th class="text-center">PER BATCH</th>
                <th class="text-center">QTY (BATCH)</th>
                <th class="text-center">QTY (METER)</th>
                <th class="text-center">FINISH</th>
                <th class="text-center">FINISH (YARD)</th>
            </tr>
            <tr>
                <td><?=$scGreige::processOptions()[$scGreige->process]?> <?=$scGreige::lebarKainOptions()[$scGreige->lebar_kain]?>"</td>
                <td><?=$greigeGroup->nama_kain?></td>
                <td class="text-right"><?=$formatter->asDecimal($greigeGroup->qty_per_batch)?></td>
                <td class="text-right"><?=$formatter->asDecimal($scGreige->qty)?></td>
                <td class="text-right"><?=$formatter->asDecimal($scGreige->qtyBatchToMeter)?></td>
                <td class="text-right"><?=$formatter->asDecimal($scGreige->qtyFinish)?></td>
                <td class="text-right"><?=$formatter->asDecimal($scGreige->qtyFinishToYard)?></td>
            </tr>
        </table>
    </div>
</div>
