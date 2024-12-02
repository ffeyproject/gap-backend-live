<?php
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rekap Dyeing By Process';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kartu-proses-dyeing-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['rekap-by-process'], ['class' => 'btn btn-default']),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar'=>[
            '{export}',
            '{toggleData}'
        ],
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $model->kartuProcess->id], [
                            'title' => Yii::t('app', 'view'),
                            'target' => '_blank'
                        ]);
                    },
                ],
            ],
            [
                'attribute'=>'woNo',
                'label'=>'Nomor WO',
                'value'=>'kartuProcess.wo.no'
            ],
            [   
                'attribute'=>'no_kartu',
                'label'=>'No Kartu',
                'value'=>'kartuProcess.no'
            ],
            [
                'attribute' => 'nama_proses',
                'label' => 'Nama Proses',
                'value' => 'process.nama_proses',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\ar\MstProcessDyeing::find()->all(), 'id', 'nama_proses'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Pilih Nama Proses'],
            ],
            [   
                'attribute'=>'warna',
                'label'=>'Warna',
                'value'=>'kartuProcess.woColor.moColor.color'
            ],
            [
                'attribute' => 'dateRange',
                'label' => 'TANGGAL',
                'value' => function ($model) {
                    $data = json_decode($model->value, true); // Decode JSON
                    $tanggal = $data['tanggal'] ?? null; // Ambil nilai no_mesin
                    return $tanggal;
                },
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
            [   
                'attribute'=>'no_mesin',
                'label' => 'No Mesin',
                'value' => function ($model) {
                    $data = json_decode($model->value, true); // Decode JSON
                    $noMesin = $data['no_mesin'] ?? null; // Ambil nilai no_mesin
                    return $noMesin;
                },
            ],
            [   
                'attribute'=>'shift_group',
                'label' => 'Shift Group',
                'value' => function ($model) { 
                    $data = json_decode($model->value, true); // Decode JSON
                    $shiftGroup = $data['shift_group'] ?? null; // Ambil nilai shift_group
                    return $shiftGroup;
                },
            ],
            [   
                'attribute'=>'panjang',
                'label' => 'Panjang',
                'value' => function ($model) { 
                    $kartuProcess = $model->kartuProcess;
                    $greige = $kartuProcess->wo->greige;
                    $greigeGroup = $greige->group;
                    $panjangTotal = $kartuProcess->getTrnKartuProsesDyeingItems()->sum('panjang_m');
                    return Yii::$app->formatter->asDecimal($panjangTotal) . ' ' . $greigeGroup->unitName;
                },
            ],
            [   
                'attribute'=>'jumlah_roll',
                'label' => 'Jml. Roll',
                'value' => function ($model) { 
                    $kartuProcess = $model->kartuProcess;
                    $jumlahRoll = $kartuProcess->getTrnKartuProsesDyeingItems()->count('id');
                    $jumlahRoll = $jumlahRoll === null ? 0 : $jumlahRoll;
                    return $jumlahRoll;
                },
            ],
            [   
                'attribute'=>'berat',
                'label' => 'Berat',
                'value' => function ($model) { 
                    $kartuProcess = $model->kartuProcess;
                    return $kartuProcess->berat;
                },
            ],
            //'note:ntext',
            //'created_at:datetime',
            //'created_by',
            //'updated_at:datetime',
            //'updated_by',
        ],
    ]); ?>


</div>
