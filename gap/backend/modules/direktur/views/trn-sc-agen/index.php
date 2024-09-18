<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnScAgenSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Sc Agens';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-sc-agen-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Sc Agen', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'sc_id',
            'date',
            'nama_agen',
            'attention',
            //'no_urut',
            //'no',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
