<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnPfpKeluar */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'PFP Keluar', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$modelsItem = $model->trnPfpKeluarItems;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-greige-keluar-view">
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
                            'no_urut',
                            'no',
                            'date:date',
                            [
                                'label'=>'Jenis',
                                'value'=>$model::jenisOptions()[$model->jenis]
                            ],
                            'destinasi',
                            'no_referensi',
                            'note:ntext',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

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
                            'posted_at:datetime',
                            'approved_at:datetime',
                            'approvalName',
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
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Items (Stock Greige)</h3>
            <div class="box-tools pull-right">
                <span class="label label-primary"><?=count($modelsItem)?></span>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Gudang</th>
                    <th>Greige Group</th>
                    <th>Greige</th>
                    <th>Color</th>
                    <th>ID</th>
                    <th>No. Document</th>
                    <th>Panjang</th>
                    <th>Unit</th>
                </tr>
                </thead>
                <tbody>
                <?php $totalPanjang = 0; foreach ($modelsItem as $index => $modelItem): ?>
                    <?php
                    $stockGreige = $modelItem->stockPfp;
                    $panjangM = $stockGreige->panjang_m;
                    $totalPanjang += $panjangM;
                    ?>
                    <tr>
                        <td><span><?= ($index + 1) ?></span></td>
                        <td><?=$stockGreige::jenisGudangOptions()[$stockGreige->jenis_gudang]?></td>
                        <td><?=$stockGreige->greigeGroup->nama_kain?></td>
                        <td><?=$stockGreige->greige->nama_kain?></td>
                        <td><?=$stockGreige->color?></td>
                        <td><?=$modelItem->stock_pfp_id?></td>
                        <td><?=$stockGreige->no_document?></td>
                        <td><?= Yii::$app->formatter->asDecimal($panjangM) ?></td>
                        <td><?=$stockGreige->greigeGroup->unitName?></td>
                    </tr>
                <?php endforeach;?>
                <tr>
                    <td colspan="7"><strong>TOTAL</strong></td>
                    <td><strong><?=Yii::$app->formatter->asDecimal($totalPanjang)?></strong></td>
                    <td>&nbsp;</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <?=$this->render('_print', ['model'=>$model, 'modelsItem'=>$modelsItem])?>
</div>

<?php
$this->registerJs($this->renderFile(__DIR__.'/js/view.js'), $this::POS_END);