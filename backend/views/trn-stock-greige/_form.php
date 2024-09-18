<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\JsExpression;
use kartik\widgets\ActiveForm;
use common\models\ar\TrnStockGreige;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnStockGreige */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-stock-greige-form">

    <?php $form = ActiveForm::begin([
        'id'=>'FrmAddPlGreige'
    ]); ?>

    <?=$form->errorSummary($model)?>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-8">
                            <?php
                            $ajaxUrl = \yii\helpers\Url::to(['ajax/lookup-greige']);
                            $greige = empty($model->greige_id) ? '' : $model->greige->nama_kain;
                            echo $form->field($model, 'greige_id')->widget(Select2::class, [
                                'initValueText' => $greige, // set the initial display text
                                'options' => ['placeholder' => 'Cari greige...'],
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
                                ]
                            ]);?>
                        </div>

                        <div class="col-md-4">
                            <?= $form->field($model, 'no_lapak')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <?php
                    $asalGreiges = [
                        TrnStockGreige::ASAL_GREIGE_WJL => TrnStockGreige::asalGreigeOptions()[TrnStockGreige::ASAL_GREIGE_WJL],
                        TrnStockGreige::ASAL_GREIGE_RAPIER => TrnStockGreige::asalGreigeOptions()[TrnStockGreige::ASAL_GREIGE_RAPIER]
                    ]; //hanya bisa dari dua asal greige ini

                    echo $form->field($model, 'asal_greige')->widget(Select2::classname(), [
                        'data' => $asalGreiges,
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>

                    <div class="row">
                        <div class="col-md-2">
                            <?= $form->field($model, 'grade')->widget(Select2::classname(), [
                                'data' => TrnStockGreige::gradeOptions(),
                                'options' => ['placeholder' => 'Pilih grade ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) ?>
                        </div>

                        <div class="col-md-3">
                            <?= $form->field($model, 'lot_lusi')->textInput(['maxlength' => true]) ?>
                        </div>

                        <div class="col-md-3">
                            <?= $form->field($model, 'lot_pakan')->textInput(['maxlength' => true]) ?>
                        </div>

                        <div class="col-md-4">
                            <?= $form->field($model, 'status_tsd')->widget(Select2::classname(), [
                                'data' => TrnStockGreige::tsdOptions(),
                                'options' => ['placeholder' => 'Pilih ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'pengirim')->textInput(['maxlength' => true]) ?>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($model, 'mengetahui')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-body">
                    <?= $form->field($model, 'no_document', [
                        'addon' => [
                            'append' => [
                                'content' => Html::button('Periksa', ['id'=>'BtnCekNoDoc', 'class'=>'btn btn-primary']),
                                'asButton' => true
                            ]
                        ]
                    ])->textInput(['maxlength' => true]) ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'no_set_lusi')->textInput(['maxlength' => true]) ?>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($model, 'panjang_m')->textInput() ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'note')->textInput() ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <p>
        Note:<br>
        Nantinnya, semua isian akan otomatis terisi ketika no_document diisi. Kecuali panjang dan no_set_lusi
    </p>
</div>

<?php
$urlCheckNoDoc = \yii\helpers\Url::to(['/ajax/lookup-pl-greige-no-doc', 'q'=>'']);
$js = <<<JS
let urlCheckNoDoc = "{$urlCheckNoDoc}";
JS;

$this->registerJs($js.$this->render('js/form.js'), View::POS_END);