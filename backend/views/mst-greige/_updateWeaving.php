<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\ar\MstGreige;

/* @var $model MstGreige */
?>

<div class="mst-greige-weaving-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-weaving-'.$model->id,
        'action' => ['update-weaving', 'id' => $model->id],
        'enableAjaxValidation' => false,
    ]); ?>

    <?= $form->field($model, 'status_weaving')->dropDownList(MstGreige::getStatusWeavingList(), ['prompt' => 'Pilih Status']) ?>

    <div class="form-group">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
$(function() {
    $('form#form-weaving-<?= $model->id ?>').on('beforeSubmit', function(e) {
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            success: function(response) {
                if ($.trim(response) === 'success') {
                    $('#modal-weaving').modal('hide'); // tutup modal
                    // reload GridView via PJAX
                    $.pjax.reload({
                        container: '#mst-greige-pjax',
                        async: false, // tunggu reload selesai
                        timeout: 1000
                    });
                    // optional: alert setelah reload
                    // alert('Status Weaving berhasil diperbarui!');
                } else {
                    alert('Gagal menyimpan data. Mohon cek input.');
                }
            },
            error: function() {
                alert('Terjadi kesalahan server.');
            }
        });
        return false; // stop default submit
    });
});
</script>