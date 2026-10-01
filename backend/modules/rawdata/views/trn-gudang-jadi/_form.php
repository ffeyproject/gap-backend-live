<?php

use common\models\ar\MstGreigeGroup;
use kartik\widgets\Select2;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnGudangJadi */
/* @var $form ActiveForm */
?>

<div class="trn-gudang-jadi-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
            <div class="box">
                <div class="box-body">
                    <?= $form->field($model, 'wo_id')->textInput() ?>

                    <?= $form->field($model, 'source_ref')->textInput(['maxlength' => true]) ?>

                    <?=$form->field($model, 'status')->widget(Select2::classname(), [
                        'data' => $model::statusOptions(),
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])?>

                    <?= $form->field($model, 'no_memo_repair')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'no_memo_ganti_greige')->textInput(['maxlength' => true]) ?>


                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6"><?= $form->field($model, 'qty')->textInput() ?></div>

                        <div class="col-md-6">
                            <?=$form->field($model, 'unit')->widget(Select2::classname(), [
                                'data' => MstGreigeGroup::unitOptions(),
                                'options' => ['placeholder' => 'Pilih ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])?>
                        </div>
                    </div>

                    <?=$form->field($model, 'jenis_gudang')->widget(Select2::classname(), [
                        'data' => $model::jenisGudangOptions(),
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])?>

                    <?=$form->field($model, 'source')->widget(Select2::classname(), [
                        'data' => $model::sourceOptions(),
                        'options' => ['placeholder' => 'Pilih ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])?>

                    <?= $form->field($model, 'date')->textInput() ?>

                    <?= $form->field($model, 'color')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box">
                <div class="box-body">
                    <?= $form->field($model, 'id_from')->textInput(['type' => 'number'])->label('ID Inspecting Item (id_from)') ?>

                    <?=$form->field($model, 'trans_from')->widget(Select2::classname(), [
                        'data' => ['INS' => 'INS (Inspecting)', 'MKL' => 'MKL (Inspecting Makloon BJ)'],
                        'options' => ['placeholder' => 'Pilih Tipe ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label('Tipe Inspecting (trans_from)')?>

                    <?= $form->field($model, 'qr_code')->textInput(['maxlength' => true])->label('QR Code') ?>

                    <?= $form->field($model, 'locs_code')->textInput(['maxlength' => true])->label('Location (locs_code)') ?>

                    <?= $form->field($model, 'note')->textarea(['rows' => 4]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$urlGetInspecting = \yii\helpers\Url::to(['get-inspecting-info']);
$js = <<<JS
function fetchInspectingQr() {
    var idFrom = $('#trngudangjadi-id_from').val();
    var transFrom = $('#trngudangjadi-trans_from').val() || 'INS';
    
    if (!idFrom || parseInt(idFrom) <= 0) {
        return;
    }
    
    $.ajax({
        url: '{$urlGetInspecting}',
        type: 'GET',
        data: {
            id: idFrom,
            trans_from: transFrom
        },
        dataType: 'json',
        success: function(res) {
            if (res.success && res.qr_code) {
                $('#trngudangjadi-qr_code').val(res.qr_code);
            } else {
                var prefix = transFrom === 'MKL' ? 'MKL' : 'INS';
                if (!$('#trngudangjadi-qr_code').val()) {
                    $('#trngudangjadi-qr_code').val(prefix + '-' + idFrom);
                }
            }
        },
        error: function() {
            var prefix = transFrom === 'MKL' ? 'MKL' : 'INS';
            if (!$('#trngudangjadi-qr_code').val()) {
                $('#trngudangjadi-qr_code').val(prefix + '-' + idFrom);
            }
        }
    });
}

$('#trngudangjadi-id_from').on('change blur', function() {
    fetchInspectingQr();
});

$('#trngudangjadi-trans_from').on('change', function() {
    if ($('#trngudangjadi-id_from').val()) {
        fetchInspectingQr();
    }
});
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>
