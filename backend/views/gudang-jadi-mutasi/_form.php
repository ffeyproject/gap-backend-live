<?php

use common\models\ar\MstGreige;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnStockGreige;
use kartik\grid\GridView;
use kartik\widgets\DatePicker;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\ar\GudangJadiMutasi */
/* @var $searchModel common\models\ar\TrnStockGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelsItem common\models\ar\GudangJadiMutasiItem[] */
/* @var $form ActiveForm */

\backend\assets\DataTablesAsset::register($this);

$itemTotalQty = 0;

$mutasiItem = [];
if(!$model->isNewRecord){
    foreach ($modelsItem as $modelItem) {
        $modelGdJadi = $modelItem->stock;
        $wo = $modelGdJadi->wo;
        $mutasiItem[] = [
            'id' => $modelItem->id,
            'no_wo' => $wo->no,
            'color' => $modelGdJadi->color,
            'no_lot' => '',
            'motif' => $wo->greigeNamaKain,
            'qty' => floatval($modelGdJadi->qty),
            'qty_fmt' => Yii::$app->formatter->asDecimal($modelGdJadi->qty),
            'unit' => $wo->greige->group->unitName,
            'stock_id' => $modelItem->stock_id,
        ];

        //mengambil no_lot
        if($modelGdJadi->source_ref !== null){
            $noLot = (new \yii\db\Query())->from(\common\models\ar\TrnInspecting::tableName())
                ->select('no_lot')
                ->where(['no'=>$modelGdJadi->source_ref])
                ->one()
            ;
            if($noLot !== false){
                $mutasiItem['no_lot'] = $noLot['no_lot'];
            }else{
                $noLot = (new \yii\db\Query())->from(\common\models\ar\InspectingMklBj::tableName())
                    ->select('no_lot')
                    ->where(['no'=>$modelGdJadi->source_ref])
                    ->one()
                ;
                if($noLot !== false){
                    $mutasiItem['no_lot'] = $noLot['no_lot'];
                }
            }
        }

        $itemTotalQty += $modelGdJadi->qty;
    }
}
?>

<div class="gudang-jadi-mutasi-form">

    <?php $form = ActiveForm::begin(['id'=>'GdJadiMutasiForm']); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?=$form->field($model, 'date')->widget(DatePicker::class, [
                                'options' => ['placeholder' => 'Masukan tanggal ...'],
                                'readonly' => true,
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'yyyy-mm-dd',
                                    'todayHighlight' => true
                                ],
                            ])?>

                            <?= $form->field($model, 'pengirim')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'kepala_gudang')->textInput(['maxlength' => true]) ?>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($model, 'dept_tujuan')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'penerima')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Items</h3><span id="TotalQty" class="pull-right"><?=$itemTotalQty?></span>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body">
                    <table id="ItemsTable" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>No. WO</th>
                            <th>Color</th>
                            <th>No. Lot</th>
                            <th>Motif</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php
    $greigeNameFilter = '';
    if(!empty($searchModel->greige_id)){
        $greigeNameFilter = MstGreige::findOne($searchModel['greige_id'])->nama_kain;
    }
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'GudangJadiGrid',
        'responsiveWrap' => false,
        'pjax' => true,
        'panel' => [
            'type' => 'default',
            'heading' => 'Gudang Jadi',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['create'], ['class' => 'btn btn-default']),
            'after'=>false,
            //'footer'=>false
        ],
        'toolbar' => [],
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template'=>'{view}',
                'buttons'=>[
                    'view' => function($url, $m, $key){
                        /* @var $m TrnGudangJadi*/
                        $data = [
                            'stock_id' => $m->id,
                            'no_wo' => $m->wo->no,
                            'color' => $m->color,
                            'no_lot' => '',
                            'motif' => $m->wo->greigeNamaKain,
                            'qty' => floatval($m->qty),
                            'qty_fmt' => Yii::$app->formatter->asDecimal($m->qty),
                            'unit' => $m->wo->greige->group->unitName
                        ];

                        //mengambil no_lot
                        if($m->source_ref !== null){
                            $noLot = (new \yii\db\Query())->from(\common\models\ar\TrnInspecting::tableName())
                                ->select('no_lot')
                                ->where(['no'=>$m->source_ref])
                                ->one()
                            ;
                            if($noLot !== false){
                                $data['no_lot'] = $noLot['no_lot'];
                            }else{
                                $noLot = (new \yii\db\Query())->from(\common\models\ar\InspectingMklBj::tableName())
                                    ->select('no_lot')
                                    ->where(['no'=>$m->source_ref])
                                    ->one()
                                ;
                                if($noLot !== false){
                                    $data['no_lot'] = $noLot['no_lot'];
                                }
                            }
                        }

                        $dataStr = \yii\helpers\Json::encode($data);
                        return Html::a('<span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>', '#', [
                            'onclick' => "addItem(event, {$dataStr})"
                        ]);
                    }
                ]
            ],

            'id',
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
                'label' => 'No. Lot',
                'value' => function($data){
                    /* @var $data TrnGudangJadi*/
                    if($data->source_ref !== null){
                        $noLot = (new \yii\db\Query())->from(\common\models\ar\TrnInspecting::tableName())
                            ->select('no_lot')
                            ->where(['no'=>$data->source_ref])
                            ->one()
                        ;
                        if($noLot){
                            return $noLot['no_lot'];
                        }else{
                            $noLot = (new \yii\db\Query())->from(\common\models\ar\InspectingMklBj::tableName())
                                ->select('no_lot')
                                ->where(['no'=>$data->source_ref])
                                ->one()
                            ;
                            if($noLot){
                                return $noLot['no_lot'];
                            }
                        }
                    }

                    return '-';
                },
            ],
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
                'attribute'=>'grade',
                'value'=>function($data){
                    /* @var $data TrnGudangJadi*/
                    return TrnStockGreige::gradeOptions()[$data->grade];
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
            [
                'attribute'=>'greige_id',
                'label' => 'Motif',
                'value'=>'wo.greigeNamaKain',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'initValueText' => $greigeNameFilter, // set the initial display text
                    'options' => ['placeholder' => 'Cari ...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['ajax/greige-search']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(member) { return member.text; }'),
                        'templateSelection' => new JsExpression('function (member) { return member.text; }'),
                    ],
                ],
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
            'dipotong:boolean',
            'hasil_pemotongan:boolean',
            //'note:ntext',
            'created_at:datetime',
            'created_by',
            //'updated_at',
            //'updated_by',
        ],
    ]); ?>
</div>

<?php
$this->registerJsVar('itemTotalQty', $itemTotalQty);
$this->registerJsVar('mutasiItems', $mutasiItem);
$this->registerJs($this->render('js/form.js'), $this::POS_END);