<?php
use kartik\widgets\DatePicker;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;
use common\models\ar\InspectingMklBjItems;

/* @var $this yii\web\View */
/* @var $model common\models\ar\InspectingMklBj */
/* @var $modelItem InspectingMklBjItems */
/* @var $form ActiveForm */
/* @var $items array */

\backend\assets\DataTablesAsset::register($this);
?>

<div class="inspecting-mkl-bj-form">
    <!--Form Header-->
    <?php $formHeader = ActiveForm::begin(['id'=>'InspectingFormHeader']); ?>
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <?= $formHeader->field($model, 'tgl_kirim')->widget(DatePicker::class, [
                        'options' => ['placeholder' => 'Masukan tanggal ...'],
                        'readonly' => true,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,
                            'todayBtn' => true,
                        ],
                    ])?>

                    <?php
                    $woData = [];
                    if(!empty($model->wo_id)){
                        $woData = \yii\helpers\ArrayHelper::map(
                            \common\models\ar\TrnWo::find()
                                ->select('id, no')
                                ->where(['trn_wo.id'=>$model->wo_id])
                                ->asArray()
                                ->all(),
                            'id',
                            'no'
                        );
                    }
                    echo $formHeader->field($model, 'wo_id')->widget(Select2::class, [
                        'data' => $woData,
                        'options' => ['placeholder' => 'Cari...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                            ],
                            'ajax' => [
                                'url' => Url::to(['/ajax/lookup-wo-all']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(wo) { return wo.text; }'),
                            'templateSelection' => new JsExpression('function (wo) { return wo.text; }'),
                        ],
                        'pluginEvents' => [
                            'select2:select' => 'function(e){$("#MotifKain").html(e.params.data.motif);}',
                            'select2:unselect' => 'function(e){$("#MotifKain").html("-");}'
                        ]
                    ]);
                    ?>

                    <p><strong>Motif: </strong> <span id="MotifKain"><?=$model->isNewRecord ? '' : $model->wo->greigeNamaKain?></span></p>

                    <?php
                    $colorData = [];
                    if(!empty($model->wo_color_id)){
                        $colorData = \yii\helpers\ArrayHelper::map(
                            \common\models\ar\TrnWoColor::find()
                                ->select('trn_wo_color.id, trn_mo_color.color as color')
                                ->joinWith('moColor', false)
                                ->where(['trn_wo_color.wo_id'=>$model->wo->id])
                                ->asArray()
                                ->all(),
                            'id',
                            'color'
                        );
                    }
                    echo $formHeader->field($model, 'wo_color_id')->widget(DepDrop::classname(), [
                        'data' => $colorData,
                        'type' => DepDrop::TYPE_SELECT2,
                        'options' => ['placeholder' => 'Select ...'],
                        'select2Options' => [
                            'pluginOptions' => ['allowClear' => true],
                            'pluginEvents' => [
                                //'select2:unselect' => 'function(e){if(kartuProsesIdOnUnSelect !== null){kartuProsesIdOnUnSelect(e)}}',
                                //'select2:select' => 'function(e){if(kartuProsesIdOnSelect !== null){kartuProsesIdOnSelect(e)}}'
                            ],
                        ],
                        'pluginOptions' => [
                            'depends' => ['inspectingmklbj-wo_id'],
                            'url' => Url::to(['/dep-drop/wo-color']),
                        ]
                    ]);
                    ?>
                </div>

                <div class="col-md-6">
                    <?= $formHeader->field($model, 'tgl_inspeksi')->widget(DatePicker::class, [
                        'options' => ['placeholder' => 'Masukan tanggal ...'],
                        'readonly' => true,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,
                            'todayBtn' => true,
                        ],
                    ])?>

                    <?= $formHeader->field($model, 'jenis')->widget(Select2::classname(), [
                        'data' => $model::jenisOptions(),
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])?>

                    <?= $formHeader->field($model, 'no_lot')->textInput()?>

                    <?= $formHeader->field($model, 'satuan')->widget(Select2::classname(), [
                        'data' => \common\models\ar\MstGreigeGroup::unitOptions(),
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])?>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end()?>
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
                    <th>Keterangan</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <?=$formItem->field($modelItem, 'grade')->widget(Select2::classname(), [
                            'data' => \common\models\ar\InspectingMklBjItems::gradeOptions(),
                            /*'options' => ['placeholder' => 'Pilih ...'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],*/
                        ])->label(false) ?>
                    </td>
                    <td><?=$formItem->field($modelItem, 'qty')->textInput()->label(false)?></td>
                    <td><?=$formItem->field($modelItem, 'join_piece')->textInput()->label(false)?></td>
                    <td><?=$formItem->field($modelItem, 'note')->textInput()->label(false)?></td>
                    <td class="text-right"><?=\yii\helpers\Html::submitButton('Enter', ['class'=>'btn btn-success'])?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php ActiveForm::end()?>
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
                    <th>Grade</th>
                    <th>Ukuran</th>
                    <th>Join Piece</th>
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
$this->registerJsVar('inspectingItems', $items);
$this->registerJs($this->renderFile(__DIR__.'/js/form.js'), $this::POS_END);