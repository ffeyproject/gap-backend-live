<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnStockGreige */

$this->title = 'Packing List Greige: '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Packing List Greige', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-stock-greige-view">
    <p>
        <?= Html::a('Ubah', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Hapus', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
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
                                'attribute'=>'greige_id',
                                'value'=>$model->greige->nama_kain
                            ],
                            'no_lapak',
                            [
                                'attribute'=>'grade',
                                'value'=>$model::gradeOptions()[$model->grade]
                            ],
                            'lot_lusi',
                            'lot_pakan',
                            'no_set_lusi',
                            'panjang_m:decimal',
                            [
                                'attribute'=>'status_tsd',
                                'value'=>$model::tsdOptions()[$model->status_tsd]
                            ],
                            'no_document',
                            'is_pemotongan:boolean',
                        ],
                    ]) ?>
                </div>

                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            [
                                'attribute'=>'asal_greige',
                                'value'=>$model::asalGreigeOptions()[$model->asal_greige]
                            ],
                            'pengirim',
                            'mengetahui',
                            'note:ntext',
                            [
                                'attribute'=>'status',
                                'value'=>$model::statusOptions()[$model->status]
                            ],
                            'date:date',
                            'created_at:datetime',
                            'created_by',
                            'updated_at',
                            'updated_by',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
