<?php
use common\models\ar\TrnStockGreige;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesDyeingItem */
/* @var $searchHint string */
/* @var $searchModel common\models\search\TrnStockGreigeSearch */
/* @var $stocks yii\data\ActiveDataProvider */
?>

<div class="kartu-proses-dyeing-item-form">
    <?php $form = ActiveForm::begin([
        'id' => 'KartuProsesItemForm',
        'action' => ['trn-kartu-proses-dyeing-item/add-create', 'processId' => $model->kartu_process_id],
        'method' => 'post',
    ]); ?>

    <?= Html::activeHiddenInput($model, 'kartu_process_id') ?>
    <?= Html::activeHiddenInput($model, 'stock_id', ['id' => 'stock-id']) ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'tube')->widget(Select2::classname(), [
                'data' => $model::tubeOptions(),
                'options' => ['placeholder' => 'Pilih ...'],
                'pluginOptions' => ['allowClear' => true],
            ]) ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'mesin')->textInput([
                'maxlength' => true,
                'required' => true,
                'placeholder' => 'Isi nama atau nomor mesin'
            ]) ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'date')->textInput(['type' => 'date']) ?>
        </div>
    </div>

    <?= $form->field($model, 'panjang_m')->textInput([
        'id' => 'panjang-m',
        'type' => 'number',
        'readonly' => true,
        'step' => '0.01',
        'min' => 0,
        'required' => true,
        'placeholder' => 'Pilih Qty dari daftar tabel stock greige',
    ])->label('Qty') ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 4]) ?>

    <?= Html::label('Alasan Penambahan Roll', 'alasan', ['class' => 'control-label']) ?>
    <?= Html::label('Alasan Penambahan Roll', 'alasan', ['class' => 'control-label']) ?>
    <?= Html::textarea('alasan', '', [
        'id' => 'alasan',
        'class' => 'form-control',
        'rows' => 3,
        'placeholder' => 'Tuliskan alasan penambahan roll (misalnya: tambahan batch untuk warna yang sama)',
        'required' => true,
    ]) ?>
    <div class="help-block text-danger" id="alasan-error" style="display:none;">
        Alasan penambahan roll harus diisi.
    </div>


    <div class="form-group">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
        <?= Html::button('Batal', ['class' => 'btn btn-secondary', 'data-dismiss' => 'modal']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<hr>
<h4>Pilih Stock Greige</h4>
<p><i><?= Html::encode($searchHint) ?></i></p>

<?php Pjax::begin(['id' => 'stocks-pjax', 'timeout' => 6000]); ?>

<?= GridView::widget([
    'dataProvider' => $stocks,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'id',
        'no_document',
        'lot_lusi',
        'lot_pakan',
        [
            'attribute' => 'greigeNamaKain',
            'label' => 'Nama Kain',
            'value' => function($stock) {
                return $stock->greigeNamaKain;
            },
        ],
        [
            'attribute' => 'panjang_m',
            'format' => ['decimal', 2],
            'label' => 'Qty (M)',
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{pilih}',
            'buttons' => [
                'pilih' => function ($url, $stock) {
                    return Html::a('Pilih', '#', [
                        'class' => 'btn btn-primary btn-sm pilih-stock',
                        'data-id' => $stock->id,
                        'data-panjang' => $stock->panjang_m,
                        'data-no_document' => $stock->no_document,
                        'data-lot_lusi' => $stock->lot_lusi,
                        'data-lot_pakan' => $stock->lot_pakan,
                    ]);
                },
            ],
        ],
    ],
]); ?>

<?php Pjax::end(); ?>

<?php
$js = <<<JS
$(document).on('click', '.pilih-stock', function(e) {
    e.preventDefault();
    var stockId = $(this).data('id');
    var panjang = $(this).data('panjang');
    var noDoc = $(this).data('no_document');

    $('#stock-id').val(stockId);
    $('#panjang-m').val(panjang);

    alert('Stock dipilih:\\nID: ' + stockId + '\\nNo Dokumen: ' + noDoc + '\\nQty: ' + panjang + ' m');
});
JS;
$this->registerJs($js);
?>

<?php
$js = <<<JS
$('#KartuProsesItemForm').on('submit', function(e) {
    var alasan = $('#alasan').val().trim();
    if (alasan === '') {
        e.preventDefault();
        $('#alasan-error').show();
        $('#alasan').focus();
        return false;
    } else {
        $('#alasan-error').hide();
    }
});
JS;
$this->registerJs($js);
?>