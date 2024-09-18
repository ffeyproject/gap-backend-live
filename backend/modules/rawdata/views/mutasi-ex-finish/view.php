<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\MutasiExFinish */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Mutasi Ex Finishes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="mutasi-ex-finish-view">

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
            'no_wo',
            'no_urut',
            'no',
            'date',
            'note:ntext',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'status',
            'approval_id',
            'approval_time:datetime',
            'reject_note',
        ],
    ]) ?>

</div>
