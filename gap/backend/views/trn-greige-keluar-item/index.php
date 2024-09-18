<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnGreigeKeluarItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Greige Keluar Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-greige-keluar-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Greige Keluar Item', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'greige_keluar_id',
            'stock_greige_id',
            'note:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
