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

                    <?= $form->field($model, 'nomor_kartu')->textInput() ?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'kombinasi')->textInput(['maxlength' => true]) ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'lusi')->textInput(['maxlength' => true]) ?>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($model, 'pakan')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

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
