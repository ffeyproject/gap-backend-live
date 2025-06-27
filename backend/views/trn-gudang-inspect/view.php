<?php
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnBuyGreige */

$this->title = 'Packing List Gudang Inspect: '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Beli Greige Jadi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

$modelsItem = $model->getTrnGudangInspectItems()->orderBy('id')->all();
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
        <?php if ($model->status == $model::STATUS_POSTED):?>
        <?= Html::a('Batal Posting', ['batal-posting', 'id' => $model->id], [
                'class' => 'btn btn-danger',
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
                        'date:date',
                        'no_document',
                        'no_lapak',
                        'operator',
                        [
                            'attribute'=>'status_tsd',
                            'value'=>function($data){
                                /* @var $data TrnStockGreige*/
                                return $data::tsdOptions()[$data->status_tsd];
                            }
                        ],
                        [
                            'label'=>'Greige',
                            'attribute'=>'greigeNamaKain',
                        ],
                        // [
                        //     'attribute'=>'grade',
                        //     'value'=>function($data){
                        //         /* @var $data TrnStockGreige*/
                        //         return $data::gradeOptions()[$data->grade];
                        //     },
                        //     'filterType' => GridView::FILTER_SELECT2,
                        //     'filterWidgetOptions' => [
                        //         'data' => TrnStockGreige::gradeOptions(),
                        //         'options' => ['placeholder' => '...'],
                        //         'pluginOptions' => [
                        //             'allowClear' => true
                        //         ],
                        //     ],
                        // ],
                        'lot_lusi',
                        'lot_pakan',
                        [
                            'attribute'=>'status',
                            'value'=>function($data){
                                /* @var $data TrnStockGreige*/
                                return $data::statusOptions()[$data->status];
                            },
                        ],
                        [
                            'attribute'=>'asal_greige',
                            'value'=>function($data){
                                /* @var $data TrnStockGreige*/
                                return $data::asalGreigeOptions()[$data->asal_greige];
                            },
                        ],
                        [
                            'attribute'=>'jenis_beli',
                            'value'=>function($data){
                                /* @var $data TrnStockGreige*/
                                return $data->jenis_beli === null ? '-' : $data::jenisBeliOptions()[$data->jenis_beli];
                            },
                        ],
                        /*[
                            'attribute'=>'jenis_gudang',
                            'value'=>'jenisGudangName',
                            'filterType' => GridView::FILTER_SELECT2,
                            'filterWidgetOptions' => [
                                'data' => TrnStockGreige::jenisGudangOptions(),
                                'options' => ['placeholder' => '...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ],
                        ],*/
                        'is_pemotongan:boolean',
                        'is_hasil_mix:boolean',
                        //'pengirim',
                        //'mengetahui',
                        //'note:ntext',
                        //'created_at',
                        //'created_by',
                        //'updated_at',
                        //'updated_by',
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
                                <th>Qty (<?= $unit ?>)</th>
                                <th>Grade</th>
                                <th>No Mesin</th>
                                <th>Ket Defect</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $totalQty = 0; foreach ($modelsItem as $index => $modelItem): ?>
                            <?php $totalQty += $modelItem->panjang_m; ?>
                            <tr>
                                <td class="<?php if($modelItem->is_out) echo 'text-danger'; ?>"><?= ($index + 1) ?></td>
                                <td class="<?php if($modelItem->is_out) echo 'text-danger'; ?>">
                                    <?= Yii::$app->formatter->asDecimal($modelItem->panjang_m) ?></td>
                                <td class="<?php if($modelItem->is_out) echo 'text-danger'; ?>">
                                    <?= $modelItem::gradeOptions()[$modelItem->grade] ?? 'Unknown' ?></td>
                                <td class="<?php if($modelItem->is_out) echo 'text-danger'; ?>">
                                    <?= $modelItem->no_set_lusi ?></td>
                                <td class="<?php if($modelItem->is_out) echo 'text-danger'; ?>">
                                    <?= $modelItem->ket_defect ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="2"><strong>TOTAL (<?= $unit ?>)</strong></td>
                                <td><strong><?= Yii::$app->formatter->asDecimal($totalQty) ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>