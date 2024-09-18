<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnBuyPfpItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Buy Pfp Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-buy-pfp-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Buy Pfp Item', ['create'], ['class' => 'btn btn-success']) ?>
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
            'buy_pfp_id',
            'panjang_m',
            //'note:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
