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
                                    // 'disabled' => true,
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
                                    // 'disabled' => true,
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
                                    echo $formHeader->field($modelHeader, 'kartu_proses_id')->widget(DepDrop::classname(), [
                                        'type' => DepDrop::TYPE_SELECT2,
                                        'options' => ['placeholder' => 'Select ...'],
                                        // 'disabled' => true,
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
                                    // 'disabled' => true,
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
                            <td><?=$formHeader->field($modelHeader, 'no_lot')->textInput(/*['disabled' => true]*/)->label(false)?></td>
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
                                    // 'disabled' => true,
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
                                    // 'disabled' => true,
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
                    <th>Grade Asal</th>
                    <th>Upgrade</th>
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
$this->registerJsVar('inspectingItems', $items);
$this->registerJs($this->renderFile(__DIR__.'/js/form-upgrade.js'), $this::POS_END);