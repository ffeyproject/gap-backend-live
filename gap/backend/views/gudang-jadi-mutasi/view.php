<?php

use backend\components\ajax_modal\AjaxModal;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\GudangJadiMutasi */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Gudang Jadi Mutasis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

$modelsItem = $model->gudangJadiMutasiItems;

echo AjaxModal::widget([
    'id' => 'GdJadMutasiModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);
?>
<div class="gudang-jadi-mutasi-view">
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
        <?php else:?>
            <?= Html::a('Ubah Nomor', ['ubah-nomor', 'id' => $model->id], ['class' => 'btn btn-info']) ?>

            <?= Html::a('<i class="glyphicon glyphicon-print"></i>', ['display', 'id'=>$model->id], [
                'class' => 'btn btn-default',
                'title' => 'Print',
                'data-toggle'=>"modal",
                'data-target'=>"#GdJadMutasiModal",
                'data-title' => 'Print Mutasi Gudang Jadi',
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
                            'nomor',
                            'date',
                            'pengirim',
                            'penerima',
                            'kepala_gudang',
                            'dept_tujuan',
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
                            'note:ntext',
                            'statusName',
                            'created_at:datetime',
                            'updated_at:datetime',
                            'created_by',
                            'updated_by',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

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
                    <th>No. WO</th>
                    <th>Color</th>
                    <th>No. Lot</th>
                    <th>Motif</th>
                    <th>Qty</th>
                    <th>Unit</th>
                </tr>
                </thead>
                <tbody>
                <?php $totalPanjang = 0; foreach ($modelsItem as $index => $modelItem): ?>
                    <?php
                    $modelGdJadi = $modelItem->stock;
                    $wo = $modelGdJadi->wo;
                    $noLot = '';
                    $totalPanjang += $modelGdJadi->qty;

                    //mengambil no_lot
                    if($modelGdJadi->source_ref !== null){
                        $noLot = (new \yii\db\Query())->from(\common\models\ar\TrnInspecting::tableName())
                            ->select('no_lot')
                            ->where(['no'=>$modelGdJadi->source_ref])
                            ->one()
                        ;
                        if($noLot !== false){
                            $noLot = $noLot['no_lot'];
                        }else{
                            $noLot = (new \yii\db\Query())->from(\common\models\ar\InspectingMklBj::tableName())
                                ->select('no_lot')
                                ->where(['no'=>$modelGdJadi->source_ref])
                                ->one()
                            ;
                            if($noLot !== false){
                                $noLot = $noLot['no_lot'];
                            }
                        }
                    }
                    ?>
                    <tr>
                        <td><span><?= ($index + 1) ?></span></td>
                        <td><?=$wo->no?></td>
                        <td><?=$modelGdJadi->color?></td>
                        <td><?=$noLot?></td>
                        <td><?=$wo->greigeNamaKain?></td>
                        <td><?=Yii::$app->formatter->asDecimal($modelGdJadi->qty)?></td>
                        <td><?=$wo->greige->group->unitName?></td>
                    </tr>
                <?php endforeach;?>
                <tr>
                    <td colspan="6"><strong>TOTAL</strong></td>
                    <td><strong><?=Yii::$app->formatter->asDecimal($totalPanjang)?></strong></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
