<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstProcessPfpSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mst-process-pfp-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'order') ?>

    <?= $form->field($model, 'max_pengulangan') ?>

    <?= $form->field($model, 'nama_proses') ?>

    <?= $form->field($model, 'tanggal')->checkbox() ?>

    <?php // echo $form->field($model, 'start')->checkbox() ?>

    <?php // echo $form->field($model, 'stop')->checkbox() ?>

    <?php // echo $form->field($model, 'no_mesin')->checkbox() ?>

    <?php // echo $form->field($model, 'shift_operator')->checkbox() ?>

    <?php // echo $form->field($model, 'temp')->checkbox() ?>

    <?php // echo $form->field($model, 'speed')->checkbox() ?>

    <?php // echo $form->field($model, 'waktu')->checkbox() ?>

    <?php // echo $form->field($model, 'program_number')->checkbox() ?>

    <?php // echo $form->field($model, 'ex_relax')->checkbox() ?>

    <?php // echo $form->field($model, 'ex_wr_oligomer')->checkbox() ?>

    <?php // echo $form->field($model, 'ex_dyeing')->checkbox() ?>

    <?php // echo $form->field($model, 'wr_pcnt')->checkbox() ?>

    <?php // echo $form->field($model, 'rpm')->checkbox() ?>

    <?php // echo $form->field($model, 'density')->checkbox() ?>

    <?php // echo $form->field($model, 'jamur')->checkbox() ?>

    <?php // echo $form->field($model, 'karat')->checkbox() ?>

    <?php // echo $form->field($model, 'over_feed')->checkbox() ?>

    <?php // echo $form->field($model, 'counter')->checkbox() ?>

    <?php // echo $form->field($model, 'lebar_jadi')->checkbox() ?>

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
