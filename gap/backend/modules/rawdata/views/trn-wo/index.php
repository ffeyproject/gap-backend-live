<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rawdata\models\TrnWoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Wos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-wo-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Trn Wo', ['create'], ['class' => 'btn btn-success']) ?>
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
            'jenis_order',
            //'greige_id',
            //'mengetahui_id',
            //'apv_mengetahui_at',
            //'reject_note_mengetahui:ntext',
            //'no_urut',
            //'no',
            //'date',
            //'plastic_size',
            //'shipping_mark:ntext',
            //'note:ntext',
            //'note_two:ntext',
            //'marketing_id',
            //'apv_marketing_at',
            //'reject_note_marketing:ntext',
            //'posted_at',
            //'closed_at',
            //'closed_by',
            //'closed_note:ntext',
            //'batal_at',
            //'batal_by',
            //'batal_note:ntext',
            //'status',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'handling_id',
            //'papper_tube_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
