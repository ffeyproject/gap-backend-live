<?php

use common\models\ar\TrnScGreige;
use common\models\ar\TrnScKomisi;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnScKomisiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trn Sc Komisi';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-sc-komisi-index">
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
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'sc_id',
            [
                'label'=>'Nomor SC',
                'attribute'=>'nomorSc',
                'value'=>'sc.no'
            ],
            [
                'label'=>'Greige Group',
                'attribute'=>'greigeGroupNamaKain',
                'value'=>'scGreige.greigeGroup.nama_kain'
            ],
            [
                'label'=>'Qty (batch)',
                'value'=>'scGreige.qty',
                'format'=>'decimal'
            ],
            [
                'value' => 'scGreige.qtyBatchToMeter',
                'label' => 'Qty Batch To Meter',
                'format' => 'decimal',
            ],
            [
                'attribute' => 'scGreige.qtyFinish',
                'label' => 'Qty Finish',
                'format' => 'decimal',
            ],
            [
                'attribute' => 'scGreige.qtyFinishToYard',
                'format' => 'decimal',
                'label' => 'Qty Finish To Yard',
            ],
            [
                'label'=>'Unit Price',
                'value'=>'scGreige.unit_price',
                'format'=>'decimal'
            ],
            [
                'label' => 'Price Param',
                'value' => function($data){
                    /* @var $data TrnScKomisi*/
                    return TrnScGreige::priceParamOptions()[$data->scGreige->price_param];
                },
                'hAlign' => 'center',
            ],
            [
                'value' => 'scGreige.totalPrice',
                'format' => 'decimal',
                'label' => 'Total Price',
            ],
            [
                'label'=>'Nama Agen',
                'attribute'=>'namaAgen',
                'value'=>'scAgen.nama_agen'
            ],
            //'sc_agen_id',
            //'sc_greige_id',
            //'tipe_komisi',
            [
                'attribute'=>'tipe_komisi',
                'value'=>function($data){
                    /* @var $data TrnScKomisi*/
                    return $data::tipeKomisiOptions()[$data->tipe_komisi];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnScKomisi::tipeKomisiOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            'komisi_amount:decimal',
            'komisiTotal:decimal'
        ],
    ]); ?>


</div>
