<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnWoColorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Wo Colors';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-wo-color-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Wo Color', ['create'], ['class' => 'btn btn-success']) ?>
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
            'wo_id',
            //'mo_color_id',
            'colorName',
            //'qty',
            //'note:ntext',
            //'greige_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
