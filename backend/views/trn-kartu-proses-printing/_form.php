<?php
use common\models\ar\TrnKartuProsesPrinting;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWoColor;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model TrnKartuProsesPrinting */
/* @var $form ActiveForm */

$lookupWoColorUrl = Url::to(['/ajax/lookup-wo-color']);
?>

<div class="trn-kartu-proses-printing-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="box">
        <div class="box-body">
            <?=$form->errorSummary($model)?>

            <div class="row" style="background-color: #f9f9f9; padding: 10px; border-radius: 4px; margin-bottom: 15px; border: 1.5px dashed #3c8dbc;">
                <div class="col-md-6">
                    <?php
                    $ajaxNkUrl = Url::to(['/ajax/lookup-existing-nk-printing']);
                    $getNkDetailsUrl = Url::to(['/ajax/get-nk-details-printing']);
                    echo $form->field($model, 'copy_nk')->widget(Select2::class, [
                        'options' => ['placeholder' => 'Pilih Nomor Kartu yang Ada (Salin Data & NK baru)...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 1,
                            'ajax' => [
                                'url' => $ajaxNkUrl,
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(nk) { return nk.text; }'),
                            'templateSelection' => new JsExpression('function (nk) { return nk.text; }'),
                        ],
                        'pluginEvents' => [
                            'select2:select' => new JsExpression('
                                function(e) {
                                    var id = e.params.data.id;
                                    if (id) {
                                        $.ajax({
                                            url: "' . $getNkDetailsUrl . '",
                                            data: {id: id},
                                            success: function(res) {
                                                if (res.success) {
                                                    $("#trnkartuprosesprinting-nomor_kartu").val(res.next_nk);
                                                    
                                                    // Set WO
                                                    if (res.wo_id) {
                                                        var newOptionWo = new Option(res.wo_no, res.wo_id, true, true);
                                                        $("#trnkartuprosesprinting-wo_id").append(newOptionWo).trigger("change");
                                                    } else {
                                                        $("#trnkartuprosesprinting-wo_id").val(null).trigger("change");
                                                    }
                                                    
                                                    // Set Kartu Proses ID
                                                    if (res.kartu_proses_id) {
                                                        var newOptionKp = new Option(res.kartu_proses_no, res.kartu_proses_id, true, true);
                                                        $("#trnkartuprosesprinting-kartu_proses_id").append(newOptionKp).trigger("change");
                                                    } else {
                                                        $("#trnkartuprosesprinting-kartu_proses_id").val(null).trigger("change");
                                                    }
                                                    
                                                    // Set Colors
                                                    var colorSelect = $("#trnkartuprosesprinting-wo_color_id");
                                                    colorSelect.empty().append(new Option("Select ...", "", false, false));
                                                    if (res.colors && res.colors.length > 0) {
                                                        res.colors.forEach(function(col) {
                                                            var opt = new Option(col.text, col.id, col.id == res.wo_color_id, col.id == res.wo_color_id);
                                                            colorSelect.append(opt);
                                                        });
                                                    }
                                                    colorSelect.val(res.wo_color_id).trigger("change");
                                                    
                                                    // Set other fields
                                                    $("#trnkartuprosesprinting-asal_greige").val(res.asal_greige).trigger("change");
                                                    $("#trnkartuprosesprinting-dikerjakan_oleh").val(res.dikerjakan_oleh);
                                                    $("#trnkartuprosesprinting-jenis_printing").val(res.jenis_printing).trigger("change");
                                                }
                                            }
                                        });
                                    }
                                }
                            '),
                            'select2:unselect' => new JsExpression('
                                function(e) {
                                    // Reset to default generated NK and clear fields
                                    $("#trnkartuprosesprinting-nomor_kartu").val("' . TrnKartuProsesPrinting::generateNomorKartu() . '");
                                    $("#trnkartuprosesprinting-wo_id").val(null).trigger("change");
                                    $("#trnkartuprosesprinting-kartu_proses_id").val(null).trigger("change");
                                    $("#trnkartuprosesprinting-wo_color_id").val(null).trigger("change").empty();
                                    $("#trnkartuprosesprinting-asal_greige").val(null).trigger("change");
                                    $("#trnkartuprosesprinting-dikerjakan_oleh").val("");
                                    $("#trnkartuprosesprinting-jenis_printing").val(null).trigger("change");
                                }
                            ')
                        ]
                    ])->label('Salin dari Nomor Kartu yang Ada (Opsional)');
                    ?>
                </div>
                <div class="col-md-6" style="padding-top: 25px;">
                    <span class="text-muted"><i class="glyphicon glyphicon-info-sign"></i> Pilih Nomor Kartu (NK) yang sudah ada untuk menduplikasi detail kartu tersebut. NK baru akan disesuaikan secara otomatis (misal: 10/26 -> 10B/26).</span>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            $ajaxUrl = Url::to(['ajax/lookup-wo-printing']);
                            $greige = empty($model->wo_id) ? '' : $model->wo->no;
                            echo $form->field($model, 'wo_id')->widget(Select2::class, [
                                'initValueText' => $greige, // set the initial display text
                                'options' => ['placeholder' => 'Cari WO...'],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'minimumInputLength' => 3,
                                    'language' => [
                                        'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                                    ],
                                    'ajax' => [
                                        'url' => $ajaxUrl,
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                    ],
                                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                    'templateResult' => new JsExpression('function(wo) { return wo.text; }'),
                                    'templateSelection' => new JsExpression('function (wo) { return wo.text; }'),
                                ],
                                'pluginEvents' => [
                                    'select2:select' => 'function(e){let lookupWoColorUrl = "'.$lookupWoColorUrl.'"; '.$this->renderFile(Yii::$app->controller->viewPath.'/js/wo-on-select.js').'}',
                                    'select2:unselect' => 'function(e){$("#trnkartuprosesprinting-wo_color_id").val(null).trigger("change"); $("#trnkartuprosesprinting-wo_color_id").empty();}'
                                ]
                            ])->label('Nomor Working Order');?>
                        </div>

                        <div class="col-md-6">
                            <?php
                            $ajaxUrl = Url::to(['ajax/lookup-kartu-proses-printing']);
                            $kp = empty($model->kartu_proses_id) ? '' : $model->kartuProses->no;
                            echo $form->field($model, 'kartu_proses_id')->widget(Select2::class, [
                                'initValueText' => $kp, // set the initial display text
                                'options' => ['placeholder' => 'Cari Kartu Proses...'],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'minimumInputLength' => 3,
                                    'language' => [
                                        'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                                    ],
                                    'ajax' => [
                                        'url' => $ajaxUrl,
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                    ],
                                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                    'templateResult' => new JsExpression('function(wo) { return wo.text; }'),
                                    'templateSelection' => new JsExpression('function (wo) { return wo.text; }'),
                                ],
                                'pluginEvents' => [
                                    'select2:select' => 'function(e){let lookupWoColorUrl = "'.$lookupWoColorUrl.'"; '.$this->renderFile(Yii::$app->controller->viewPath.'/js/kp-on-select.js').'}',
                                    'select2:unselect' => 'function(e){$("#trnkartuprosesprinting-wo_color_id").val(null).trigger("change"); $("#trnkartuprosesprinting-wo_color_id").empty();}'
                                ]
                            ])->label('Nomor Kartu Proses');?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            $dataColors = ArrayHelper::map(TrnWoColor::find()->with('moColor')->where(['wo_id'=>$model->wo_id])->asArray()->all(), 'id', function ($data){
                                return $data['moColor']['color'];
                            });

                            echo $form->field($model, 'wo_color_id')->widget(Select2::classname(), [
                                'data' => $dataColors,
                                'options' => ['placeholder' => 'Select ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);
                            ?>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($model, 'asal_greige')->widget(Select2::classname(), [
                                'data' => TrnStockGreige::asalGreigeOptions(),
                                'options' => ['placeholder' => 'Pilih ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'dikerjakan_oleh')->textInput(['maxlength' => true]) ?>
                        </div>

                        <div class="col-md-6">
                            <?=$form->field($model, 'date')->widget(\kartik\widgets\DatePicker::classname(), [
                                'options' => ['placeholder' => 'Pilih Tanggal ...'],
                                'readonly' => true,
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'yyyy-mm-dd',
                                    'todayHighlight' => true,
                                    //'todayBtn' => true,
                                ]
                            ])?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'jenis_printing')->widget(Select2::classname(), [
                                'data' => TrnKartuProsesPrinting::jenisPrintingOptions(),
                                'options' => ['placeholder' => 'Pilih ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) ?>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($model, 'nomor_kartu')->textInput(['readonly' => true]) ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>
                </div>
            </div>
        </div>

        <div class="box-footer">
            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
