<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnPotongStock */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Potong Stock Gudang Jadi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$modelsItem = $model->trnPotongStockItems;
$unit = \common\models\ar\MstGreigeGroup::unitOptions()[$model->stock->unit];

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-potong-stock-view">
    <p>
        <?php if($model->status == $model::STATUS_DRAFT):?>
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Posting', ['posting', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Anda yakin akan memposting item ini?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php else: ?>
            <?php
            $isLokal = ($model->stock->wo && substr($model->stock->wo->no, -1) == 'L');
            $param1 = 1;
            $param2 = $isLokal ? 1 : 0;
            $param3 = 1;
            ?>
            <?= Html::a('<i class="fa fa-qrcode"></i> Print QR Stock Asal (Sisa: ' . Yii::$app->formatter->asDecimal($model->stock->qty) . ' ' . $unit . ')', ['/trn-gudang-jadi/qr', 'id' => $model->stock_id, 'param1' => $param1, 'param2' => $param2, 'param3' => $param3], ['class' => 'btn btn-info', 'target' => '_blank']) ?>
        <?php endif;?>

        <?= Html::a('Add New', ['create'], ['class' => 'btn btn-default']) ?>
    </p>

    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><strong>Informasi Pemotongan & Stock Asal</strong></h3>
                </div>
                <div class="box-body">
                    <?php
                    $stockAsal = $model->stock;
                    $statusAsalLabel = \common\models\ar\TrnGudangJadi::statusOptions()[$stockAsal->status] ?? '-';
                    $statusBadge = $stockAsal->status == \common\models\ar\TrnGudangJadi::STATUS_STOCK ? '<span class="label label-success">' . $statusAsalLabel . '</span>' : '<span class="label label-danger">' . $statusAsalLabel . '</span>';
                    ?>
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'no',
                            'no_urut',
                            [
                                'label' => 'ID Stock Asal',
                                'format' => 'raw',
                                'value' => '<strong>' . $model->stock_id . '</strong>' . ($model->status == $model::STATUS_POSTED ? ' ' . Html::a('<i class="fa fa-qrcode"></i> Print QR', ['/trn-gudang-jadi/qr', 'id' => $model->stock_id, 'param1' => $param1, 'param2' => $param2, 'param3' => $param3], ['class' => 'btn btn-info btn-xs', 'target' => '_blank']) : ''),
                            ],
                            [
                                'label' => 'Nomor Roll / Stock',
                                'value' => $stockAsal->no ?: '-',
                            ],
                            [
                                'label' => 'Nomor WO',
                                'value' => $stockAsal->wo ? $stockAsal->wo->no : '-',
                            ],
                            [
                                'label' => 'Motif / Kain',
                                'value' => ($stockAsal->wo && $stockAsal->wo->greige) ? $stockAsal->wo->greige->nama_kain : '-',
                            ],
                            [
                                'label' => 'Warna',
                                'value' => $stockAsal->color ?: '-',
                            ],
                            [
                                'label' => 'Qty Stock Asal Saat Ini (Sisa)',
                                'format' => 'raw',
                                'value' => '<strong style="color: #0073b7; font-size: 14px;">' . Yii::$app->formatter->asDecimal($stockAsal->qty) . ' ' . $unit . '</strong>',
                            ],
                            [
                                'label' => 'Status Stock Asal',
                                'format' => 'raw',
                                'value' => $statusBadge,
                            ],
                            'diperintahkan_oleh',
                            'note:ntext',
                            'date:date',
                            [
                                'label'=>'Status Dokumen Potong',
                                'value'=>$model::statusOptions()[$model->status]
                            ],
                            'created_at:datetime',
                            'created_by',
                            'updated_at:datetime',
                            'updated_by',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><strong>Roll Baru Hasil Pemotongan</strong></h3>
                    <div class="box-tools pull-right">
                        <span class="label label-primary"><?=count($modelsItem)?> Item</span>
                    </div>
                </div>
                <div class="box-body">
                    <?php
                    $newStocks = [];
                    if ($model->status == $model::STATUS_POSTED) {
                        $newStocks = \common\models\ar\TrnGudangJadi::find()
                            ->where(['like', 'note', 'Potong ID: ' . $model->id])
                            ->andWhere(['hasil_pemotongan' => true])
                            ->orderBy(['id' => SORT_ASC])
                            ->all();
                    }
                    ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">No</th>
                            <th>Qty (<?=$unit?>)</th>
                            <th>ID Stock Baru</th>
                            <?php if ($model->status == $model::STATUS_POSTED): ?>
                                <th style="width: 100px; text-align: center;">QR Code</th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $totalQty = 0; foreach ($modelsItem as $index => $modelItem): ?>
                            <?php
                            $totalQty += $modelItem->qty;
                            $matchedNewStock = $newStocks[$index] ?? null;
                            ?>
                            <tr>
                                <td style="text-align: center; vertical-align: middle;"><strong><?= ($index + 1) ?></strong></td>
                                <td style="vertical-align: middle;"><?= Yii::$app->formatter->asDecimal($modelItem->qty) ?></td>
                                <td style="vertical-align: middle;">
                                    <?php if ($matchedNewStock): ?>
                                        <span class="badge bg-green">ID: <?=$matchedNewStock->id?></span> (<?=$matchedNewStock->no ?: '-'?>)
                                    <?php else: ?>
                                        <span class="text-muted"><em>Menunggu Posting</em></span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($model->status == $model::STATUS_POSTED): ?>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <?php if ($matchedNewStock): ?>
                                            <?= Html::a('<i class="fa fa-qrcode"></i> Print QR', ['/trn-gudang-jadi/qr', 'id' => $matchedNewStock->id, 'param1' => $param1, 'param2' => $param2, 'param3' => $param3], ['class' => 'btn btn-success btn-xs', 'target' => '_blank']) ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach;?>
                        <tr style="background-color: #f9f9f9;">
                            <td><strong>TOTAL</strong></td>
                            <td colspan="<?=$model->status == $model::STATUS_POSTED ? 3 : 2?>"><strong style="color: #00a65a; font-size: 14px;"><?=Yii::$app->formatter->asDecimal($totalQty)?> <?=$unit?></strong></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
