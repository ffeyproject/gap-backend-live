<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnKartuProsesPfpItem */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Kartu Proses Pfp Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="trn-kartu-proses-pfp-item-view">

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
            'order_pfp_id',
            'kartu_process_id',
            'stock_id',
            'panjang_m',
            'mesin',
            'tube',
            'note:ntext',
            'status',
            'date',
            'created_at',
        ],
    ]) ?>

</div>
