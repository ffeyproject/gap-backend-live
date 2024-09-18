<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnKartuProsesDyeingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Kartu Proses Dyeings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-dyeing-index">
    <p>
        <?= Html::a('Create Trn Kartu Proses Dyeing', ['create'], ['class' => 'btn btn-success']) ?>
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
            //'kartu_proses_id',
            'no_urut',
            'no',
            'asal_greige',
            //'dikerjakan_oleh',
            //'lusi',
            //'pakan',
            //'note:ntext',
            //'date',
            //'posted_at',
            //'approved_at',
            //'approved_by',
            //'delivered_at',
            //'delivered_by',
            //'reject_notes:ntext',
            'status',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'memo_pg:ntext',
            //'memo_pg_at',
            //'memo_pg_by',
            //'memo_pg_no',
            //'berat',
            //'lebar',
            //'k_density_lusi',
            //'k_density_pakan',
            //'lebar_preset',
            //'lebar_finish',
            //'berat_finish',
            //'t_density_lusi',
            //'t_density_pakan',
            //'handling',
            //'hasil_tes_gosok',
            //'wo_color_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
