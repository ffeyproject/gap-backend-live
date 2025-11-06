<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="kurangi-qty-form">
    <?php $form = ActiveForm::begin([
        'action' => ['kurangi-qty', 'id' => $model->id],
        'options' => ['data-pjax' => false],
    ]); ?>

    <div class="form-group">
        <label>Jumlah Batch yang akan dikurangi</label>
        <input type="number" name="qty_kurang" class="form-control" min="1" required>
    </div>

    <div class="form-group mt-2">
        <label>Alasan Pengurangan Qty</label>
        <textarea name="alasan" class="form-control" rows="2" placeholder="Tuliskan alasan pengurangan qty..."
            required></textarea>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Kurangi', ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>