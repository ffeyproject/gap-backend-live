<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnInspectingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Inspectings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-inspecting-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Inspecting', ['create'], ['class' => 'btn btn-success']) ?>
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
            //'kartu_process_dyeing_id',
            //'jenis_process',
            //'no_urut',
            //'no',
            //'date',
            //'tanggal_inspeksi',
            //'no_lot',
            //'kombinasi',
            //'note:ntext',
            //'status',
            //'unit',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'approved_at',
            //'approved_by',
            //'approval_reject_note:ntext',
            //'delivered_at',
            //'delivered_by',
            //'delivery_reject_note:ntext',
            //'kartu_process_printing_id',
            //'memo_repair_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
