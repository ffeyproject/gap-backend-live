<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnGudangJadiOpnamePcs;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model backend\modules\rawdata\models\TrnGudangJadiOpnamePcs */
/* @var $form kartik\widgets\ActiveForm */

// Jika ada relasi gudang jadi dan unit belum sesuai numeric, ikuti satuan gudang jadi
if ($model->gudangJadi && !empty($model->gudangJadi->unit)) {
    if (empty($model->unit) || !is_numeric($model->unit)) {
        $model->unit = (string)$model->gudangJadi->unit;
    }
}

// Unit options: mengutamakan standar MstGreigeGroup
$unitOptions = [
    '1' => 'Yard',
    '2' => 'Meter',
    '3' => 'Pcs',
    '4' => 'Kilogram',
    'YARDS' => 'Yard (Yards)',
    'YARD' => 'Yard (Yard)',
    'METER' => 'Meter (Meter)',
    'MTR' => 'Meter (Mtr)',
    'PCS' => 'Pcs',
    'KG' => 'Kilogram (Kg)',
];

$getGudangJadiUrl = Url::to(['get-gudang-jadi']);
?>

<div class="trn-gudang-jadi-opname-pcs-form">

    <?php $form = ActiveForm::begin(['id' => 'TrnGudangJadiOpnamePcsForm']); ?>

    <div class="box">
        <div class="box-body">
            <!-- Info Box Gudang Jadi Terkait -->
            <div id="gj-info-box" class="alert alert-info" style="<?= $model->gudangJadi ? '' : 'display: none;' ?> padding: 10px 15px; margin-bottom: 15px;">
                <i class="fa fa-info-circle"></i> <strong>Data Master Gudang Jadi Terkait:</strong>
                <span id="gj-info-text">
                    <?php if ($model->gudangJadi): ?>
                        Nomor WO: <strong><?= ($model->gudangJadi->wo) ? Html::encode($model->gudangJadi->wo->no) : '-' ?></strong> | 
                        Buyer: <strong><?= ($model->gudangJadi->wo && $model->gudangJadi->wo->mo && $model->gudangJadi->wo->mo->scGreige && $model->gudangJadi->wo->mo->scGreige->sc && $model->gudangJadi->wo->mo->scGreige->sc->cust) ? Html::encode($model->gudangJadi->wo->mo->scGreige->sc->cust->name) : '-' ?></strong> | 
                        Color: <strong><?= Html::encode($model->gudangJadi->color ?: '-') ?></strong> | 
                        Satuan Master: <span class="badge bg-green"><?= isset(MstGreigeGroup::unitOptions()[$model->gudangJadi->unit]) ? MstGreigeGroup::unitOptions()[$model->gudangJadi->unit] : $model->gudangJadi->unit ?></span>
                    <?php endif; ?>
                </span>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'id_trn_gudang_jadi', [
                        'addon' => [
                            'append' => [
                                'content' => '<button type="button" class="btn btn-default" id="btn-sync-gj" title="Cek & Ikuti Data Gudang Jadi"><i class="fa fa-refresh"></i> Cek</button>',
                                'asButton' => true
                            ]
                        ]
                    ])->textInput(['placeholder' => 'Masukkan ID Trn Gudang Jadi...']) ?>

                    <?= $form->field($model, 'opname_code')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'qr_code')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'qr_code_desc')->textarea(['rows' => 3]) ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'qty')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'unit')->widget(Select2::classname(), [
                                'data' => $unitOptions,
                                'options' => [
                                    'id' => 'select2-unit-field',
                                    'placeholder' => 'Pilih Satuan...',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'tags' => true,
                                ],
                            ])->hint('<small class="text-muted"><i class="fa fa-magic"></i> Satuan otomatis mengikuti data Gudang Jadi saat ID diisi.</small>') ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'grade')->widget(Select2::classname(), [
                        'data' => [
                            1 => 'Grade A',
                            2 => 'Grade B',
                            3 => 'Grade C',
                            4 => 'Grade D',
                            5 => 'Grade E',
                            6 => 'Grade F',
                            7 => 'Grade G',
                        ],
                        'options' => ['placeholder' => 'Pilih Grade...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]) ?>

                    <?= $form->field($model, 'locs_code')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'join_piece')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'status')->widget(Select2::classname(), [
                        'data' => TrnGudangJadiOpnamePcs::statusOptions(),
                        'options' => ['placeholder' => 'Pilih Status...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]) ?>

                    <?= $form->field($model, 'remark')->textarea(['rows' => 3]) ?>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <?= Html::submitButton('<i class="fa fa-save"></i> Simpan', ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fa fa-arrow-left"></i> Batal', ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
function fetchGudangJadiInfo(idGudangJadi) {
    if (!idGudangJadi || idGudangJadi.trim() === '') {
        $('#gj-info-box').slideUp();
        return;
    }

    $.ajax({
        url: '{$getGudangJadiUrl}',
        type: 'GET',
        data: { id: idGudangJadi },
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                // Otomatis set satuan / unit mengikuti Gudang Jadi
                if (res.unit) {
                    $('#select2-unit-field').val(res.unit).trigger('change');
                }

                // Tampilkan info box
                var infoHtml = 'Nomor WO: <strong>' + (res.wo_no || '-') + '</strong> | ' +
                               'Buyer: <strong>' + (res.buyer || '-') + '</strong> | ' +
                               'Color: <strong>' + (res.color || '-') + '</strong> | ' +
                               'Satuan Master: <span class="badge bg-green">' + (res.unit_name || res.unit) + '</span>';
                $('#gj-info-text').html(infoHtml);
                $('#gj-info-box').slideDown();
            } else {
                $('#gj-info-text').html('<span class="text-warning"><i class="fa fa-warning"></i> ' + (res.message || 'Data Gudang Jadi tidak ditemukan') + '</span>');
                $('#gj-info-box').slideDown();
            }
        },
        error: function() {
            console.error('Gagal mengambil data Gudang Jadi');
        }
    });
}

// Trigger saat input ID Gudang Jadi berubah atau tombol Refresh diklik
$('#trngudangjadiopnamepcs-id_trn_gudang_jadi').on('change blur', function() {
    fetchGudangJadiInfo($(this).val());
});

$('#btn-sync-gj').on('click', function(e) {
    e.preventDefault();
    fetchGudangJadiInfo($('#trngudangjadiopnamepcs-id_trn_gudang_jadi').val());
});
JS;

$this->registerJs($js, View::POS_READY);
?>
