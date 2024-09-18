<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnKartuProsesPfpItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Kartu Proses Pfp Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-pfp-item-index">
    <p>
        <?= Html::a('Create Trn Kartu Proses Pfp Item', ['create'], ['class' => 'btn btn-success']) ?>
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
            'order_pfp_id',
            'kartu_process_id',
            'stock_id',
            'panjang_m',
            //'mesin',
            //'tube',
            //'note:ntext',
            //'status',
            //'date',
            //'created_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
