<?php
use backend\models\form\InspectingHeaderForm;
use backend\models\form\InspectingItemsForm;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnInspecting;
use common\models\ar\InspectingItem;
use common\models\ar\MstK3l;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model TrnInspecting */
/* @var $modelHeader InspectingHeaderForm */
/* @var $modelItem InspectingItemsForm */
/* @var $nomorKartu string*/
/* @var $kombinasi string*/
/* @var $items array*/

\backend\assets\DataTablesAsset::register($this);
?>

<div class="inspecting-form">

    <!--Form Header-->
    <?php $formHeader = ActiveForm::begin(['id'=>'InspectingFormHeader']); ?>
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <table class="table table-bordered">
                        <tr>
                            <th>Tgl. Kirim</th>
                            <td>
                                <?= $formHeader->field($modelHeader, 'tgl_kirim')->widget(DatePicker::class, [
                                    'options' => ['placeholder' => 'Masukan tanggal ...'],
                                    'readonly' => true,
                                    'pluginOptions' => [
                                        'autoclose'=>true,
                                        'format' => 'yyyy-mm-dd',
                                        'todayHighlight' => true,
                                        'todayBtn' => true,
                                    ],
                                ])->label(false)?>
                            </td>
                        </tr>
                        <tr>
                            <th>No. K3l</th>
                            <td id="k3l_code">
                                <?= $formHeader->field($modelHeader, 'k3l_code')->widget(\kartik\select2\Select2::className(), [
                                    'options' => ['placeholder' => 'Pilih ...', 'value' => $k3l_code],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                    ],
                                    'data' => MstK3l::optionList(),
                                ])->label(false) ?>
                            </td>
                        </tr>
                        <tr>
                            <th>No. WO</th>
                            <td id="NoWo"><?=$model->wo->no?></td>
                        </tr>
                        <tr>
                            <th>No. Kartu</th>
                            <td>
                                <?php
                                /*echo $formHeader->field($modelHeader, 'kartu_proses_id')->widget(Select2::class, [
                                    'options' => ['placeholder' => 'Cari...'],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'minimumInputLength' => 3,
                                        'language' => [
                                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                                        ],
                                        'ajax' => [
                                            'url' => Url::to(['ajax/lookup-wo-dyeing']),
                                            'dataType' => 'json',
                                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                        ],
                                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                        'templateResult' => new JsExpression('function(wo) { return wo.text; }'),
                                        'templateSelection' => new JsExpression('function (wo) { return wo.text; }'),
                                    ],
                                    'pluginEvents' => [
                                        'select2:select' => 'function(e){let lookupWoColorUrl = "'.$lookupWoColorUrl.'"; '.$this->renderFile(Yii::$app->controller->viewPath.'/js/wo-on-select.js').'}',
                                        'select2:unselect' => 'function(e){$("#trnkartuprosesdyeing-wo_color_id").val(null).trigger("change"); $("#trnkartuprosesdyeing-wo_color_id").empty();}'
                                    ]
                                ])->label(false);*/

                                echo $formHeader->field($modelHeader, 'kartu_proses_id')->widget(DepDrop::classname(), [
                                    'type' => DepDrop::TYPE_SELECT2,
                                    'options' => ['placeholder' => 'Select ...'],
                                    'select2Options' => [
                                        'initValueText' => $nomorKartu,
                                        'pluginOptions' => ['allowClear' => true],
                                        'pluginEvents' => [
                                            'select2:unselect' => 'function(e){resetData();}',
                                            'select2:select' => 'function(e){if(kartuProsesIdOnSelect !== null){kartuProsesIdOnSelect(e)}}'
                                        ],
                                    ],
                                    'pluginOptions' => [
                                        'depends' => ['inspectingheaderform-jenis_order'],
                                        'url' => Url::to(['/dep-drop/lookup-create-inspecting']),
                                    ]
                                ])->label(false);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Buyer</th>
                            <td id="BuyerName"><?=$model->sc->customerName?></td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-4">
                    <table class="table table-bordered">
                        <tr>
                            <th>Tgl. Inspeksi</th>
                            <td>
                                <?= $formHeader->field($modelHeader, 'tgl_inspeksi')->widget(DatePicker::class, [
                                    'options' => ['placeholder' => 'Masukan tanggal ...'],
                                    'readonly' => true,
                                    'pluginOptions' => [
                                        'autoclose'=>true,
                                        'format' => 'yyyy-mm-dd',
                                        'todayHighlight' => true,
                                        'todayBtn' => true,
                                    ],
                                ])->label(false)?>
                            </td>
                        </tr>
                        <tr>
                            <th>No. Lot</th>
                            <td><?=$formHeader->field($modelHeader, 'no_lot')->textInput()->label(false)?></td>
                        </tr>
                        <tr>
                            <th>Motif</th>
                            <td id="Motif"><?=$model->wo->greige->nama_kain?></td>
                        </tr>
                        <tr>
                            <th>Design</th>
                            <td id="Design"><?=$model->mo->design?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <?= $formHeader->field($modelHeader, 'status')->widget(Select2::classname(), [
                                    'data' => MstGreigeGroup::unitOptions(),
                                    'options' => ['placeholder' => 'Pilih ...'],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                ])->label(false) ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Jenis Inspeksi</th>
                            <td>
                                <?= $formHeader->field($modelHeader, 'jenis_inspek')->widget(Select2::classname(), [
                                    'data' => TrnInspecting::jenisInspeksiOptions(),
                                    'options' => ['placeholder' => 'Pilih ...'],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                ])->label(false) ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-4">
                    <table class="table table-bordered">
                        <tr>
                            <th>Kombinasi</th>
                            <td id="Kombinasi"><?=$kombinasi?></td>
                        </tr>
                        <tr>
                            <th>Stamping</th>
                            <td id="Stamping"><?=$model->mo->face_stamping?></td>
                        </tr>
                        <tr>
                            <th>Piece Length</th>
                            <td id="PieceLength"><?=$model->mo->piece_length?></td>
                        </tr>
                        <tr>
                            <th>Jenis Order</th>
                            <td>
                                <?= $formHeader->field($modelHeader, 'jenis_order')->widget(Select2::classname(), [
                                    'data' => ['dyeing'=>'Dyeing', 'printing'=>'Printing', 'memo_repair'=>'Memo Repair'],
                                    'options' => ['placeholder' => 'Pilih ...'],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                    'pluginEvents' => [
                                        'select2:unselect' => 'function(e){resetData();}',
                                    ],
                                ])->label(false)?>
                            </td>
                        </tr>
                        <tr>
                            <th>Lokal/Export</th>
                            <td id="TipeKontrak">
                                <?php
                                $tipeKontrak = 'Lokal';
                                if($model->sc->tipe_kontrak == \common\models\ar\TrnSc::TIPE_KONTRAK_EXPORT){
                                    $tipeKontrak = 'Export';
                                }

                                echo $tipeKontrak;
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <!--Form Header-->

    <!--Form Item-->
    <?php $formItem = ActiveForm::begin(['id'=>'InspectingFormItem']); ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Input Items</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Grade</th>
                        <th>Ukuran</th>
                        <th>Join Piece</th>
                        <th>Lot No</th>
                        <th>Defect</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?=$formItem->field($modelItem, 'grade')->widget(Select2::classname(), [
                            'data' => InspectingItem::gradeOptions(),
                            //'options' => ['placeholder' => 'Pilih ...'],
                            'pluginOptions' => [
                                //'allowClear' => true
                            ],
                        ])->label(false) ?>
                        </td>
                        <td><?=$formItem->field($modelItem, 'ukuran')->textInput()->label(false)?></td>
                        <td><?=$formItem->field($modelItem, 'join_piece')->textInput()->label(false)?></td>
                        <td><?=$formItem->field($modelItem, 'lot_no')->textInput()->label(false)?></td>
                        <td><?=$formItem->field($modelItem, 'defect')->textInput()->label(false)?></td>
                        <td><?=$formItem->field($modelItem, 'keterangan')->textInput()->label(false)?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="box-footer text-right">
            <?=\yii\helpers\Html::submitButton('Enter', ['class'=>'btn btn-success'])?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <!--Form Item-->

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Items</h3>
            <div class="box-tools pull-right">
                <span class="label label-primary" id="ItemCounter">0</span>
            </div>
        </div>
        <div class="box-body">
            <table id="InspectingItemTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>No</th>
                        <th>Grade</th>
                        <th>Ukuran</th>
                        <th>Join Piece</th>
                        <th>Lot No</th>
                        <th>Defect</th>
                        <th>Keterangan</th>
                        <th>QR</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>






<!--<table class="table table-bordered">
        <thead>
        <tr>
            <th>Grade</th>
            <th>Ukuran</th>
            <th>Join Piece</th>
            <th>Keterangan</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <div class="form-group highlight-addon field-inspectingitemsform-grade required">


                    <select id="inspectingitemsform-grade" class="form-control select2-hidden-accessible" name="InspectingItemsForm[grade]" aria-required="true" data-s2-options="s2options_d6851687" data-krajee-select2="select2_f577b156" style="width: 1px; height: 1px; visibility: hidden;" data-select2-id="inspectingitemsform-grade" tabindex="-1" aria-hidden="true">
                        <option value="" data-select2-id="11">Pilih ...</option>
                        <option value="1">Grade A</option>
                        <option value="2">Grade B</option>
                        <option value="3">Grade C</option>
                        <option value="4">Piece Kecil</option>
                        <option value="5">Sample</option>
                    </select><span class="select2 select2-container select2-container--krajee select2-container--focus" dir="ltr" data-select2-id="10" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-inspectingitemsform-grade-container"><span class="select2-selection__rendered" id="select2-inspectingitemsform-grade-container" role="textbox" aria-readonly="true"><span class="select2-selection__placeholder">Pilih ...</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>

                    <div class="help-block"></div>

                </div>                    </td>
            <td><div class="form-group highlight-addon field-inspectingitemsform-ukuran required">


                    <input type="text" id="inspectingitemsform-ukuran" class="form-control" name="InspectingItemsForm[ukuran]" aria-required="true">

                    <div class="help-block"></div>

                </div></td>
            <td><div class="form-group highlight-addon field-inspectingitemsform-join_piece">


                    <input type="text" id="inspectingitemsform-join_piece" class="form-control" name="InspectingItemsForm[join_piece]">

                    <div class="help-block"></div>

                </div></td>
            <td><div class="form-group highlight-addon field-inspectingitemsform-keterangan">


                    <input type="text" id="inspectingitemsform-keterangan" class="form-control" name="InspectingItemsForm[keterangan]">

                    <div class="help-block"></div>

                </div></td>
        </tr>
        </tbody>
    </table>-->








<div class="row">
    <div class="col-md-12 text-right"><button id="BtnSubmitForm" class="btn btn-success">Save</button></div>
</div>

<?php
$this->registerJsVar('kartuProsesIdOnSelect', null);
$this->registerJsVar('kartuProsesIdOnUnSelect', null);
$this->registerJsVar('kpModel', null);
$this->registerJsVar('jenisProses', null);
$this->registerJsVar('kpUrl', Url::to(['/ajax/lookup-kp-by-id']));
$this->registerJsVar('inspectingItems', $items);
$this->registerJs($this->renderFile(__DIR__.'/js/form-update.js'), $this::POS_END);