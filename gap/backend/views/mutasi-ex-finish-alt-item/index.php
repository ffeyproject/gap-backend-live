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
        'resizableColumns' => false,
        'responsiveWrap' => false,
        'pjax' => true,
        'toolbar' => [
            '{toggleData}'
        ],
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
            //'after'=>'',
            //'footer'=>false
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template'=>'{add-mix}',
                'buttons'=>[
                    'add-mix' => function($url, $model, $key){
                        /* @var $model MutasiExFinishAltItem*/

                        if($model->status != $model::STATUS_STOCK){
                            return '';
                        }

                        $data = [
                            'id' => $model->id,
                            'greige_id' => $model->gudangJadi->wo->greige_id,
                            'motif' => $model->gudangJadi->wo->greige->nama_kain,
                            'grade' => $model->grade,
                            'grade_name' => TrnStockGreige::gradeOptions()[$model->grade],
                            'qty' => $model->qty,
                            'qty_fmt' => Yii::$app->formatter->asDecimal($model->qty),
                            'unit' => $model->gudangJadi->unit,
                            'unit_name' => MstGreigeGroup::unitOptions()[$model->gudangJadi->unit],
                            'status' => $model->status,
                            'status_name' => $model->statusName,
                            'no_wo' => $model->gudangJadi->wo->no,
                            'nama_buyer' => $model->gudangJadi->wo->mo->scGreige->sc->customerName,
                        ];
                        $dataStr = \yii\helpers\Json::encode($data);
                        return Html::a('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>', '#', [
                            'title' => 'Tambah kedalam item untuk dijual',
                            'onclick' => "addItem(event, {$dataStr})"
                        ]);
                    }
                ]
            ],

            'id',
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
            //'gudang_jadi_id',
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

    <div class="panel panel-default">
        <div class="panel-heading"><strong>Item Untuk Dijual</strong></div>

        <div class="panel-body">
            <table id="ItemsTable" class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Motif</th>
                    <th>Grade</th>
                    <th>Qty</th>
                    <th>Unit</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="panel-footer">
            <?=Html::button('Jual', ['id'=>'BtnJual', 'class'=>'btn btn-success'])?>
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
