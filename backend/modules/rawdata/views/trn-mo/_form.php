<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnMo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-mo-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sc_id')->textInput() ?>

    <?= $form->field($model, 'sc_greige_id')->textInput() ?>

    <?= $form->field($model, 'process')->textInput() ?>

    <?= $form->field($model, 'approval_id')->textInput() ?>

    <?= $form->field($model, 'approved_at')->textInput() ?>

    <?= $form->field($model, 'no_urut')->textInput() ?>

    <?= $form->field($model, 'no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 're_wo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'design')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'handling')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'article')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'strike_off')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'heat_cut')->checkbox() ?>

    <?= $form->field($model, 'sulam_pinggir')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'border_size')->textInput() ?>

    <?= $form->field($model, 'block_size')->textInput() ?>

    <?= $form->field($model, 'foil')->checkbox() ?>

    <?= $form->field($model, 'face_stamping')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'selvedge_stamping')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'selvedge_continues')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'side_band')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tag')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hanger')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'folder')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'album')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'joint')->checkbox() ?>

    <?= $form->field($model, 'joint_qty')->textInput() ?>

    <?= $form->field($model, 'packing_method')->textInput() ?>

    <?= $form->field($model, 'shipping_method')->textInput() ?>

    <?= $form->field($model, 'shipping_sorting')->textInput() ?>

    <?= $form->field($model, 'plastic')->textInput() ?>

    <?= $form->field($model, 'arsip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'jet_black')->checkbox() ?>

    <?= $form->field($model, 'piece_length')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'est_produksi')->textInput() ?>

    <?= $form->field($model, 'est_packing')->textInput() ?>

    <?= $form->field($model, 'target_shipment')->textInput() ?>

    <?= $form->field($model, 'jenis_gudang')->textInput() ?>

    <?= $form->field($model, 'posted_at')->textInput() ?>

    <?= $form->field($model, 'closed_at')->textInput() ?>

    <?= $form->field($model, 'closed_by')->textInput() ?>

    <?= $form->field($model, 'closed_note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'reject_notes')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'batal_at')->textInput() ?>

    <?= $form->field($model, 'batal_by')->textInput() ?>

    <?= $form->field($model, 'batal_note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>