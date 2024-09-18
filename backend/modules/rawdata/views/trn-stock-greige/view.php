<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnStockGreige */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Stock Greiges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="trn-stock-greige-view">

    <h1><?= Html::encode($this->title) ?></h1>

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

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'greige_group_id',
            'greige_id',
            'asal_greige',
            'no_lapak',
            'grade',
            'lot_lusi',
            'lot_pakan',
            'no_set_lusi',
            'panjang_m',
            'status_tsd',
            'no_document',
            'pengirim',
            'mengetahui',
            'note:ntext',
            'status',
            'date',
            'jenis_gudang',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'nomor_wo',
            'keputusan_qc',
            'color',
        ],
    ]) ?>

</div>
