<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnStockGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Stock Greiges';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-stock-greige-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Stock Greige', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'greige_group_id',
            'greige_id',
            'asal_greige',
            //'no_lapak',
            //'grade',
            //'lot_lusi',
            //'lot_pakan',
            //'no_set_lusi',
            'panjang_m',
            //'status_tsd',
            //'no_document',
            //'pengirim',
            //'mengetahui',
            //'note:ntext',
            //'status',
            //'date',
            //'jenis_gudang',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'nomor_wo',
            //'keputusan_qc',
            'color',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
