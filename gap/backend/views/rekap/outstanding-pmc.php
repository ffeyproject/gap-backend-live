<?php

use common\models\ar\TrnMoColor;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnWo;
use common\models\ar\TrnWoColor;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnWoColorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Outstanding PMC';
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
            //'{toggleData}',
            '{export}'
        ],
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['outstanding-pmc'], ['class' => 'btn btn-default']),
            'after'=>false,
            //'footer'=>false
        ],
        /*'beforeHeader' => [
            [
                'columns'=>[
                    ['content'=>'SALES CONTRACT', 'options'=>['colspan'=>7, 'class'=>'text-center']],
                    ['content'=>'SC GREIGE GROUP', 'options'=>['colspan'=>10, 'class'=>'text-center']],
                    ['content'=>'MARKETING ORDER', 'options'=>['colspan'=>6, 'class'=>'text-center']],
                    ['content'=>'WORKING ORDER', 'options'=>['colspan'=>4, 'class'=>'text-center']],
                ]
            ],
        ],*/
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            //'id',
            [
                'attribute' => 'dateRangeWo',
                'label' => 'Tgl. WO',
                'value' => 'wo.date',
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
                'label'=>'No. WO',
                'attribute'=>'woNo',
                'value'=>'wo.no',
            ],
            [
                'label'=>'Jenis Proses',
                'attribute'=>'proccess',
                'value' => function($data){
                    /* @var $data TrnWoColor*/
                    return TrnScGreige::processOptions()[$data->mo->scGreige->process];
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
                'attribute'=>'customerName',
                'value'=>'mo.scGreige.sc.customerName'
            ],
            [
                'label'=>'Marketing',
                'attribute'=>'marketingName',
                'value'=>'mo.scGreige.sc.marketingName'
            ],
            [
                'label'=>'Jenis Order',
                'attribute'=>'tipeKontrak',
                'value' => function($data){
                    /* @var $data TrnWoColor*/
                    return TrnSc::tipeKontrakOptions()[$data->mo->scGreige->sc->tipe_kontrak];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnSc::tipeKontrakOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions'=>[
                        'allowClear' => true,
                    ]
                ],
            ],
            [
                'label'=>'No. Lab. Dip.',
                //'attribute'=>'marketingName',
                'value'=>'mo.no_lab_dip'
            ],
            [
                'label'=>'Quality',
                'value' => function($data){
                    /* @var $data TrnWoColor*/
                    return $data->wo->greigeGroupNamaKain;
                },
            ],
            [
                'label'=>'Color',
                'value' => 'moColor.color',
            ],
            [
                'label'=>'Dasar Kain',
                //'attribute'=>'marketingName',
                'value'=>''
            ],
            [
                'label'=>'Kode Design',
                'value' => function($data){
                    /* @var $data TrnWoColor*/
                    return $data->mo->design;
                },
            ],
            [
                'label'=>'Qty (Greige)',
                'attribute'=>'qtyBatchToUnit',
                'format' => 'decimal',
                'pageSummary'=>true,
            ],
            [
                'label'=>'Qty Finish (M)',
                'attribute'=>'qtyFinishToMeter',
                'format' => 'decimal',
                'pageSummary'=>true,
            ],
            [
                'label'=>'Qty Finish (Y)',
                'attribute'=>'qtyFinishToYard',
                'format' => 'decimal',
                'pageSummary'=>true,
            ],
            [
                'label'=>'Qty (Batch)',
                'attribute'=>'qty',
                'format' => 'decimal',
                'pageSummary'=>true,
            ],
            [
                'label'=>'Tgl. Delivery',
                'value' => 'wo.tgl_kirim',
            ],
            [
                'label'=>'Handling',
                'value' => function($data){
                    /* @var $data TrnWoColor*/
                    return $data->wo->handling->name;
                },
            ],
            [
                'label'=>'No. SC',
                'attribute'=>'scNo',
                'value'=>'mo.scGreige.sc.no',
            ],
            [
                'label'=>'Selvedge Stamping',
                'value' => 'mo.selvedge_stamping',
            ],
            [
                'label'=>'Face Stamping',
                'value' => 'mo.face_stamping',
                'format'=>'html'
            ],
            [
                'label'=>'Band',
                'value' => 'mo.side_band',
            ],
            [
                'label'=>'Label',
                'value' => 'mo.label',
            ],
            [
                'label'=>'Tag',
                'value' => 'mo.tag',
            ],
            [
                'label'=>'Sticker',
                'value' => '',
            ],
            [
                'label'=>'Ket.',
                'value' => '',
            ],
        ],
    ]); ?>
</div>