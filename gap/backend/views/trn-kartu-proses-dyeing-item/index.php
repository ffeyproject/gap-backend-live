<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesDyeingItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Kartu Proses Dyeing Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-dyeing-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Kartu Proses Dyeing Item', ['create'], ['class' => 'btn btn-success']) ?>
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
            //'kartu_process_id',
            //'stock_id',
            //'mesin',
            //'tube',
            //'note:ntext',
            //'status',
            //'date',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
