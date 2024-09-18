<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnMemoPerubahanDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Memo Perubahan Datas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-memo-perubahan-data-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Memo Perubahan Data', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'description:ntext',
            'date',
            'status',
            'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'no_urut',
            //'no',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
