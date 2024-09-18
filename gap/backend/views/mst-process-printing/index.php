<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\MstProcessPrintingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Master Data Process Printing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-process-printing-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before'=>Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']).
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success']),
                ['class'=>'btn-group', 'role'=>'group']
            ),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar'=>[
            '{export}',
            //'{toggleData}'
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            //'id',
            'order',
            'max_pengulangan',
            'nama_proses',
            'tanggal:boolean',
            'start:boolean',
            'stop:boolean',
            'no_mesin:boolean',
            'operator:boolean',
            'temp:boolean',
            'speed_depan:boolean',
            'speed_belakang:boolean',
            'speed:boolean',
            'resep:boolean',
            'density:boolean',
            'jumlah_pcs:boolean',
            'lebar_jadi:boolean',
            'panjang_jadi:boolean',
            'info_kualitas:boolean',
            'gangguan_produksi:boolean',
            'over_feed:boolean',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',

            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],
        ],
    ]); ?>


</div>
