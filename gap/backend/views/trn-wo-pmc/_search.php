<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnWoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trn-wo-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sc_id') ?>

    <?= $form->field($model, 'sc_greige_id') ?>

    <?= $form->field($model, 'mo_id') ?>

    <?= $form->field($model, 'jenis_order') ?>

    <?php // echo $form->field($model, 'greige_id') ?>

    <?php // echo $form->field($model, 'mengetahui_id') ?>

    <?php // echo $form->field($model, 'apv_mengetahui_at') ?>

    <?php // echo $form->field($model, 'reject_note_mengetahui') ?>

    <?php // echo $form->field($model, 'no_urut') ?>

    <?php // echo $form->field($model, 'no') ?>

    <?php // echo $form->field($model, 'date') ?>

    <?php // echo $form->field($model, 'papper_tube') ?>

    <?php // echo $form->field($model, 'plastic_size') ?>

    <?php // echo $form->field($model, 'shipping_mark') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'note_two') ?>

    <?php // echo $form->field($model, 'marketing_id') ?>

    <?php // echo $form->field($model, 'apv_marketing_at') ?>

    <?php // echo $form->field($model, 'reject_note_marketing') ?>

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
