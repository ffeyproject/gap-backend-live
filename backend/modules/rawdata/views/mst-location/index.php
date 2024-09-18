<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\MstLocationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mst Location';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-location-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Mst Location', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        // 'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'column_1',
            'column_2',
            'column_3',
            'column_4',
            // 'gap',
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
