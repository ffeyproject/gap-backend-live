<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesDyeing */
/* @var $items common\models\ar\TrnKartuProsesDyeingItem[] */
/* @var $wos common\models\ar\TrnWo[] */
/* @var $suggestedNk string */

$this->title = 'Gabung Kartu Proses: ' . $model->nomor_kartu;
$this->params['breadcrumbs'][] = ['label' => 'Processing Dyeing', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->no, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Gabung Kartu';

$urlAjaxKartu = Url::to(['ajax-search-kartu-by-motif', 'mo_id' => $model->mo_id, 'id_exclude' => $model->id]);
$urlAjaxDetails = Url::to(['ajax-get-kartu-details']);
$urlAjaxColors = Url::to(['ajax-get-wo-colors']);

$woOptions = [];
foreach ($wos as $wo) {
    $woOptions[$wo->id] = $wo->no;
}
?>

<div class="trn-kartu-proses-dyeing-gabung">

    <div class="box box-primary">
        <div class="box-body">
            <p class="text-info">Silakan cari kartu proses kedua (Kartu 2) yang memiliki motif sama dengan Kartu 1 untuk digabungkan.</p>

            <?php $form = ActiveForm::begin(['id' => 'gabung-form']); ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading"><strong>Kartu 1 (Induk)</strong></div>
                        <div class="panel-body">
                            <table class="table table-bordered">
                                <tr><th>Nomor WO</th><td><?= Html::encode($model->wo ? $model->wo->no : '') ?></td></tr>
                                <tr><th>Nomor Kartu</th><td><?= Html::encode($model->nomor_kartu) ?></td></tr>
                                <tr><th>Motif</th><td><?= Html::encode($model->mo->design) ?></td></tr>
                            </table>
                            <h4>Pilih Item Roll dari Kartu 1:</h4>
                            <table class="table table-condensed table-striped">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="check-all-1"></th>
                                        <th>Tube</th>
                                        <th>Panjang (M)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><input type="checkbox" class="chk-item-1" name="selected_items[]" value="<?= $item->id ?>"></td>
                                        <td><?= $item->tube == 1 ? 'Kiri' : 'Kanan' ?></td>
                                        <td><?= Yii::$app->formatter->asDecimal($item->stock->panjang_m) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading"><strong>Kartu 2 (Tujuan Gabung)</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>Cari Kartu 2 (Motif Sama):</label>
                                <?= Select2::widget([
                                    'name' => 'kartu2_id',
                                    'id' => 'kartu2_id',
                                    'options' => ['placeholder' => 'Ketik nomor kartu...'],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'minimumInputLength' => 1,
                                        'language' => [
                                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                                        ],
                                        'ajax' => [
                                            'url' => $urlAjaxKartu,
                                            'dataType' => 'json',
                                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                        ],
                                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                        'templateResult' => new JsExpression('function(city) { return city.text; }'),
                                        'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                                    ],
                                    'pluginEvents' => [
                                        "change" => "function() { fetchKartu2Details($(this).val()); }",
                                    ]
                                ]) ?>
                            </div>

                            <div id="kartu2-details" style="display:none;">
                                <h4>Pilih Item Roll dari Kartu 2:</h4>
                                <table class="table table-condensed table-striped">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="check-all-2"></th>
                                            <th>Tube</th>
                                            <th>Panjang (M)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="kartu2-items-body">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>
            <h4>Data Kartu Gabungan (Baru)</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group required">
                        <label>Nomor Kartu (Baru)</label>
                        <?= Html::textInput('new_nomor_kartu', $suggestedNk, ['class' => 'form-control', 'required' => true, 'placeholder' => 'Contoh: '.$model->nomor_kartu.'G1']) ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group required">
                        <label>WO</label>
                        <?= Select2::widget([
                            'name' => 'new_wo_id',
                            'id' => 'new_wo_id',
                            'data' => $woOptions,
                            'options' => ['placeholder' => 'Pilih WO...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                            'pluginEvents' => [
                                "change" => "function() { fetchWoColors($(this).val()); }",
                            ]
                        ]) ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group required">
                        <label>Warna</label>
                        <select name="new_wo_color_id" id="new_wo_color_id" class="form-control" required>
                            <option value="">Pilih WO Dahulu</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group text-right">
                <?= Html::a('Batal', ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
                <?= Html::submitButton('Simpan Gabung Kartu', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$js = <<<JS
$('#check-all-1').click(function() {
    $('.chk-item-1').prop('checked', this.checked);
});

$('#check-all-2').click(function() {
    $('.chk-item-2').prop('checked', this.checked);
});

function fetchKartu2Details(id) {
    if (!id) {
        $('#kartu2-details').hide();
        $('#kartu2-items-body').empty();
        return;
    }
    
    $.ajax({
        url: '{$urlAjaxDetails}',
        data: {id: id},
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                var tbody = $('#kartu2-items-body');
                tbody.empty();
                if (res.items.length === 0) {
                    tbody.append('<tr><td colspan="3">Tidak ada item.</td></tr>');
                } else {
                    $.each(res.items, function(i, item) {
                        tbody.append('<tr>'+
                            '<td><input type="checkbox" class="chk-item-2" name="selected_items[]" value="'+item.id+'"></td>'+
                            '<td>'+item.tube+'</td>'+
                            '<td>'+item.panjang_m+'</td>'+
                        '</tr>');
                    });
                }
                $('#kartu2-details').show();
            }
        }
    });
}

function fetchWoColors(woId) {
    var selectColor = $('#new_wo_color_id');
    selectColor.empty().append('<option value="">-- Pilih Warna --</option>');
    if (!woId) return;

    $.ajax({
        url: '{$urlAjaxColors}',
        data: {id: woId},
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                $.each(res.colors, function(i, color) {
                    selectColor.append('<option value="'+color.id+'">'+color.name+'</option>');
                });
            }
        }
    });
}
JS;
$this->registerJs($js);
?>
