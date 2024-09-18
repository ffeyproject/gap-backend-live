<?php
use common\models\ar\TrnInspecting;
use common\models\ar\TrnInspectingSearch;
use common\models\ar\TrnScGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel TrnInspectingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inspectings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inspecting-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar' => [
            [
                'content'=> Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success']),
                'options' => ['class' => 'btn-group']
            ],
            //'{export}',
            //'{toggleData}'
        ],
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            //'id',
	    ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],
            'no',
            [
                'attribute'=>'jenis_process',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    if($data->kartu_process_dyeing_id !== null){
                        $scGreige = $data->kartuProcessDyeing->scGreige;
                        return $scGreige::processOptions()[$scGreige->process];
                    }else if($data->kartu_process_printing_id !== null){
                        $scGreige = $data->kartuProcessPrinting->scGreige;
                        return $scGreige::processOptions()[$scGreige->process];
                    }else if($data->memo_repair_id !== null){
                        $scGreige = $data->memoRepair->scGreige;
                        return $scGreige::processOptions()[$scGreige->process];
                    }
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnScGreige::processOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            //'process_dyeing_id',
            //'process_printing_id',
            //'no_urut',
            [
                'attribute'=>'woNo',
                'label'=>'No WO',
                'value'=>'wo.no'
            ],
            [
                'attribute'=>'kpdNo',
                'label'=>'No Kartu Proses Dyeing',
                'value'=>'kartuProcessDyeing.no'
            ],
            [
                'attribute'=>'kppNo',
                'label'=>'No Kartu Proses Printing',
                'value'=>'kartuProcessPrinting.no'
            ],
            //[
                //'attribute'=>'memoRepairNo',
                //'label'=>'No Memo Repair',
                //'value'=>'memoRepair.no'
            //],
	    'kombinasi',
            'date:date',
	    'tanggal_inspeksi:date',
            'no_lot',            //'date',
            //'tanggal_inspeksi',
            //'no_lot',
            //'kombinasi',
            //'note:ntext',
            [
                'attribute'=>'status',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnInspecting::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            //'grade_a_pcs',
            //'grade_b_pcs',
            //'grade_c_pcs',
            //'piece_kecil_pcs',
            //'sample_pcs',
            //'grade_a_roll',
            //'grade_b_roll',
            //'grade_c_roll',
            //'piece_kecil_roll',
            //'sample_roll',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'approved_at',
            //'approved_by',

            
        ],
    ]); ?>


</div>
