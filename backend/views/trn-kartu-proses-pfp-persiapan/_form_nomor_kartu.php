<?php
/** @var $model \common\models\ar\TrnKartuProsesPfp */

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin([
    'id' => 'form-edit-nomor-kartu',
    'action' => ['trn-kartu-proses-pfp/edit-nomor-kartu', 'id' => $model->id],
]);
?>

<?= $form->field($model, 'nomor_kartu')->textInput() ?>

<div class="form-group">
    <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
// submit form via AJAX
$('#form-edit-nomor-kartu').off('beforeSubmit').on('beforeSubmit', function(e) {
    e.preventDefault();
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        dataType: 'json',
        data: form.serialize(),
        success: function (response) {
            if (response.success) {
                // reload halaman utama
                location.reload();
            } else if (response.html) {
                // replace isi modal body dengan HTML baru (error)
                $('#kartuProsesPfpModalNomor .modal-body').html(response.html);
            } else {
                alert('Validasi gagal.');
            }
        },
        error: function () {
            alert('Terjadi kesalahan saat mengirim data.');
        }
    });
    return false; // blok submit default
});
JS;
$this->registerJs($js);
?>