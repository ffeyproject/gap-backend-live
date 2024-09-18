<?php

use common\models\ar\MstGreigeGroup;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\MstGreigeGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mst Greige Groups';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mst-greige-group-index">
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
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],
            'id',
            [
                'attribute' => 'jenis_kain',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => MstGreigeGroup::jenisKainOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
                'value' => function($data){
                    /* @var $data MstGreigeGroup*/
                    return MstGreigeGroup::jenisKainOptions()[$data->jenis_kain];
                },
            ],
            [
                'attribute' => 'lebar_kain',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => MstGreigeGroup::lebarKainOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
                'value' => function($data){
                    /* @var $data MstGreigeGroup*/
                    return MstGreigeGroup::lebarKainOptions()[$data->lebar_kain];
                },
            ],
            'nama_kain',
            'qty_per_batch:decimal',
            [
                'attribute' => 'unit',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => MstGreigeGroup::unitOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
                'value' => function($data){
                    /* @var $data MstGreigeGroup*/
                    return MstGreigeGroup::unitOptions()[$data->unit];
                },
            ],
            //'nilai_penyusutan',
            //'gramasi_kain',
            //'sulam_pinggir',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'aktif:boolean',
        ],
    ]); ?>


</div>
