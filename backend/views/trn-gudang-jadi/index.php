<?php

use common\models\ar\MstGreige;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use yii\grid\CheckboxColumn;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\web\JqueryAsset;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnGudangJadiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

\backend\assets\DataTablesAsset::register($this);

$this->title = 'Gudang Jadi';
$this->params['breadcrumbs'][] = $this->title;

$greigeNameFilter = '';
if(!empty($searchModel->greige_id)){
    $greigeNameFilter = MstGreige::findOne($searchModel['greige_id'])->nama_kain;
}
?>
<!-- <div class="trn-gudang-jadi-index" style="overflow-x: auto; width: 100%;"> -->
<div class="trn-gudang-jadi-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        // 'options' => ['style' => ['width' => '1920px;']],
        'id' => 'GdJadiGrid',
        'resizableColumns' => false,
        'responsiveWrap' => false,
        'pjax' => true,
        'rowOptions' => function($model) {
            /* @var $model TrnGudangJadi */
            if ($model->opnamePcs !== null) {
                return ['class' => 'info', 'title' => 'Sudah Masuk Stok Opname (Kode: ' . $model->opnamePcs->opname_code . ')'];
            }
            return [];
        },
        'panel' => [
            'type' => 'default',
            'before'=>
                    Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']).' '.
                    Html::a('<i class=" glyphicon glyphicon-plus-sign"></i> Add All Items', 'javascript:void(0)', [
                        'class' => 'btn btn-success',
                        'onclick' => 'choseAllItems(); return false;'
                    ]) . ' <span class="label label-info" style="margin-left: 10px; padding: 6px 10px; font-size: 11px;"><i class="fa fa-info-circle"></i> Baris Biru = Sudah Masuk Stok Opname</span>',
            'after'=>false,
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            /*['class' => 'kartik\grid\ActionColumn', 'template' => '{view}'],
            [
                'class' => 'kartik\grid\CheckboxColumn',
                // you may configure additional properties here
            ],*/
            [
                'class' => 'kartik\grid\ActionColumn',
                'template'=>'{add-mix}',
                'buttons'=>[
                    'add-mix' => function($url, $model, $key){
                        /* @var $model TrnGudangJadi*/

                        if($model->status === $model::STATUS_STOCK || $model->status === $model::STATUS_SIAP_KIRIM){
                            $data = $model->attributes;
                            $data['jenisGudangName'] = $model::jenisGudangOptions()[$model->jenis_gudang];
                            $data['marketingName'] = $model->wo->mo->scGreige->sc->marketing->full_name;
                            $data['customerName'] = $model->wo->mo->scGreige->sc->customerName;
                            $data['scNo'] = $model->wo->mo->scGreige->sc->no;
                            $data['woNo'] = $model->wo->no;
                            $data['sourceName'] = TrnGudangJadi::sourceOptions()[$model->source];
                            $data['unitName'] = MstGreigeGroup::unitOptions()[$model->unit];
                            $data['gradeName'] = $model->gradeName;
                            $data['motif'] = $model->wo->greigeNamaKain;
                            // $data['id_asal'] = $model->inspecting ? $model->inspecting->inspecting_id : ($model->inspectingMklbj ? $model->inspectingMklbj->inspecting_id : NULL);
                            $data['qtyFormatted'] = Yii::$app->formatter->asDecimal($model->qty);
                            $dataStr = \yii\helpers\Json::encode($data);
                            return Html::a('<span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>', '#', [
                                'title' => 'Tambah kedalam item',
                                'class' => 'add-mix-btn',
                                'onclick' => "addSelectedItem(event, {$dataStr})"
                            ]);
                        }

                        return '';
                    }
                ]
            ],
            [
                'label' => 'QR',
                'headerOptions' => ['style' => 'width:50px;'],
                'format' => 'raw',
                'value' => function ($data) {
                    $printed = $data->qr_print_at ? 'btn btn-success center-block' : 'btn btn-default center-block';
                    return Html::a('<span><i class="fa fa-qrcode"></i></span>', '#', [
                        'class' => $printed,
                        'data-id' => $data->id, // Add a data attribute to store the ID
                        'onclick' => 'openQRWindow(event, ' . $data->id . '); return false;', // Call custom function
                    ]);
                    // return Html::a('<span><i class="fa fa-qrcode"></i></span>', '',
                    //     [
                    //         'onclick' => "window.open ('".Url::toRoute(['trn-gudang-jadi/qr',  'id' => $data->id])."'); return false", 
                    //         'class' => $printed
                    //     ]);
                },
            ],
            [
                'contentOptions' => ['style' => 'white-space: nowrap;'],
                'attribute' => 'qr_print_at',
                'headerOptions' => ['style' => 'width:100px;'],
                'label'=>'Tanggal Print Qr',
                'value' => function($data){
                    return $data->qr_print_at;
                },
            ],
            'id',
            [
                'contentOptions' => ['style' => 'white-space: nowrap;'],
                'attribute' => 'jenis_gudang',
                // 'headerOptions' => ['style' => 'width:100px;'],
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
                'contentOptions' => ['style' => 'white-space: nowrap;'],
                'attribute' => 'id_asal',
                'format' => 'raw',
                'filter' => false, // Disable filter for this column
                // 'headerOptions' => ['style' => 'width:100px;'],
                'label'=>'ID Inspecting',
                'value' => function($data){
                    $asal = $data->id_from && $data->trans_from == 'INS' && $data->inspecting ? $data->qr_code : ($data->id_from && $data->trans_from == 'MKL' && $data->inspectingMklbj ? $data->qr_code : NULL);
                    return $asal;
                },
            ],
            [
                'contentOptions' => ['style' => 'white-space: nowrap;'],
                'attribute'=>'scNo',
                'label'=>'Nomor SC',
                'value'=>'wo.mo.scGreige.sc.no'
            ],
            [
                'contentOptions' => ['style' => 'white-space: nowrap;'],
                'attribute'=>'woNo',
                'label'=>'Nomor WO',
                'value'=>'wo.no'
            ],
            [
                'contentOptions' => ['style' => 'white-space: nowrap;'],
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
                'attribute' => 'color',
                'contentOptions' => ['style' => 'white-space: nowrap;'],
            ],
            // 'color',
            [
                'contentOptions' => ['style' => 'white-space: nowrap;'],
                'label' => 'No. Lot',
                'attribute' => 'no_lot',
                // 'headerOptions' => ['style' => 'width:50px;'],
                'value' => function($data) {
                    return $data->noLot;
                },

            ],
            [
                'attribute' => 'qty',
                'format' => 'raw',
                'value' => function($model){
                    /* @var $model TrnGudangJadi */
                    $qtyFormatted = Yii::$app->formatter->asDecimal($model->qty);
                    if (empty($model->locs_code)) {
                        return Html::a($qtyFormatted, '#', [
                            'class' => 'btn-set-lokasi text-danger',
                            'data-id' => $model->id,
                            'data-jenis-gudang' => $model->jenis_gudang,
                            'data-qty' => $qtyFormatted,
                            'title' => 'Set Lokasi',
                            'style' => 'text-decoration: underline; font-weight: bold; cursor: pointer;'
                        ]);
                    }
                    return $qtyFormatted;
                }
            ],
            //'no_urut',
            //'no',
            [
                'contentOptions' => ['style' => 'white-space: nowrap;'],
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
            [
                'contentOptions' => ['style' => 'white-space: nowrap;'],
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
                'contentOptions' => ['style' => 'white-space: nowrap;'],
                'attribute'=>'marketingName',
                'label'=>'Marketing',
                'value'=>'wo.mo.scGreige.sc.marketing.full_name'
            ],
            [
                'contentOptions' => ['style' => 'white-space: nowrap;'],
                'attribute'=>'customerName',
                // 'headerOptions' => ['style' => 'width:150px;'],
                'label'=>'Buyer',
                'value'=>'wo.mo.scGreige.sc.cust.name'
            ],
            [
                'contentOptions' => ['style' => 'white-space: nowrap;'],
                'attribute'=>'locs_code',
                'value'=>'locs_code',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnGudangJadi::getLocationAreas(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            'source_ref',
            [
                'header' => 'Made In Indonesia',
                'class' => CheckboxColumn::class,
                'checkboxOptions' => function ($data, $key, $index, $column) {
                    $no_wo = substr($data->wo->no, -1);
                    $defaultCheck = ($no_wo == 'L' ? true : false);
                    return [
                        'class' => 'checkbox-param1',
                        'id' => 'param1-' . $data->id,
                        'checked' => $defaultCheck,
                    ];
                },
            ],
            [
                'header' => 'Registrasi K3L',
                'class' => CheckboxColumn::class,
                'checkboxOptions' => function ($data, $key, $index, $column) {
                    $no_wo = substr($data->wo->no, -1);
                    $defaultCheck = ($no_wo == 'L' ? true : false);
                    return [
                        'class' => 'checkbox-param2',
                        'id' => 'param2-' . $data->id,
                        'checked' => $defaultCheck,
                    ];
                },
            ],
            [
                'header' => 'Aktifkan Pembulatan Decimal',
                'class' => CheckboxColumn::class,
                'checkboxOptions' => function ($data, $key, $index, $column) {
                    $defaultCheck = true;
                    return [
                        'class' => 'checkbox-param3',
                        'id' => 'param3-' . $data->id,
                        'checked' => $defaultCheck,
                    ];
                },
            ],
            [
                'contentOptions' => ['style' => 'white-space: nowrap;'],
                'attribute' => 'source',
                // 'headerOptions' => ['style' => 'width:100px;'],
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
            
            
            [
                'contentOptions' => ['style' => 'white-space: nowrap;'],
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
                'contentOptions' => ['style' => 'white-space: nowrap;'],
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
            [
                'attribute' => 'created_at',
                'format' => ['datetime'], // adjust the format as needed
                'contentOptions' => ['style' => 'white-space: nowrap;'],
            ],
            // 'created_at:datetime',
            [
                'attribute' => 'created_by',
                'contentOptions' => ['style' => 'white-space: nowrap;'],
            ],
            //'updated_at',
            //'updated_by',
        ],
    ]); ?>
</div>
<div id="selected-items-div">
    <?=$this->render('_selected-items')?>
</div>

<!-- Modal Input Keterangan Stok Keluar -->
<div class="modal fade" id="modalStockKeluar" tabindex="-1" role="dialog" aria-labelledby="modalStockKeluarLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalStockKeluarLabel"><i class="glyphicon glyphicon-export"></i> Set Stok Keluar (Out)</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="stock-keluar-id" value="" />
                <div class="form-group">
                    <label for="stock-keluar-note">Keterangan / Alasan Keluar <span class="text-danger">*</span></label>
                    <textarea id="stock-keluar-note" class="form-group form-control" rows="4" placeholder="Masukkan alasan/keterangan stok keluar (misal: Diambil untuk packing, Pindah gudang, dll)..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="submitStockKeluar()">Submit & Set Out</button>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJsVar('selectedItems', []);
$this->registerJsVar('wmsLocationsUrl', Url::to(['ajax/wms-locations']));
$this->registerJsVar('saveLocationUrl', Url::to(['trn-gudang-jadi/save-location']));
$this->registerJsVar('setStockKeluarUrl', Url::to(['trn-gudang-jadi/set-stock-keluar']));

$this->registerJs($this->renderFile(__DIR__.'/js/index.js'), View::POS_END);
// Define a global JavaScript variable with the base URL
$this->registerJs('var baseUrl = ' . json_encode(Yii::$app->urlManager->createUrl(['/'])), View::POS_HEAD);

$jsStockKeluar = <<<JS
function openModalStockKeluar(e, id) {
    if(e) e.preventDefault();
    $('#stock-keluar-id').val(id);
    $('#stock-keluar-note').val('');
    $('#modalStockKeluar').modal('show');
}

function openModalStockKeluarBatch(e) {
    if(e) e.preventDefault();
    let ids = [];
    let data = itemTable.rows().data();
    for (let i = 0; i < data.length; i++) {
        ids.push(data[i].id);
    }

    if (ids.length === 0) {
        alert('Tidak ada item yang dipilih di dalam tabel Items!');
        return;
    }

    $('#stock-keluar-id').val(ids.join(','));
    $('#stock-keluar-note').val('');
    $('#modalStockKeluar').modal('show');
}

function submitStockKeluar() {
    var rawIds = $('#stock-keluar-id').val();
    var note = $('#stock-keluar-note').val().trim();

    if(!rawIds) {
        alert('ID Stok tidak valid!');
        return;
    }
    if(!note) {
        alert('Keterangan stok keluar wajib diisi!');
        $('#stock-keluar-note').focus();
        return;
    }

    var idsArray = rawIds.split(',');

    $.ajax({
        url: setStockKeluarUrl,
        type: 'POST',
        data: {
            ids: idsArray,
            note: note
        },
        dataType: 'json',
        success: function(res) {
            if(res.success) {
                $('#modalStockKeluar').modal('hide');
                alert(res.message);
                itemTable.clear().draw();
                if(typeof updateRowNumbers === 'function') updateRowNumbers();
                $.pjax.reload({container: '#GdJadiGrid-pjax'});
            } else {
                alert(res.message);
            }
        },
        error: function(err) {
            alert('Terjadi kesalahan sistem, silakan coba lagi.');
        }
    });
}
JS;
$this->registerJs($jsStockKeluar, View::POS_END);
?>