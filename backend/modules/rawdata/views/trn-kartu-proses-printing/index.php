<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnKartuProsesPrintingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Kartu Proses Printings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-kartu-proses-printing-index">
    <p>
        <?= Html::a('Create Trn Kartu Proses Printing', ['create'], ['class' => 'btn btn-success']) ?>
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
            //'memo_pg:ntext',
            //'memo_pg_at',
            //'memo_pg_by',
            //'memo_pg_no',
            //'delivered_at',
            //'delivered_by',
            //'reject_notes:ntext',
            //'wo_color_id',
            //'kombinasi',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
