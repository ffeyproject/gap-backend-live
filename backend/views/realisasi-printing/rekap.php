<?php
use common\models\ar\TrnKartuProsesPrinting;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesPrintingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Realisasi Printing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="realisasi-printing">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['rekap'], ['class' => 'btn btn-default']),
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
                'attribute'=>'customerName',
                'label'=>'Buyer',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return $data->sc->customerName;
                },
            ],
            [
                'attribute'=>'woNo',
                'label'=>'Nomor WO',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return Html::a($data->wo->no, ['/trn-wo/view', 'id'=>$data->wo_id], ['title'=>'Lihat WO', 'target'=>'blank']);
                },
                'format'=>'raw'
            ],
            [   
                'attribute'=>'motif',
                'label'=>'Motif',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return $data->wo->greigeNamaKain;
                },
            ],
            [   
                'attribute'=>'warna',
                'label'=>'Warna',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return $data->woColor->moColor->color;
                },
            ],
            [   
                'attribute'=>'no',
                'label'=>'NK',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return Html::a($data->no, ['/trn-kartu-proses-printing/view', 'id'=>$data->id], ['title'=>'Lihat Kartu', 'target'=>'blank']);
                },
                'format'=>'html'
            ],
            [
                'label'=>'Tgl. Kirim',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return $data->wo->tgl_kirim;
                },
                'format'=>'date'
            ],
            [
                'label'=>'Handling',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return $data->wo->handling->name;
                },
            ],
            [
                'label'=>'T. Finish',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return Yii::$app->formatter->asDecimal($data->wo->colorQtyFinish) .'M / '. Yii::$app->formatter->asDecimal($data->wo->colorQtyFinishToYard).'Y';
                },
            ],
            [
                'label'=>'Panjang Greige',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return $data->getTrnKartuProsesPrintingItems()->sum('panjang_m');
                },
                'format'=>'decimal'
            ],
            [
                'label'=>'Greige',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return $data->wo->greigeNamaKain;
                },
            ],
            [
                'label'=>'PRINTING',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';
                    $pj = '-';

                    /* @var $data TrnKartuProsesPrinting*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPrintingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>6])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['tanggal'])){
                            $tg = $v['tanggal'];
                        }
                        if(isset($v['operator'])){
                            $sh = $v['operator'];
                        }
                        if(isset($v['panjang_jadi'])){
                            $pj = $v['panjang_jadi'];
                        }
                    }

                    return $tg.' / '.$sh.' / '.$pj;
                },
            ],
            [
                'label'=>'STEAMER',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';
                    $pj = '-';

                    /* @var $data TrnKartuProsesPrinting*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPrintingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>2])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['tanggal'])){
                            $tg = $v['tanggal'];
                        }
                        if(isset($v['operator'])){
                            $sh = $v['operator'];
                        }
                    }

                    return $tg.' / '.$sh;
                },
            ],
            [
                'label'=>'RC CONTINUES',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';
                    $pj = '-';

                    /* @var $data TrnKartuProsesPrinting*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPrintingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>3])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['tanggal'])){
                            $tg = $v['tanggal'];
                        }
                        if(isset($v['operator'])){
                            $sh = $v['operator'];
                        }
                    }

                    return $tg.' / '.$sh;
                },
            ],
            [
                'label'=>'RESIN FINISH',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';
                    $pj = '-';

                    /* @var $data TrnKartuProsesPrinting*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPrintingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>5])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['tanggal'])){
                            $tg = $v['tanggal'];
                        }
                        if(isset($v['operator'])){
                            $sh = $v['operator'];
                        }
                        if(isset($v['panjang_jadi'])){
                            $pj = $v['panjang_jadi'];
                        }
                    }

                    return $tg.' / '.$sh .' / '.$pj;
                },
            ],
            [
                'label'=>'HEAT CUT',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';
                    $pj = '-';

                    /* @var $data TrnKartuProsesPrinting*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPrintingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>7])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['tanggal'])){
                            $tg = $v['tanggal'];
                        }
                        if(isset($v['operator'])){
                            $sh = $v['operator'];
                        }
                        if(isset($v['panjang_jadi'])){
                            $pj = $v['panjang_jadi'];
                        }
                    }

                    return $tg.' / '.$sh.' / '.$pj;
                },
            ],
            [
                'label' => 'Total Qty Gudang',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
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

