<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnKartuProsesMaklon */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Kartu Proses Maklons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="trn-kartu-proses-maklon-view">

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
            'sc_id',
            'sc_greige_id',
            'mo_id',
            'wo_id',
            'vendor_id',
            'no_urut',
            'no',
            'dikerjakan_oleh',
            'note:ntext',
            'date',
            'posted_at',
            'approved_at',
            'approved_by',
            'status',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'wo_color_id',
            'unit',
        ],
    ]) ?>

</div>
