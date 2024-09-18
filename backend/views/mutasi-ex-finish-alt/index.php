<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\MutasiExFinishAltSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mutasi Ex Finish Alts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mutasi-ex-finish-alt-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Mutasi Ex Finish Alt', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'no_referensi',
            'pemohon',
            'created_at',
            'created_by',
            //'updated_at',
            //'updated_by',
            //'no_urut',
            //'no',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
