<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnWo */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Wos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="trn-wo-view">

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
            'jenis_order',
            'greige_id',
            'mengetahui_id',
            'apv_mengetahui_at',
            'reject_note_mengetahui:ntext',
            'no_urut',
            'no',
            'date',
            'papper_tube',
            'plastic_size',
            'shipping_mark:ntext',
            'note:ntext',
            'note_two:ntext',
            'marketing_id',
            'apv_marketing_at',
            'reject_note_marketing:ntext',
            'posted_at',
            'closed_at',
            'closed_by',
            'closed_note:ntext',
            'batal_at',
            'batal_by',
            'batal_note:ntext',
            'status',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ],
    ]) ?>

</div>
