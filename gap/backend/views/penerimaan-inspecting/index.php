<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnInspecting;
use common\models\ar\TrnInspectingSearch;
use common\models\ar\TrnScGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel TrnInspectingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Penerimaan Packing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inspecting-index">
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
            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            //'id',
            [
                'attribute' => 'dateRange',
                'label' => 'Tanggal Kirim',
                'value' => 'date',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        'locale'=>[
                            'format'=>'Y-m-d',
                            'separator'=>' to ',
                        ]
                    ]
                ],
            ],
            'no',
            //'wo_id',
            'woNo',
            'kombinasi',
            [
                'label' => 'Warna',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    return '-';
                }
            ],
            [
                'label' => 'No. Design',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    return $data->mo->design;
                }
            ],
            [
                'label' => 'Article',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    return $data->mo->article;
                }
            ],
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
            'kpdNo',
            'kppNo',
            'memoRepairNo',
            [
                'label' => 'No. Lot',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    return $data->no_lot;
                }
            ],
            [
                'label' => 'Total Qty',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    $qTotal = (new \yii\db\Query())->from(\common\models\ar\InspectingItem::tableName())
                        ->where(['inspecting_id'=>$data->id])
                        ->sum('qty');
                    return $qTotal > 0 ? $qTotal : 0;
                },
                'format' => 'decimal'
            ],
            //'process_dyeing_id',
            //'process_printing_id',
            //'no_urut',
            [
                'label' => 'Satuan',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    return MstGreigeGroup::unitOptions()[$data->unit];
                }
            ],
            //'tanggal_inspeksi',
            //'no_lot',
            //'kombinasi',
            //'note:ntext',
            /*[
                'attribute'=>'status',
                'value'=>function($data){
                    ///* @var $data Inspecting
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => Inspecting::statusOptions(),
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
        ],
    ]); ?>


</div>
