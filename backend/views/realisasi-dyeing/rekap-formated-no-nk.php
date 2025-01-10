<?php
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Realisasi Dyeing Formated No NK';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="realisasi-dyieng-formated-no-nk">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['rekap-formated-no-nk'], ['class' => 'btn btn-default']),
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
                    return Html::a($data->wo->no, ['/trn-wo/view', 'id'=>$data->wo_id], ['title'=>'Lihat WO', 'target'=>'blank']);
                },
                'group' => true,
                'format'=>'raw'
            ],
            [   
                'attribute'=>'customerName',
                'label'=>'Buyer',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return $data->sc->customerName;
                },
                'group' => true,
                'subGroupOf' => 1
            ],
            [   
                'attribute'=>'motif',
                'label'=>'Motif',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return $data->wo->greigeNamaKain;
                },
                'group' => true,
                'subGroupOf' => 2
            ],
            [
                'label'=>'Handling',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return $data->wo->handling->name;
                },
                'group' => true,
                'subGroupOf' => 3
            ],
            [
                'label'=>'BATCH TOTAL',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return $data->wo->colorQty;
                },
                'group' => true,
                'subGroupOf' => 1
            ],
            [
                'label'=>'JML PANJANG',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return Yii::$app->formatter->asDecimal($data->wo->colorQtyFinish) .'M / '. Yii::$app->formatter->asDecimal($data->wo->colorQtyFinishToYard).'Y';
                },
                'group' => true,
                'subGroupOf' => 1,
            ],
            [   
                'attribute'=>'warna',
                'label'=>'Warna',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return $data->moColor->color;
                },
                'enableSorting' => true,
            ],
            [
                'attribute' => 'dateRangeWo',
                'label' => 'TANGGAL WO',
                'value' => 'wo.date',
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
        ],
    ]); ?>
</div>
