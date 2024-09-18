<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMemoRepair */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Memo Repair', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-memo-repair-view">
    <p>
        <?php if($model->status == $model::STATUS_DRAFT):?>
            <?= Html::a('Ubah', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Hapus', ['delete', 'id' => $model->id], [
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
        <?php else:?>
            <?php
            if($model->status == $model::STATUS_ON_REPAIR){
                echo Html::a('Proses Repair Selesai', ['repair-done', 'id' => $model->id], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => 'Are you sure you want to continue process this?',
                        'method' => 'post',
                    ],
                ]);
            }else{

            }
            ?>
        <?php endif;?>

        <?= Html::a('Buat Baru', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            [
                                'label'=>'Nomor Retur Buyer',
                                'attribute'=>'retur_buyer_id',
                                'value'=>$model->returBuyer->no
                            ],
                            [
                                'attribute'=>'sc_id',
                                'label'=>'Nomor SC',
                                'value'=>$model->sc->no
                            ],
                            //'sc_greige_id',
                            [
                                'attribute'=>'mo_id',
                                'label'=>'Nomor MO',
                                'value'=>$model->mo->no
                            ],
                            [
                                'attribute'=>'wo_id',
                                'label'=>'Nomor WO',
                                'value'=>$model->wo->no
                            ],
                            'date:date',
                            'no_urut',
                            'no',
                        ],
                    ]) ?>
                </div>

                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            [
                                'attribute'=>'status',
                                'value'=>$model::statusOptions()[$model->status]
                            ],
                            'created_at:datetime',
                            'created_by',
                            'updated_at:datetime',
                            'updated_by',
                            'mutasi_at:datetime',
                            'mutasi_by',
                        ],
                    ]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Catatan</strong></p>
                    <?=$model->note?>
                </div>

                <div class="col-md-6">
                    <p><strong>Catatan Mutasi</strong></p>
                    <?=$model->mutasi_note?>
                </div>
            </div>
        </div>
    </div>

    <?php echo $this->render('child/items', ['model' => $model->returBuyer]);?>
</div>
