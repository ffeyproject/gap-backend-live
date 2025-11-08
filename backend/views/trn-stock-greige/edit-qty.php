<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var \common\models\ar\TrnStockGreige[] $models */
?>

<div class="modal-header">
    <h5 class="modal-title">Edit Quantity Stock & Opname</h5>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>

<div class="modal-body">
    <?php $form = ActiveForm::begin([
        'id' => 'form-edit-qty',
        'action' => ['edit-qty', 'ids' => implode(',', array_column($models, 'id'))],
        'enableAjaxValidation' => false,
        'method' => 'post',
    ]); ?>

    <table class="table table-bordered table-sm">
        <thead class="thead-light">
            <tr>
                <th style="width:60px;">No</th>
                <th>Nama Kain</th>
                <th>Grade</th>
                <th>Panjang Lama</th>
                <th>Panjang Baru</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; foreach ($models as $model): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= Html::encode($model->greige->nama_kain ?? '-') ?></td>
                <td><?= Html::encode($model::gradeOptions()[$model->grade] ?? '-') ?></td>
                <td><?= Yii::$app->formatter->asDecimal($model->panjang_m) ?></td>
                <td>
                    <?= Html::input('number', "TrnStockGreige[{$model->id}][panjang_m_baru]", $model->panjang_m, [
                        'class' => 'form-control form-control-sm text-right',
                        'min' => 0,
                        'step' => '0.01',
                    ]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php ActiveForm::end(); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    <button type="button" id="btn-save-edit-qty" class="btn btn-primary">Save Changes</button>
</div>

<?php
$js = <<<JS
$('#btn-save-edit-qty').on('click', function(e){
    e.preventDefault();
    var form = $('#form-edit-qty');
    var btn = $(this);
    btn.prop('disabled', true).text('Saving...');

    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        dataType: 'json',
        data: form.serialize(),
        success: function(res){
            if (res.success) {
                $('#modal-edit-qty').modal('hide');
                $.pjax.reload({container:'#StockGreigeGrid'});
            } else {
                alert(res.message || 'Terjadi kesalahan.');
            }
        },
        error: function(xhr){
            alert('Terjadi kesalahan server: ' + xhr.statusText);
        },
        complete: function(){
            btn.prop('disabled', false).text('Save Changes');
        }
    });
});
JS;
$this->registerJs($js);
?>