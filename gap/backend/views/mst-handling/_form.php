<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstHandling */
/* @var $form ActiveForm */

if($model->ket_washing === null){
    $model->ket_washing = '';
}else{
    switch ($model->ket_washing){
        case true:
            $model->ket_washing = 1;
            break;
        case false:
            $model->ket_washing = 0;
            break;
    }
}

if($model->ket_wr === null){
    $model->ket_wr = '';
}else{
    switch ($model->ket_wr){
        case true:
            $model->ket_wr = 1;
            break;
        case false:
            $model->ket_wr = 0;
            break;
    }
}
?>

<div class="mst-handling-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <?php
                    $greigeInit = empty($model->greige) ? '' : $model->greige->nama_kain.' (Alias: '.$model->greige->alias.')';
                    echo $form->field($model, 'greige_id')->widget(Select2::classname(), [
                        'options' => ['placeholder' => 'Search for a greige ...'],
                        'initValueText' => $greigeInit,
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                            ],
                            'ajax' => [
                                'url' => Url::to(['/ajax/lookup-greige']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(greige) { return greige.text; }'),
                            'templateSelection' => new JsExpression('function (greige) { return greige.text; }'),
                        ],
                    ]);
                    ?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'lebar_preset')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'lebar_finish')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'keterangan')->textarea(['rows'=>2]) ?>

                    <?=$form->field($model, 'ket_washing')->radioList([1=>'Ya', 0=>'Tidak', ''=>'Tidak Diset'])?>

                    <?=$form->field($model, 'ket_wr')->radioList(['1'=>'Ya', '0'=>'Tidak', ''=>'Tidak Diset'])?>

                    <?=$form->field($model, 'berat_persiapan')->textInput()?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'no_hanger')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'berat_finish')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'densiti_lusi')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'densiti_pakan')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'buyer_ids')->textInput() ?>

                    <div class="form-group">
                        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
