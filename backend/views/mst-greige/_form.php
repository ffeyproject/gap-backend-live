<?php

use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstGreige */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-body">
                <?php
                $greigeGroup = empty($model->group_id) ? '' : $model->group->nama_kain;
                echo $form->field($model, "group_id")->widget(Select2::classname(), [
                    'initValueText' => $greigeGroup, // set the initial display text
                    'options' => ['placeholder' => 'Cari greige group ...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => \yii\helpers\Url::to(['ajax/greige-group-search']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(greigeGroup) { return greigeGroup.text; }'),
                        'templateSelection' => new JsExpression('function (greigeGroup) { return greigeGroup.text; }'),
                    ]
                ]);?>

                <?= $form->field($model, 'nama_kain')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'aktif')->checkbox() ?>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box">
            <div class="box-body">
                <?= $form->field($model, 'no_dok_referensi')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'gap')->textInput() ?>

                <?php echo $form->field($model, 'stock')->textInput(); ?>

                <?php echo $form->field($model, 'available')->textInput(); ?>

                <?php echo $form->field($model, 'booked_wo')->textInput(); ?>

                <?php echo $form->field($model, 'booked_opfp')->textInput(); ?>

                <?php echo $form->field($model, 'booked')->textInput(); ?>

                <?php echo $form->field($model, 'stock_pfp')->textInput(); ?>

                <?php echo $form->field($model, 'available_pfp')->textInput(); ?>

                <?php echo $form->field($model, 'booked_pfp')->textInput(); ?>

                <?php echo $form->field($model, 'stock_wip')->textInput(); ?>

                <?php echo $form->field($model, 'booked_wip')->textInput(); ?>

                <?php echo $form->field($model, 'stock_ef')->textInput(); ?>

                <?php echo $form->field($model, 'booked_ef')->textInput(); ?>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>