<?php
/* @var $this yii\web\View */

use common\models\ar\TrnMo;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;

/* @var $model TrnMo */
/* @var $scGreige TrnScGreige */
/* @var $sc TrnSc */

$scGreigeGroup = $scGreige->greigeGroup;
$formatter = Yii::$app->formatter;
?>

<table class="table table-bordered small" style="page-break-inside:avoid">
    <tr>
        <th class="text-center" rowspan="2" style="vertical-align: middle;">MOTIF GREIGE</th>
        <th class="text-center" rowspan="2" style="vertical-align: middle;">ARTICLE</th>
        <th class="text-center" rowspan="2" style="vertical-align: middle;">COLOR</th>
        <th class="text-center" rowspan="2" style="vertical-align: middle;">QTY (BATCH)</th>
        <th class="text-center" colspan="3" style="vertical-align: middle;">QUANTITY</th>
    </tr>
    <tr>
        <th class="text-center" style="vertical-align: middle;">GREIGE (<?=$scGreigeGroup::unitOptions()[$scGreigeGroup->unit]?>)</th>
        <th class="text-center" style="vertical-align: middle;">FINISH (METER)</th>
        <th class="text-center" style="vertical-align: middle;">FINISH (YARD)</th>
    </tr>
    <?php
    $totalQty = 0;
    $totalQtyBatchToUnit = 0;
    $totalQtyFinishMeter = 0;
    $totalQtyFinishToYard = 0;

    $i = 0;
    ?>

    <?php foreach ($model->trnMoColors as $color):?>
        <tr>
            <td><?=$scGreigeGroup->nama_kain?></td>
            <td><?=$model->article?></td>
            <td><?=$color->color?></td>
            <td class="text-right">
                <?php
                $qty = floatval($color->qty);
                $totalQty += $qty;
                echo $formatter->asDecimal($qty);
                ?>
            </td>
            <td class="text-right">
                <?php
                $qtyBatchToUnit = $color->getQtyBatchToUnit();
                $totalQtyBatchToUnit += $qtyBatchToUnit;
                echo $formatter->asDecimal($qtyBatchToUnit);
                ?>
            </td>
            <td class="text-right">
                <?php
                $qtyFinishMeter = $color->getQtyFinishToMeter();
                $totalQtyFinishMeter += $qtyFinishMeter;
                echo $formatter->asDecimal($qtyFinishMeter);
                ?>
            </td>
            <td class="text-right">
                <?php
                $qtyFinishToYard = $color->getQtyFinishToYard();
                $totalQtyFinishToYard += $qtyFinishToYard;
                echo $formatter->asDecimal($qtyFinishToYard);
                ?>
            </td>
        </tr>
    <?php endforeach;?>
    <tfoot>
    <tr>
        <th class="text-right" colspan="3">TOTAL</th>
        <th class="text-right"><?=$formatter->asDecimal($totalQty)?></th>
        <th class="text-right"><?=$formatter->asDecimal($totalQtyBatchToUnit)?></th>
        <th class="text-right"><?=$formatter->asDecimal($totalQtyFinishMeter)?></th>
        <th class="text-right"><?=$formatter->asDecimal($totalQtyFinishToYard)?></th>
    </tr>
    </tfoot>
</table>
