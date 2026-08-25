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
        <?= Html::a('<i class="glyphicon glyphicon-arrow-left"></i> Kembali', ['index'], ['class' => 'btn btn-default']) ?>
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
                        'attribute' => 'qty',
                        'value' => Yii::$app->formatter->asDecimal($model->qty),
                    ],
                    'unit',
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
                    'id_trn_gudang_jadi',
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
