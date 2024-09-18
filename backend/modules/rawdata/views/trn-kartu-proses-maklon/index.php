<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnKartuProsesMaklonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Kartu Proses Maklons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-maklon-index">
    <p>
        <?= Html::a('Create Trn Kartu Proses Maklon', ['create'], ['class' => 'btn btn-success']) ?>
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
            'vendor_id',
            'no_urut',
            'no',
            //'note:ntext',
            //'date',
            //'posted_at',
            //'approved_at',
            //'approved_by',
            'status',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'unit',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
