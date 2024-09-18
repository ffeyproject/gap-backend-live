<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnKartuProsesMaklonItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Kartu Proses Maklon Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-maklon-item-index">
    <p>
        <?= Html::a('Create Trn Kartu Proses Maklon Item', ['create'], ['class' => 'btn btn-success']) ?>
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
            'kartu_process_id',
            'stock_id',
            'panjang_m',
            //'note:ntext',
            //'date',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
