<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\MstProcessPfpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Master Data Process PFP';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-process-pfp-index">
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
            'shift_operator:boolean',
            'temp:boolean',
            'speed:boolean',
            'waktu:boolean',
            'program_number:boolean',
            'ex_relax:boolean',
            'ex_wr_oligomer:boolean',
            'ex_dyeing:boolean',
            'wr_pcnt:boolean',
            'rpm:boolean',
            'density:boolean',
            'jamur:boolean',
            'karat:boolean',
            'over_feed:boolean',
            'counter:boolean',
            'lebar_jadi:boolean',
            'info_kualitas:boolean',
            'gangguan_produksi:boolean',
            'gangguan_produksi:boolean',
            'gramasi:boolean',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',

            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],
        ],
    ]); ?>


</div>
