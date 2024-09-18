<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnSc */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Scs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="trn-sc-view">

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
            'cust_id',
            'jenis_order',
            'currency',
            'bank_acct_id',
            'direktur_id',
            'manager_id',
            'marketing_id',
            'no_urut',
            'no',
            'tipe_kontrak',
            'date',
            'pmt_term',
            'pmt_method',
            'ongkos_angkut',
            'due_date',
            'delivery_date',
            'destination:ntext',
            'packing',
            'jet_black:boolean',
            'no_po',
            'disc_grade_b',
            'disc_piece_kecil',
            'consignee_name',
            'apv_dir_at',
            'reject_note_dir:ntext',
            'apv_mgr_at',
            'reject_note_mgr:ntext',
            'notify_party:ntext',
            'buyer_name_in_invoice',
            'note:ntext',
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
