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
                    <?php use yii\helpers\ArrayHelper; use yii\widgets\ActiveForm; ?>

                    <?php $form = ActiveForm::begin([
                        'action' => ['transfer-to-greige', 'id' => $model->id],
                        'method' => 'post',
                    ]); ?>

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center">
                                <?php if($model->status == $model::STATUS_POSTED):?>
                                    <?= Html::checkbox('check_all', false, [
                                        'class' => 'check-all',
                                        'label' => null,
                                    ]) ?>
                                <?php endif;?>
                            </th>
                            <th>No</th>
                            <th>Qty (<?= $unit ?>)</th>
                            <th>Grade</th>
                            <th>No Mesin</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $totalQty = 0; foreach ($modelsItem as $index => $modelItem): ?>
                            <?php $totalQty += $modelItem->panjang_m; ?>
                            <tr>
                                <td class="text-center">
                                    <?php if(!$modelItem->is_out && $model->status == $model::STATUS_POSTED):?>
                                        <?= Html::checkbox('selected_items[]', false, [
                                            'value' => $modelItem->id,
                                            'class' => 'item-checkbox',
                                            'data-qty' => $modelItem->panjang_m,
                                        ]) ?>
                                    <?php endif;?>
                                </td>
                                <td class="<?php if($modelItem->is_out) echo 'text-danger'; ?>"><?= ($index + 1) ?></td>
                                <td class="<?php if($modelItem->is_out) echo 'text-danger'; ?>"><?= Yii::$app->formatter->asDecimal($modelItem->panjang_m) ?></td>
                                <td class="<?php if($modelItem->is_out) echo 'text-danger'; ?>"><?= $modelItem::gradeOptions()[$modelItem->grade] ?? 'Unknown' ?></td>
                                <td class="<?php if($modelItem->is_out) echo 'text-danger'; ?>"><?= $modelItem->no_set_lusi ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="2"><strong>TOTAL (<?= $unit ?>)</strong></td>
                            <td><strong><?= Yii::$app->formatter->asDecimal($totalQty) ?></strong></td>
                        </tr>
                        <?php if($model->status == $model::STATUS_POSTED):?>
                            <tr>
                                <td colspan="2"><strong>TOTAL TERPILIH (<?= $unit ?>)</strong></td>
                                <td><strong id="selected-total">0</strong></td>
                            </tr>
                        <?php endif;?>
                        </tbody>
                    </table>
                    <?php if($model->status == $model::STATUS_POSTED):?>
                        <div style="margin-top: 15px;">
                            <?= Html::submitButton('Terima ke Gudang Greige', [
                                'class' => 'btn btn-success',
                                'data' => [
                                    'confirm' => 'Yakin ingin transfer item ke gudang greige?',
                                ],
                            ]) ?>
                        </div>
                    <?php endif;?>


                    <?php ActiveForm::end(); ?>
                    <?php
                        $js = <<<JS
                        function updateSelectedTotal() {
                            let total = 0;
                            $('.item-checkbox:checked').each(function() {
                                let qty = parseFloat($(this).data('qty'));
                                if (!isNaN(qty)) {
                                    total += qty;
                                }
                            });
                            $('#selected-total').text(total.toFixed(2));
                        }

                        $(document).on('change', '.item-checkbox', updateSelectedTotal);

                        $('.check-all').on('change', function() {
                            $('input.item-checkbox').prop('checked', this.checked);
                            updateSelectedTotal();
                        });
                        JS;
                        $this->registerJs($js);
                    ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>
