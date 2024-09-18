<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnScGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Sc Greiges';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-sc-greige-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Sc Greige', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'sc_id',
            'greige_group_id',
            'process',
            'lebar_kain',
            //'merek',
            //'grade',
            //'piece_length',
            //'unit_price',
            //'price_param',
            //'qty',
            //'woven_selvedge:ntext',
            //'note:ntext',
            //'closed:boolean',
            //'closing_note:ntext',
            //'no_order_greige',
            //'no_urut_order_greige',
            //'order_greige_note:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
