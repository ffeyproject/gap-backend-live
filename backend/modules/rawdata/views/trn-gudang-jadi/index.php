<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadi;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnGudangJadiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gudang Jadi';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="trn-gudang-jadi-index">
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'id' => 'GdJadiGrid',
            'resizableColumns' => false,
            'responsiveWrap' => false,
            'toolbar' => [
                '{toggleData}'
            ],
            'panel' => [
                'type' => 'default',
                'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
                'after'=>Html::a('Set Sebagai Siap Kirim', ['set-siap-kirim'], [
                        'class' => 'btn btn-info',
                        'onclick' => 'siapKirim(event);',
                        'title' => 'Set Sebagai Siap Kirim'
                    ]).' '.Html::a('Set Sebagai Stock', ['set-stock'], [
                        'class' => 'btn btn-default',
                        'onclick' => 'stock(event);',
                        'title' => 'Set Sebagai Stock'
                    ]).' '.Html::a('Kirim', ['kirim'], [
                        'class' => 'btn btn-default',
                        'onclick' => 'kirim(event);',
                        'title' => 'Kirim'
                    ]),
                //'footer'=>false
            ],
            'columns' => [
                ['class' => 'kartik\grid\SerialColumn'],
                ['class' => 'kartik\grid\ActionColumn', 'template' => '{view}'],
                [
                    'class' => 'kartik\grid\CheckboxColumn',
                    // you may configure additional properties here
                ],

                //'id',
                [
                    'attribute' => 'jenis_gudang',
                    'value' => function($data){
                        /* @var $data TrnGudangJadi*/
                        return TrnGudangJadi::jenisGudangOptions()[$data->jenis_gudang];
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => TrnGudangJadi::jenisGudangOptions(),
                        'options' => ['placeholder' => '...'],
                        'pluginOptions'=>[
                            'allowClear' => true,
                        ]
                    ],
                ],
                [
                    'attribute'=>'marketingName',
                    'label'=>'Marketing',
                    'value'=>'wo.mo.scGreige.sc.marketing.full_name'
                ],
                [
                    'attribute'=>'customerName',
                    'label'=>'Buyer',
                    'value'=>'wo.mo.scGreige.sc.cust.name'
                ],
                [
                    'attribute'=>'scNo',
                    'label'=>'Nomor SC',
                    'value'=>'wo.mo.scGreige.sc.no'
                ],
                [
                    'attribute'=>'woNo',
                    'label'=>'Nomor WO',
                    'value'=>'wo.no'
                ],
                'color',
                [
                    'attribute' => 'source',
                    'value' => function($data){
                        /* @var $data TrnGudangJadi*/
                        return TrnGudangJadi::sourceOptions()[$data->source];
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => TrnGudangJadi::sourceOptions(),
                        'options' => ['placeholder' => '...'],
                        'pluginOptions'=>[
                            'allowClear' => true,
                        ]
                    ],
                ],
                'source_ref',
                [
                    'attribute' => 'unit',
                    'value' => function($data){
                        /* @var $data TrnGudangJadi*/
                        return MstGreigeGroup::unitOptions()[$data->unit];
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => MstGreigeGroup::unitOptions(),
                        'options' => ['placeholder' => '...'],
                        'pluginOptions'=>[
                            'allowClear' => true,
                        ]
                    ],
                ],
                'qty:decimal',
                //'no_urut',
                //'no',

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

                [
                    'attribute' => 'status',
                    'value' => function($data){
                        /* @var $data TrnGudangJadi*/
                        return TrnGudangJadi::statusOptions()[$data->status];
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => TrnGudangJadi::statusOptions(),
                        'options' => ['placeholder' => '...'],
                        'pluginOptions'=>[
                            'allowClear' => true,
                        ]
                    ],
                ],
                //'note:ntext',
                'created_at:datetime',
                'created_by',
                //'updated_at',
                //'updated_by',
            ],
        ]); ?>
    </div>

<?php
$js = <<<JS
console.log('intro');
JS;

//$this->registerJs($js.$this->renderFile(__DIR__.'/js/index.js'), View::POS_END);
