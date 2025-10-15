<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model common\models\ar\TrnWoColor */
?>

<div class="reduce-qty-form">
    <?php $form = ActiveForm::begin([
        'action' => ['trn-wo-color/reduce-qty', 'id' => $model->id],
        'method' => 'post',
    ]); ?>

    <h4><strong>Kurangi Qty Color</strong></h4>
    <table class="table table-bordered table-striped">
        <tr>
            <th style="width: 30%;">Color</th>
            <td><?= Html::encode($model->moColor->color) ?></td>
        </tr>
        <tr>
            <th>Qty Batch Saat Ini</th>
            <td><?= number_format($model->qty, 2) ?> Batch</td>
        </tr>
        <tr>
            <th>Greige</th>
            <td><?= number_format($model->qtyBatchToMeter, 2) ?></td>
        </tr>
    </table>

    <div class="form-group">
        <label><strong>Masukkan jumlah yang akan dikurangi (Batch)</strong></label>
        <?= Html::input('number', 'reduce_qty', '', [
            'class' => 'form-control',
            'min' => 1,
            'max' => $model->qty,
            'step' => '0.01',
            'required' => true,
            'placeholder' => 'Masukkan jumlah batch yang ingin dikurangi...'
        ]) ?>
        <!-- <small class="text-muted">
            Pengurangan batch otomatis menyesuaikan total greige (meter).
        </small> -->
    </div>

    <div class="form-group" style="margin-top: 15px;">
        <?= Html::submitButton('Kurangi Qty', ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>