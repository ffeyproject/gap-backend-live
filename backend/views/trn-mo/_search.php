<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-mo-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sc_id') ?>

    <?= $form->field($model, 'sc_greige_id') ?>

    <?= $form->field($model, 'approval_id') ?>

    <?= $form->field($model, 'approved_at') ?>

    <?php // echo $form->field($model, 'no_urut') ?>

    <?php // echo $form->field($model, 'no') ?>

    <?php // echo $form->field($model, 'date') ?>

    <?php // echo $form->field($model, 're_wo') ?>

    <?php // echo $form->field($model, 'design') ?>

    <?php // echo $form->field($model, 'article') ?>

    <?php // echo $form->field($model, 'strike_off') ?>

    <?php // echo $form->field($model, 'heat_cut')->checkbox() ?>

    <?php // echo $form->field($model, 'sulam_pinggir') ?>

    <?php // echo $form->field($model, 'handling') ?>

    <?php // echo $form->field($model, 'border_size') ?>

    <?php // echo $form->field($model, 'block_size') ?>

    <?php // echo $form->field($model, 'foil')->checkbox() ?>

    <?php // echo $form->field($model, 'face_stamping') ?>

    <?php // echo $form->field($model, 'selvedge_stamping') ?>

    <?php // echo $form->field($model, 'selvedge_continues') ?>

    <?php // echo $form->field($model, 'side_band') ?>

    <?php // echo $form->field($model, 'tag') ?>

    <?php // echo $form->field($model, 'hanger') ?>

    <?php // echo $form->field($model, 'label') ?>

    <?php // echo $form->field($model, 'folder') ?>

    <?php // echo $form->field($model, 'album') ?>

    <?php // echo $form->field($model, 'joint')->checkbox() ?>

    <?php // echo $form->field($model, 'joint_qty') ?>

    <?php // echo $form->field($model, 'packing_method') ?>

    <?php // echo $form->field($model, 'shipping_method') ?>

    <?php // echo $form->field($model, 'shipping_sorting') ?>

    <?php // echo $form->field($model, 'plastic') ?>

    <?php // echo $form->field($model, 'arsip') ?>

    <?php // echo $form->field($model, 'jet_black')->checkbox() ?>

    <?php // echo $form->field($model, 'piece_length') ?>

    <?php // echo $form->field($model, 'est_produksi') ?>

    <?php // echo $form->field($model, 'est_packing') ?>

    <?php // echo $form->field($model, 'target_shipment') ?>

    <?php // echo $form->field($model, 'posted_at') ?>

    <?php // echo $form->field($model, 'closed_at') ?>

    <?php // echo $form->field($model, 'closed_by') ?>

    <?php // echo $form->field($model, 'closed_note') ?>

    <?php // echo $form->field($model, 'reject_notes') ?>

    <?php // echo $form->field($model, 'batal_at') ?>

    <?php // echo $form->field($model, 'batal_by') ?>

    <?php // echo $form->field($model, 'batal_note') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'note') ?>

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
