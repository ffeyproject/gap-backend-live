<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnTerimaMakloonProcessItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Terima Makloon Process Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-terima-makloon-process-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Terima Makloon Process Item', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'terima_makloon_id',
            'qty',
            'note:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
