<?php
use common\models\ar\TrnScGreige;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnScGreige */
/* @var $wo common\models\ar\TrnWo */
?>

<div class="trn-sc-greige-form">
    <?php $form = ActiveForm::begin([
        'id' => 'TrnScGreigeForm',
        'options' => ['onsubmit' => 'return false;']
    ]); ?>

    <?= $form->field($model, 'lebar_kain')->dropDownList(TrnScGreige::lebarKainOptions()) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'onclick' => 'submitUbahLebarKain(event)']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script>
function submitUbahLebarKain(event) {
    event.preventDefault();
    var form = $('#TrnScGreigeForm');
    $.ajax({
        url: '<?= \yii\helpers\Url::to(['ubah-lebar-kain', 'id' => $wo->id]) ?>',
        type: 'post',
        data: form.serialize(),
        success: function (response) {
            if (response.success) {
                $('#trnWoModal').modal('hide');
                location.reload();
            } else {
                if (response.validation) {
                    $.each(response.validation, function (key, val) {
                        form.yiiActiveForm('updateAttribute', key, val);
                    });
                }
            }
        }
    });
}
</script>
