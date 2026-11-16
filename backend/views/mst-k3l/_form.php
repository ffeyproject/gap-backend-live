<?php

use common\models\ar\MstK3l;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstK3l */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mst-k3l-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><?= $model->isNewRecord ? 'Form Tambah Data' : 'Form Ubah Data' ?></h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <?php if ($model->isNewRecord): ?>
                        <?= $form->field($model, 'k3l_code')->textInput(['maxlength' => 45, 'placeholder' => 'Contoh: 20-D-000648']) ?>
                    <?php else: ?>
                        <?= $form->field($model, 'k3l_code')->textInput(['readonly' => true]) ?>
                    <?php endif; ?>

                    <?= $form->field($model, 'k3l_desc')->textInput(['maxlength' => 175, 'placeholder' => 'Deskripsi K3L']) ?>

                    <?= $form->field($model, 'k3l_active')->dropDownList(MstK3l::statusOptions()) ?>
                </div>
            </div>
        </div>

        <div class="box-footer">
            <div class="form-group">
                <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Batal', ['index'], ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
