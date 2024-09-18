<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstCustomer */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Customers', 'url' => ['index']];
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
    <?= Html::a('Add New', ['create'], ['class' => 'btn btn-success']) ?>
</p>

<div class="box">
    <div class="box-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'name',
                'cust_no',
                'telp',
                'fax',
                'email:email',
                'address:ntext',
                'cp_name',
                'cp_phone',
                'cp_email:email',
                'npwp',
                'aktif:boolean',
            ],
        ]) ?>
    </div>
</div>
