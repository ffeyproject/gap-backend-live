<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */

$this->title = $model->locs_code;
$this->params['breadcrumbs'][] = ['label' => 'Sub Location', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>

<p>
    <?= Html::a('Update', ['update', 'id' => $model->locs_code], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $model->locs_code], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
    ]) ?>
    <?= Html::a('Add New', ['create'], ['class' => 'btn btn-default']) ?>
    <?= Html::a('PRINT <span><i class="fa fa-qrcode"></i></span>', ['qr', 'id' => $model->locs_code], ['class' => 'btn btn-success']) ?>
</p>

<div class="box">
    <div class="box-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'locs_loc_id',
                'locs_code',
                'locs_description',
                'locs_active',
                'locs_floor_code',
                'locs_line_code',
                'locs_column_code',
                'locs_rack_code',
                'locs_add_date',
                'locs_add_by',
                'locs_upd_date',
                'locs_upd_by'
            ],
        ]) ?>
    </div>
</div>
