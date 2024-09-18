<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnInspectingItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Inspecting Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-inspecting-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Inspecting Item', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'no',
            'note:ntext',
            'qty',
            'trn_inspecting_roll_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
