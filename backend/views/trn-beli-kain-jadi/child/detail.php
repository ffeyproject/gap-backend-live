<?php

use common\models\ar\TrnBeliKainJadi;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnBeliKainJadi */
?>

<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        //'id',
                        'date:date',
                        [
                            'label'=>'Nomor WO',
                            'value'=>Html::a($model->wo->no, ['/trn-wo/view', 'id'=>$model->wo_id], ['title'=>'Detail WO', 'target'=>'blank']),
                            'format'=>'raw'
                        ],
                        [
                            'label'=>'Color',
                            'value'=>$model->woColor->moColor->color,
                        ],
                        'no',
                        [
                            'attribute'=>'vendor_id',
                            'value'=>$model->vendor->name
                        ],
                        [
                            'attribute'=>'unit',
                            'value'=>\common\models\ar\MstGreigeGroup::unitOptions()[$model->unit]
                        ],
                        [
                            'attribute'=>'jenis_gudang',
                            'value'=>\common\models\ar\TrnGudangJadi::jenisGudangOptions()[$model->jenis_gudang]
                        ],
                        [
                            'attribute'=>'status',
                            'value'=>$model::statusOptions()[$model->status]
                        ],
                    ],
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'created_at:datetime',
                        'created_by',
                        'updated_at:datetime',
                        'updated_by',
                        'approved_at:datetime',
                        'approved_by',
                        'note:ntext',
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>

<?php
/*echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'jenis_gudang',
            'sc_id',
            'sc_greige_id',
            'mo_id',
            'wo_id',
            'vendor_id',
            'date',
            'no_urut',
            'no',
            'unit',
            'note:ntext',
            'status',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'pengirim',
        ],
    ]);*/
?>
