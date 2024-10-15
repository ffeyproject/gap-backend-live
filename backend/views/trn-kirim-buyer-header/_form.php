<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimBuyerHeader */
/* @var $form ActiveForm */

$readonly = (!$model->isNewRecord && $model->status == 2) ? true : false; // status posted
?>

<div class="trn-kirim-buyer-header-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box">
        <div class="box-body">
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
                    <?php
                    if($model->isNewRecord){
                        $ajaxUrl = Url::to(['ajax/customer-search']);
                        $customer = empty($model->customer_id) ? '' : $model->customer->name.' ('.$model->customer->cust_no.')';
                        echo $form->field($model, "customer_id")->widget(Select2::class, [
                            'initValueText' => $customer, // set the initial display text
                            'options' => ['placeholder' => 'Cari buyer ...'],
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
                                'templateResult' => new JsExpression('function(greige) { return greige.text; }'),
                                'templateSelection' => new JsExpression('function (greige) { return greige.text; }'),
                            ],
                        ]);
                    }else{
                        echo '<div class="form-group">';
                        echo '<label class="control-label" for="NamaBuyerInput">Buyer</label>';
                        echo '<input type="text" id="NamaBuyerInput" class="form-control" value="'.$model->customer->name.'" disabled>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6"><?= $form->field($model, 'nama_buyer')->textInput(['maxlength' => true, 'disabled' => $readonly]) ?></div>

                <div class="col-md-6"><?= $form->field($model, 'alamat_buyer')->textInput(['maxlength' => true, 'disabled' => $readonly]) ?></div>
            </div>

            <div class="row">
                <div class="col-md-4"><?= $form->field($model, 'pengirim')->textInput(['maxlength' => true, 'disabled' => $readonly]) ?></div>

                <div class="col-md-4"><?= $form->field($model, 'penerima')->textInput(['maxlength' => true, 'disabled' => $readonly]) ?></div>

                <div class="col-md-4"><?= $form->field($model, 'kepala_gudang')->textInput(['maxlength' => true, 'disabled' => $readonly]) ?></div>
            </div>

            <div class="row">
                <div class="col-md-6"><?= $form->field($model, 'plat_nomor')->textInput(['maxlength' => true, 'disabled' => $readonly]) ?></div>

                <div class="col-md-6">
                    <?= $form->field($model, 'is_export')->checkbox(['disabled' => $readonly]) ?>

                    <?= $form->field($model, 'is_resmi')->checkbox(['disabled' => $readonly]) ?>
                </div>
            </div>

            <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

            <?php if (!$model->isNewRecord): ?>
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($model, 'no')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="box-footer">
            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
