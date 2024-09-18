<?php
use common\models\ar\TrnKartuProsesPrinting;
use common\models\ar\TrnKartuProsesPrintingItem;
use common\models\ar\TrnStockGreige;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrnKartuProsesPrinting */

$wo = $model->wo;
$greige = $wo->greige;
$greigeGroup = $greige->group;

$canCreateItem = false;

if($model->status === TrnKartuProsesPrinting::STATUS_DRAFT){
    $canCreateItem = true;
}

$dataProvider = new ActiveDataProvider([
    'query' => $model->getTrnKartuProsesPrintingItems(),
    'pagination' => false,
    'sort' => false
]);
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">ITEMS</h3>
        <div class="box-tools pull-right">
            <?=$canCreateItem ? Html::a('<i class="glyphicon glyphicon-plus"></i>', ['/trn-kartu-proses-printing-item/create', 'processId' => $model->id], [
                'class' => 'btn btn-xs btn-success',
                'title' => 'Add Items',
                'data-toggle'=>"modal",
                'data-target'=>"#kartuProsesPrintingModal",
                'data-title' => 'Add Items'
            ]) : ''?>
        </div>
    </div>
    <div class="box-body">
        <p><?='<strong>Greige: '.$greige->nama_kain.' - Per Batch: '.Yii::$app->formatter->asDecimal($greigeGroup->qty_per_batch).' '.$greigeGroup::unitOptions()[$greigeGroup->unit].'</strong>'?></p>
        <div class="row">
            <div class="col-md-12">
                <?=GridView::widget([
                    'dataProvider' => $dataProvider,
                    'id' => 'KartuProsesPrintingItemsGrid',
                    'pjax' => true,
                    'responsiveWrap' => false,
                    'resizableColumns' => false,
                    'showPageSummary' => true,
                    'toolbar' => false,
                    'columns' => [
                        ['class' => 'kartik\grid\SerialColumn'],

                        //'id',
                        //'process_id',
                        'date:date',
                        //'mesin',
                        [
                            'attribute'=>'panjang_m',
                            'label'=>'Qty',
                            'format'=>'decimal',
                            'pageSummary' => true,
                            'hAlign' => 'right'
                        ],
                        [
                            'label'=>'Unit',
                            'value'=>function($data) use($greigeGroup){
                                return $greigeGroup->unitName;
                            },
                        ],
                        [
                            'label'=>'Grade',
                            'value'=>function($data){
                                /* @var $data TrnKartuProsesPrintingItem*/
                                return TrnStockGreige::gradeOptions()[$data->stock->grade];
                            },
                        ],
                        //'note:ntext',
                        //'status',
                        //'created_at',
                        //'created_by',
                        //'updated_at',
                        //'updated_by',

                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'controller' => 'trn-kartu-proses-printing-item',
                            'template' => '{delete} {print}',
                            'buttons' => [
                                'delete' => function($url, $model, $key) use($canCreateItem) {
                                    /* @var $model TrnKartuProsesPrintingItem*/
                                    if($canCreateItem){
                                        return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, [
                                            'class' => 'btn btn-xs btn-danger',
                                            'title' => 'Delete: '.$model->id,
                                            'data' => [
                                                'confirm' => 'Are you sure you want to delete this item?',
                                                'method' => 'post',
                                            ],
                                        ]);
                                    }

                                    return '';
                                },
                            ]
                        ],
                    ],
                ])?>
            </div>
        </div>
    </div>
</div>
