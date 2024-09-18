<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstProcessPfp */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mst-process-pfp-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'nama_proses')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'order')->textInput() ?>

                    <?= $form->field($model, 'max_pengulangan')->textInput() ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'tanggal')->checkbox() ?>

                    <?= $form->field($model, 'start')->checkbox() ?>

                    <?= $form->field($model, 'stop')->checkbox() ?>

                    <?= $form->field($model, 'no_mesin')->checkbox() ?>

                    <?= $form->field($model, 'shift_operator')->checkbox() ?>

                    <?= $form->field($model, 'temp')->checkbox() ?>

                    <?= $form->field($model, 'speed')->checkbox() ?>

                    <?= $form->field($model, 'waktu')->checkbox() ?>

                    <?= $form->field($model, 'program_number')->checkbox() ?>

                    <?= $form->field($model, 'ex_relax')->checkbox() ?>

                    <?= $form->field($model, 'ex_wr_oligomer')->checkbox() ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'ex_dyeing')->checkbox() ?>

                    <?= $form->field($model, 'wr_pcnt')->checkbox() ?>

                    <?= $form->field($model, 'rpm')->checkbox() ?>

                    <?= $form->field($model, 'density')->checkbox() ?>

                    <?= $form->field($model, 'jamur')->checkbox() ?>

                    <?= $form->field($model, 'karat')->checkbox() ?>

                    <?= $form->field($model, 'over_feed')->checkbox() ?>

                    <?= $form->field($model, 'counter')->checkbox() ?>

                    <?= $form->field($model, 'lebar_jadi')->checkbox() ?>

                    <?= $form->field($model, 'info_kualitas')->checkbox() ?>

                    <?= $form->field($model, 'gangguan_produksi')->checkbox() ?>

                    <?= $form->field($model, 'gramasi')->checkbox() ?>
                </div>
            </div>
        </div>

        <div class="box-footer">
            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
