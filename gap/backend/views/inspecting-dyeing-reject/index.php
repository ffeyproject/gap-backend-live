<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\InspectingDyeingRejectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inspecting Dyeing Rejects';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inspecting-printing-reject-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar'=>false,
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            'id',
            'kartu_proses_id',
            'no_urut',
            'no',
            'date:date',
            'untuk_bagian',
            'pcs',
            //'keterangan',
            'penerima',
            'mengetahui',
            'pengirim',
            //'created_at',
            //'created_by',
        ],
    ]); ?>


</div>
