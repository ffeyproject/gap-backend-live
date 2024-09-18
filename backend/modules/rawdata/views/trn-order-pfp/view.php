<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnOrderPfp */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Order Pfps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="trn-order-pfp-view">

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
            'no_urut',
            'no',
            'qty',
            'note:ntext',
            'status',
            'date',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'handling_id',
            'approved_by',
            'approved_at',
            'approval_note:ntext',
            'proses_sampai',
            'dasar_warna',
        ],
    ]) ?>

</div>
