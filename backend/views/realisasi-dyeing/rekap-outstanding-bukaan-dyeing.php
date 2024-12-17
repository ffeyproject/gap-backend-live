<?php
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rekap Outstanding Bukaan Dyeing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rekap-outstanding-bukaan-dyeing">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['rekap-outstanding-bukaan-dyeing'], ['class' => 'btn btn-default']),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar'=>[
            '{export}',
            //'{toggleData}'
        ],
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            //['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            'id',
            [
                'attribute' => 'dateRange',
                'label' => 'TANGGAL WO',
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
            [
                'attribute'=>'no',
                'label'=>'Nomor WO',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return Html::a($data->no, ['/trn-wo/view', 'id'=>$data->id ], ['title'=>'Lihat WO', 'target'=>'blank']);
                },
                'format'=>'raw'
            ],
            [
                'attribute'=>'scGreigeNamaKain',
                'label'=>'Motif Greige',
                'value'=>'mo.scGreige.greigeGroup.nama_kain',
            ],
            [
                'attribute'=>'qtyBatch',
                'label'=>'Qty Batch',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return $data->colorQty;
                },
                'format'=>'decimal',
            ],
            [
                'attribute'=>'bukaan',
                'label'=>'Bukaan',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return $data->kartuProsesDyeingBukaan ;
                },
                'format'=>'decimal',
            ],
            [
                'attribute'=>'sisaan',
                'label'=>'Sisaan',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return $data->colorQty - $data->kartuProsesDyeingBukaan;
                },
                'format'=>'decimal',
            ],
        ],
    ]); ?>
</div>
