<?php
use common\models\ar\TrnKartuProsesPrinting;
use common\models\ar\TrnMemoRepair;
use common\models\ar\TrnStockGreige;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnMemoRepairSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inspect Memo Repair';
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="kartu-proses-printing-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['kartu-proses-printing'], ['class' => 'btn btn-default']),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar'=>false,
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            'id',
            //'sc_greige_id',
            [
                'attribute'=>'scNo',
                'label'=>'Nomor sc',
                'value'=>'sc.no'
            ],
            [
                'attribute'=>'moNo',
                'label'=>'Nomor MO',
                'value'=>'mo.no'
            ],
            [
                'attribute'=>'woNo',
                'label'=>'Nomor WO',
                'value'=>'wo.no'
            ],
            [
                'attribute'=>'returBuyerNo',
                'label'=>'Nomor Retur Buyer',
                'value'=>'returBuyer.no'
            ],
            [
                'attribute' => 'dateRange',
                'label' => 'Tanggal',
                'value' => 'date',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        //'timePicker'=>true,
                        //'timePickerIncrement'=>5,
                        'locale'=>[
                            //'format'=>'Y-m-d H:i:s',
                            'format'=>'Y-m-d',
                            'separator'=>' to ',
                        ]
                    ]
                ]
            ],
            //'no_urut',
            'no',
            //'note:ntext',
            [
                'attribute' => 'status',
                'value' => function($data){
                    /* @var $data TrnMemoRepair*/
                    return TrnMemoRepair::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnMemoRepair::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            'created_at:datetime',
            'created_by',
            //'updated_at',
            //'updated_by',
            //'mutasi_at',
            //'mutasi_by',
            //'mutasi_note:ntext',
            [
                'class' => 'kartik\grid\ActionColumn',
                'template'=>'{view} {inspect}',
                'buttons'=>[
                    'view'=>function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', ['/trn-memo-repair/view', 'id'=>$model->id], ['title'=>'Detail Memo Repair', 'target'=>'_blank']);
                    },
                    'inspect'=>function ($url, $model, $key) {
                        /* @var $model TrnMemoRepair*/
                        return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>', ['create', 'process'=>$model->scGreige->process, 'repairId'=>$model->id], ['title'=>'Buat Inspecting']);
                    },
                ]
            ],
        ],
    ]); ?>


</div>
