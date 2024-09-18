<?php
use common\models\ar\TrnInspecting;
use common\models\ar\TrnInspectingSearch;
use common\models\ar\TrnScGreige;
use common\models\ar\InspectingItem;
use common\models\ar\MstGreigeGroup;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel TrnInspectingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Riwayat Penerimaan Packing';
$this->params['breadcrumbs'][] = $this->title;
$dataProvider->pagination->pageSize = 10;
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
            'woNo',
            'kombinasi',
            'no_lot',
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
            [
                'label' => 'Total Grade A',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    $qTotal = (new \yii\db\Query())->from(\common\models\ar\InspectingItem::tableName())
                        ->where(['inspecting_id'=>$data->id])
                        ->andWhere(['grade' => InspectingItem::GRADE_A])
                        ->sum('qty');
                    return $qTotal > 0 ? $qTotal : 0;
                },
                'format' => 'decimal'
            ],
            [
                'label' => 'Total Grade A+',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    $qTotal = (new \yii\db\Query())->from(\common\models\ar\InspectingItem::tableName())
                        ->where(['inspecting_id'=>$data->id])
                        ->andWhere(['grade' => InspectingItem::GRADE_A_PLUS])
                        ->sum('qty');
                    return $qTotal > 0 ? $qTotal : 0;
                },
                'format' => 'decimal'
            ],
            [
                'label' => 'Total Grade A*',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    $qTotal = (new \yii\db\Query())->from(\common\models\ar\InspectingItem::tableName())
                        ->where(['inspecting_id'=>$data->id])
                        ->andWhere(['grade' => InspectingItem::GRADE_A_ASTERISK])
                        ->sum('qty');
                    return $qTotal > 0 ? $qTotal : 0;
                },
                'format' => 'decimal'
            ],
            [
                'label' => 'Total Grade B',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    $qTotal = (new \yii\db\Query())->from(\common\models\ar\InspectingItem::tableName())
                        ->where(['inspecting_id'=>$data->id])
                        ->andWhere(['grade' => InspectingItem::GRADE_B])
                        ->sum('qty');
                    return $qTotal > 0 ? $qTotal : 0;
                },
                'format' => 'decimal'
            ],
            [
                'label' => 'Total Grade C',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    $qTotal = (new \yii\db\Query())->from(\common\models\ar\InspectingItem::tableName())
                        ->where(['inspecting_id'=>$data->id])
                        ->andWhere(['grade' => InspectingItem::GRADE_C])
                        ->sum('qty');
                    return $qTotal > 0 ? $qTotal : 0;
                },
                'format' => 'decimal'
            ],
            [
                'label' => 'Total Piece Kecil',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    $qTotal = (new \yii\db\Query())->from(\common\models\ar\InspectingItem::tableName())
                        ->where(['inspecting_id'=>$data->id])
                        ->andWhere(['grade' => InspectingItem::GRADE_PK])
                        ->sum('qty');
                    return $qTotal > 0 ? $qTotal : 0;
                },
                'format' => 'decimal'
            ],
            [
                'label' => 'Total Contoh',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    $qTotal = (new \yii\db\Query())->from(\common\models\ar\InspectingItem::tableName())
                        ->where(['inspecting_id'=>$data->id])
                        ->andWhere(['grade' => InspectingItem::GRADE_SAMPLE])
                        ->sum('qty');
                    return $qTotal > 0 ? $qTotal : 0;
                },
                'format' => 'decimal'
            ],
            [
                'label' => 'Total',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    $qTotal = (new \yii\db\Query())->from(\common\models\ar\InspectingItem::tableName())
                        ->where(['inspecting_id'=>$data->id])
                        ->sum('qty');
                    return $qTotal > 0 ? $qTotal : 0;
                },
                'format' => 'decimal'
            ],
            [
                'attribute'=>'unit',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                   return \common\models\ar\MstGreigeGroup::unitOptions()[$data->unit];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' =>  \common\models\ar\MstGreigeGroup::unitOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            //'date',
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

            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],
        ],
    ]); ?>


</div>
