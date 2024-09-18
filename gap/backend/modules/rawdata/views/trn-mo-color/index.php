<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnMoColorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Mo Colors';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-mo-color-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Mo Color', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'sc_id',
            'sc_greige_id',
            'mo_id',
            'color',
            //'qty',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
