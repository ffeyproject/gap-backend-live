<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnPfpKeluarItem */

$this->title = $model->pfp_keluar_id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Pfp Keluar Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="trn-pfp-keluar-item-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'pfp_keluar_id' => $model->pfp_keluar_id, 'stock_pfp_id' => $model->stock_pfp_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'pfp_keluar_id' => $model->pfp_keluar_id, 'stock_pfp_id' => $model->stock_pfp_id], [
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
            'pfp_keluar_id',
            'stock_pfp_id',
            'note:ntext',
        ],
    ]) ?>

</div>
