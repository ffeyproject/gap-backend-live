<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstProcessDyeing */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Master Data Process Dyeing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="mst-process-dyeing-view">
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Add New', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="box">
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'order',
                    'max_pengulangan',
                    'nama_proses',
                    'tanggal:boolean',
                    'start:boolean',
                    'stop:boolean',
                    'no_mesin:boolean',
                    'shift_group:boolean',
                    'temp:boolean',
                    'speed:boolean',
                    'gramasi:boolean',
                    'program_number:boolean',
                    'density:boolean',
                    'over_feed:boolean',
                    'lebar_jadi:boolean',
                    'panjang_jadi:boolean',
                    'info_kualitas:boolean',
                    'gangguan_produksi:boolean',
                    'created_at:datetime',
                    'created_by',
                    'updated_at:datetime',
                    'updated_by',
                ],
            ]) ?>
        </div>
    </div>
</div>
