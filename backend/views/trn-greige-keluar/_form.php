<?php

use backend\modules\user\models\User;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnGreigeKeluar;
use kartik\grid\GridView;
use kartik\widgets\Select2;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnGreigeKeluar */
/* @var $form ActiveForm */
/* @var $searchModel common\models\ar\TrnStockGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelsItem \common\models\ar\TrnGreigeKeluarItem[] */

\backend\assets\DataTablesAsset::register($this);

$keluarItems = array();
if (!$model->isNewRecord) {
    foreach ($modelsItem as $modelItem) {
        $modelStock = $modelItem->stockGreige;
        $keluarItems[] = array(
            'id' => $modelStock->id,
            'nama_greige' => $modelStock->greige->nama_kain,
            'grade' => $modelStock->grade,
            'grade_name' => $modelStock::gradeOptions()[$modelStock->grade],
            'qty' => $modelStock->panjang_m,
            'qty_fmt' => Yii::$app->formatter->asDecimal($modelStock->panjang_m),
            'lot_lusi' => $modelStock->lot_lusi,
            'lot_pakan' => $modelStock->lot_pakan,
            'asal_greige' => $modelStock->asal_greige,
            'asal_greige_name' => $modelStock::asalGreigeOptions()[$modelStock->asal_greige],
        );
    }
}
?>

<div class="trn-greige-keluar-form">

    <?php $form = ActiveForm::begin(['id' => 'GreigeKeluarForm']); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-body">

                    <?= $form->field($model, 'jenis')->widget(Select2::classname(), [
                        'data' => array_filter(TrnGreigeKeluar::jenisOptions(), function ($key) {
                            return $key !== TrnGreigeKeluar::JENIS_MAKLOON;
                        }, ARRAY_FILTER_USE_KEY),
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]) ?>

                    <?= $form->field($model, 'destinasi')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'no_referensi')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'approved_by')->widget(Select2::classname(), [
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'data' => User::getUsersByRoles(),
                    ])->label('Diperintahkan Oleh') ?>

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
                </div>
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

        // ✅ KUNING jika stock greige ada di stock opname
        'rowOptions' => function ($model) {
            /* @var $model \common\models\ar\TrnStockGreige */
            return $model->isDuplicated
                ? array('style' => 'background-color:#fff3cd;')
                : array();
        },

        'responsiveWrap' => false,
        'pjax' => true,
        'panel' => [
            'type' => 'default',
            'heading' => 'Stocks',
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['create'], ['class' => 'btn btn-default']),
            'after' => false,
        ],
        'toolbar' => [],
        'showPageSummary' => true,
        'columns' => [
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        /* @var $model TrnStockGreige */
                        $data = array(
                            'id' => $model->id,
                            'nama_greige' => $model->greige->nama_kain,
                            'grade' => $model->grade,
                            'grade_name' => $model::gradeOptions()[$model->grade],
                            'qty' => $model->panjang_m,
                            'qty_fmt' => Yii::$app->formatter->asDecimal($model->panjang_m),
                            'lot_lusi' => $model->lot_lusi,
                            'lot_pakan' => $model->lot_pakan,
                            'asal_greige' => $model->asal_greige,
                            'asal_greige_name' => $model::asalGreigeOptions()[$model->asal_greige],
                        );

                        $dataStr = \yii\helpers\Json::encode($data);

                        return Html::a('<span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>', '#', [
                            'onclick' => "addItem(event, {$dataStr})",
                        ]);
                    },
                ],
            ],

            'id',
            [
                'attribute' => 'dateRange',
                'label' => 'TANGGAL',
                'value' => 'date',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'Y-m-d',
                            'separator' => ' to ',
                        ],
                    ],
                ],
            ],
            'no_document',
            'no_lapak',
            [
                'attribute' => 'status_tsd',
                'value' => function ($data) {
                    /* @var $data TrnStockGreige */
                    return $data::tsdOptions()[$data->status_tsd];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\TrnStockGreige::tsdOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ],
            ],
            [
                'label' => 'Greige',
                'attribute' => 'greigeNamaKain',
                'value' => 'greige.nama_kain',
            ],
            [
                'attribute' => 'grade',
                'value' => function ($data) {
                    /* @var $data TrnStockGreige */
                    return $data::gradeOptions()[$data->grade];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::gradeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ],
            ],
            'lot_lusi',
            'lot_pakan',
            'no_set_lusi',
            [
                'attribute' => 'panjang_m',
                'format' => 'decimal',
                'pageSummary' => true,
            ],
            [
                'label' => 'Status',
                'value' => function ($data) {
                    /* @var $data TrnStockGreige */
                    return $data::statusOptions()[$data->status];
                },
            ],
            [
                'attribute' => 'asal_greige',
                'value' => function ($data) {
                    /* @var $data TrnStockGreige */
                    return $data::asalGreigeOptions()[$data->asal_greige];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnStockGreige::asalGreigeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ],
            ],
            'is_pemotongan:boolean',
            'is_hasil_mix:boolean',
        ],
    ]); ?>

</div>

<?php
$this->registerJsVar('keluarItems', $keluarItems);
$this->registerJs($this->render('js/form.js'), \yii\web\View::POS_END);
?>