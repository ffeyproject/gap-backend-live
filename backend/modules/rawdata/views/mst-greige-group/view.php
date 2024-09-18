<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\MstGreigeGroup */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Mst Greige Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="mst-greige-group-view">

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
            'jenis_kain',
            'nama_kain',
            'qty_per_batch',
            'unit',
            'nilai_penyusutan',
            'gramasi_kain',
            'sulam_pinggir',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'aktif:boolean',
        ],
    ]) ?>

</div>
