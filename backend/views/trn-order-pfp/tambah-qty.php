<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="tambah-qty-form">
    <?php $form = ActiveForm::begin([
        'action' => ['tambah-qty', 'id' => $model->id],
        'options' => ['data-pjax' => false],
    ]); ?>

    <div class="form-group">
        <label>Jumlah Batch yang akan ditambah</label>
        <input type="number" name="qty_tambah" class="form-control" min="1" required>
    </div>

    <div class="form-group mt-2">
        <label>Alasan Penambahan Qty</label>
        <textarea name="alasan" class="form-control" rows="2" placeholder="Tuliskan alasan penambahan qty..."
            required></textarea>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Tambah', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>