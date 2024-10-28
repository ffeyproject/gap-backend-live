<?php

use yii\helpers\Html;
use common\models\ar\TrnKirimBuyer;
$formatter = Yii::$app->formatter;
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMoSearch */
?>

<div class="rekap-total">
    <?php   if(!empty($searchModel->dateRange)){    ?>

    <?php
    ?>
    <div class="box">
        <div class="box-header">
            <div class="box-title">Rekap Total Pengiriman</div>
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
                        <td style="padding: 0px 5px 0px 5px;">Total Qty</td>
                        <td style="padding: 0px 5px 0px 5px;">:</td>
                        <td style="padding: 0px 5px 0px 5px;"><?= $totalQty?> Yard</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>    
    <?php } ?>

</div>
