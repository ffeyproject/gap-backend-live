<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\modules\rawdata\models\TrnKirimBuyerBal $model */

$this->title = "Detail Bal #$model->id";
$this->params['breadcrumbs'][] = ['label' => 'Kirim Buyer Bal', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kirim-buyer-bal-view">

    <p>
        <?= Html::a('Ubah', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Hapus', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Yakin ingin menghapus data bal ini?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'trn_kirim_buyer_id',
                'value' => $model->trnKirimBuyer ? $model->trnKirimBuyer->id : null,
            ],
            'no_bal',
            'header_id',
        ],
    ]) ?>

</div>