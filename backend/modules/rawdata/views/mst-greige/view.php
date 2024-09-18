<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\MstGreige */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Mst Greiges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="mst-greige-view">

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
            'group_id',
            'nama_kain',
            'alias',
            'no_dok_referensi',
            'gap',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'aktif:boolean',
            'stock',
            'booked',
            'stock_pfp',
            'booked_pfp',
            'stock_wip',
            'booked_wip',
            'stock_ef',
            'booked_ef',
        ],
    ]) ?>

</div>
