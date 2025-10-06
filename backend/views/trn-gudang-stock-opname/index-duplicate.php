<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

$this->title = 'Data Duplikasi Stock Greige Opname';
$this->params['breadcrumbs'][] = 'Gudang Stock Opname > ' . $this->title;

?>
<div class="trn-stock-greige-opname-duplicate-index">

    <?php
    $form = ActiveForm::begin([
        'id' => 'bulk-duplicate-form',
        'action' => ['duplicate-bulk'],
        'method' => 'post',
    ]);
    ?>

    <?= Html::hiddenInput('ids', '', ['id' => 'bulk-duplicate-ids']) ?>

    <p>
        <?= Html::button('<i class="glyphicon glyphicon-plus"></i> Migrasi Ke Stock', [
            'class' => 'btn btn-info',
            'id' => 'btn-duplicate-bulk'
        ]) ?>

        <?= Html::button('<i class="glyphicon glyphicon-log-out"></i> Keluar Stock Opname', [
            'class' => 'btn btn-danger',
            'id' => 'btn-keluar-bulk'
        ]) ?>
    </p>

    <?php ActiveForm::end(); ?>

    <?php Pjax::begin(['id' => 'StockGreigeOpnameGrid-pjax']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'StockGreigeOpnameGrid',
        'responsiveWrap' => false,
        'pjax' => true,
        'panel' => [
            'type' => 'primary',
            'before'=>Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index-duplicate'], ['class' => 'btn btn-default']),
                ['class'=>'btn-group', 'role'=>'group']
            ),
            'after'=>false,
        ],
        'showPageSummary'=>true,
        'columns' => [
            [
                'class' => 'kartik\grid\CheckboxColumn',
                'checkboxOptions' => function ($model) {
                    return [
                        'value'    => $model->id,
                        'disabled' => $model->status != \common\models\ar\TrnStockGreige::STATUS_VALID, // disable kalau bukan VALID
                    ];
                }
            ],
            'id',
            'stock_greige_id',
            [
                'attribute' => 'dateRange',
                'label' => 'Tanggal',
                'value' => 'date',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        'locale'=>[
                            'format'=>'Y-m-d',
                            'separator'=>' to ',
                        ]
                    ]
                ],
            ],
            'no_document',
            'no_lapak',
            [
                'label'=>'Nama Kain',
                'attribute'=>'greigeNamaKain',
                'value'=>function($data){
                    return $data->greigeNamaKain;
                }
            ],
            [
                'attribute'=>'grade',
                'value'=>function($data){
                    return $data::gradeOptions()[$data->grade] ?? $data->grade;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\TrnStockGreige::gradeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            'lot_lusi',
            'lot_pakan',
            'no_set_lusi',
            [
                'attribute'=>'panjang_m',
                'format'=>'decimal',
                'pageSummary'=>true
            ],
            [
                'attribute'=>'asal_greige',
                'value'=>function($data){
                    return $data::asalGreigeOptions()[$data->asal_greige] ?? $data->asal_greige;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\TrnStockGreige::asalGreigeOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            [
                'attribute'=>'status_tsd',
                'value'=>function($data){
                    /* @var $data TrnGudangInspect*/
                    return $data::tsdOptions()[$data->status_tsd];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\TrnGudangStockOpname::tsdOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'attribute'=>'status',
                'value'=>function($data){
                    return $data::statusOptions()[$data->status] ?? $data->status;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ar\TrnStockGreige::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            'note:ntext',
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<?php
// $js = <<<JS
// $('#btn-duplicate-bulk').on('click', function() {
//     var keys = $('#StockGreigeOpnameGrid').yiiGridView('getSelectedRows');
//     if (keys.length === 0) {
//         alert('Pilih minimal 1 data untuk diduplikasi.');
//         return;
//     }
//     if (confirm('Yakin ingin menduplikasi ' + keys.length + ' data ke TrnStockGreige?')) {
//         $('#bulk-duplicate-ids').val(keys.join(',')); 
//         $('#bulk-duplicate-form').submit(); 
//     }
// });
// JS;
// $this->registerJs($js);

$js = <<<JS
$('#btn-duplicate-bulk').on('click', function() {
    var keys = $('#StockGreigeOpnameGrid').yiiGridView('getSelectedRows');
    if (keys.length === 0) {
        alert('Pilih minimal 1 data untuk diduplikasi.');
        return;
    }
    if (confirm('Yakin ingin menduplikasi ' + keys.length + ' data ke TrnStockGreige?')) {
        $('#bulk-duplicate-ids').val(keys.join(',')); 
        $('#bulk-duplicate-form').attr('action', 'duplicate-bulk').submit(); 
    }
});

$('#btn-keluar-bulk').on('click', function() {
    var keys = $('#StockGreigeOpnameGrid').yiiGridView('getSelectedRows');
    if (keys.length === 0) {
        alert('Pilih minimal 1 data untuk keluar stock opname.');
        return;
    }
    if (confirm('Yakin ingin mengeluarkan ' + keys.length + ' data dari stock opname?')) {
        $('#bulk-duplicate-ids').val(keys.join(','));
        $('#bulk-duplicate-form').attr('action', 'keluar-bulk').submit();
    }
});
JS;
$this->registerJs($js);
?>