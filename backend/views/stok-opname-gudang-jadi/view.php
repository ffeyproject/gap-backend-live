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
        <?php if (!$model->id_trn_gudang_jadi): ?>
            <?= Html::a('<i class="fa fa-plus-circle"></i> Tambah / Sinkronkan ke Stock Gudang Jadi', ['create-stock', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'data-confirm' => 'Buat dan sinkronkan data ini ke Stock Gudang Jadi?',
                'title' => 'Buat Stock Gudang Jadi dari data Opname ini',
            ]) ?>
        <?php endif; ?>
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
                            return '<span class="label label-default">Kosong</span> ' . 
                                Html::a('<i class="fa fa-plus"></i> Buat Stock', ['create-stock', 'id' => $model->id], [
                                    'class' => 'btn btn-xs btn-success',
                                    'data-confirm' => 'Buat dan sinkronkan stock gudang jadi untuk item ini?',
                                    'title' => 'Buat Stock Gudang Jadi',
                                ]);
                        }
                    ],
                    [
                        'label' => 'Nomor WO',
                        'value' => $model->woNo,
                    ],
                    [
                        'label' => 'Nomor SC',
                        'value' => $model->scNo,
                    ],
                    [
                        'label' => 'Marketing',
                        'value' => $model->marketingName,
                    ],
                    [
                        'label' => 'Buyer',
                        'value' => $model->customerName,
                    ],
                    [
                        'label' => 'Motif / Kain',
                        'value' => $model->motif,
                    ],
                    [
                        'label' => 'Color / Warna',
                        'value' => $model->color,
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
                        'format' => 'raw',
                        'value' => function($model) {
                            if ($model->status === TrnGudangJadiOpnamePcs::STATUS_VERIFIED) {
                                return '<span class="label label-success"><i class="fa fa-check"></i> ' . Html::encode($model->statusName) . '</span>';
                            } elseif ($model->status === TrnGudangJadiOpnamePcs::STATUS_OUT) {
                                return '<span class="label label-danger"><i class="fa fa-times-circle"></i> ' . Html::encode($model->statusName) . '</span>';
                            }
                            return '<span class="label label-warning"><i class="fa fa-cubes"></i> ' . Html::encode($model->statusName) . '</span>';
                        }
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
