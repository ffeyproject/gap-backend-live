<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\dialog\Dialog;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MutasiPfp */

$this->title = 'Mutasi PFP: ' . $model->no;
$this->params['breadcrumbs'][] = ['label' => 'Penerimaan Mutasi PFP', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="penerimaan-mutasi-pfp-view">

    <p>
        <?php if($model->status == $model::STATUS_POSTED):?>
            <?= Html::a('<i class="fa fa-check"></i> Terima', ['terima', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'data' => [
                    'confirm' => 'Are you sure you want to terima this item?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('<i class="fa fa-close"></i> Tolak', ['tolak', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to tolak this item?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif;?>
    </p>

    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Detail Mutasi PFP</h3>
                </div>
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'no',
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
                            'date:date',
                            'note:ntext',
                            'reject_note:ntext',
                            [
                                'attribute'=>'status',
                                'value'=>$model->statusName
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">System Info</h3>
                </div>
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'created_at:datetime',
                            'created_by',
                            'updated_at:datetime',
                            'updated_by',
                            'approval_id',
                            'approval_time:datetime',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Daftar Item Roll PFP</h3>
                    <div class="box-tools pull-right">
                        <span class="label label-primary"><?=count($model->mutasiPfpItems)?> Items</span>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Kain</th>
                            <th class="text-right">Panjang (M)</th>
                            <th class="text-center">Grade</th>
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
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td><?= $stock->greige->nama_kain ?></td>
                                <td class="text-right"><?= Yii::$app->formatter->asDecimal($stock->panjang_m) ?></td>
                                <td class="text-center"><?= $stock::gradeOptions()[$stock->grade] ?></td>
                                <td><?= $stock->color ?></td>
                                <td><?= $stock->lot_lusi ?> - <?= $stock->lot_pakan ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                        <tr class="info">
                            <th colspan="2" class="text-right">TOTAL</th>
                            <th class="text-right"><?= Yii::$app->formatter->asDecimal($totalQty) ?></th>
                            <th colspan="3"></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
