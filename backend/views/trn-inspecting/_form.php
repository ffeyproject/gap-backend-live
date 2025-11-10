<?php
use backend\models\form\InspectingHeaderForm;
use backend\models\form\InspectingItemsForm;
use common\models\ar\InspectingItem;
use common\models\ar\MstGreigeGroup;
use common\models\ar\MstK3l;
use common\models\ar\TrnInspecting;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $modelHeader InspectingHeaderForm */
/* @var $modelItem InspectingItemsForm */

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
                                    'options' => ['placeholder' => 'Pilih ...'],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                    ],
                                    'data' => MstK3l::optionList(),
                                ])->label(false) ?>
                            </td>
                        </tr>
                        <tr>
                            <th>No. WO</th>
                            <td id="NoWo">
                                -
                            </td>
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
                                        'pluginOptions' => ['allowClear' => true],
                                        'pluginEvents' => [
                                            'select2:unselect' => 'function(e){if(kartuProsesIdOnUnSelect !== null){kartuProsesIdOnUnSelect(e)}}',
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
                            <td id="BuyerName">-</td>
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
                            <td id="Motif">-</td>
                        </tr>
                        <tr>
                            <th>Design</th>
                            <td id="Design">-</td>
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
                    </table>
                </div>

                <div class="col-md-4">
                    <table class="table table-bordered">
                        <tr>
                            <th>Kombinasi</th>
                            <td id="Kombinasi">-</td>
                        </tr>
                        <tr>
                            <th>Stamping</th>
                            <td id="Stamping">-</td>
                        </tr>
                        <tr>
                            <th>Piece Length</th>
                            <td id="PieceLength">-</td>
                        </tr>
                        <tr>
                            <th>Jenis Order</th>
                            <td>
                                <?= $formHeader->field($modelHeader, 'jenis_order')->widget(Select2::classname(), [
                                    'data' => ['dyeing'=>'Dyeing', 'printing'=>'Printing'],
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

                        <tr>
                            <th>Lokal/Export</th>
                            <td id="TipeKontrak"></td>
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
                        <th>No Urut</th>
                        <th>Grade</th>
                        <th>Ukuran</th>
                        <th>Join Piece</th>
                        <th>No Lot</th>
                        <th>Defect</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?= $formItem->field($modelItem, 'no_urut')
                                ->textInput(['placeholder' => 'Otomatis jika dikosongkan'])
                                ->label(false) ?>
                        </td>
                        <td>
                            <?= $formItem->field($modelItem, 'grade')->widget(Select2::classname(), [
                                'data' => InspectingItem::gradeOptions(),
                            ])->label(false) ?>
                        </td>
                        <td><?= $formItem->field($modelItem, 'ukuran')->textInput()->label(false) ?></td>
                        <td><?= $formItem->field($modelItem, 'join_piece')->textInput()->label(false) ?></td>
                        <td><?= $formItem->field($modelItem, 'lot_no')->textInput()->label(false) ?></td>
                        <td><?= $formItem->field($modelItem, 'defect')->textInput()->label(false) ?></td>
                        <td><?= $formItem->field($modelItem, 'keterangan')->textInput()->label(false) ?></td>
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
                        <th>No</th>
                        <th>No Urut</th> <!-- NEW -->
                        <th>Grade</th>
                        <th>Ukuran</th>
                        <th>Join Piece</th>
                        <th>Lot No</th>
                        <th>Defect</th>
                        <th>Keterangan</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 text-right"><button id="BtnSubmitForm" class="btn btn-success">Save</button></div>
</div>

<?php
$this->registerJsVar('kartuProsesIdOnSelect', null);
$this->registerJsVar('kartuProsesIdOnUnSelect', null);
$this->registerJsVar('kpModel', null);
$this->registerJsVar('jenisProses', null);
$this->registerJsVar('kpUrl', Url::to(['/ajax/lookup-kp-by-id']));
$this->registerJsVar('inspectingItems', []);
$this->registerJs($this->renderFile(__DIR__.'/js/form.js'), $this::POS_END);