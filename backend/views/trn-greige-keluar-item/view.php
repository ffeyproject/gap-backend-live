<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnGreigeKeluarItem */

$this->title = $model->greige_keluar_id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Greige Keluar Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="trn-greige-keluar-item-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'greige_keluar_id' => $model->greige_keluar_id, 'stock_greige_id' => $model->stock_greige_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'greige_keluar_id' => $model->greige_keluar_id, 'stock_greige_id' => $model->stock_greige_id], [
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
            'greige_keluar_id',
            'stock_greige_id',
            'note:ntext',
        ],
    ]) ?>

</div>
