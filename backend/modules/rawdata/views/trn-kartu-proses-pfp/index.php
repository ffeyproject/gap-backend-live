<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnKartuProsesPfpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Kartu Proses Pfps';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-pfp-index">
    <p>
        <?= Html::a('Create Trn Kartu Proses Pfp', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'greige_group_id',
            'greige_id',
            'order_pfp_id',
            'no_urut',
            'no',
            //'no_proses',
            //'asal_greige',
            //'dikerjakan_oleh',
            //'lusi',
            //'pakan',
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
            //'delivered_at',
            //'delivered_by',
            //'reject_notes:ntext',
            //'berat',
            //'lebar',
            //'k_density_lusi',
            //'k_density_pakan',
            //'gramasi',
            //'lebar_preset',
            //'lebar_finish',
            //'berat_finish',
            //'t_density_lusi',
            //'t_density_pakan',
            //'handling',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
