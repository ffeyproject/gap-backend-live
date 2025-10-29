<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model \common\models\ar\TrnKartuProsesDyeingItem */
?>

<div class="delete-item-form">
    <p>
        Anda akan menghapus <strong>Roll #<?= Html::encode($model->id) ?></strong>
        dengan Qty <strong><?= Yii::$app->formatter->asDecimal($model->panjang_m, 2) ?> m</strong><br>
        dari <strong>Kartu Proses NK: <?= Html::encode($model->kartuProcess->nomor_kartu ?? '-') ?></strong>.
    </p>

    <?php $form = ActiveForm::begin([
        'id' => 'form-delete-item',
        'action' => ['/trn-kartu-proses-dyeing-item/delete-item', 'id' => $model->id],
        'method' => 'post',
    ]); ?>

    <?= $form->field(new \yii\base\DynamicModel(['alasan']), 'alasan')
        ->textarea([
            'rows' => 3,
            'placeholder' => 'Masukkan alasan penghapusan roll ini...',
            'required' => true,
        ])
        ->label('Alasan Hapus Roll') ?>

    <div class="form-group">
        <?= Html::submitButton('Hapus Sekarang', ['class' => 'btn btn-danger']) ?>
        <?= Html::button('Batal', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>