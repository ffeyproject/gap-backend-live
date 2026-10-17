<?php

use common\models\ar\MstGreigeGroup;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnGudangJadi */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Gudang Jadi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-gudang-jadi-view">
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
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
                                'label'=>'Jenis Gudang',
                                'value'=>$model::jenisGudangOptions()[$model->jenis_gudang]
                            ],
                            [
                                'label'=>'Nomor WO',
                                'value'=>$model->wo->no
                            ],
                            [
                                'attribute'=>'source',
                                'value'=>$model::sourceOptions()[$model->source]
                            ],
                            'source_ref',
                            'id_from',
                            'trans_from',
                            'qr_code',
                            'locs_code',
                            'qty:decimal',
                            [
                                'attribute'=>'unit',
                                'value'=> MstGreigeGroup::unitOptions()[$model->unit]
                            ],
                            //'no_urut',
                            //'no',
                            'date:date',
                            [
                                'attribute'=>'status',
                                'value'=> $model::statusOptions()[$model->status]
                            ],
                            'no_memo_repair',
                            'no_memo_ganti_greige',
                            [
                                'label' => 'Stock Opname Pcs Terkait',
                                'format' => 'raw',
                                'value' => function($model) {
                                    $ops = \common\models\ar\TrnGudangJadiOpnamePcs::find()->where(['id_trn_gudang_jadi' => $model->id])->all();
                                    if (empty($ops)) {
                                        return '<span class="text-muted">(Tidak ada data opname pcs)</span>';
                                    }
                                    $html = '<ul style="padding-left: 20px; margin-bottom: 0;">';
                                    foreach ($ops as $op) {
                                        $html .= '<li>' . Html::encode($op->opname_code . ' (#' . $op->id . ')') 
                                            . ' &mdash; Qty: <strong class="text-primary">' . Yii::$app->formatter->asDecimal($op->qty, 2) . ' ' . Html::encode($op->unit) . '</strong>'
                                            . ' | Lokasi: <code>' . Html::encode($op->locs_code) . '</code>'
                                            . ' | Status: <span class="label ' . ($op->status == \common\models\ar\TrnGudangJadiOpnamePcs::STATUS_OUT ? 'label-danger' : 'label-success') . '">' . $op->getStatusName() . '</span>'
                                            . '</li>';
                                    }
                                    $html .= '</ul>';
                                    return $html;
                                }
                            ],
                        ],
                    ]) ?>
                </div>

                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'note:ntext',
                            'created_at:datetime',
                            'created_by',
                            'updated_at:datetime',
                            'updated_by',
                            //'approved_at:datetime',
                            //'approved_by',
                            //'approval_reject_note:ntext',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
