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
        <?php endif;?>

        <?= Html::a('Add New', ['create'], ['class' => 'btn btn-default']) ?>
    </p>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <!--<h3 class="box-title"><strong></strong></h3>
                    <div class="box-tools pull-right"></div>-->
                </div>
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'stock_id',
                            'no_urut',
                            'no',
                            'note:ntext',
                            'date:date',
                            'diperintahkan_oleh',
                            [
                                'label'=>'Status',
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
                            <th>Qty (<?=$unit?>)</th>
                            <th>Stock ID Gudang Jadi</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $totalQty = 0; foreach ($modelsItem as $index => $modelItem): ?>
                            <?php
                            $totalQty += $modelItem->qty;
                            ?>
                            <tr>
                                <td><span><?= ($index + 1) ?></span></td>
                                <td><?= Yii::$app->formatter->asDecimal($modelItem->qty) ?></td>
                                <td><?=$modelItem->potongStock->stock_id?></td>
                            </tr>
                        <?php endforeach;?>
                        <tr>
                            <td><strong>TOTAL (<?=$unit?>)</strong></td>
                            <td><strong><?=Yii::$app->formatter->asDecimal($totalQty)?></strong></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
