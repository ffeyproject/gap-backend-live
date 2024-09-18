<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnBeliKainJadiItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Beli Kain Jadi Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-beli-kain-jadi-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Beli Kain Jadi Item', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'beli_kain_jadi_id',
            'qty',
            'note:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
