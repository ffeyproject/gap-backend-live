<?php
use common\models\ar\{ MstGreigeGroup, TrnGudangJadi, TrnScGreige, TrnStockGreige };
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\grid\GridView;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnGudangJadiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

\backend\assets\DataTablesAsset::register($this);

$this->title = 'Laporan Stock';
$this->params['breadcrumbs'][] = $this->title;
// var_dump($dataProvider);
// die;
?>

<div class="trn-laporan-stock-index">
    <div class="box">
        <div class="box-body">
            <?php
                $form = ActiveForm::begin(['method' => 'get', 'action' => ['trn-laporan-stock/index']]);
                // Default values are set here
                // $startDate = Yii::$app->request->get('startDate', date('Y-m-d')); // Current date in 'yyyy-mm-dd' format
                // $endDate = Yii::$app->request->get('endDate', date('Y-m-d')); // Current date in 'yyyy-mm-dd' format
                $jenis_gudang = Yii::$app->request->get('jenis_gudang');
                $grade = Yii::$app->request->get('grade');
                $unit = Yii::$app->request->get('unit');
                $status = Yii::$app->request->get('status');
                // $source = Yii::$app->request->get('source', 1);
                $motif = Yii::$app->request->get('motif', '');
                $color = Yii::$app->request->get('color', '');
                // $no_wo = Yii::$app->request->get('no_wo', '');
                $limit = Yii::$app->request->get('limit', 50);
            ?>
            <div class="form-row">
                <div class="form-group col-md-8">
                    <?php
                            echo '<label>Pilih Motif</label>';
                            echo Html::textInput('motif', $motif, ['class' => 'form-control', 'placeholder' => 'Select Motif ...']);
                        ?>
                </div>
                <div class="form-group col-md-2">
                    <?php
                            echo '<label>Pilih Color</label>';
                            echo Html::textInput('color', $color, ['class' => 'form-control', 'placeholder' => 'Select Color ...']);
                        ?>
                </div>
                <div class="form-group col-md-2">
                    <?php
                            echo '<label>Limit Data Untuk Ditampilkan</label>';
                            echo Select2::widget([
                                'name' => 'limit',
                                'value' => $limit,
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                                'data' => [50 => 50, 100 => 100, 200 => 200, 500 => 500, 1000 => 1000, count($dataProvider->allModels) => count($dataProvider->allModels)],
                                'options' => ['multiple' => false, 'placeholder' => 'Select limit ...']
                            ]); 
                        ?>
                </div>
                <div class="form-group col-md-3">
                    <?php
                            echo '<label>Pilih Status</label>';
                            echo Select2::widget([
                                'name' => 'status',
                                'value' => $status,
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                                'data' => TrnGudangJadi::statusOptions(),
                                'options' => ['multiple' => false, 'placeholder' => 'Select status ...']
                            ]); 
                        ?>
                </div>
                <div class="form-group col-md-3">
                    <?php
                            echo '<label>Pilih Jenis Gudang</label>';
                            echo Select2::widget([
                                'name' => 'jenis_gudang',
                                'value' => $jenis_gudang,
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                                'data' => TrnGudangJadi::jenisGudangOptions(),
                                'options' => ['multiple' => false, 'placeholder' => 'Select Jenis Gudang ...']
                            ]); 
                        ?>
                </div>
                <div class="form-group col-md-3">
                    <?php
                            echo '<label>Pilih Grade</label>';
                            echo Select2::widget([
                                'name' => 'grade',
                                'value' => $grade,
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                                'data' => TrnStockGreige::gradeOptions(),
                                'options' => ['multiple' => false, 'placeholder' => 'Select Grade ...']
                            ]); 
                        ?>
                </div>
                <div class="form-group col-md-3">
                    <?php
                            echo '<label>Pilih Satuan</label>';
                            echo Select2::widget([
                                'name' => 'unit',
                                'value' => $unit,
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                                'data' => MstGreigeGroup::unitOptions(),
                                'options' => ['multiple' => false, 'placeholder' => 'Select Satuan ...']
                            ]); 
                        ?>
                </div>
                <div class="form-group col-md-12">
                    <?php
                            echo '<div class="form-group">';
                            echo Html::submitButton('Search', ['class' => 'btn btn-primary btn-block']);
                            echo '</div>';
                        ?>
                </div>
            </div>
            <?php 
                ActiveForm::end();
            ?>
        </div>
    </div>
    <div class="box">
        <div class="box-body">
            <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => NULL,
            'id' => 'summaryStock',
            'resizableColumns' => false,
            'responsiveWrap' => false,
            'pjax' => true,
            'toolbar' => [
                '{toggleData}',
                '{export}'
            ],
            'panel' => [
                'type' => 'default',
                'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
                'after'=>false,
            ],
            'showPageSummary'=>true,
            'columns' => [
                ['class' => 'kartik\grid\SerialColumn'],
                [
                    'attribute' => 'mst_greige_group_nama_kain',
                    'label' => 'Motif Sales Contract',
                    'value' => function ($model) {
                        return $model['mst_greige_group_nama_kain'];
                    },
                    'headerOptions' => ['hidden' => false], // Hide the header cell
                    'visible' => true, // Hide the column in the GridView
                ],
                [
                    'attribute' => 'mst_greige_nama_kain',
                    'label' => 'Motif',
                    'value' => function ($model) {
                        return $model['mst_greige_nama_kain'];
                    },
                    'headerOptions' => ['hidden' => false], // Hide the header cell
                    'visible' => true, // Hide the column in the GridView
                ],
                [
                    'attribute' => 'color',
                    'label' => 'Color',
                    'value' => function ($model) {
                        return $model['color'];
                    },
                    'headerOptions' => ['hidden' => false], // Hide the header cell
                    'visible' => true, // Hide the column in the GridView
                ],
                [
                    'attribute' => 'status',
                    'label' => 'Status',
                    'value' => function ($model) {
                        return TrnGudangJadi::statusOptions()[$model['status']];
                    },
                    'headerOptions' => ['hidden' => false], // Hide the header cell
                    'visible' => true, // Hide the column in the GridView
                ],
                [
                    'attribute' => 'jenis_gudang',
                    'label' => 'Jenis Gudang',
                    'value' => function ($model) {
                        return TrnGudangJadi::jenisGudangOptions()[$model['jenis_gudang']];
                    },
                    'headerOptions' => ['hidden' => false], // Hide the header cell
                    'visible' => true, // Hide the column in the GridView
                ],
                [
                    'attribute' => 'sc_greige_jenis_proses',
                    'label' => 'Jenis Proses',
                    'value' => function ($model) {
                        $proses = array_key_exists('sc_greige_jenis_proses', $model) ? $model['sc_greige_jenis_proses'] : 0;
                        $lebar_kain = array_key_exists('sc_greige_lebar_kain', $model) ? $model['sc_greige_lebar_kain'] : 0;
                        $jenis_barang = ($proses > 0) ? ($lebar_kain > 0 ? TrnScGreige::processOptions()[$proses].' '.TrnScGreige::lebarKainOptions()[$lebar_kain].'"' : TrnScGreige::processOptions()[$proses]) : '';
                        return strtoupper($jenis_barang);
                    },
                    'headerOptions' => ['hidden' => false], // Hide the header cell
                    'visible' => true, // Hide the column in the GridView
                ],
                [
                    'attribute' => 'grade_a',
                    'label' => 'GRADE A',
                    'format' => 'decimal',
                    'value' => function ($model) {
                        return ($model['grade_a'] > 0) ? $model['grade_a'] : '';
                    },
                    'pageSummary'=>true,
                ],
                [
                    'attribute' => 'grade_b',
                    'label' => 'GRADE B',
                    'format' => 'decimal',
                    'value' => function ($model) {
                        return ($model['grade_b'] > 0) ? $model['grade_b'] : '';
                    },
                    'pageSummary'=>true,
                ],
                [
                    'attribute' => 'grade_c',
                    'label' => 'GRADE C',
                    'format' => 'decimal',
                    'value' => function ($model) {
                        return ($model['grade_c'] > 0) ? $model['grade_c'] : '';
                    },
                    'pageSummary'=>true,
                ],
                [
                    'attribute' => 'grade_d',
                    'label' => 'GRADE D',
                    'format' => 'decimal',
                    'value' => function ($model) {
                        return ($model['grade_d'] > 0) ? $model['grade_d'] : '';
                    },
                    'pageSummary'=>true,
                ],
                [
                    'attribute' => 'grade_e',
                    'label' => 'GRADE E',
                    'format' => 'decimal',
                    'value' => function ($model) {
                        return ($model['grade_e'] > 0) ? $model['grade_e'] : '';
                    },
                    'pageSummary'=>true,
                ],
                [
                    'attribute' => 'grade_ng',
                    'label' => 'GRADE NG',
                    'format' => 'decimal',
                    'value' => function ($model) {
                        return ($model['grade_ng'] > 0) ? $model['grade_ng'] : '';
                    },
                    'pageSummary'=>true,
                ],
                [
                    'attribute' => 'grade_a_plus',
                    'label' => 'GRADE A+',
                    'format' => 'decimal',
                    'value' => function ($model) {
                        return ($model['grade_a_plus'] > 0) ? $model['grade_a_plus'] : '';
                    },
                    'pageSummary'=>true,
                ],
                [
                    'attribute' => 'grade_a_asterisk',
                    'label' => 'GRADE A*',
                    'format' => 'decimal',
                    'value' => function ($model) {
                        return ($model['grade_a_asterisk'] > 0) ? $model['grade_a_asterisk'] : '';
                    },
                    'pageSummary'=>true,
                ],
                [
                    'attribute' => 'grade_putih',
                    'label' => 'GRADE PUTIH',
                    'format' => 'decimal',
                    'value' => function ($model) {
                        return ($model['grade_putih'] > 0) ? $model['grade_putih'] : '';
                    },
                    'pageSummary' => true,
                ],        
                [
                    'attribute' => 'total_qty',
                    'label' => 'TOTAL QTY',
                    'format' => 'decimal',
                    'value' => function ($model) {
                        return ($model['total_qty'] > 0) ? $model['total_qty'] : '';
                    },
                    'pageSummary'=>true,
                ],
                [
                    'attribute' => 'unit', // <-- satuan
                    'label' => 'SATUAN',
                    'format' => 'raw', // You can change the format as needed
                    'value' => function ($model) {
                        $unitSatuan = MstGreigeGroup::unitOptions()[$model['unit']];
                        return strtoupper($unitSatuan);
                    },
                    'headerOptions' => ['hidden' => false, 'style' => 'text-align: center; vertical-align: middle;'], // Hide the header cell
                    'visible' => true, // Hide the column in the GridView
                ]
            ]
        ]); ?>
        </div>
    </div>
</div>