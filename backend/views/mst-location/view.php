<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstGreige */
$this->title = $model->loc_name;
$this->params['breadcrumbs'][] = ['label' => 'Location', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>

<p>
    <?= Html::a('Update', ['update', 'id' => $model->loc_id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $model->loc_id], [
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
                'loc_id',
                'loc_name',
                'loc_description',
                'loc_active',
                'loc_add_date',
                'loc_add_by',
                'loc_upd_date',
                'loc_upd_by'
            ],
        ]) ?>
    </div>
</div>
