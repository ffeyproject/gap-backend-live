<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnMo */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Mos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="trn-mo-view">

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
            'process',
            'approval_id',
            'approved_at',
            'no_urut',
            'no',
            'date',
            're_wo',
            'design',
            'handling',
            'article',
            'strike_off:ntext',
            'heat_cut:boolean',
            'sulam_pinggir',
            'border_size',
            'block_size',
            'foil:boolean',
            'face_stamping:ntext',
            'selvedge_stamping',
            'selvedge_continues',
            'side_band',
            'tag',
            'hanger',
            'label',
            'folder',
            'album',
            'joint:boolean',
            'joint_qty',
            'packing_method',
            'shipping_method',
            'shipping_sorting',
            'plastic',
            'arsip',
            'jet_black:boolean',
            'piece_length',
            'est_produksi',
            'est_packing',
            'target_shipment',
            'jenis_gudang',
            'posted_at',
            'closed_at',
            'closed_by',
            'closed_note:ntext',
            'reject_notes:ntext',
            'batal_at',
            'batal_by',
            'batal_note:ntext',
            'status',
            'note:ntext',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ],
    ]) ?>

</div>