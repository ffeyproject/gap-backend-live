<?php

use common\models\ar\TrnGudangJadiOpnamePcs;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnGudangJadiOpnamePcs */

$this->title = 'Detail Stok Opname Pcs #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Gudang Jadi', 'url' => ['/trn-gudang-jadi/index']];
$this->params['breadcrumbs'][] = ['label' => 'Stok Opname Gudang Jadi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stok-opname-gudang-jadi-view">

    <p>
        <?= Html::a('<i class="glyphicon glyphicon-arrow-left"></i> Kembali ke Data Pcs', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a('<i class="fa fa-pie-chart"></i> Kembali ke Rekap Stok Opname', ['rekap'], ['class' => 'btn btn-info']) ?>
    </p>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-info-circle"></i> Informasi Detail Stok Opname Pcs (ID: #<?= $model->id ?>)</h3>
        </div>
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'opname_code',
                    'qr_code',
                    'qr_code_desc:ntext',
                    [
                        'attribute' => 'id_trn_gudang_jadi',
                        'label' => 'ID Gudang Jadi',
                        'format' => 'raw',
                        'value' => function($model) {
                            if ($model->id_trn_gudang_jadi) {
                                return Html::a('ID #' . $model->id_trn_gudang_jadi, ['/trn-gudang-jadi/view', 'id' => $model->id_trn_gudang_jadi], [
                                    'target' => '_blank',
                                    'class' => 'label label-info',
                                ]);
                            }
                            return '-';
                        }
                    ],
                    [
                        'label' => 'Nomor WO',
                        'value' => $model->gudangJadi && $model->gudangJadi->wo ? $model->gudangJadi->wo->no : '-',
                    ],
                    [
                        'label' => 'Nomor SC',
                        'value' => $model->gudangJadi && $model->gudangJadi->wo && $model->gudangJadi->wo->mo && $model->gudangJadi->wo->mo->scGreige && $model->gudangJadi->wo->mo->scGreige->sc ? $model->gudangJadi->wo->mo->scGreige->sc->no : '-',
                    ],
                    [
                        'label' => 'Marketing',
                        'value' => $model->gudangJadi && $model->gudangJadi->wo && $model->gudangJadi->wo->mo && $model->gudangJadi->wo->mo->scGreige && $model->gudangJadi->wo->mo->scGreige->sc && $model->gudangJadi->wo->mo->scGreige->sc->marketing ? $model->gudangJadi->wo->mo->scGreige->sc->marketing->full_name : '-',
                    ],
                    [
                        'label' => 'Buyer',
                        'value' => $model->gudangJadi && $model->gudangJadi->wo && $model->gudangJadi->wo->mo && $model->gudangJadi->wo->mo->scGreige && $model->gudangJadi->wo->mo->scGreige->sc && $model->gudangJadi->wo->mo->scGreige->sc->cust ? $model->gudangJadi->wo->mo->scGreige->sc->cust->name : '-',
                    ],
                    [
                        'label' => 'Motif / Kain',
                        'value' => $model->gudangJadi && $model->gudangJadi->wo ? $model->gudangJadi->wo->greigeNamaKain : (!empty($model->qr_code_desc) ? $model->qr_code_desc : '-'),
                    ],
                    [
                        'label' => 'Color / Warna',
                        'value' => $model->gudangJadi && !empty($model->gudangJadi->color) ? $model->gudangJadi->color : '-',
                    ],
                    [
                        'attribute' => 'qty',
                        'value' => Yii::$app->formatter->asDecimal($model->qty),
                    ],
                    [
                        'attribute' => 'unit',
                        'label' => 'Satuan',
                        'value' => $model->unitName,
                    ],
                    [
                        'attribute' => 'grade',
                        'value' => $model->gradeName,
                    ],
                    'join_piece',
                    'locs_code',
                    [
                        'attribute' => 'status',
                        'value' => $model->statusName,
                    ],
                    'remark:ntext',
                    'created_at:datetime',
                    'created_by',
                    'updated_at:datetime',
                    'updated_by',
                ],
            ]) ?>
        </div>
    </div>

</div>
