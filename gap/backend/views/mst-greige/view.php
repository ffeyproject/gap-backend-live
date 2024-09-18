<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstGreige */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Greiges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>

<p>
    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
    ]) ?>
    <?= Html::a('Add New', ['create'], ['class' => 'btn btn-default']) ?>
</p>

<div class="box">
    <div class="box-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                [
                    'value' => $model->group->nama_kain,
                    'label' => 'Group'
                ],
                'nama_kain',
                'alias',
                'no_dok_referensi',
                'gap',
                'created_at:datetime',
                'created_by',
                'updated_at:datetime',
                'updated_by',
                'aktif:boolean',
                'stock:decimal',
                'available:decimal',
                'booked_wo:decimal',
                'booked:decimal',
                'stock_pfp:decimal',
                'booked_pfp:decimal',
                'stock_wip:decimal',
                'booked_wip:decimal',
                'stock_ef:decimal',
                'booked_ef:decimal'
            ],
        ]) ?>
    </div>
</div>
