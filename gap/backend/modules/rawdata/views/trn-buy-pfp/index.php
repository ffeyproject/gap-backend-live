<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnBuyPfpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Buy Pfps';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-buy-pfp-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Buy Pfp', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'greige_group_id',
            'greige_id',
            'no_document',
            'vendor',
            //'note:ntext',
            //'status',
            //'date',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'approval_id',
            //'approval_time:datetime',
            //'reject_note:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
