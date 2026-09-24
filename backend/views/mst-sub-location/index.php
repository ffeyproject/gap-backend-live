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
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'pjax' => true,
        'panel' => [
            'type' => 'default',
            'before'=>Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh', ['index'], ['class' => 'btn btn-default']).' '.
                Html::a('<i class="glyphicon glyphicon-plus"></i> Tambah Sub Lokasi', ['create'], ['class' => 'btn btn-success']),
                ['class'=>'btn-group', 'role'=>'group']
            ),
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],
            [
                'attribute' => 'locs_code',
                'label' => 'Kode Sub Lokasi',
                'value' => function($data){
                    return Html::a($data->locs_code, ['view', 'id'=>$data->locs_code], ['title'=>'Detail Sub Location']);
                },
                'format'=>'raw'
            ],
            'locs_description',
            'locs_floor_code',
            'locs_line_code',
            'locs_column_code',
            'locs_rack_code',
            [
                'attribute' => 'locs_loc_id',
                'label' => 'Master Location',
                'value' => function($data){
                    return $data->location ? $data->location->loc_name : $data->locs_loc_id;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \yii\helpers\ArrayHelper::map(MstLocation::find()->all(), 'loc_id', 'loc_name'),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ]
                ],
            ],
            [
                'attribute' => 'locs_active',
                'label' => 'Aktif',
                'value' => function($data){
                    return ($data->locs_active === 'Y' || $data->locs_active === true || $data->locs_active === 1 || $data->locs_active === '1') ? 'Ya' : 'Tidak';
                },
                'filter' => ['Y' => 'Ya', 'N' => 'Tidak'],
            ],
        ],
    ]); ?>
</div>
