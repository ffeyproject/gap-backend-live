<?php

use common\models\ar\MstLocation;
use kartik\widgets\Alert;
use yii\bootstrap\Collapse;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\MstSubLocationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sub Location';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-sub-location-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
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
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],
            [
                'attribute' => 'Kode Lokasi',
                'value' => function($data){
                    /* @var $data MstGreige*/
                    return Html::a($data->locs_code, ['view', 'id'=>$data->locs_code], ['title'=>'Detail Sub Location']);
                },
                'format'=>'raw'
            ],
            'locs_floor_code',
            'locs_line_code',
            'locs_column_code',
            'locs_rack_code',
            'locs_loc_id',
        ],
    ]); ?>
</div>
