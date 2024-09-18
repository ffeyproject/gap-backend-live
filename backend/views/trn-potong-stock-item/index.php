<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnPotongStockItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Potong Stock Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-potong-stock-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Potong Stock Item', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'potong_stock_id',
            'qty',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
