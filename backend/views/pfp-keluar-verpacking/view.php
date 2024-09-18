<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\PfpKeluarVerpacking */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pfp Keluar Verpackings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

$modelsItem = $model->pfpKeluarVerpackingItems;
?>
<div class="pfp-keluar-verpacking-view">

    <p>
        <?php if ($model->status == $model::STATUS_DRAFT):?>
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
                    'confirm' => 'Are you sure you want to posting this item?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif;?>
    </p>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            //'no_urut',
                            'no',
                            'pfpKeluarNo',
                            'jenisName',
                            'greigeNamaKain',
                            'satuanName',
                            'tgl_kirim:date',
                            'tgl_inspect:date',
                            'note:ntext',
                            'woNo',
                            'statusName',
                            'created_at:datetime',
                            'created_by',
                            'updated_at:datetime',
                            'updated_by',
                        ],
                    ]) ?>
                </div>
            </div>

            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Vendor</h3>
                </div>

                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'send_to_vendor:boolean',
                            'vendorName',
                            'vendor_address:ntext',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Items</h3>
                    <div class="box-tools pull-right">
                        <span class="label label-primary"><?=count($modelsItem)?></span>
                    </div>
                </div>

                <div class="box-body">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Ukuran (<?=$model->satuanName?>)</th>
                            <th>Join Piece</th>
                            <th>Keterangan</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $totalUkuran = 0; foreach ($modelsItem as $index => $modelItem): ?>
                            <?php
                            $totalUkuran += $modelItem->ukuran;
                            ?>
                            <tr>
                                <td><span><?= ($index + 1) ?></span></td>
                                <td><?= Yii::$app->formatter->asDecimal($modelItem->ukuran) ?></td>
                                <td><?= $modelItem->join_piece ?></td>
                                <td><?=$modelItem->keterangan?></td>
                            </tr>
                        <?php endforeach;?>
                        <tr>
                            <td><strong>TOTAL (<?=$model->satuanName?>)</strong></td>
                            <td><strong><?=Yii::$app->formatter->asDecimal($totalUkuran)?></strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
