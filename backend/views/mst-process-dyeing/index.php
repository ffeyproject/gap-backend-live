<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\MstProcessDyeingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Master Data Process Dyeing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-process-dyeing-index">
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
            //['class' => 'kartik\grid\SerialColumn'],

            //'id',
            'order',
            'max_pengulangan',
            'nama_proses',
            'tanggal:boolean',
            'start:boolean',
            'stop:boolean',
            'no_mesin:boolean',
            'shift_group:boolean',
            'temp:boolean',
            'speed:boolean',
            'gramasi:boolean',
            'program_number:boolean',
            'density:boolean',
            'over_feed:boolean',
            'lebar_jadi:boolean',
            'panjang_jadi:boolean',
            'info_kualitas:boolean',
            'gangguan_produksi:boolean',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',

            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],
        ],
    ]); ?>
</div>
