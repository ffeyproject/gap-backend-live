<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\MutasiExFinishSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mutasi Ex Finishes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mutasi-ex-finish-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Mutasi Ex Finish', ['create'], ['class' => 'btn btn-success']) ?>
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
            'no_wo',
            'no_urut',
            //'no',
            //'date',
            //'note:ntext',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'status',
            //'approval_id',
            //'approval_time:datetime',
            //'reject_note',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
