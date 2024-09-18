<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKirimMakloonItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Kirim Makloon Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kirim-makloon-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Kirim Makloon Item', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'kirim_makloon_id',
            'stock_id',
            'qty',
            'note:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
