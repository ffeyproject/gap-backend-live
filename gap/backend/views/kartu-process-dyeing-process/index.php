<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\KartuProcessDyeingProcessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Kartu Process Dyeing Processes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kartu-process-dyeing-process-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Kartu Process Dyeing Process', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'kartu_process_id',
            'process_id',
            'value',
            'note:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
