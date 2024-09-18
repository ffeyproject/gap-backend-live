<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\KartuProcessPrintingProcess */

$this->title = $model->kartu_process_id;
$this->params['breadcrumbs'][] = ['label' => 'Kartu Process Printing Processes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="kartu-process-printing-process-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'kartu_process_id' => $model->kartu_process_id, 'process_id' => $model->process_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'kartu_process_id' => $model->kartu_process_id, 'process_id' => $model->process_id], [
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
            'kartu_process_id',
            'process_id',
            'value:ntext',
            'note:ntext',
        ],
    ]) ?>

</div>
