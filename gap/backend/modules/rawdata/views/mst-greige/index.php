<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\MstGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mst Greiges';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-greige-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Mst Greige', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'group_id',
            'nama_kain',
            'alias',
            'no_dok_referensi',
            //'gap',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'aktif:boolean',
            //'stock',
            //'booked',
            //'stock_pfp',
            //'booked_pfp',
            //'stock_wip',
            //'booked_wip',
            //'stock_ef',
            //'booked_ef',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
