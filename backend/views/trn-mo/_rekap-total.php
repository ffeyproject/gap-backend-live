<?php

use yii\helpers\Html;
$formatter = Yii::$app->formatter;
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMoSearch */
?>

<div class="rekap-total">
    <?php   if(!empty($searchModel->dateRange)){    ?>

    <?php
        $totalFinishMeter  =  0;
        $totalFinishYard  =  0;
        $dataProvider->pagination = false;
        $model = $dataProvider->getModels();

        foreach ($model as $key => $value) {
            $meter = 0;
            $yard = 0;
            $colors = $value->trnMoColors;
            foreach ($colors as $key => $color) {
                $meter += $color->qtyFinishToMeter;
                $yard += $color->qtyFinishToYard;
            }
            $totalFinishMeter += $meter;
            $totalFinishYard += $yard;
        }
    ?>
    <div class="box">
        <div class="box-header">
            <div class="box-title">Rekap Turun Order</div>
        </div>
        <div class="box-body">
            <table border="1">
                <thead>
                    <tr>
                        <th colspan="3" class="text-center bg-gray" style="padding: 0px 5px 0px 5px;">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 0px 5px 0px 5px;">Total Finish Meter</td>
                        <td style="padding: 0px 5px 0px 5px;">:</td>
                        <td style="padding: 0px 5px 0px 5px;"><?= $formatter->asDecimal($totalFinishMeter); ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 0px 5px 0px 5px;">Total Finish Yard</td>
                        <td style="padding: 0px 5px 0px 5px;">:</td>
                        <td style="padding: 0px 5px 0px 5px;"><?= $formatter->asDecimal($totalFinishYard); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>    
    <?php } ?>

</div>
