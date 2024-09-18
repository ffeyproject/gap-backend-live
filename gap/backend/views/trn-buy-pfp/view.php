<?php
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnBuyPfp */

$this->title = 'Masuk PFP: '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Masuk PFP', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

$modelsItem = $model->trnBuyPfpItems;
$unit = $model->greigeGroup->unitName;
?>
<div class="trn-buy-pfp-view">

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
        <div class="col-md-8">
            <div class="box">
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            [
                                'attribute'=>'greigeGroupName',
                                'label'=>'Greige Group'
                            ],
                            [
                                'attribute'=>'greigeName',
                                'label'=>'Greige'
                            ],
                            'jenisName',
                            'no_referensi',
                            'no_document',
                            'vendor',
                            'color',
                            'note:ntext',
                            'date:date',
                            [
                                'attribute'=>'statusName',
                                'label'=>'Status'
                            ],
                            'created_at:datetime',
                            'created_by',
                            'updated_at:datetime',
                            'updated_by',
                            'approval_id',
                            'approval_time:datetime',
                            'reject_note:ntext',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
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
                        </tr>
                        </thead>
                        <tbody>
                        <?php $totalPanjang = 0; foreach ($modelsItem as $index => $modelItem): ?>
                            <?php
                            $totalPanjang += $modelItem->panjang_m;
                            ?>
                            <tr>
                                <td><span><?= ($index + 1) ?></span></td>
                                <td><?= Yii::$app->formatter->asDecimal($modelItem->panjang_m) ?></td>
                            </tr>
                        <?php endforeach;?>
                        <tr>
                            <td><strong>TOTAL (<?=$unit?>)</strong></td>
                            <td><strong><?=Yii::$app->formatter->asDecimal($totalPanjang)?></strong></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
