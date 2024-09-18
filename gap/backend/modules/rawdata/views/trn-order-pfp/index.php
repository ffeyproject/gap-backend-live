<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnOrderPfpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Order Pfps';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-order-pfp-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Order Pfp', ['create'], ['class' => 'btn btn-success']) ?>
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
            'no_urut',
            'no',
            //'qty',
            //'note:ntext',
            //'status',
            //'date',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'handling_id',
            //'approved_by',
            //'approved_at',
            //'approval_note:ntext',
            //'proses_sampai',
            //'dasar_warna',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
