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

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnGreigeKeluar */
/* @var $form ActiveForm */
/* @var $searchModel common\models\ar\TrnStockGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelsItem \common\models\ar\TrnGreigeKeluarItem[] */

\backend\assets\DataTablesAsset::register($this);

$keluarItems = [];
if(!$model->isNewRecord){
    foreach ($modelsItem as $modelItem) {
        $modelStock = $modelItem->stockGreige;
        $keluarItems[] = [
            'id' => $modelStock->id,
            'nama_greige' => $modelStock->greige->nama_kain,
            'grade' => $modelStock->grade,
            'grade_name' => $modelStock::gradeOptions()[$modelStock->grade],
            'qty' => $modelStock->panjang_m,
            'qty_fmt' => Yii::$app->formatter->asDecimal($modelStock->panjang_m),
            'lot_lusi' => $modelStock->lot_lusi,
            'lot_pakan' => $modelStock->lot_pakan,
            'asal_greige' => $modelStock->asal_greige,
            'asal_greige_name' => $modelStock::asalGreigeOptions()[$modelStock->asal_greige]
        ];
    }
}
$lookupStockGreige = Url::to(['trn-greige-keluar-makloon/lookup-stock-greige']);
?>
<div class="trn-greige-keluar-form">

    <?php $form = ActiveForm::begin(['id'=>'GreigeKeluarForm']); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                <?php
                    $wo = $model->wo_id === null ? '' : $model->wo->no;
                    echo $form->field($model, 'wo_id')->widget(Select2::class, [
                        'initValueText' => $wo, // set the initial display text
                        'options' => ['placeholder' => 'Cari WO...', 'id' => 'select-wo'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                            ],
                            'ajax' => [
                                'url' => Url::to(['ajax/lookup-wo-makloon']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(wo) { return wo.text; }'),
                            'templateSelection' => new JsExpression('function (wo) { return wo.text; }'),
                        ],
                        'pluginEvents' => [
                            'select2:select' => 'function(e){let lookupStockGreigeUrl = "'.$lookupStockGreige.'"; '.$this->renderFile(Yii::$app->controller->viewPath.'/js/wo-on-select.js').'}',
                            'select2:unselect' => 'function(e){ $("#stock-greige-container").hide();}'
                        ]
                    ])->label('Nomor Working Order');
                    ?>


                    <?= $form->field($model, 'destinasi')->textInput(['maxlength' => true]) ?>

                    <?=$form->field($model, 'approved_by')->widget(Select2::class, [
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'data' => User::getUsersByRoles(),
                    ])->label('Diperintahkan Oleh')?>

                    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Items</h3>
                    <div class="box-tools pull-right"></div>
                </div>
                <div class="box-body">
                    <table id="ItemsTable" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Greige</th>
                            <th>Grade</th>
                            <th>Qty</th>
                            <th>Lot Lusi</th>
                            <th>Lot Pakan</th>
                            <th>Asal Greige</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div id="total-items-container"><b>Total Qty : <span id="totalFooter">0</span></b></div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div id="stock-greige-container" style="display:block;">
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
        ]); ?>
    </div>

</div>
<?php
$this->registerJsVar('keluarItems', $keluarItems);
$this->registerJs($this->render('js/form.js'), $this::POS_END);

