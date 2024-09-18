<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnScSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-sc-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cust_id') ?>

    <?= $form->field($model, 'jenis_order') ?>

    <?= $form->field($model, 'currency') ?>

    <?= $form->field($model, 'bank_acct_id') ?>

    <?php // echo $form->field($model, 'direktur_id') ?>

    <?php // echo $form->field($model, 'manager_id') ?>

    <?php // echo $form->field($model, 'marketing_id') ?>

    <?php // echo $form->field($model, 'no_urut') ?>

    <?php // echo $form->field($model, 'no') ?>

    <?php // echo $form->field($model, 'tipe_kontrak') ?>

    <?php // echo $form->field($model, 'date') ?>

    <?php // echo $form->field($model, 'pmt_term') ?>

    <?php // echo $form->field($model, 'pmt_method') ?>

    <?php // echo $form->field($model, 'ongkos_angkut') ?>

    <?php // echo $form->field($model, 'due_date') ?>

    <?php // echo $form->field($model, 'delivery_date') ?>

    <?php // echo $form->field($model, 'destination') ?>

    <?php // echo $form->field($model, 'packing') ?>

    <?php // echo $form->field($model, 'jet_black')->checkbox() ?>

    <?php // echo $form->field($model, 'no_po') ?>

    <?php // echo $form->field($model, 'disc_grade_b') ?>

    <?php // echo $form->field($model, 'disc_piece_kecil') ?>

    <?php // echo $form->field($model, 'consignee_name') ?>

    <?php // echo $form->field($model, 'apv_dir_at') ?>

    <?php // echo $form->field($model, 'reject_note_dir') ?>

    <?php // echo $form->field($model, 'apv_mgr_at') ?>

    <?php // echo $form->field($model, 'reject_note_mgr') ?>

    <?php // echo $form->field($model, 'notify_party') ?>

    <?php // echo $form->field($model, 'buyer_name_in_invoice') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'posted_at') ?>

    <?php // echo $form->field($model, 'closed_at') ?>

    <?php // echo $form->field($model, 'closed_by') ?>

    <?php // echo $form->field($model, 'closed_note') ?>

    <?php // echo $form->field($model, 'batal_at') ?>

    <?php // echo $form->field($model, 'batal_by') ?>

    <?php // echo $form->field($model, 'batal_note') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
