<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnInspecting */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Inspectings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="trn-inspecting-view">

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
            'sc_id',
            'sc_greige_id',
            'mo_id',
            'wo_id',
            'kartu_process_dyeing_id',
            'jenis_process',
            'no_urut',
            'no',
            'date',
            'tanggal_inspeksi',
            'no_lot',
            'kombinasi',
            'note:ntext',
            'status',
            'unit',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'approved_at',
            'approved_by',
            'approval_reject_note:ntext',
            'delivered_at',
            'delivered_by',
            'delivery_reject_note:ntext',
            'kartu_process_printing_id',
            'memo_repair_id',
        ],
    ]) ?>

</div>
