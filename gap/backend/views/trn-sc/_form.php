<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnSc */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-sc-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cust_id')->textInput() ?>

    <?= $form->field($model, 'jenis_order')->textInput() ?>

    <?= $form->field($model, 'currency')->textInput() ?>

    <?= $form->field($model, 'bank_acct_id')->textInput() ?>

    <?= $form->field($model, 'direktur_id')->textInput() ?>

    <?= $form->field($model, 'manager_id')->textInput() ?>

    <?= $form->field($model, 'marketing_id')->textInput() ?>

    <?= $form->field($model, 'no_urut')->textInput() ?>

    <?= $form->field($model, 'no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tipe_kontrak')->textInput() ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'pmt_term')->textInput() ?>

    <?= $form->field($model, 'pmt_method')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ongkos_angkut')->textInput() ?>

    <?= $form->field($model, 'due_date')->textInput() ?>

    <?= $form->field($model, 'delivery_date')->textInput() ?>

    <?= $form->field($model, 'destination')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'packing')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'jet_black')->checkbox() ?>

    <?= $form->field($model, 'no_po')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'disc_grade_b')->textInput() ?>

    <?= $form->field($model, 'disc_piece_kecil')->textInput() ?>

    <?= $form->field($model, 'consignee_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'apv_dir_at')->textInput() ?>

    <?= $form->field($model, 'reject_note_dir')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'apv_mgr_at')->textInput() ?>

    <?= $form->field($model, 'reject_note_mgr')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'notify_party')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'buyer_name_in_invoice')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'posted_at')->textInput() ?>

    <?= $form->field($model, 'closed_at')->textInput() ?>

    <?= $form->field($model, 'closed_by')->textInput() ?>

    <?= $form->field($model, 'closed_note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'batal_at')->textInput() ?>

    <?= $form->field($model, 'batal_by')->textInput() ?>

    <?= $form->field($model, 'batal_note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
