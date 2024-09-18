<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnMixedGreigeItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Mixed Greige Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-mixed-greige-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Mixed Greige Item', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'mix_id',
            'stock_greige_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
