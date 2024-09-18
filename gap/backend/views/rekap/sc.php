<?php

use backend\components\ajax_modal\AjaxModal;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnScGreigeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rekap SC';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="trn-sc-greige-index">
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'resizableColumns' => false,
            'responsiveWrap' => false,
            //'floatHeader' => true,
            //'floatHeaderOptions'=>['scrollingTop'=>'50', 'zIndex'=>800],
            //'floatOverflowContainer' => true,
            //'perfectScrollbar' => true,
            //'containerOptions' => ['style' => 'height: 80%;'],
            'toolbar' => [
                [
                    'content'=>
                        Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], [
                            'class' => 'btn btn-default',
                            'title' => 'Refresh data'
                        ])
                ],
                '{export}',
            ],
            'panel' => [
                'type' => GridView::TYPE_DEFAULT
            ],
            'columns' => [
                ['class' => 'kartik\grid\SerialColumn'],

                //'id',
                [
                    'label'=>'Nomor SC',
                    'attribute'=>'nomorSc',
                    'value'=>'sc.no'
                ],
                [
                    'attribute' => 'dateRange',
                    'label' => 'TANGGAL SC',
                    'value' => 'sc.date',
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
                    'attribute' => 'scTipeKontrak',
                    'label' => 'Orientasi',
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => TrnSc::tipeKontrakOptions(),
                        'options' => ['placeholder' => '...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ],
                    'value' => function($data){
                        /* @var $data TrnScGreige*/
                        return TrnSc::tipeKontrakOptions()[$data->sc->tipe_kontrak];
                    },
                ],
                [
                    'attribute'=>'process',
                    'value'=>function($data){
                        /* @var $data TrnScGreige*/
                        return $data::processOptions()[$data->process];
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => TrnScGreige::processOptions(),
                        'options' => ['placeholder' => '...'],
                        'pluginOptions'=>[
                            'allowClear' => true,
                        ]
                    ],
                ],
                [
                    'label'=>'Nama Buyer',
                    'attribute'=>'scCustomerName',
                    'value'=>'sc.customerName'
                ],
                [
                    'label'=>'Marketing',
                    'attribute'=>'scMarketingName',
                    'value'=>'sc.marketingName'
                ],
                [
                    'label'=>'NO. PO',
                    'attribute'=>'scNoPo',
                    'value'=>'sc.no_po'
                ],
                [
                    'label'=>'Nama Kain',
                    'attribute'=>'greigeGroupNamaKain',
                    'value'=>'greigeGroup.nama_kain'
                ],
                [
                    'label'=>'Harga',
                    'value'=>function($data){
                        /* @var $data TrnScGreige*/
                        $pp = $data::priceParamOptions()[$data->price_param];
                        $currency = $data->sc->currencyName;
                        $price = Yii::$app->formatter->asDecimal($data->unit_price);
                        return $currency.' '.$price.' '.$pp;
                    }
                ],
                'qty:decimal',
                'qtyFinish:decimal',
                [
                    'label'=>'Qty Finish (Yard)',
                    'attribute'=>'qtyFinishToYard',
                    'format'=>'decimal'
                ],
                [
                    'attribute'=>'lebar_kain',
                    'value'=>function($data){
                        /* @var $data TrnScGreige*/
                        return $data::lebarKainOptions()[$data->lebar_kain];
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => TrnScGreige::lebarKainOptions(),
                        'options' => ['placeholder' => '...'],
                        'pluginOptions'=>[
                            'allowClear' => true,
                        ]
                    ],
                ],
                [
                    'label'=>'Sisa MO (Batch)',
                    'attribute'=>'sisaMoBatch',
                    'format'=>'decimal'
                ],
                [
                    'label'=>'Sisa WO (Batch)',
                    'attribute'=>'sisaWoBatch',
                    'format'=>'decimal'
                ],
            ],
        ]); ?>


    </div>
<?php
echo AjaxModal::widget([
    'id' => 'trnScModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);
