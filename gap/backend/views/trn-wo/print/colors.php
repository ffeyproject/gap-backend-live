<?php

use common\models\ar\MstGreige;
use common\models\ar\TrnWo;
use common\models\ar\TrnWoColor;

/* @var $this yii\web\View */
/* @var $model TrnWo */
/* @var $unitMeter string */
/* @var $greige MstGreige */
/* @var $colors TrnWoColor[] */

$formatter = Yii::$app->formatter;
$satuan = \common\models\ar\MstGreigeGroup::unitOptions()[$greige->group->unit];
?>

<table class="table table-bordered small" style="page-break-inside:avoid">
    <tr>
        <th class="text-center" style="vertical-align: middle;">No</th>
        <th class="text-center" style="vertical-align: middle;">Warna</th>
        <th class="text-center" style="vertical-align: middle;">Qty (Batch)</th>
        <th class="text-center" style="vertical-align: middle;">Greige (<?=$satuan?>)</th>
        <th class="text-center" style="vertical-align: middle;">Finish</th>
        <th class="text-center" style="vertical-align: middle;">Keterangan</th>
    </tr>

    <?php $summaryFinish = 0; $summaryFinishYard = 0; $i=1; foreach ($colors as $color):?>
        <tr>
            <td class="text-center" style="padding: 3px;"><?=$i;?></td>
            <td style="padding: 3px;"><?=$color->moColor->color?></td>
            <td class="text-right" style="padding: 3px;">
                <?=$formatter->asDecimal($color->qty)?>
            </td>
            <td class="text-right" style="padding: 3px;"><?=$formatter->asDecimal($color->qtyBatchToUnit)?></td>
            <td class="text-right" style="padding: 3px;">
                <?php
                $qtyFinish = $color->getQtyFinishToMeter();
                $summaryFinish += $qtyFinish;
                $qtyFinishYard = $color->qtyFinishToYard;
                $summaryFinishYard += $qtyFinishYard;
                $string = $formatter->asDecimal($qtyFinish).' M'.' ('.$formatter->asDecimal($qtyFinishYard).' Yard)';
                echo $string;
                ?>
            </td>
            <td style="padding: 3px;"><?=$color->note?></td>
        </tr>
        <?php $i++; endforeach;?>

    <?php

    ?>
    <tr>
        <td colspan="4" style="padding: 3px;"><strong>TOTAL</strong></td>
        <td class="text-right" style="padding: 3px;">
            <?php
            $string = $formatter->asDecimal($summaryFinish).' M'.' ('.$formatter->asDecimal($summaryFinishYard).' Yard)';
            echo $string;
            ?>
        </td>
        <td style="padding: 3px;">&nbsp;</td>
    </tr>
</table>
