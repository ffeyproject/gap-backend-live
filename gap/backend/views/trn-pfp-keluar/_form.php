<?php

use backend\modules\user\models\User;
use common\models\ar\TrnStockGreige;
use kartik\grid\GridView;
use kartik\widgets\Select2;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnPfpKeluar */
/* @var $form ActiveForm */
/* @var $searchModel common\models\ar\TrnStockGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelsItem common\models\ar\TrnPfpKeluarItem[] */

\backend\assets\DataTablesAsset::register($this);

$keluarItems = [];
if(!$model->isNewRecord){
    foreach ($modelsItem as $modelItem) {
        $modelStock = $modelItem->stockPfp;
        $keluarItems[] = [
            'id' => $modelStock->id,
            'nama_greige' => $modelStock->greige->nama_kain,
            'qty' => $modelStock->panjang_m,
            'qty_fmt' => Yii::$app->formatter->asDecimal($modelStock->panjang_m),
            'color' => $modelStock->color,
            'asal_greige' => $modelStock->asal_greige,
            'asal_greige_name' => $modelStock::asalGreigeOptions()[$modelStock->asal_greige],
            'jenis_gudang' => $modelStock->jenis_gudang,
            'jenis_gudang_name' => $modelStock->pfpJenisGudangName,
            /*'grade' => $modelStock->grade,
            'grade_name' => $modelStock::gradeOptions()[$modelStock->grade],
            'lot_lusi' => $modelStock->lot_lusi,
            'lot_pakan' => $modelStock->lot_pakan,*/
        ];
    }
}
?>

    <div class="trn-greige-keluar-form">

        <?php $form = ActiveForm::begin(['id'=>'GreigeKeluarForm']); ?>

        <div class="row">
            <div class="col-md-6">
                <div class="box">
                    <div class="box-body">
                        <?= $form->field($model, 'jenis')->widget(Select2::classname(), [
                            'data' => $model::jenisOptions(),
                            'options' => ['placeholder' => 'Pilih ...'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]) ?>

                        <?= $form->field($model, 'destinasi')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'no_referensi')->textInput(['maxlength' => true]) ?>

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
                                <th>Qty</th>
                                <th>Color</th>
                                <th>Asal Greige</th>
                                <th>Jenis Gudang</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="box-footer">Total: <span id="TotalQty">0</span></div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

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
                                'qty' => $model->panjang_m,
                                'qty_fmt' => Yii::$app->formatter->asDecimal($model->panjang_m),
                                'color' => $model->color,
                                'asal_greige' => $model->asal_greige,
                                'asal_greige_name' => $model::asalGreigeOptions()[$model->asal_greige],
                                'jenis_gudang' => $model->jenis_gudang,
                                'jenis_gudang_name' => $model->pfpJenisGudangName,
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
                'color',
                [
                    'attribute'=>'status',
                    'value'=>function($data){
                        /* @var $data TrnStockGreige*/
                        return $data::statusOptions()[$data->status];
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => TrnStockGreige::statusOptions(),
                        'options' => ['placeholder' => '...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ],
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
                [
                    'attribute'=>'pfp_jenis_gudang',
                    'value'=>function($data){
                        /* @var $data TrnStockGreige*/
                        return $data::pfpJenisGudangOptions()[$data->pfp_jenis_gudang];
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => TrnStockGreige::pfpJenisGudangOptions(),
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

<?php
$this->registerJsVar('keluarItems', $keluarItems);
$this->registerJs($this->render('js/form.js'), $this::POS_END);
$this->registerJsFile('@web/js/jqueryNumber/jquery.number.min.js', ['depends'=>\yii\web\JqueryAsset::class]);
