<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\MstCustomer */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Mst Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="mst-customer-view">

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
            'cust_no',
            'name',
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
