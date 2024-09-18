<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnScSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Scs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-sc-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Sc', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            //'cust_id',
            'jenis_order',
            //'currency',
            //'bank_acct_id',
            //'direktur_id',
            //'manager_id',
            'marketing_id',
            //'no_urut',
            'no',
            'tipe_kontrak',
            'date',
            //'pmt_term',
            //'pmt_method',
            //'ongkos_angkut',
            //'due_date',
            //'delivery_date',
            //'destination:ntext',
            //'packing',
            //'jet_black:boolean',
            //'no_po',
            //'disc_grade_b',
            //'disc_piece_kecil',
            //'consignee_name',
            //'apv_dir_at',
            //'reject_note_dir:ntext',
            //'apv_mgr_at',
            //'reject_note_mgr:ntext',
            //'notify_party:ntext',
            //'buyer_name_in_invoice',
            //'note:ntext',
            //'posted_at',
            //'closed_at',
            //'closed_by',
            //'closed_note:ntext',
            //'batal_at',
            //'batal_by',
            //'batal_note:ntext',
            //'status',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',

            ['class' => 'yii\grid\ActionColumn', 'template'=>'{view} {update}'],
        ],
    ]); ?>


</div>
