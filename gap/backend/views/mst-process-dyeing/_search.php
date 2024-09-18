<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstProcessDyeingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mst-process-dyeing-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'nama_proses') ?>

    <?= $form->field($model, 'tanggal')->checkbox() ?>

    <?= $form->field($model, 'start')->checkbox() ?>

    <?= $form->field($model, 'stop')->checkbox() ?>

    <?php // echo $form->field($model, 'no_mesin')->checkbox() ?>

    <?php // echo $form->field($model, 'shift_group')->checkbox() ?>

    <?php // echo $form->field($model, 'temp')->checkbox() ?>

    <?php // echo $form->field($model, 'speed')->checkbox() ?>

    <?php // echo $form->field($model, 'gramasi')->checkbox() ?>

    <?php // echo $form->field($model, 'program_number')->checkbox() ?>

    <?php // echo $form->field($model, 'density')->checkbox() ?>

    <?php // echo $form->field($model, 'over_feed')->checkbox() ?>

    <?php // echo $form->field($model, 'lebar_jadi')->checkbox() ?>

    <?php // echo $form->field($model, 'panjang_jadi')->checkbox() ?>

    <?php // echo $form->field($model, 'info_kualitas')->checkbox() ?>

    <?php // echo $form->field($model, 'gangguan_produksi')->checkbox() ?>

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
