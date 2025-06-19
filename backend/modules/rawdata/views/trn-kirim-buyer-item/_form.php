<?php

use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\ar\TrnGudangJadi;
use backend\modules\rawdata\models\TrnKirimBuyer;

/** @var yii\web\View $this */
/** @var backend\modules\rawdata\models\TrnKirimBuyerItem $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="trn-kirim-buyer-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'kirim_buyer_id',
                'value' => $model->kirimBuyer ? $model->kirimBuyer->id : null,
            ],
            [
                'attribute' => 'stock_id',
                'value' => $model->stock ? $model->stock->id : null,
            ],
            [
                'label' => 'Nomor WO',
                'value' => $model->stock && $model->stock->wo ? $model->stock->wo->no : null,
            ],
            'qty:decimal',
            [
                'attribute' => 'no_bal',
                'format' => 'raw',
                'value' => Html::activeTextInput($model, 'no_bal', ['class' => 'form-control']),
            ],
            [
                'attribute' => 'bal_id',
                'format' => 'raw',
                'value' => Html::activeTextInput($model, 'bal_id', ['class' => 'form-control']),
            ],
            [
                'attribute' => 'note',
                'format' => 'raw',
                'value' => Html::activeTextarea($model, 'note', ['class' => 'form-control']),
            ],
        ],
    ]) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Kembali', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>