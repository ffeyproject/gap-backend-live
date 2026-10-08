<?php

use common\models\ar\TrnGudangJadiOpnamePcs;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnGudangJadiOpnamePcs */

$this->title = 'Opname Pcs #' . $model->id . ' (' . $model->qr_code . ')';
$this->params['breadcrumbs'][] = ['label' => 'Gudang Jadi Stok Opname', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

$gradeLabels = [
    1 => 'Grade A',
    2 => 'Grade B',
    3 => 'Grade C',
    4 => 'Grade D',
    5 => 'Grade E',
    6 => 'Grade F',
    7 => 'Grade G',
];
?>
<div class="trn-gudang-jadi-opname-pcs-view">
    <p>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> Ubah', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-trash"></i> Hapus', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Apakah Anda yakin ingin menghapus data ini?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('<i class="glyphicon glyphicon-arrow-left"></i> Kembali', ['index'], ['class' => 'btn btn-default']) ?>
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
                                'attribute' => 'id_trn_gudang_jadi',
                                'label' => 'ID Trn Gudang Jadi',
                                'value' => $model->id_trn_gudang_jadi ?: '-',
                            ],
                            'opname_code',
                            'qr_code',
                            'qr_code_desc:ntext',
                            [
                                'attribute' => 'unit',
                                'value' => $model->getUnitName(),
                            ],
                            [
                                'attribute' => 'grade',
                                'value' => isset($gradeLabels[$model->grade]) ? $gradeLabels[$model->grade] : $model->grade,
                            ],
                            'locs_code',
                            'join_piece',
                            [
                                'attribute' => 'status',
                                'value' => $model->getStatusName(),
                            ],
                            'remark:ntext',
                        ],
                    ]) ?>
                </div>

                <div class="col-md-6">
                    <h4>Informasi Gudang Jadi Terkait</h4>
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            [
                                'label' => 'Nomor WO',
                                'value' => ($model->gudangJadi && $model->gudangJadi->wo) ? $model->gudangJadi->wo->no : '-',
                            ],
                            [
                                'label' => 'Nomor SC',
                                'value' => ($model->gudangJadi && $model->gudangJadi->wo && $model->gudangJadi->wo->mo && $model->gudangJadi->wo->mo->scGreige && $model->gudangJadi->wo->mo->scGreige->sc) ? $model->gudangJadi->wo->mo->scGreige->sc->no : '-',
                            ],
                            [
                                'label' => 'Marketing',
                                'value' => ($model->gudangJadi && $model->gudangJadi->wo && $model->gudangJadi->wo->mo && $model->gudangJadi->wo->mo->scGreige && $model->gudangJadi->wo->mo->scGreige->sc && $model->gudangJadi->wo->mo->scGreige->sc->marketing) ? $model->gudangJadi->wo->mo->scGreige->sc->marketing->full_name : '-',
                            ],
                            [
                                'label' => 'Buyer',
                                'value' => ($model->gudangJadi && $model->gudangJadi->wo && $model->gudangJadi->wo->mo && $model->gudangJadi->wo->mo->scGreige && $model->gudangJadi->wo->mo->scGreige->sc && $model->gudangJadi->wo->mo->scGreige->sc->cust) ? $model->gudangJadi->wo->mo->scGreige->sc->cust->name : '-',
                            ],
                            [
                                'label' => 'Color',
                                'value' => $model->gudangJadi ? ($model->gudangJadi->color ?: '-') : '-',
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
</div>
