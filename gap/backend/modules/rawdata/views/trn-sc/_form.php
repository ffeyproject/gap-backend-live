<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnSc */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-sc-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($model, 'cust_id')->textInput() ?>

    <?php echo $form->field($model, 'jenis_order')->textInput() ?>

    <?php echo $form->field($model, 'currency')->textInput() ?>

    <?php echo $form->field($model, 'bank_acct_id')->textInput() ?>

    <?php echo $form->field($model, 'direktur_id')->textInput() ?>

    <?php echo $form->field($model, 'manager_id')->textInput() ?>

    <?php echo $form->field($model, 'marketing_id')->textInput() ?>

    <?php echo $form->field($model, 'no_urut')->textInput() ?>

    <?php echo $form->field($model, 'no')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'tipe_kontrak')->textInput() ?>

    <?php echo $form->field($model, 'date')->textInput() ?>

    <?php echo $form->field($model, 'pmt_term')->textInput() ?>

    <?php echo $form->field($model, 'pmt_method')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'ongkos_angkut')->textInput() ?>

    <?php echo $form->field($model, 'due_date')->textInput() ?>

    <?php echo $form->field($model, 'delivery_date')->textInput() ?>

    <?php echo $form->field($model, 'destination')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'packing')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'jet_black')->checkbox() ?>

    <?php echo $form->field($model, 'no_po')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'disc_grade_b')->textInput() ?>

    <?php echo $form->field($model, 'disc_piece_kecil')->textInput() ?>

    <?php echo $form->field($model, 'consignee_name')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'apv_dir_at')->textInput() ?>

    <?php echo $form->field($model, 'reject_note_dir')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'apv_mgr_at')->textInput() ?>

    <?php echo $form->field($model, 'reject_note_mgr')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'notify_party')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'buyer_name_in_invoice')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'posted_at')->textInput() ?>

    <?php echo $form->field($model, 'closed_at')->textInput() ?>

    <?php echo $form->field($model, 'closed_by')->textInput() ?>

    <?php echo $form->field($model, 'closed_note')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'batal_at')->textInput() ?>

    <?php echo $form->field($model, 'batal_by')->textInput() ?>

    <?php echo $form->field($model, 'batal_note')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'status')->textInput() ?>

    <?php //echo $form->field($model, 'created_at')->textInput() ?>

    <?php //echo $form->field($model, 'created_by')->textInput() ?>

    <?php //echo $form->field($model, 'updated_at')->textInput() ?>

    <?php //echo $form->field($model, 'updated_by')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
