<?php
use common\models\ar\TrnKartuProsesPfp;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesPfpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rekap Outstanding Bukaan PFP';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rekap-outstanding-bukaan-pfp">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['rekap-outstanding-bukaan-pfp'], ['class' => 'btn btn-default']),
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
                'label' => 'TANGGAL ORDER PFP',
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
                'label'=>'Nomor Order PFP',
                'value'=>function($data){
                    /* @var $data TrnOrderPfp*/
                    return Html::a($data->no, ['/trn-order-pfp/view', 'id'=>$data->id ], ['title'=>'Lihat WO', 'target'=>'blank']);
                },
                'format'=>'raw'
            ],
            [
                'attribute'=>'greigeNamaKain',
                'label'=>'Motif Greige',
                'value'=>'greigeGroup.nama_kain',
            ],
            [
                'attribute'=>'qtyBatch',
                'label'=>'Qty Batch',
                'value'=>function($data){
                    /* @var $data TrnOrderPfp*/
                    return $data->qty;
                },
                'format'=>'decimal',
            ],
            [
                'attribute'=>'bukaan',
                'label'=>'Bukaan',
                'value'=>function($data){
                    /* @var $data TrnOrderPfp*/
                    return $data->KartuProsesPfpBukaan;
                },
                'format'=>'decimal',
            ],
            [
                'attribute'=>'sisaan',
                'label'=>'Sisaan',
                'value'=>function($data){
                    /* @var $data TrnOrderPfp*/
                    return $data->qty - $data->KartuProsesPfpBukaan;
                },
                'format'=>'decimal',
            ],
        ],
    ]); ?>
</div>

