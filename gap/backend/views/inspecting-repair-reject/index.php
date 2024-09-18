<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\InspectingRepairRejectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inspecting Repair Rejects';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inspecting-repair-reject-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Inspecting Repair Reject', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'memo_repair_id',
            'no_urut',
            'no',
            'date',
            //'untuk_bagian',
            //'pcs',
            //'keterangan',
            //'penerima',
            //'mengetahui',
            //'pengirim',
            //'created_at',
            //'created_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
