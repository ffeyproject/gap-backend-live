<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnKartuProsesPrinting */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Kartu Proses Printings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="trn-kartu-proses-printing-view">

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
            'kartu_proses_id',
            'no_urut',
            'no',
            'nomor_kartu',
            'no_proses',
            'asal_greige',
            'dikerjakan_oleh',
            'lusi',
            'pakan',
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
            'memo_pg:ntext',
            'memo_pg_at',
            'memo_pg_by',
            'memo_pg_no',
            'delivered_at',
            'delivered_by',
            'reject_notes:ntext',
            'wo_color_id',
            'kombinasi',
        ],
    ]) ?>

</div>
