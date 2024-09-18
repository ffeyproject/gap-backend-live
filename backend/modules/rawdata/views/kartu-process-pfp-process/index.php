<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\KartuProcessPfpProcessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Kartu Process Pfp Processes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kartu-process-pfp-process-index">
    <p>
        <?= Html::a('Create Kartu Process Pfp Process', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'kartu_process_id',
            'process_id',
            'value:ntext',
            'note:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
