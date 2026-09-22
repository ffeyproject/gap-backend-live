<?php
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWo;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Dyeing untuk Processing';
$this->params['breadcrumbs'][] = ['label' => 'Rekap', 'url' => ['/rekap/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="realisasi-dyeing-processing">

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-filter"></i> <strong>Filter Tahun & Bulan WO</strong></h3>
        </div>
        <div class="box-body">
            <?php $form = ActiveForm::begin([
                'action' => ['rekap-processing'],
                'method' => 'get',
            ]); ?>
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'woYear')->widget(Select2::class, [
                        'data' => TrnWo::yearOptions(),
                        'options' => ['placeholder' => '-- Pilih Tahun WO --'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ])->label('Pilih Tahun WO') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'woMonth')->widget(Select2::class, [
                        'data' => TrnWo::monthOptions(),
                        'options' => ['placeholder' => '-- Semua Bulan (Opsional) --'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ])->label('Pilih Bulan WO (Opsional)') ?>
                </div>
                <div class="col-md-6" style="padding-top: 25px;">
                    <?= Html::submitButton('<i class="glyphicon glyphicon-search"></i> Tampilkan Data', ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('<i class="fa fa-file-excel-o"></i> Export Excel', array_merge(['export-processing'], Yii::$app->request->queryParams), ['class' => 'btn btn-success', 'target' => '_blank', 'data-pjax' => '0', 'title' => 'Export Semua Data (500+ Baris) ke Excel']) ?>
                    <?= Html::a('<i class="glyphicon glyphicon-refresh"></i> Reset', ['rekap-processing'], ['class' => 'btn btn-default']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?php if (empty($searchModel->woYear)): ?>
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> <strong>Silahkan pilih Tahun WO terlebih dahulu</strong> pada filter di atas untuk memunculkan data tabel.
        </div>
    <?php else: ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['rekap-processing', 'TrnKartuProsesDyeingSearch[woYear]' => $searchModel->woYear, 'TrnKartuProsesDyeingSearch[woMonth]' => $searchModel->woMonth], ['class' => 'btn btn-default']),
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
            //['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            // 'id',
            [
                'attribute'=>'woNo',
                'label'=>'Nomor WO',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return $data->wo ? Html::a($data->wo->no, ['/trn-wo/view', 'id'=>$data->wo_id], ['title'=>'Lihat WO', 'target'=>'_blank']) : '-';
                },
                'group' => true,
                'format'=>'raw'
            ],
            [   
                'attribute'=>'customerName',
                'label'=>'Buyer',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return $data->sc ? $data->sc->customerName : '-';
                },
                'group' => true,
                'subGroupOf' => 1
            ],
            [   
                'attribute'=>'motif',
                'label'=>'Motif',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return $data->wo ? $data->wo->greigeNamaKain : '-';
                },
                'group' => true,
                'subGroupOf' => 2
            ],
            [
                'label'=>'Handling',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return ($data->wo && $data->wo->handling) ? $data->wo->handling->name : '-';
                },
                'group' => true,
                'subGroupOf' => 3
            ],
            [
                'label'=>'BATCH TOTAL',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return $data->wo ? $data->wo->colorQty : 0;
                },
                'group' => true,
                'subGroupOf' => 1
            ],
            [
                'label'=>'JML PANJANG',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    if (!$data->wo) return '-';
                    $finish = is_numeric($data->wo->colorQtyFinish) ? Yii::$app->formatter->asDecimal($data->wo->colorQtyFinish) : '-';
                    $finishToYard = is_numeric($data->wo->colorQtyFinishToYard) ? Yii::$app->formatter->asDecimal($data->wo->colorQtyFinishToYard) : '-';
                    return $finish . 'M / ' . $finishToYard . 'Y';
                },
                'group' => true,
                'subGroupOf' => 1,
            ],
            [   
                'attribute'=>'warna',
                'label'=>'Warna',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return ($data->woColor && $data->woColor->moColor) ? $data->woColor->moColor->color : '-';
                },
                'group' => true,
                'subGroupOf' => 1,
                'enableSorting' => true,
            ],
            [
                'attribute' => 'woDateRange',
                'label' => 'TANGGAL WO',
                'value' => 'wo.date',
                'format' => ['date', 'php:d/m/y'],
                'group' => true,
                'subGroupOf' => 1,
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
                'attribute' => 'woTglKirimRange',
                'label' => 'TANGGAL KIRIM',
                'value' => 'wo.tgl_kirim',
                'format' => ['date', 'php:d/m/y'],
                'group' => true,
                'subGroupOf' => 1,
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
                'attribute'=>'nomor_kartu',
                'label'=>'NK',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    if (!empty($data->id) && !empty($data->nomor_kartu)) {
                        return Html::a($data->nomor_kartu, ['/processing-dyeing/view', 'id'=>$data->id], ['title'=>'Lihat Kartu', 'target'=>'_blank']);
                    }
                    return '<span class="label label-warning">Belum Ada NK</span>';
                },
                'format'=>'html'
            ],
            [
                'label'=>'Panjang Greige',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    if (empty($data->id)) {
                        return null;
                    }
                    $sum = $data->getTrnKartuProsesDyeingItems()->sum('panjang_m');
                    return is_numeric($sum) ? (float)$sum : null;
                },
                'format'=>'decimal'
            ],
            [
                'label'=>'PSP',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    if (empty($data->id)) return '';
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>1])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['tanggal']) && !empty($v['tanggal'])){
                            return date('d/m/y', strtotime($v['tanggal']));
                        }
                    }

                    return '';
                },
            ],
            [
                'label'=>'Relaxing',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    if (empty($data->id)) return '';
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>3])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['tanggal']) && !empty($v['tanggal'])){
                            return date('d/m/y', strtotime($v['tanggal']));
                        }
                    }

                    return '';
                },
            ],
            [
                'label'=>'DYEING',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    if (empty($data->id)) return '';
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>8])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['tanggal']) && !empty($v['tanggal'])){
                            return date('d/m/y', strtotime($v['tanggal']));
                        }
                    }

                    return '';
                },
            ],
            [
                'label'=>'DY 1',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    if (empty($data->id)) return '';
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>15])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['tanggal']) && !empty($v['tanggal'])){
                            return date('d/m/y', strtotime($v['tanggal']));
                        }
                    }

                    return '';
                },
            ],
            [
                'label'=>'DY 2',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    if (empty($data->id)) return '';
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>18])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['tanggal']) && !empty($v['tanggal'])){
                            return date('d/m/y', strtotime($v['tanggal']));
                        }
                    }

                    return '';
                },
            ],
            [
                'label'=>'DY 3',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    if (empty($data->id)) return '';
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>19])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['tanggal']) && !empty($v['tanggal'])){
                            return date('d/m/y', strtotime($v['tanggal']));
                        }
                    }

                    return '';
                },
            ],
            [
                'label'=>'TOPING LEVEL',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    if (empty($data->id)) return '';
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>21])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['tanggal']) && !empty($v['tanggal'])){
                            return date('d/m/y', strtotime($v['tanggal']));
                        }
                    }

                    return '';
                },
            ],
            [
                'attribute' => 'dateRangeMasukPacking',
                'label' => 'PACKING',
                'value' => function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    if (empty($data->id) || empty($data->approved_at)) return '';
                    if (is_numeric($data->approved_at) && (int)$data->approved_at > 100000000) {
                        return date('d/m/y', (int)$data->approved_at);
                    } elseif (!is_numeric($data->approved_at) && strtotime($data->approved_at)) {
                        return date('d/m/y', strtotime($data->approved_at));
                    }
                    return '';
                },
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
                'label'=>'Panjang Jadi',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    if (empty($data->id)) return null;
                    $r = null;
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>11])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['panjang_jadi']) && is_numeric($v['panjang_jadi'])){
                            $r = (float)$v['panjang_jadi'];
                        }
                    }

                    return $r;
                },
                'format'=>'decimal'
            ],

            [
                'label' => 'Total Qty Gudang',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    if (empty($data->id)) return null;
                    $inspecting = $data->trnInspectingsDelivered;
                    $total = 0;
                    $id = null;
                    foreach ($inspecting as $item) {
                        $qTotal = (new \yii\db\Query())->from(\common\models\ar\InspectingItem::tableName())
                        ->where(['inspecting_id'=>$item->id])
                        ->sum('qty');
                        $total += (float)$qTotal;
                        $id = $item->id;
                    }
                    if($id === null){
                        return Yii::$app->formatter->asDecimal($total);
                    }
                    return Html::a(Yii::$app->formatter->asDecimal($total), ['/penerimaan-inspecting/view', 'id'=>$id], ['title'=>'Lihat Inspecting Detail', 'target'=>'_blank']);

                },
                'format'=>'raw'
            ],
        ],
    ]); ?>

    <?php endif; ?>
</div>
