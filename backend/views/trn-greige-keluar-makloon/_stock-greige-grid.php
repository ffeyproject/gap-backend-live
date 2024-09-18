<?php
use backend\modules\user\models\User;
use common\models\ar\TrnStockGreige;
use kartik\grid\GridView;
use kartik\widgets\Select2;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\web\JsExpression;
use yii\helpers\Url;
use yii\widgets\Pjax;
?>


<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'StockGreigeGrid',
        'responsiveWrap' => false,
        'pjax' => true,
        'panel' => [
            'type' => 'default',
            'heading' => 'Stocks',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['create'], ['class' => 'btn btn-default']),
            'after'=>false,
            //'footer'=>false
        ],
        'toolbar' => [],
        'showPageSummary'=>true,
        'columns' => [
            //['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template'=>'{view}',
                'buttons'=>[
                    'view' => function($url, $model, $key){
                        /* @var $model TrnStockGreige*/
                        $data = [
                            'id' => $model->id,
                            'nama_greige' => $model->greige->nama_kain,
                            'grade' => $model->grade,
                            'grade_name' => $model::gradeOptions()[$model->grade],
                            'qty' => $model->panjang_m,
                            'qty_fmt' => Yii::$app->formatter->asDecimal($model->panjang_m),
                            'lot_lusi' => $model->lot_lusi,
                            'lot_pakan' => $model->lot_pakan,
                            'asal_greige' => $model->asal_greige,
                            'asal_greige_name' => $model::asalGreigeOptions()[$model->asal_greige]
                        ];
                        $dataStr = \yii\helpers\Json::encode($data);
                        return Html::a('<span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>', '#', [
                            'onclick' => "addItem(event, {$dataStr})"
                        ]);
                    }
                ]
            ],
            /*[
                'class' => 'kartik\grid\CheckboxColumn',
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    if($model->status != $model::STATUS_VALID){
                        return ['value' => '', 'disabled'=>'disabled'];
                    }
                    return ['value' => $model->id];
                }
            ],*/

            'id',
            [
                'attribute' => 'dateRange',
                'label' => 'TANGGAL',
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
            'no_document',
            'no_lapak',
            [
                'attribute'=>'status_tsd',
                'value'=>function($data){
                    /* @var $data TrnStockGreige*/
                    return $data::tsdOptions()[$data->status_tsd];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\TrnStockGreige::tsdOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'label'=>'Greige',
                'attribute'=>'greigeNamaKain',
                'value'=>'greige.nama_kain'
            ],
            [
                'attribute'=>'grade',
                'value'=>function($data){
                    /* @var $data TrnStockGreige*/
                    return $data::gradeOptions()[$data->grade];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::gradeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            'lot_lusi',
            'lot_pakan',
            'no_set_lusi',
            [
                'attribute'=>'panjang_m',
                'format'=>'decimal',
                'pageSummary'=>true
            ],
            [
                'label'=>'Status',
                'value'=>function($data){
                    /* @var $data TrnStockGreige*/
                    return $data::statusOptions()[$data->status];
                },
                /*'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],*/
            ],
            [
                'attribute'=>'asal_greige',
                'value'=>function($data){
                    /* @var $data TrnStockGreige*/
                    return $data::asalGreigeOptions()[$data->asal_greige];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::asalGreigeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            /*[
                'attribute'=>'jenis_gudang',
                'value'=>'jenisGudangName',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::jenisGudangOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],*/
            'is_pemotongan:boolean',
            'is_hasil_mix:boolean',
            //'pengirim',
            //'mengetahui',
            //'note:ntext',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
        ],
    ]);

    