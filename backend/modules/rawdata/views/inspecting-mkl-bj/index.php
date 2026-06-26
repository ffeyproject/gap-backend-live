<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\InspectingMklBjSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inspecting Mkl Bjs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inspecting-mkl-bj-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Inspecting Mkl Bj', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'wo_id',
            'wo_color_id',
            'tgl_inspeksi',
            'tgl_kirim',
            //'no_lot',
            //'jenis',
            //'satuan',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'status',
            //'no_urut',
            //'no',
            //'delivered_at',
            //'delivered_by',
            //'delivery_reject_note:ntext',
            //'k3l_code',
            //'defect',
            //'inspection_table',
            //'jenis_inspek',
            //'no_memo',
            //'note:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
