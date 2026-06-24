<?php

use common\models\ar\TrnStockGreige;
use kartik\grid\GridView;
use kartik\widgets\Select2;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MutasiPfp */
/* @var $form ActiveForm */
/* @var $searchModel common\models\ar\TrnStockGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

\backend\assets\DataTablesAsset::register($this);

$keluarItems = [];
if(!$model->isNewRecord){
    foreach ($model->mutasiPfpItems as $modelItem) {
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
            'jenis_gudang_name' => $modelStock->jenisGudangName,
        ];
    }
}
?>

<div class="mutasi-pfp-form">

    <?php $form = ActiveForm::begin(['id'=>'MutasiPfpForm']); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <?= $form->field($model, 'no_wo')->textInput(['maxlength' => true]) ?>

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
        ],
        'toolbar' => [],
        'showPageSummary'=>true,
        'columns' => [
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
                            'jenis_gudang_name' => $model->jenisGudangName,
                        ];
                        $dataStr = \yii\helpers\Json::encode($data);
                        return Html::a('<span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>', '#', [
                            'onclick' => "addItem(event, {$dataStr})"
                        ]);
                    }
                ]
            ],

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
        ],
    ]); ?>
</div>

<?php
$this->registerJsVar('keluarItems', $keluarItems);
$this->registerJs($this->render('js/form.js'), $this::POS_END);
$this->registerJsFile('@web/js/jqueryNumber/jquery.number.min.js', ['depends'=>\yii\web\JqueryAsset::class]);
