<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstCustomer */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-body">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'cust_no')->textInput(['maxlength' => true, 'readonly' => true]) ?>

                <div class="form-group">
                    <?= Html::button('Generate No Customer', ['class' => 'btn btn-warning', 'id' => 'btn-generate']) ?>
                </div>

                <?= $form->field($model, 'telp')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'fax')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'cp_name')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box">
            <div class="box-body">
                <?= $form->field($model, 'cp_phone')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'cp_email')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'npwp')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>

                <?= $form->field($model, 'aktif')->checkbox() ?>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success pull-right']) ?>
</div>


<?php ActiveForm::end(); ?>

<?php
$generateUrl = \yii\helpers\Url::to(['mst-customer/generate-cust-no']);
$js = <<<JS
$('#btn-generate').on('click', function() {
    var name = $('#mstcustomer-name').val();
    if (!name) {
        alert('Isi nama buyer dulu');
        return;
    }
    $.get('$generateUrl', {name: name}, function(data) {
        $('#mstcustomer-cust_no').val(data.cust_no); // pakai id sesuai input Yii2
    });
});
JS;
$this->registerJs($js);
?>