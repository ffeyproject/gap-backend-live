<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\MutasiExFinishAltItem;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\MutasiExFinishAltItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Stock Ex Finish GD Jadi Items';
$this->params['breadcrumbs'][] = $this->title;

\backend\assets\DataTablesAsset::register($this);
\backend\assets\BootstrapDatePickerAsset::register($this);
//\backend\assets\Select2Asset::register($this);
?>
<div class="mutasi-ex-finish-alt-item-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'MutasiExFinishGrid',
        'resizableColumns' => false,
        'responsiveWrap' => false,
        'pjax' => true,
        'pjaxSettings' => [
            'options' => [
                'id' => 'MutasiExFinishGrid-pjax',
            ],
        ],
        'toolbar' => [
            '{toggleData}'
        ],
        'panel' => [
            'type' => 'default',
            'heading' => '<i class="glyphicon glyphicon-list"></i> Daftar Stock Ex Finish GD Jadi Items',
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh', ['index'], ['class' => 'btn btn-default']) . ' ' .
                        Html::button('<i class="glyphicon glyphicon-check"></i> Tambah Semua yang Diceklis', [
                            'id' => 'btnAddChecked',
                            'class' => 'btn btn-primary',
                            'title' => 'Tambahkan semua item yang diceklis di halaman ini ke daftar jual'
                        ]) . ' ' .
                        Html::button('<i class="glyphicon glyphicon-unchecked"></i> Hapus Ceklis Halaman Ini', [
                            'id' => 'btnUncheckPage',
                            'class' => 'btn btn-default',
                            'title' => 'Hilangkan ceklis pada halaman ini'
                        ]) . ' ' .
                        '<span id="grid-checked-counter" class="label label-info" style="font-size: 12px; padding: 6px 10px; margin-left: 5px;">0 item diceklis di halaman ini</span>',
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\CheckboxColumn',
                'headerOptions' => ['class' => 'kartik-sheet-style', 'style' => 'width: 35px; text-align: center; vertical-align: middle;'],
                'contentOptions' => ['style' => 'text-align: center; vertical-align: middle;'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    /* @var $model MutasiExFinishAltItem */
                    if ($model->status != $model::STATUS_STOCK) {
                        return ['disabled' => true];
                    }

                    $data = [
                        'id' => $model->id,
                        'gudang_jadi_id' => $model->gudang_jadi_id,
                        'greige_id' => $model->gudangJadi->wo->greige_id,
                        'motif' => $model->gudangJadi->wo->greige->nama_kain,
                        'grade' => $model->grade,
                        'grade_name' => TrnStockGreige::gradeOptions()[$model->grade],
                        'qty' => (float)$model->qty,
                        'qty_fmt' => Yii::$app->formatter->asDecimal($model->qty),
                        'unit' => $model->gudangJadi->unit,
                        'unit_name' => MstGreigeGroup::unitOptions()[$model->gudangJadi->unit],
                        'status' => $model->status,
                        'status_name' => $model->statusName,
                        'no_wo' => $model->gudangJadi->wo->no,
                        'nama_buyer' => $model->gudangJadi->wo->mo->scGreige->sc->customerName,
                    ];

                    return [
                        'value' => $model->id,
                        'class' => 'item-checkbox',
                        'data-item' => \yii\helpers\Json::encode($data),
                    ];
                }
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template'=>'{add-mix}',
                'headerOptions' => ['style' => 'width: 45px; text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'buttons'=>[
                    'add-mix' => function($url, $model, $key){
                        /* @var $model MutasiExFinishAltItem*/

                        if($model->status != $model::STATUS_STOCK){
                            return '';
                        }

                        $data = [
                            'id' => $model->id,
                            'gudang_jadi_id' => $model->gudang_jadi_id,
                            'greige_id' => $model->gudangJadi->wo->greige_id,
                            'motif' => $model->gudangJadi->wo->greige->nama_kain,
                            'grade' => $model->grade,
                            'grade_name' => TrnStockGreige::gradeOptions()[$model->grade],
                            'qty' => (float)$model->qty,
                            'qty_fmt' => Yii::$app->formatter->asDecimal($model->qty),
                            'unit' => $model->gudangJadi->unit,
                            'unit_name' => MstGreigeGroup::unitOptions()[$model->gudangJadi->unit],
                            'status' => $model->status,
                            'status_name' => $model->statusName,
                            'no_wo' => $model->gudangJadi->wo->no,
                            'nama_buyer' => $model->gudangJadi->wo->mo->scGreige->sc->customerName,
                        ];
                        $dataStr = \yii\helpers\Json::encode($data);
                        return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>', '#', [
                            'class' => 'btn btn-xs btn-primary btn-add-single',
                            'title' => 'Tambah item ini ke daftar jual',
                            'onclick' => "addItem(event, {$dataStr})"
                        ]);
                    }
                ]
            ],

            'id',
            [
                'attribute' => 'gudang_jadi_id',
                'label' => 'ID Stock GD Jadi',
                'format' => 'raw',
                'value' => function($data){
                    /* @var $data MutasiExFinishAltItem*/
                    return Html::a($data->gudang_jadi_id, ['/trn-gudang-jadi/view', 'id' => $data->gudang_jadi_id], ['target' => '_blank', 'data-pjax' => '0', 'title' => 'Lihat Detail Stock Gudang Jadi']);
                }
            ],
            /*[
                'label' => 'Tanggal',
                'value' => 'mutasi.created_at',
                'format' => 'date'
            ],*/
            [
                'attribute' => 'dateRange',
                'label' => 'Tanggal',
                'value' => 'mutasi.created_at',
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
                'label' => 'Ex WO',
                'attribute' => 'wo_no',
                'value' => 'gudangJadi.wo.no',
            ],
            [
                'label' => 'No. Referensi',
                'attribute' => 'no_referensi',
                'value' => 'mutasi.no_referensi',
            ],
            //'mutasi_id',
            [
                'label' => 'Motif',
                'attribute' => 'greigeNamaKain',
                'value' => function($data){
                    /* @var $data MutasiExFinishAltItem*/
                    return $data->gudangJadi->wo->greige->nama_kain;
                },
            ],
            [
                'attribute'=>'grade',
                'value'=>function($data){
                    /* @var $data MutasiExFinishAltItem*/
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
            'qty:decimal',
            [
                'label' => 'Unit',
                'value' => function($data){
                    /* @var $data MutasiExFinishAltItem*/
                    return MstGreigeGroup::unitOptions()[$data->gudangJadi->unit];
                },
            ],
            [
                'attribute'=>'status',
                'value' => 'statusName',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => MutasiExFinishAltItem::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
        ],
    ]); ?>

    <div class="panel panel-success" style="margin-top: 20px;">
        <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 15px;">
            <strong style="font-size: 15px;"><i class="glyphicon glyphicon-shopping-cart"></i> Item Untuk Dijual</strong>
            <div>
                <span id="selected-summary" class="label label-primary" style="font-size: 12px; padding: 6px 12px; margin-right: 10px;">0 Item Terpilih</span>
                <button type="button" id="btnClearItems" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> Kosongkan Daftar</button>
            </div>
        </div>

        <div class="panel-body">
            <div class="table-responsive">
                <table id="ItemsTable" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
                    <thead>
                    <tr class="info">
                        <th style="width: 40px; text-align: center;">No</th>
                        <th style="width: 70px; text-align: center;">ID Item</th>
                        <th style="width: 100px; text-align: center;">Stock GD Jadi</th>
                        <th>Ex WO</th>
                        <th>Motif</th>
                        <th style="text-align: center;">Grade</th>
                        <th style="text-align: right;">Qty</th>
                        <th style="text-align: center;">Unit</th>
                        <th>Buyer</th>
                        <th style="width: 50px; text-align: center;">Aksi</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                    <tr class="warning" style="font-weight: bold;">
                        <td colspan="6" class="text-right">TOTAL:</td>
                        <td id="total-qty-cell" class="text-right">0.00</td>
                        <td id="total-unit-cell" class="text-center">-</td>
                        <td colspan="2"><span id="total-summary-text">0 item</span></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="panel-footer text-right">
            <?=Html::button('<i class="glyphicon glyphicon-ok"></i> Jual Item Terpilih', ['id'=>'BtnJual', 'class'=>'btn btn-success btn-lg'])?>
        </div>
    </div>
</div>

    <!--<form class="form-horizontal">
        <div class="form-group">
            <label for="NamaBuyer" class="col-sm-2 control-label">Nama Buyer</label>
            <div class="col-sm-6">
                <select id="NamaBuyer" class="form-control"></select>
            </div>
            <div class="col-sm-2">
                <div class="radio">
                    <label>
                        <input type="radio" name="isResmi" id="isResmiResmi" value="resmi"> Resmi
                    </label>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="radio">
                    <label>
                        <input type="radio" name="isResmi" id="isResmiTidakResmi" value="tidak_resmi"> Tidak Resmi
                    </label>
                </div>
            </div>
        </div>
    </form>-->

<?php
$actionUrl = \yii\helpers\Url::to(['jual-ex-finish/create']);
$urlCustomerSearch = \yii\helpers\Url::to(['/ajax/customer-search']);

$this->registerJsVar('formJual', $this->render('_form_jual'));
$this->registerJsVar('actionUrl', $actionUrl);
$this->registerJsVar('urlCustomerSearch', $urlCustomerSearch);

/*$js = <<<JS
var actionUrl = "{$actionUrl}";
JS;*/

$this->registerJs($this->render('js/index.js'), $this::POS_END);

/*$this->registerCss('
.select2-selection__rendered {
  line-height: 32px !important;
}

.select2-selection {
  height: 34px !important;
}
');*/
