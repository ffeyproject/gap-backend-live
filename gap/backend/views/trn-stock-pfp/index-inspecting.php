<?php
use common\models\ar\TrnInspecting;
use common\models\ar\TrnInspectingSearch;
use common\models\ar\TrnScGreige;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel TrnInspectingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inspectings';
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
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
        'toolbar'=>false,
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            'id',
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
                    }else if($data->kartu_process_pfp_id !== null){
                        return TrnScGreige::processOptions()[TrnScGreige::PROCESS_PFP];
                    }else{
                        $scGreige = $data->kartuProcessMaklon->scGreige;
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
            'no',
            [
                'label'=>'No. Kartu Proses',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    if($data->kartu_process_dyeing_id !== null){
                        $kartuProses = $data->kartuProcessDyeing;
                    }else if($data->kartu_process_pfp_id !== null){
                        $kartuProses = $data->kartuProcessPfp;
                    }else if($data->kartu_process_printing_id !== null){
                        $kartuProses = $data->kartuProcessPrinting;
                    }else $kartuProses = $data->kartuProcessMaklon;

                    return $kartuProses->no;
                }
            ],
            //'date',
            //'tanggal_inspeksi',
            //'no_lot',
            //'kombinasi',
            //'note:ntext',
            /*[
                'attribute'=>'status',
                'value'=>function($data){
                    // @var $data TrnInspecting
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
            ],*/
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

            [
                'class' => 'kartik\grid\ActionColumn',
                'template'=>'{view} {terima-hasil-inspecting}',
                'buttons'=>[
                    'view'=>function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', ['/trn-inspecting/view', 'id'=>$model->id], ['title'=>'Detail Inspecting', 'target'=>'_blank']);
                    },
                    'terima-hasil-inspecting'=>function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>', $url, [
                            'title'=>'Terima hasil inspecting',
                            'data' => [
                                'confirm' => 'Are you sure you want to terima this item?',
                                'method' => 'post',
                            ],
                        ]);
                    }
                ]
            ],
        ],
    ]); ?>


</div>
