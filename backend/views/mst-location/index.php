<?php

use common\models\ar\MstLocation;
use kartik\widgets\Alert;
use yii\bootstrap\Collapse;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\MstLocationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Location';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-location-index">
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
            // 'id',
            // [
            //     'attribute' => 'groupNamaKain',
            //     'label' => 'Greige Group',
            //     'value' => function($data){
            //         var_dump($data);
            //         die;
            //         /* @var $data MstGreige*/
            //         return Html::a($data->group->nama_kain, ['view', 'id'=>$data->loc_id], ['target'=>'blank', 'title'=>'Detail Location']);
            //     },
            //     'format'=>'raw'
            // ],
            [
                'attribute' => 'Location Name',
                'value' => function($data){
                    /* @var $data MstGreige*/
                    return Html::a($data->loc_name, ['view', 'id'=>$data->loc_id], ['title'=>'Detail Location']);
                },
                'format'=>'raw'
            ],
            // 'alias',
            // 'no_dok_referensi',
            // 'gap:decimal',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            // 'aktif:boolean',
            // 'stock:decimal',
            // 'available',
            // 'booked_wo',
            // 'booked:decimal',
            // 'stock_pfp:decimal',
            // 'booked_pfp:decimal'
        ],
    ]); ?>
</div>
