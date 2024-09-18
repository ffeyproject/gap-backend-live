<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\MstVendor;
use common\models\ar\TrnWoColor;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnTerimaMakloonFinish */
/* @var $modelsItem common\models\ar\TrnTerimaMakloonFinishItem[] */
/* @var $form ActiveForm */

$lookupWoColorUrlByKirimMakloon = Url::to(['/ajax/lookup-wo-color-by-kirim-makloon']);
?>

<div class="trn-terima-makloon-finish-form">

    <?php $form = ActiveForm::begin(['id'=>'dynamic-form']); ?>

    <?=$form->errorSummary($model)?>

    <div class="row">
        <div class="col-md-8">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            if($model->isNewRecord){
                                $ajaxUrl = Url::to(['ajax/lookup-kirim-makloon-posted']);
                                $wo = empty($model->wo_id) ? '' : $model->wo->no;
                                echo $form->field($model, 'kirim_makloon_id')->widget(Select2::class, [
                                    'initValueText' => $wo, // set the initial display text
                                    'options' => ['placeholder' => 'Cari...'],
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
                                        'select2:select' => 'function(e){let lookupWoColorUrl = "'.$lookupWoColorUrlByKirimMakloon.'"; '.$this->renderFile(Yii::$app->controller->viewPath.'/js/wo-on-select.js').'}',
                                        'select2:unselect' => 'function(e){$("#trnterimamakloonfinish-wo_color_id").val(null).trigger("change"); $("#trnterimamakloonfinish-wo_color_id").empty();}'
                                    ]
                                ])->label('Nomor Pengiriman Ke Makloon');
                            }else{
                                echo '<div class="form-group"><label class="control-label" for="nomorWo">Nomor Pengiriman</label><input value="'.$model->kirimMakloon->no.'" type="text" id="nomorWo" class="form-control" disabled></div>';
                            }
                            ?>
                        </div>

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
                    </div>

                    <div class="row">
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
                        <div class="col-md-6">
                            <?= $form->field($model, 'pengirim')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">

                        </div>

                        <div class="col-md-6"></div>
                    </div>

                    <?= $form->field($model, 'note')->textarea(['rows' => 3]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="box">
                        <div class="box-body">
                            <?= $form->field($model, 'jenis_gudang')->widget(Select2::classname(), [
                                'data' => \common\models\ar\TrnGudangJadi::jenisGudangOptions(),
                                'options' => ['placeholder' => 'Pilih ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) ?>
                        </div>

                        <div class="box-footer">
                            <div class="form-group">
                                <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6"></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box">
                <div class="box-body">
                    <?=$this->render('child/_form_items', ['form'=>$form, 'model'=>$model, 'modelsItem'=>$modelsItem])?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
