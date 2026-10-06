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

use mdm\admin\components\Helper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnGudangJadiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

\backend\assets\DataTablesAsset::register($this);

$this->title = 'Gudang Jadi';
$this->params['breadcrumbs'][] = $this->title;

$canSyncRak = Helper::checkRoute('sync-rak-opname');
$canMoveLocation = Helper::checkRoute('move-location');

$greigeNameFilter = '';
if(!empty($searchModel->greige_id)){
    $greigeNameFilter = MstGreige::findOne($searchModel['greige_id'])->nama_kain;
}

// Ambil daftar rak yang memiliki data di Gudang Jadi (status Stock) atau Stok Opname (hanya jika punya akses sync rak)
$rakOptions = [];
if ($canSyncRak) {
    $gjRaks = TrnGudangJadi::find()
        ->select(['locs_code', 'count(*) as total_count'])
        ->where(['status' => TrnGudangJadi::STATUS_STOCK])
        ->andWhere(['is not', 'locs_code', null])
        ->andWhere(['!=', 'locs_code', ''])
        ->groupBy('locs_code')
        ->asArray()
        ->all();

    $opnameRaks = \common\models\ar\TrnGudangJadiOpnamePcs::find()
        ->select(['locs_code', 'count(*) as total_count'])
        ->where(['!=', 'status', \common\models\ar\TrnGudangJadiOpnamePcs::STATUS_OUT])
        ->andWhere(['is not', 'locs_code', null])
        ->andWhere(['!=', 'locs_code', ''])
        ->groupBy('locs_code')
        ->asArray()
        ->all();

    $gjRakMap = \yii\helpers\ArrayHelper::map($gjRaks, 'locs_code', 'total_count');
    $opRakMap = \yii\helpers\ArrayHelper::map($opnameRaks, 'locs_code', 'total_count');
    $allActiveRaks = array_unique(array_merge(array_keys($gjRakMap), array_keys($opRakMap)));
    sort($allActiveRaks, SORT_NATURAL);

    foreach ($allActiveRaks as $r) {
        $gjCount = isset($gjRakMap[$r]) ? (int)$gjRakMap[$r] : 0;
        $opCount = isset($opRakMap[$r]) ? (int)$opRakMap[$r] : 0;
        $rakOptions[$r] = "{$r} (Gudang Jadi: {$gjCount} roll | Opname: {$opCount} roll)";
    }
}

$beforeButtons = Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']);
if ($canSyncRak) {
    $beforeButtons .= ' ' . Html::button('<i class="fa fa-refresh"></i> Sync Rak vs Opname', [
        'class' => 'btn btn-primary',
        'id' => 'btn-sync-rak-opname',
        'data-toggle' => 'modal',
        'data-target' => '#modal-sync-rak',
        'title' => 'Sinkronkan status stock pada rak tertentu dengan hasil Stok Opname'
    ]);
}
if ($canMoveLocation) {
    $beforeButtons .= ' ' . Html::button('<i class="fa fa-arrows"></i> Move Location <span class="badge bg-green" id="badge-move-count" style="display: none; margin-left: 5px;">0</span>', [
        'class' => 'btn btn-warning',
        'id' => 'btn-move-location',
        'onclick' => 'openModalMoveLocation(event);',
        'title' => 'Pindahkan lokasi untuk item yang dipilih'
    ]);
}
$beforeButtons .= ' ' . Html::a('<i class=" glyphicon glyphicon-plus-sign"></i> Add All Items', 'javascript:void(0)', [
    'class' => 'btn btn-success',
    'onclick' => 'choseAllItems(); return false;'
]) . ' <span class="label label-info" style="margin-left: 10px; padding: 6px 10px; font-size: 11px;"><i class="fa fa-info-circle"></i> Baris Biru = Sudah Masuk Stok Opname</span>';
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
            'before' => $beforeButtons,
            'after'=>false,
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\CheckboxColumn',
                'headerOptions' => ['class' => 'kartik-sheet-style', 'style' => 'width: 30px;'],
            ],
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
                // 'headerOptions' => ['style' => 'width:100px;'],
                'label'=>'ID Inspecting',
                'value' => function($data){
                    /* @var $data TrnGudangJadi */
                    if (!empty($data->qr_code)) {
                        return $data->qr_code;
                    }
                    if (!empty($data->id_from)) {
                        $prefix = $data->trans_from ?: 'INS';
                        return $prefix . '-' . $data->id_from;
                    }
                    if ($data->opnamePcs !== null && !empty($data->opnamePcs->qr_code)) {
                        return $data->opnamePcs->qr_code;
                    }
                    return null;
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
                'value' => function($data) {
                    return $data->getColorResolved();
                },
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
            [
                'contentOptions' => ['style' => 'white-space: nowrap;'],
                'attribute' => 'source_ref',
                'format' => 'raw',
                'value' => function($data) {
                    /* @var $data TrnGudangJadi */
                    if (empty($data->source_ref)) {
                        return '-';
                    }
                    $url = $data->getSourceUrl();
                    if ($url) {
                        return Html::a(Html::encode($data->source_ref), $url, [
                            'target' => '_blank',
                            'data-pjax' => '0',
                            'title' => 'Buka detail inspecting di tab baru',
                            'style' => 'text-decoration: underline; font-weight: bold; color: #337ab7;'
                        ]);
                    }
                    return Html::encode($data->source_ref);
                }
            ],
            [
                'header' => 'Made In Indonesia',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($data) {
                    $no_wo = !empty($data->wo->no) ? substr($data->wo->no, -1) : '';
                    $defaultCheck = ($no_wo == 'L');
                    return Html::checkbox('param1_' . $data->id, $defaultCheck, [
                        'class' => 'checkbox-param1',
                        'id' => 'param1-' . $data->id,
                        'value' => 1,
                    ]);
                },
            ],
            [
                'header' => 'Registrasi K3L',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($data) {
                    $no_wo = !empty($data->wo->no) ? substr($data->wo->no, -1) : '';
                    $defaultCheck = ($no_wo == 'L');
                    return Html::checkbox('param2_' . $data->id, $defaultCheck, [
                        'class' => 'checkbox-param2',
                        'id' => 'param2-' . $data->id,
                        'value' => 1,
                    ]);
                },
            ],
            [
                'header' => 'Aktifkan Pembulatan Decimal',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($data) {
                    return Html::checkbox('param3_' . $data->id, true, [
                        'class' => 'checkbox-param3',
                        'id' => 'param3-' . $data->id,
                        'value' => 1,
                    ]);
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

<?php if ($canMoveLocation): ?>
<!-- Modal Move Location Gudang Jadi -->
<div class="modal fade" id="modal-move-location" tabindex="-1" role="dialog" aria-labelledby="modalMoveLocationLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-yellow">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalMoveLocationLabel"><i class="fa fa-arrows"></i> Pindahkan Lokasi Barang (Gudang Jadi)</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    Jumlah item terpilih: <strong><span id="count-selected-items">0</span> item</strong>
                </div>
                <div class="form-group">
                    <label class="control-label" for="target-locs-code">Pilih Lokasi Tujuan: <span class="text-danger">*</span></label>
                    <select id="target-locs-code" name="target_locs_code" class="form-control" style="width: 100%;">
                        <option value="">-- Pilih Lokasi Tujuan --</option>
                        <?php foreach (\common\models\ar\MstSubLocation::optionList() as $locCode => $locDesc): ?>
                            <option value="<?= Html::encode($locCode) ?>"><?= Html::encode($locDesc) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="btn-submit-move-location" onclick="submitMoveLocation(event);"><i class="fa fa-check"></i> Simpan Perpindahan</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($canSyncRak): ?>
<!-- Modal Sync Rak vs Opname -->
<div class="modal fade" id="modal-sync-rak" tabindex="-1" role="dialog" aria-labelledby="modalSyncRakLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalSyncRakLabel" style="color: #fff;"><i class="fa fa-refresh"></i> Sinkronisasi Stok Rak dengan Stok Opname</h4>
            </div>
            <div class="modal-body">
                <div class="callout callout-info" style="margin-bottom: 15px;">
                    <h4><i class="fa fa-info-circle"></i> Cara Kerja:</h4>
                    <p>Pilih rak yang ingin disinkronkan. Sistem akan membandingkan data fisik <strong>Gudang Jadi</strong> (status Stock) dengan data <strong>Stok Opname Pcs</strong> pada rak tersebut. Roll yang <strong>TIDAK DITEMUKAN</strong> di data Stok Opname akan otomatis diubah statusnya menjadi <strong>OUT</strong>.</p>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="control-label" for="sync-locs-code">Pilih Rak / Lokasi (Bisa Pilih Banyak): <span class="text-danger">*</span></label>
                            <div style="margin-bottom: 6px;">
                                <button type="button" class="btn btn-xs btn-default" id="btn-select-all-raks"><i class="fa fa-check-square-o"></i> Pilih Semua Rak</button>
                                <button type="button" class="btn btn-xs btn-default" id="btn-deselect-all-raks" style="margin-left: 5px;"><i class="fa fa-square-o"></i> Kosongkan Pilihan</button>
                                <span class="badge bg-blue" id="badge-sync-rak-count" style="margin-left: 10px; display: none;">0 rak</span>
                            </div>
                            <select id="sync-locs-code" name="sync_locs_code[]" class="form-control" multiple="multiple" style="width: 100%;">
                                <?php foreach ($rakOptions as $rCode => $rLabel): ?>
                                    <option value="<?= Html::encode($rCode) ?>"><?= Html::encode($rLabel) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-top: 28px;">
                        <button type="button" class="btn btn-info btn-block" id="btn-check-sync-rak" onclick="checkSyncRakPreview(event);">
                            <i class="fa fa-search"></i> Cek & Bandingkan Data
                        </button>
                    </div>
                </div>

                <div id="sync-rak-loading" style="display: none; text-align: center; padding: 20px;">
                    <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                    <p style="margin-top: 10px; font-weight: bold;">Sedang membandingkan data rak...</p>
                </div>

                <!-- Preview Area -->
                <div id="sync-rak-preview-area" style="display: none; margin-top: 15px;">
                    <div id="sync-rak-alert-container"></div>
                    <div class="box box-solid box-default" style="border: 1px solid #d2d6de;">
                        <div class="box-header with-border bg-gray-light">
                            <h3 class="box-title" style="font-size: 15px; font-weight: bold;"><i class="fa fa-bar-chart"></i> Ringkasan Perbandingan: <span id="preview-rak-title" class="text-primary"></span></h3>
                        </div>
                        <div class="box-body" style="padding: 0;">
                            <table class="table table-bordered table-striped" style="margin-bottom: 0;">
                                <tbody>
                                    <tr>
                                        <th style="width: 55%; font-size: 13px;">Total Stok Aktif di Master Gudang Jadi</th>
                                        <td class="text-right"><strong id="val-total-gj" style="font-size: 14px;">0</strong> roll (<span id="val-total-gj-qty">0</span> Y/M)</td>
                                    </tr>
                                    <tr>
                                        <th style="font-size: 13px;">Total Roll di Stok Opname (Fisik)</th>
                                        <td class="text-right text-info"><strong id="val-total-opname" style="font-size: 14px;">0</strong> roll (<span id="val-total-opname-qty">0</span> Y/M)</td>
                                    </tr>
                                    <tr class="success">
                                        <th style="font-size: 13px;"><i class="fa fa-check text-success"></i> Roll Cocok (Tetap Berstatus Stock)</th>
                                        <td class="text-right text-success"><strong id="val-matched" style="font-size: 14px;">0</strong> roll (<span id="val-matched-qty">0</span> Y/M)</td>
                                    </tr>
                                    <tr class="danger">
                                        <th style="font-size: 13px;"><i class="fa fa-times text-danger"></i> Roll Tidak Ada di Opname (Akan di-OUT-kan)</th>
                                        <td class="text-right text-danger"><strong id="val-unmatched" style="font-size: 16px;">0</strong> roll (<span id="val-unmatched-qty">0</span> Y/M)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Breakdown Area for Multiple Raks -->
                    <div id="sync-rak-breakdown-area" style="display: none; margin-top: 10px;">
                        <button type="button" class="btn btn-xs btn-default btn-block" id="btn-toggle-breakdown" style="text-align: left; padding: 6px 10px; font-weight: bold;">
                            <i class="fa fa-list"></i> Lihat Rincian Per-Rak (<span id="count-breakdown-raks">0</span> rak) <i class="fa fa-caret-down pull-right"></i>
                        </button>
                        <div id="table-breakdown-wrapper" style="display: none; max-height: 220px; overflow-y: auto; border: 1px solid #ddd; margin-top: 5px;">
                            <table class="table table-bordered table-condensed table-striped" style="margin-bottom: 0; font-size: 12px;">
                                <thead>
                                    <tr class="bg-gray">
                                        <th>Kode Rak</th>
                                        <th class="text-center">GJ (Stock)</th>
                                        <th class="text-center">Opname (Fisik)</th>
                                        <th class="text-center">Cocok</th>
                                        <th class="text-center">Akan OUT</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-sync-breakdown"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-group" id="group-sync-note" style="margin-top: 15px;">
                        <label for="sync-rak-note">Catatan Alasan Keluar (Opsional):</label>
                        <input type="text" id="sync-rak-note" class="form-control" placeholder="Contoh: Tidak ditemukan saat Stok Opname..." />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id="btn-submit-sync-rak" style="display: none;" onclick="submitSyncRak(event);">
                    <i class="fa fa-check-circle"></i> Eksekusi & Ubah Status OUT
                </button>
            </div>
        </div>
    </div>
</div>

<script>
window.getSelectedSyncRaks = function() {
    var selectEl = document.getElementById('sync-locs-code');
    if (!selectEl) return [];
    var result = [];
    var options = selectEl.options;
    for (var i = 0; i < options.length; i++) {
        if (options[i].selected && options[i].value) {
            result.push(options[i].value);
        }
    }
    return result;
};

window.checkSyncRakPreview = function(e) {
    if (e && e.preventDefault) e.preventDefault();
    var locsCodes = window.getSelectedSyncRaks();
    
    if (locsCodes.length === 0) {
        alert('Pilih minimal 1 rak / lokasi terlebih dahulu!');
        return false;
    }

    var btnCheck = document.getElementById('btn-check-sync-rak');
    var previewArea = document.getElementById('sync-rak-preview-area');
    var btnSubmit = document.getElementById('btn-submit-sync-rak');
    var loadingEl = document.getElementById('sync-rak-loading');

    if (previewArea) previewArea.style.display = 'none';
    if (btnSubmit) btnSubmit.style.display = 'none';
    if (loadingEl) loadingEl.style.display = 'block';
    if (btnCheck) {
        btnCheck.disabled = true;
        btnCheck.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memeriksa...';
    }

    var checkUrl = '<?= Url::to(['trn-gudang-jadi/check-sync-rak']) ?>';

    $.ajax({
        url: checkUrl,
        type: 'POST',
        data: { locs_codes: locsCodes },
        dataType: 'json',
        cache: false,
        success: function(res) {
            if (btnCheck) {
                btnCheck.disabled = false;
                btnCheck.innerHTML = '<i class="fa fa-search"></i> Cek & Bandingkan Data';
            }
            if (loadingEl) loadingEl.style.display = 'none';

            if (res && res.success) {
                document.getElementById('preview-rak-title').innerText = res.locs_title;
                document.getElementById('val-total-gj').innerText = res.total_gj;
                document.getElementById('val-total-gj-qty').innerText = res.total_gj_qty;
                document.getElementById('val-total-opname').innerText = res.total_opname;
                document.getElementById('val-total-opname-qty').innerText = res.total_opname_qty;
                document.getElementById('val-matched').innerText = res.matched_count;
                document.getElementById('val-matched-qty').innerText = res.matched_qty;
                document.getElementById('val-unmatched').innerText = res.unmatched_count;
                document.getElementById('val-unmatched-qty').innerText = res.unmatched_qty;

                var noteInput = document.getElementById('sync-rak-note');
                var noteGroup = document.getElementById('group-sync-note');
                var alertContainer = document.getElementById('sync-rak-alert-container');

                if (noteInput) {
                    var rakLabel = res.total_rak === 1 ? ('rak ' + res.locs_codes[0]) : (res.total_rak + ' rak');
                    noteInput.value = 'Tidak ditemukan saat Stok Opname di ' + rakLabel;
                }

                if (alertContainer) {
                    if (res.unmatched_count > 0) {
                        alertContainer.innerHTML = '<div class="alert alert-warning" style="margin-bottom: 10px;">' +
                            '<i class="fa fa-exclamation-triangle"></i> Ditemukan <strong>' + res.unmatched_count + ' roll</strong> di master Gudang Jadi yang <strong>TIDAK ADA</strong> di hasil Stok Opname fisik pada ' + res.total_rak + ' rak terpilih. Klik tombol merah di bawah untuk mengubah statusnya menjadi <strong>OUT</strong>.' +
                            '</div>';
                        if (noteGroup) noteGroup.style.display = 'block';
                    } else {
                        alertContainer.innerHTML = '<div class="alert alert-success" style="margin-bottom: 10px;">' +
                            '<i class="fa fa-check-circle"></i> <strong>Semua data pada ' + res.total_rak + ' rak terpilih sudah cocok & lengkap!</strong> Sebanyak <strong>' + res.matched_count + ' roll</strong> di master Gudang Jadi sudah terverifikasi ada di Stok Opname fisik. Tidak ada roll yang perlu diubah statusnya menjadi OUT.' +
                            '</div>';
                        if (noteGroup) noteGroup.style.display = 'none';
                    }
                }

                // Render breakdown if multiple raks
                var breakdownArea = document.getElementById('sync-rak-breakdown-area');
                var tbodyBreakdown = document.getElementById('tbody-sync-breakdown');
                if (breakdownArea && tbodyBreakdown) {
                    if (res.breakdown && res.breakdown.length > 1) {
                        document.getElementById('count-breakdown-raks').innerText = res.breakdown.length;
                        var rowsHtml = '';
                        for (var b = 0; b < res.breakdown.length; b++) {
                            var item = res.breakdown[b];
                            var outStyle = item.unmatched_count > 0 ? 'class="text-danger font-weight-bold"' : 'class="text-muted"';
                            rowsHtml += '<tr>' +
                                '<td><strong>' + item.locs_code + '</strong></td>' +
                                '<td class="text-center">' + item.gj_count + '</td>' +
                                '<td class="text-center">' + item.op_count + '</td>' +
                                '<td class="text-center text-success">' + item.matched_count + '</td>' +
                                '<td class="text-center ' + (item.unmatched_count > 0 ? 'danger text-danger' : '') + '"><strong>' + item.unmatched_count + '</strong></td>' +
                                '</tr>';
                        }
                        tbodyBreakdown.innerHTML = rowsHtml;
                        breakdownArea.style.display = 'block';
                    } else {
                        breakdownArea.style.display = 'none';
                    }
                }

                if (previewArea) previewArea.style.display = 'block';

                if (btnSubmit) {
                    btnSubmit.style.display = 'inline-block';
                    if (res.unmatched_count > 0) {
                        btnSubmit.className = 'btn btn-danger';
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = '<i class="fa fa-check-circle"></i> Eksekusi & Ubah Status OUT (' + res.unmatched_count + ' roll)';
                    } else {
                        btnSubmit.className = 'btn btn-success disabled';
                        btnSubmit.disabled = true;
                        btnSubmit.innerHTML = '<i class="fa fa-check"></i> Semua Data Cocok (0 Roll OUT)';
                    }
                }
            } else {
                alert((res && res.message) ? res.message : 'Gagal memeriksa data rak.');
            }
        },
        error: function(xhr, status, err) {
            if (btnCheck) {
                btnCheck.disabled = false;
                btnCheck.innerHTML = '<i class="fa fa-search"></i> Cek & Bandingkan Data';
            }
            if (loadingEl) loadingEl.style.display = 'none';

            var errMsg = 'Terjadi kesalahan sistem (' + xhr.status + ')';
            if (xhr.responseText) {
                try {
                    var parsed = JSON.parse(xhr.responseText);
                    if (parsed.message) errMsg += ': ' + parsed.message;
                } catch(errJson) {
                    errMsg += ': ' + xhr.responseText.substring(0, 150);
                }
            }
            alert(errMsg);
        }
    });
    return false;
};

window.submitSyncRak = function(e) {
    if (e && e.preventDefault) e.preventDefault();
    var locsCodes = window.getSelectedSyncRaks();
    var unmatchedEl = document.getElementById('val-unmatched');
    var unmatchedCount = unmatchedEl ? unmatchedEl.innerText : '0';
    var noteEl = document.getElementById('sync-rak-note');
    var note = noteEl ? noteEl.value.trim() : '';

    if (locsCodes.length === 0) {
        alert('Pilih minimal 1 rak / lokasi terlebih dahulu!');
        return false;
    }

    var rakText = locsCodes.length === 1 ? ('rak ' + locsCodes[0]) : (locsCodes.length + ' rak terpilih');
    var confirmMsg = 'PERINGATAN SINKRONISASI ' + rakText.toUpperCase() + ':\n\n' +
        'Sebanyak ' + unmatchedCount + ' roll pada master Gudang Jadi yang TIDAK ADA di Stok Opname akan diubah statusnya menjadi OUT.\n\n' +
        'Apakah Anda yakin ingin melanjutkan proses ini?';

    if (!confirm(confirmMsg)) {
        return false;
    }

    var btnSubmit = document.getElementById('btn-submit-sync-rak');
    var oldText = btnSubmit ? btnSubmit.innerHTML : '';
    if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';
    }

    var syncUrl = '<?= Url::to(['trn-gudang-jadi/sync-rak-opname']) ?>';

    $.ajax({
        url: syncUrl,
        type: 'POST',
        data: {
            locs_codes: locsCodes,
            note: note
        },
        dataType: 'json',
        success: function(res) {
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = oldText;
            }
            if (res && res.success) {
                $('#modal-sync-rak').modal('hide');
                alert(res.message);
                window.location.reload();
            } else {
                alert((res && res.message) ? res.message : 'Gagal memproses sinkronisasi rak.');
            }
        },
        error: function(xhr, status, err) {
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = oldText;
            }
            alert('Terjadi kesalahan saat memproses data: ' + (xhr.responseText || err));
        }
    });
    return false;
};
</script>
<?php endif; ?>

<?php
$this->registerJsVar('selectedItems', []);
$this->registerJsVar('wmsLocationsUrl', Url::to(['ajax/wms-locations']));
$this->registerJsVar('saveLocationUrl', Url::to(['trn-gudang-jadi/save-location']));
$this->registerJsVar('setStockKeluarUrl', Url::to(['trn-gudang-jadi/set-stock-keluar']));
if ($canMoveLocation) {
    $this->registerJsVar('moveLocationUrl', Url::to(['trn-gudang-jadi/move-location']));
}
if ($canSyncRak) {
    $this->registerJsVar('checkSyncRakUrl', Url::to(['trn-gudang-jadi/check-sync-rak']));
    $this->registerJsVar('syncRakOpnameUrl', Url::to(['trn-gudang-jadi/sync-rak-opname']));
}

$this->registerJs($this->renderFile(__DIR__.'/js/index.js'), View::POS_END);
// Define a global JavaScript variable with the base URL
$this->registerJs('var baseUrl = ' . json_encode(Yii::$app->urlManager->createUrl(['/'])), View::POS_HEAD);

if ($canMoveLocation) {
    $jsMoveLocation = <<<JS
window.getSelectedGudangJadiIds = function() {
    var ids = [];
    $('#GdJadiGrid input[name="selection[]"]:checked').each(function() {
        var v = $(this).val();
        if (v && ids.indexOf(v) === -1) {
            ids.push(v);
        }
    });
    return ids;
};

window.syncMoveButtonBadge = function() {
    var ids = window.getSelectedGudangJadiIds();
    var count = ids.length;
    var \$badgeMove = $('#badge-move-count');
    
    if (count > 0) {
        \$badgeMove.text(count).show();
    } else {
        \$badgeMove.text('0').hide();
    }
};

window.openModalMoveLocation = function(e) {
    if (e) e.preventDefault();
    var ids = window.getSelectedGudangJadiIds();

    if (ids.length === 0) {
        alert('Harap pilih / centang minimal 1 data terlebih dahulu pada kotak pilihan tabel.');
        return false;
    }

    $('#count-selected-items').text(ids.length);
    $('#target-locs-code').val('');
    if ($.fn.select2) {
        $('#target-locs-code').select2({
            dropdownParent: $('#modal-move-location'),
            width: '100%'
        });
    }
    $('#modal-move-location').modal('show');
    return false;
};

window.submitMoveLocation = function(e) {
    if (e) e.preventDefault();
    var ids = window.getSelectedGudangJadiIds();
    if (ids.length === 0) {
        alert('Tidak ada data yang dipilih.');
        $('#modal-move-location').modal('hide');
        window.syncMoveButtonBadge();
        return false;
    }

    var targetLoc = $('#target-locs-code').val();
    if (!targetLoc) {
        alert('Harap pilih lokasi tujuan terlebih dahulu.');
        return false;
    }

    if (!confirm('Apakah Anda yakin ingin memindahkan ' + ids.length + ' item ke lokasi "' + targetLoc + '"?')) {
        return false;
    }

    var \$btn = $('#btn-submit-move-location');
    var oldText = \$btn.html();
    \$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memproses...');

    $.ajax({
        url: moveLocationUrl,
        type: 'POST',
        dataType: 'json',
        data: {
            ids: ids,
            target_locs_code: targetLoc
        },
        success: function(res) {
            if (res.success) {
                $('#modal-move-location').modal('hide');
                alert(res.message);
                $.pjax.reload({container: '#GdJadiGrid-pjax'});
            } else {
                alert(res.message || 'Gagal memindahkan lokasi.');
            }
        },
        error: function(xhr, status, error) {
            alert('Terjadi kesalahan pada server: ' + (xhr.responseText || error));
        },
        complete: function() {
            \$btn.prop('disabled', false).html(oldText);
            setTimeout(window.syncMoveButtonBadge, 300);
        }
    });
    return false;
};

$(document).on('change', 'input[name="selection[]"], input[name="selection_all"], .select-on-check-all, .kv-all-select', function() {
    setTimeout(window.syncMoveButtonBadge, 50);
});

$(document).on('click', '.select-on-check-all, .kv-all-select, input[name="selection_all"]', function() {
    setTimeout(window.syncMoveButtonBadge, 100);
});

$(document).on('pjax:success pjax:complete pjax:end', function() {
    setTimeout(window.syncMoveButtonBadge, 50);
});

$(document).on('click', '#btn-move-location', function(e) {
    window.openModalMoveLocation(e);
});

window.syncMoveButtonBadge();
JS;
    $this->registerJs($jsMoveLocation, View::POS_END);
}

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

if ($canSyncRak) {
    $jsSyncRak = <<<JS
$('#modal-sync-rak').on('show.bs.modal', function() {
    $('#sync-rak-preview-area').hide();
    $('#btn-submit-sync-rak').hide();
    $('#sync-rak-loading').hide();
});

$('#modal-sync-rak').on('shown.bs.modal', function() {
    if ($.fn.select2) {
        if (!$('#sync-locs-code').hasClass("select2-hidden-accessible")) {
            $('#sync-locs-code').select2({
                placeholder: 'Pilih satu atau beberapa rak...',
                dropdownParent: $('#modal-sync-rak'),
                width: '100%',
                closeOnSelect: false
            });
        }
    }
});

$(document).on('click', '#btn-select-all-raks', function(e) {
    if (e) e.preventDefault();
    var allVals = [];
    $('#sync-locs-code option').each(function() {
        var v = $(this).val();
        if (v) allVals.push(v);
    });
    $('#sync-locs-code').val(allVals).trigger('change');
});

$(document).on('click', '#btn-deselect-all-raks', function(e) {
    if (e) e.preventDefault();
    $('#sync-locs-code').val([]).trigger('change');
});

$(document).on('change', '#sync-locs-code', function(e) {
    var val = $(this).val() || [];
    var count = Array.isArray(val) ? val.length : (val ? 1 : 0);
    var \$badge = $('#badge-sync-rak-count');
    if (count > 0) {
        \$badge.text(count + ' rak dipilih').show();
    } else {
        \$badge.hide();
        $('#sync-rak-preview-area').hide();
        $('#btn-submit-sync-rak').hide();
    }
});

$(document).on('click', '#btn-toggle-breakdown', function(e) {
    if (e) e.preventDefault();
    var \$wrapper = $('#table-breakdown-wrapper');
    var \$icon = $(this).find('.pull-right');
    if (\$wrapper.is(':visible')) {
        \$wrapper.slideUp();
        \$icon.removeClass('fa-caret-up').addClass('fa-caret-down');
    } else {
        \$wrapper.slideDown();
        \$icon.removeClass('fa-caret-down').addClass('fa-caret-up');
    }
});

$(document).on('click', '#btn-check-sync-rak', function(e) {
    window.checkSyncRakPreview(e);
});

$(document).on('click', '#btn-submit-sync-rak', function(e) {
    window.submitSyncRak(e);
});
JS;
    $this->registerJs($jsSyncRak, View::POS_END);
}
?>
