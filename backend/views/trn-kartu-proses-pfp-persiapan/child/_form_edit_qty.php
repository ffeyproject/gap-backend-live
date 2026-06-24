<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesPfpItem */
/* @var $stocks yii\data\ActiveDataProvider */
/* @var $searchModel common\models\search\TrnStockGreigeSearch */
?>

<div class="edit-qty-form">
    <?php $form = ActiveForm::begin([
        'id' => 'edit-qty-form',
        'action' => ['trn-kartu-proses-pfp-item/edit-qty', 'id' => $model->id], // ✅ arahkan ke controller PFP
        'method' => 'post',
    ]); ?>

    <!-- hidden input untuk relasi induk -->
    <?= Html::activeHiddenInput($model, 'kartu_process_id') ?>

    <!-- hidden input untuk stock_id -->
    <?= Html::activeHiddenInput($model, 'stock_id', ['id' => 'stock-id']) ?>

    <?= $form->field($model, 'panjang_m')->textInput([
        'type' => 'number',
        'step' => '0.01',
        'min' => 0,
        'id' => 'panjang-m',
        'readonly' => true,
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
        <?= Html::button('Batal', [
            'class' => 'btn btn-secondary',
            'data-bs-dismiss' => 'modal'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<hr>

<h5>Pilih Stock Greige</h5>

<?php Pjax::begin(['id' => 'stocks-pjax', 'timeout' => 5000]); ?>

<?= GridView::widget([
    'dataProvider' => $stocks,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'id',
        [
            'attribute' => 'greigeNamaKain',
            'label' => 'Nama Kain',
            'value' => function($stock){
                return $stock->greigeNamaKain;
            },
            'filter' => Html::activeTextInput($searchModel, 'greigeNamaKain', ['class'=>'form-control']),
        ],
        [
            'attribute' => 'no_document',
            'filter' => Html::activeTextInput($searchModel, 'no_document', ['class'=>'form-control']),
        ],
        [
            'attribute' => 'lot_lusi',
            'filter' => Html::activeTextInput($searchModel, 'lot_lusi', ['class'=>'form-control']),
        ],
        [
            'attribute' => 'lot_pakan',
            'filter' => Html::activeTextInput($searchModel, 'lot_pakan', ['class'=>'form-control']),
        ],
        [
            'attribute' => 'panjang_m',
            'filter' => Html::activeTextInput($searchModel, 'panjang_m', ['class'=>'form-control']),
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
// ketika tombol "Pilih" diklik
$(document).on('click', '.pilih-stock', function(e) {
    e.preventDefault();
    var stockId = $(this).data('id');
    var no_document = $(this).data('no_document');
    var lot_lusi = $(this).data('lot_lusi');
    var lot_pakan = $(this).data('lot_pakan');
    var panjang = $(this).data('panjang');

    // isi hidden input stock_id dan qty
    $('#stock-id').val(stockId);
    $('#panjang-m').val(panjang);

    alert('Stock dipilih: ' + stockId + '\\nNo Document: ' + no_document + '\\nLot Lusi: ' + lot_lusi + '\\nLot Pakan: ' + lot_pakan + '\\nPanjang: ' + panjang);
});

// cegah Enter submit otomatis di input panjang
$('#panjang-m').on('keypress', function(e) {
    if (e.which === 13) {
        e.preventDefault();
        return false;
    }
});
JS;
$this->registerJs($js);
?>