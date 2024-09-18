<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnReturBuyerItem;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnReturBuyer */
/* @var  $modelsItem TrnReturBuyerItem[]*/
/* @var $form ActiveForm */
?>

<div class="trn-retur-buyer-form">

    <?php $form = ActiveForm::begin(['id'=>'dynamic-form']); ?>

    <?=$form->errorSummary($model)?>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            if($model->isNewRecord){
                                $ajaxUrl = Url::to(['ajax/lookup-wo']);
                                $wo = empty($model->wo_id) ? '' : $model->wo->no;
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
                                            'url' => $ajaxUrl,
                                            'dataType' => 'json',
                                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                        ],
                                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                        'templateResult' => new JsExpression('function(wo) { return wo.text; }'),
                                        'templateSelection' => new JsExpression('function (wo) { return wo.text; }'),
                                    ],
                                    /*'pluginEvents' => [
                                        'select2:select' => 'function(e){let lookupWoColorUrl = "'.$lookupWoColorUrl.'"; '.$this->renderFile(Yii::$app->controller->viewPath.'/js/wo-on-select.js').'}',
                                        'select2:unselect' => 'function(e){$("#trnkartuprosesprinting-wo_color_id").val(null).trigger("change"); $("#trnkartuprosesmaklon-wo_color_id").empty();}'
                                    ]*/
                                ])->label('Nomor Working Order');
                            }else{
                                echo '<div class="form-group"><label class="control-label" for="nomorWo">Nomor WO</label><input value="'.$model->wo->no.'" type="text" id="nomorWo" class="form-control" disabled></div>';
                            }
                            ?>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($model, 'unit')->widget(Select2::classname(), [
                                'data' => MstGreigeGroup::unitOptions(),
                                'options' => ['placeholder' => 'Pilih ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'jenis_gudang')->widget(Select2::classname(), [
                                'data' => \common\models\ar\TrnGudangJadi::jenisGudangOptions(),
                                'options' => ['placeholder' => 'Pilih ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) ?>
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
                            <?=$form->field($model, 'date_document')->widget(\kartik\widgets\DatePicker::classname(), [
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
                            <?= $form->field($model, 'no_document')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'penanggungjawab')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'nama_qc')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <?=$this->render('child/_form_items', ['form'=>$form, 'model'=>$model, 'modelsItem'=>$modelsItem])?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
