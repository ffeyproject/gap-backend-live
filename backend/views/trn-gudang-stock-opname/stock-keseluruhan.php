<?php

use backend\components\ajax_modal\AjaxModal;
use common\models\ar\TrnGudangStockOpname;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnStockGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Stock Opname Keseluruhan';
$this->params['breadcrumbs'][] = $this->title;

\backend\assets\DataTablesAsset::register($this);

echo AjaxModal::widget([
    'id' => 'allStockModal',
    'size' => 'modal-md',
    'header' => '<h4 class="modal-title">...</h4>',
]);
?>
<div class="trn-stock-greige-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Html::a('Lihat Stock Keseluruhan', ['seluruh-stock'], [
            'class' => 'btn btn-success',
            'title' => 'Lihat Stock Keseluruhan',
            'data-toggle'=>"modal",
            'data-target'=>"#allStockModal",
            'data-title' => 'Lihat Stock Keseluruhan'
        ])?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'StockGreigeGrid',
        'responsiveWrap' => false,
        'pjax' => true,
        'panel' => [
            'type' => 'default',
            'before'=>Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['stock-keseluruhan'], ['class' => 'btn btn-default']).
                // Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success']).
                Html::a('<i class="glyphicon glyphicon-plus"></i> Bulk', ['create-dua'], ['class' => 'btn btn-info']),
                // Html::a('Change Notes', ['change-notes'], [
                //     'class' => 'btn btn-warning',
                //     'onclick' => 'changeNotes(event);',
                //     'title' => 'Change Notes Selected Items'
                // ]).
                ['class'=>'btn-group', 'role'=>'group']
            ),
            'after'=>false,
            //'footer'=>false
        ],
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            // [
            //     'class' => 'kartik\grid\CheckboxColumn',
            //     'checkboxOptions' => function ($model, $key, $index, $column) {
            //         $data = [
            //             'id' => $model->id,
            //             'tanggal' => Yii::$app->formatter->asDate($model->trnGudangStockOpname->date),
            //             'no_document' => $model->trnGudangStockOpname->no_document,
            //             'no_lapak' => $model->trnGudangStockOpname->no_lapak,
            //             'nama_greige' => $model->trnGudangStockOpname->greige->nama_kain,
            //             'grade' => $model->grade,
            //             'grade_name' => $model::gradeOptions()[$model->grade],
            //             'qty' => $model->panjang_m,
            //             'qty_fmt' => Yii::$app->formatter->asDecimal($model->panjang_m),
            //             'lot_lusi' => $model->trnGudangStockOpname->lot_lusi,
            //             'lot_pakan' => $model->trnGudangStockOpname->lot_pakan,
            //             'asal_greige' => $model->trnGudangStockOpname->asal_greige,
            //             'asal_greige_name' => $model->trnGudangStockOpname::asalGreigeOptions()[$model->trnGudangStockOpname->asal_greige],
            //             'no_mc_weaving' => $model->no_set_lusi
            //         ];
            //         $dataStr = \yii\helpers\Json::encode($data);
            
            //         return [
            //             'value' => $model->id,
            //             'data-item' => $dataStr,   // simpan data di attribute
            //         ];
            //     }
            // ],
            

            [
                'class' => 'kartik\grid\ActionColumn',
                'header' => Html::a('<span class="glyphicon glyphicon-plus-sign"></span>', [''], [
                    'onclick' => 'selectAll(event);',
                    'title' => 'Select semua item'
                ]),
                'template'=>'{add-set-out}',
                'buttons'=>[
                    'add-set-out' => function($url, $model, $key){
                        if($model->trnGudangStockOpname->status != $model->trnGudangStockOpname::STATUS_POSTED){
                            return '';
                        }
            
                        $data = [
                            'id' => $model->id,
                            'tanggal' => Yii::$app->formatter->asDate($model->trnGudangStockOpname->date),
                            'no_document' => $model->trnGudangStockOpname->no_document,
                            'no_lapak' => $model->trnGudangStockOpname->no_lapak,
                            'nama_greige' => $model->trnGudangStockOpname->greige->nama_kain,
                            'grade' => $model->grade,
                            'grade_name' => $model::gradeOptions()[$model->grade],
                            'qty' => $model->panjang_m,
                            'qty_fmt' => Yii::$app->formatter->asDecimal($model->panjang_m),
                            'lot_lusi' => $model->trnGudangStockOpname->lot_lusi,
                            'lot_pakan' => $model->trnGudangStockOpname->lot_pakan,
                            'asal_greige' => $model->trnGudangStockOpname->asal_greige,
                            'asal_greige_name' => $model->trnGudangStockOpname::asalGreigeOptions()[$model->trnGudangStockOpname->asal_greige],
                            'no_mc_weaving' => $model->no_set_lusi
                        ];
                        $dataStr = \yii\helpers\Json::encode($data);
            
                        return Html::a('<span class="glyphicon glyphicon-plus-sign"></span>', '#', [
                            'class' => 'add-set-out',
                            'data-item' => $dataStr
                        ]);
                    }
                ]
            ],            

            'id',
            [
                'attribute' => 'dateRange',
                'label' => 'TANGGAL',
                'value' => 'trnGudangStockOpname.date',
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
            [
                'attribute'=>'no_document',
                'value'=>'trnGudangStockOpname.no_document'
            ],
            // 'no_lapak',
            [
                'attribute'=>'no_lapak',
                'value'=>'trnGudangStockOpname.no_lapak'
            ],
            [
                'attribute' => 'status_tsd',
                'value' => function($data) {
                    return \common\models\ar\TrnGudangStockOpname::tsdOptions()[$data->trnGudangStockOpname->status_tsd] ?? '-';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\TrnGudangStockOpname::tsdOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'label'=>'Greige',
                'attribute'=>'greigeNamaKain',
                'value'=>'trnGudangStockOpname.greige.nama_kain'
            ],
            [
                'attribute'=>'grade',
                'value'=>function($data){
                    /* @var $data TrnStockGreige*/
                    return $data::gradeOptions()[$data->grade];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnGudangStockOpname::gradeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'attribute'=>'lot_lusi',
                'value'=>'trnGudangStockOpname.lot_lusi'
            ],
            [
                'attribute'=>'lot_pakan',
                'value'=>'trnGudangStockOpname.lot_pakan'
            ],
            [   'label'=>'No. MC Weaving',
                'attribute'=>'no_set_lusi',
                'value'=>'no_set_lusi'
            ],
            [
                'attribute'=>'panjang_m',
                'format'=>'decimal',
                'pageSummary'=>true
            ],
            [
                'attribute'=>'status',
                'value'=>function($data){
                    /* @var $data TrnStockGreige*/
                    return $data->trnGudangStockOpname::statusOptions()[$data->trnGudangStockOpname->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnGudangStockOpname::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [   
                'label'=>'Keluar',
                'attribute'=>'is_out',
                'format'=>'boolean',
            ],
            [
                'attribute'=>'asal_greige',
                'value'=>function($data){
                    /* @var $data TrnStockGreige*/
                    return $data->trnGudangStockOpname::asalGreigeOptions()[$data->trnGudangStockOpname->asal_greige];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnGudangStockOpname::asalGreigeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            // /*[
            //     'attribute'=>'jenis_gudang',
            //     'value'=>'jenisGudangName',
            //     'filterType' => GridView::FILTER_SELECT2,
            //     'filterWidgetOptions' => [
            //         'data' => TrnStockGreige::jenisGudangOptions(),
            //         'options' => ['placeholder' => '...'],
            //         'pluginOptions' => [
            //             'allowClear' => true
            //         ],
            //     ],
            // ],*/
            'trnGudangStockOpname.is_pemotongan:boolean',
            'trnGudangStockOpname.is_hasil_mix:boolean',
            //'pengirim',
            //'mengetahui',
            //'note:ntext',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
        ],
    ]); ?>

    <?=$this->render('_transfer-out')?>
</div>

<?php
$this->registerJsVar('setOutItems', []);

$actionUrl = \yii\helpers\Url::to(['set-out']);
$js = <<<JS
var actionUrl = "{$actionUrl}";
JS;
//$this->registerJsVar('actionUrl', $actionUrl);
$this->registerJs($js.$this->render('js/set-out.js'), View::POS_END);
