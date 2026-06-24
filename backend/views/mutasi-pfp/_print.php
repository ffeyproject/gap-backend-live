<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MutasiPfp */
?>

<div style="text-align: center; margin-bottom: 20px;">
    <h3 style="margin: 0;">MUTASI PFP</h3>
    <p style="margin: 0; font-size: 14px;">Nomor: <?= $model->no ?></p>
</div>

<table>
    <tr>
        <td style="width: 50%;">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'no_urut',
                    'no_wo',
                    [
                        'label' => 'Greige Group',
                        'value' => $model->greigeGroup->nama_kain
                    ],
                    [
                        'label' => 'Greige',
                        'value' => $model->greige->nama_kain
                    ],
                    'note:ntext',
                ],
            ]) ?>
        </td>
        <td style="width: 50%;">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'date:date',
                    [
                        'label'=>'Dibuat Oleh',
                        'value'=>$model->createdBy->full_name
                    ],
                    'created_at:datetime',
                ],
            ]) ?>
        </td>
    </tr>
</table>

<br>

<h4 style="margin-bottom: 5px;">Daftar Item Roll PFP</h4>
<table border="1" style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Kain</th>
            <th>Panjang (M)</th>
            <th>Grade</th>
            <th>Warna</th>
            <th>No Lot</th>
        </tr>
    </thead>
    <tbody>
        <?php $totalQty = 0; foreach($model->mutasiPfpItems as $index => $item): ?>
            <?php 
                $stock = $item->stockPfp; 
                $totalQty += $stock->panjang_m;
            ?>
            <tr>
                <td style="text-align: center;"><?= $index + 1 ?></td>
                <td><?= $stock->greige->nama_kain ?></td>
                <td style="text-align: right;"><?= Yii::$app->formatter->asDecimal($stock->panjang_m) ?></td>
                <td style="text-align: center;"><?= $stock::gradeOptions()[$stock->grade] ?></td>
                <td><?= $stock->color ?></td>
                <td><?= $stock->lot_lusi ?> - <?= $stock->lot_pakan ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" style="text-align: right;">TOTAL</th>
            <th style="text-align: right;"><?= Yii::$app->formatter->asDecimal($totalQty) ?></th>
            <th colspan="3"></th>
        </tr>
    </tfoot>
</table>

<br><br>

<table style="width: 100%;">
    <tr>
        <td style="width: 30%;" class="text-center">
            <p>Dibuat Oleh</p>
            <p style="height: 80px;"></p>
            <p><?=$model->createdBy->full_name?></p>
        </td>
    </tr>
</table>
