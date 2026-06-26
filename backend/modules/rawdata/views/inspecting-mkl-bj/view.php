<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\InspectingMklBj */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inspecting Mkl Bjs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="inspecting-mkl-bj-view">

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
            'wo_id',
            'wo_color_id',
            'tgl_inspeksi',
            'tgl_kirim',
            'no_lot',
            'jenis',
            'satuan',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'status',
            'no_urut',
            'no',
            'delivered_at',
            'delivered_by',
            'delivery_reject_note:ntext',
            'k3l_code',
            'defect',
            'inspection_table',
            'jenis_inspek',
            'no_memo',
            'note:ntext',
        ],
    ]) ?>

    <h3>Items</h3>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>No Urut</th>
                <th>Grade</th>
                <th>Defect</th>
                <th>Lot No</th>
                <th>Qty</th>
                <th>Join Piece</th>
                <th>Note</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($model->inspectingMklBjItems as $item): ?>
                <tr>
                    <td><?= $item->id ?></td>
                    <td><?= $item->no_urut ?></td>
                    <td><?= \common\models\ar\InspectingMklBjItems::gradeOptions()[$item->grade] ?? $item->grade ?></td>
                    <td><?= Html::encode($item->defect) ?></td>
                    <td><?= Html::encode($item->lot_no) ?></td>
                    <td><?= $item->qty ?></td>
                    <td><?= $item->join_piece ?></td>
                    <td><?= Html::encode($item->note) ?></td>
                    <td>
                        <?= Html::a('Edit', ['update-item', 'itemId' => $item->id], ['class' => 'btn btn-xs btn-primary']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
