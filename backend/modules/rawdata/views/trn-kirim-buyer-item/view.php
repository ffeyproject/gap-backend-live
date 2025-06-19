<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\modules\rawdata\models\TrnKirimBuyerItem;
use common\models\ar\TrnGudangJadi;

/** @var yii\web\View $this */
/** @var backend\modules\rawdata\models\TrnKirimBuyerItem $model */

$this->title = "Detail Item #$model->id";
$this->params['breadcrumbs'][] = ['label' => 'Kirim Buyer Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kirim-buyer-item-view">

    <p>
        <?= Html::a('Ubah', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Hapus', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Yakin ingin menghapus item ini?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'kirim_buyer_id',
                'value' => $model->kirimBuyer ? $model->kirimBuyer->id : null,
            ],
            [
                'attribute' => 'stock_id',
                'value' => $model->stock ? $model->stock->id : null,
            ],
            [
                'label' => 'Jenis Gudang',
                'value' => $model->stock ? TrnGudangJadi::jenisGudangOptions()[$model->stock->jenis_gudang] ?? null : null,
            ],
            [
                'label' => 'Nomor WO',
                'value' => $model->stock && $model->stock->wo ? $model->stock->wo->no : null,
            ],
            [
                'label' => 'Nomor SC',
                'value' => $model->stock && $model->stock->wo && $model->stock->wo->mo && $model->stock->wo->mo->scGreige ? $model->stock->wo->mo->scGreige->sc->no : null,
            ],
            [
                'label' => 'Marketing',
                'value' => $model->stock && $model->stock->wo && $model->stock->wo->mo && $model->stock->wo->mo->scGreige ? $model->stock->wo->mo->scGreige->sc->marketing->full_name : null,
            ],
            [
                'label' => 'Buyer',
                'value' => $model->stock && $model->stock->wo && $model->stock->wo->mo && $model->stock->wo->mo->scGreige ? $model->stock->wo->mo->scGreige->sc->cust->name : null,
            ],
            'qty:decimal',
            'no_bal',
            'bal_id',
            'note:ntext',
        ],
    ]) ?>

</div>