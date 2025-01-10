<?php
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Realisasi Dyeing Formated';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="realisasi-dyieng-formated">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['rekap-formated'], ['class' => 'btn btn-default']),
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
                    return $data->woColor->moColor->color;
                },
                'enableSorting' => true,
            ],
            [
                'attribute' => 'woDateRange',
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
            [   
                'attribute'=>'no',
                'label'=>'NK',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return $data->no ? Html::a($data->no, ['/trn-kartu-proses-dyeing/view', 'id'=>$data->id], ['title'=>'Lihat Kartu', 'target'=>'blank']) : null;
                    },
                'format'=>'html'
            ],
            [
                'label'=>'Panjang Greige',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return $data->getTrnKartuProsesDyeingItems()->sum('panjang_m');
                },
                'format'=>'decimal'
            ],
            [
                'label'=>'PSP',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesDyeing*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>1])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['tanggal'])){
                            $tg = $v['tanggal'];
                        }
                        if(isset($v['shift_group'])){
                            $sh = $v['shift_group'];
                        }
                    }

                    return $tg.' / '.$sh;
                },
            ],
            [
                'label'=>'Relaxing',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesDyeing*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>3])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['tanggal'])){
                            $tg = $v['tanggal'];
                        }
                        if(isset($v['shift_group'])){
                            $sh = $v['shift_group'];
                        }
                    }

                    return $tg.' / '.$sh;
                },
            ],
            [
                'label'=>'DYEING',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesDyeing*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>8])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['tanggal'])){
                            $tg = $v['tanggal'];
                        }
                        if(isset($v['shift_group'])){
                            $sh = $v['shift_group'];
                        }
                    }

                    return $tg.' / '.$sh;
                },
            ],
            [
                'label'=>'DY 1',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesDyeing*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>15])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['tanggal'])){
                            $tg = $v['tanggal'];
                        }
                        if(isset($v['shift_group'])){
                            $sh = $v['shift_group'];
                        }
                    }

                    return $tg.' / '.$sh;
                },
            ],
            [
                'label'=>'DY 2',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesDyeing*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>18])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['tanggal'])){
                            $tg = $v['tanggal'];
                        }
                        if(isset($v['shift_group'])){
                            $sh = $v['shift_group'];
                        }
                    }

                    return $tg.' / '.$sh;
                },
            ],
            [
                'label'=>'DY 3',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesDyeing*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>19])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['tanggal'])){
                            $tg = $v['tanggal'];
                        }
                        if(isset($v['shift_group'])){
                            $sh = $v['shift_group'];
                        }
                    }

                    return $tg.' / '.$sh;
                },
            ],
            [
                'label'=>'TOPING LEVEL',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesDyeing*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>21])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['tanggal'])){
                            $tg = $v['tanggal'];
                        }
                        if(isset($v['shift_group'])){
                            $sh = $v['shift_group'];
                        }
                    }

                    return $tg.' / '.$sh;
                },
            ],
            [
                'attribute' => 'dateRangeMasukPacking',
                'label' => 'PACKING',
                'value' => 'approved_at',
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
                'label'=>'Panjang Jadi',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    $r = 0;
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>11])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['panjang_jadi'])){
                            $r = $v['panjang_jadi'];
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
                    $inspecting = $data->trnInspectingsDelivered;
                    $total = 0;
                    $id = null;
                    foreach ($inspecting as $item) {
                        $qTotal = (new \yii\db\Query())->from(\common\models\ar\InspectingItem::tableName())
                        ->where(['inspecting_id'=>$item->id])
                        ->sum('qty');
                        $total += $qTotal;
                        $id = $item->id;
                    }
                    if($id === null){
                        return $total;
                    }
                    return Html::a($total, ['/penerimaan-inspecting/view', 'id'=>$id], ['title'=>'Lihat Inspecting Detail', 'target'=>'blank']);

                },
                'format'=>'html'
            ],
        ],
    ]); ?>
</div>
