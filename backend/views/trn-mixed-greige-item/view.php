<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMixedGreigeItem */

$this->title = $model->mix_id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Mixed Greige Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="trn-mixed-greige-item-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'mix_id' => $model->mix_id, 'stock_greige_id' => $model->stock_greige_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'mix_id' => $model->mix_id, 'stock_greige_id' => $model->stock_greige_id], [
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
            'mix_id',
            'stock_greige_id',
        ],
    ]) ?>

</div>
