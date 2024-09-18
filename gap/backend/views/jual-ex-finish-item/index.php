<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\JualExFinishItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Jual Ex Finish Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jual-ex-finish-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Jual Ex Finish Item', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'jual_id',
            'greige_id',
            'grade',
            'qty',
            //'unit',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
