<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnKirimBuyer;
use common\models\ar\TrnKirimBuyerHeader;
use common\models\ar\TrnKirimBuyerSearch;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel TrnKirimBuyerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rekap Accounting';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-gudang-jadi-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'resizableColumns' => false,
        'responsiveWrap' => false,
        'toolbar' => [
            '{toggleData}',
            '{export}'
        ],
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['accounting'], ['class' => 'btn btn-default']),
            'after'=>false,
            //'footer'=>false
        ],
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            //'id',
            [
                'label'=>'NO. SJ',
                'attribute'=>'headerNo',
                'value'=>'header.no'
            ],
            [
                'label'=>'KODE CUST',
                //'attribute'=>'customerName',
                'value'=>'wo.mo.scGreige.sc.customerCode'
            ],
            [
                'label'=>'KODE KAIN',
                'value'=>'wo.greige.group.jenisKainName',
            ],
            [
                'attribute' => 'dateRange',
                'label' => 'TANGGAL KIRIM',
                'value' => 'header.date',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        //'timePicker'=>true,
                        //'timePickerIncrement'=>5,
                        'locale'=>[
                            //'format'=>'Y-m-d H:i:s',
                            'format'=>'Y-m-d',
                            'separator'=>' to ',
                        ]
                    ]
                ]
            ],
            [
                'label'=>'NO. KONTRAK',
                'attribute'=>'scNo',
                'value'=>'wo.mo.scGreige.sc.no',
            ],
            [
                'label'=>'NO. MEMO',
                'value'=>'',
            ],
            [
                'label'=>'No. WO',
                'attribute'=>'woNo',
                'value'=>'wo.no'
            ],
            [
                'label'=>'KOTA',
                'value'=>'wo.mo.scGreige.sc.destination',
            ],
            [
                'label'=>'Marketing',
                'attribute'=>'marketingName',
                'value'=>'wo.mo.scGreige.sc.marketingName'
            ],
            [
                'label'=>'JENIS KAIN',
                'value'=>'wo.greige.group.jenisKainName',
            ],
            [
                'label'=>'NAMA BUYER',
                'attribute'=>'customerName',
                'value'=>'wo.mo.scGreige.sc.customerName'
            ],
            [
                'label'=>'NAMA KAIN',
                'attribute'=>'scGreigeNamaKain',
                'value'=>'wo.mo.scGreige.greigeGroup.nama_kain'
            ],
            [
                'label'=>'ARTIKEL',
                'value'=>'wo.mo.article'
            ],
            [
                'label' => 'QTY',
                'value' => function($data){
                    /* @var $data TrnKirimBuyer*/
                    return $data->getTrnKirimBuyerItems()->count('id');
                },
                'format' => 'decimal',
                'pageSummary'=>true,
            ],
            [
                'label'=>'SATUAN',
                'value'=>'PCS'
            ],
            [
                'label' => 'JUMLAH',
                'value' => function($data){
                    /* @var $data TrnKirimBuyer*/
                    return $data->getTrnKirimBuyerItems()->sum('qty');
                },
                'format' => 'decimal',
                'pageSummary'=>true,
            ],
            [
                'label' => 'UNIT',
                'attribute' => 'unit',
                'value' => function($data){
                    /* @var $data TrnKirimBuyer*/
                    return MstGreigeGroup::unitOptions()[$data->unit];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => MstGreigeGroup::unitOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            [
                'label'=>'NO. FAK',
                'value'=>'header.no'
            ],
            [
                'label'=>'TERM',
                'value'=>'wo.mo.scGreige.sc.pmt_term'
            ],
            [
                'label'=>'HARGA',
                'value'=>'wo.mo.scGreige.unit_price',
                'format'=>'decimal'
            ],
            [
                'label'=>'TOTAL',
                'value' => function($data){
                    /* @var $data TrnKirimBuyer*/
                    $harga = $data->wo->mo->scGreige->unit_price;
                    $qty = $data->getTrnKirimBuyerItems()->sum('qty');
                    return $harga * $qty;
                },
                'format'=>'decimal'
            ],
            /*[
                'label' => 'Status Pengiriman',
                'attribute' => 'headerStatus',
                'value' => function($data){
                    //@var $data TrnKirimBuyer
                    return TrnKirimBuyerHeader::statusOptions()[$data->header->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnKirimBuyerHeader::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],*/
        ],
    ]); ?>
</div>