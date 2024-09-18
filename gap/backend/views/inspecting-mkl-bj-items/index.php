<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\InspectingMklBjItemsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inspecting Mkl Bj Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inspecting-mkl-bj-items-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Inspecting Mkl Bj Items', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'roll_id',
            'no',
            'qty',
            'note',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
