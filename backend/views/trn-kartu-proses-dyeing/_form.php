<?php
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWoColor;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model TrnKartuProsesDyeing */
/* @var $form yii\widgets\ActiveForm */

$lookupWoColorUrl = Url::to(['/ajax/lookup-wo-color']);
?>

<div class="kartu-proses-dyeing-form">
    <?php $form = ActiveForm::begin(); ?>

    <?=$form->errorSummary($model)?>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <?php
                    $wo = $model->wo_id === null ? '' : $model->wo->no;
                    echo $form->field($model, 'wo_id')->widget(Select2::class, [
                        'initValueText' => $wo, // set the initial display text
                        'options' => ['placeholder' => 'Cari WO...'],
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
                    ])->label('Nomor Working Order');?>

                    <?php
                    // $kp = $model->kartu_proses_id === null ? '' : $model->kartuProses->no;
                    // echo $form->field($model, 'kartu_proses_id')->widget(Select2::class, [
                    //     'initValueText' => $kp, // set the initial display text
                    //     'options' => ['placeholder' => 'Cari Kartu Proses...'],
                    //     'pluginOptions' => [
                    //         'allowClear' => true,
                    //         'minimumInputLength' => 3,
                    //         'language' => [
                    //             'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                    //         ],
                    //         'ajax' => [
                    //             'url' => Url::to(['ajax/lookup-kartu-proses-dyeing']),
                    //             'dataType' => 'json',
                    //             'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    //         ],
                    //         'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    //         'templateResult' => new JsExpression('function(wo) { return wo.text; }'),
                    //         'templateSelection' => new JsExpression('function (wo) { return wo.text; }'),
                    //     ],
                    //     'pluginEvents' => [
                    //         'select2:select' => 'function(e){let lookupWoColorUrl = "'.$lookupWoColorUrl.'"; '.$this->renderFile(Yii::$app->controller->viewPath.'/js/kp-on-select.js').'}',
                    //         'select2:unselect' => 'function(e){$("#trnkartuprosesdyeing-wo_color_id").val(null).trigger("change"); $("#trnkartuprosesdyeing-wo_color_id").empty();}'
                    //     ]
                    // ])->label('Nomor Kartu Proses');?>

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

                    <?= $form->field($model, 'asal_greige')->widget(Select2::classname(), [
                        'data' => TrnStockGreige::asalGreigeOptions(),
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]) ?>

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

                    <?= $form->field($model, 'dikerjakan_oleh')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'nomor_kartu')->textInput() ?>

                    <?= $form->field($model, 'is_redyeing')->checkbox(['label' => 'Re-Dyeing', 'value' => true, 'uncheckValue' => false]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">KONSTRUKSI GREIGE</h3>
                </div>
                <div class="box-body">
                    <?= $form->field($model, 'lebar')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'lusi')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'pakan')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'k_density_lusi')->textInput(['maxlength' => true])->label('Density Lusi') ?>

                    <?= $form->field($model, 'k_density_pakan')->textInput(['maxlength' => true])->label('Density Pakan') ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>