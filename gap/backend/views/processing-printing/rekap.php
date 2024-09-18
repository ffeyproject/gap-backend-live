<?php
use common\models\ar\TrnKartuProsesPrinting;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesPrintingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rekap Processing Printing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kartu-proses-printing-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
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
                'attribute' => 'dateRange',
                'label' => 'Tanggal',
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
                'label'=>'Buyer',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return $data->sc->customerName;
                },
            ],
            [
                'label'=>'No. WO',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return $data->wo->no;
                },
            ],
            [
                'label'=>'Motif',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return $data->wo->greigeNamaKain;
                },
            ],
            [
                'label'=>'Warna',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return $data->woColor->moColor->color;
                },
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
                'label'=>'Hand',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return $data->wo->handling->name;
                },
            ],
            [
                'label'=>'Note',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return $data->wo->note;
                },
                'format'=>'html'
            ],
            [
                'label'=>'T. Finish',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return Yii::$app->formatter->asDecimal($data->wo->colorQtyFinish) .'M / '. Yii::$app->formatter->asDecimal($data->wo->colorQtyFinishToYard).'Y';
                },
            ],
            [
                'label'=>'NK',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return $data->no;
                },
                'format'=>'html'
            ],
            [
                'label'=>'Panjang',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return $data->wo->colorQtyBatchToMeter;
                },
                'format'=>'decimal'
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
                'label'=>'Berat Greige',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return $data->berat;
                },
                //'format'=>'decimal'
            ],
            [
                'label'=>'Pcs',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return $data->getTrnKartuProsesPrintingItems()->count();
                },
                'format'=>'decimal'
            ],
            [
                'label'=>'Tgl. Buka / Shift',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesPrinting*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPrintingProcess::tableName())
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
                'label'=>'Washing / Shift',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

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
                        if(isset($v['shift_group'])){
                            $sh = $v['shift_group'];
                        }
                    }

                    return $tg.' / '.$sh;
                },
            ],
            [
                'label'=>'Relaxing / Shift',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

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
                        if(isset($v['shift_group'])){
                            $sh = $v['shift_group'];
                        }
                    }

                    return $tg.' / '.$sh;
                },
            ],
            [
                'label'=>'Scutcher Relaxing / Shift',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesPrinting*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPrintingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>4])
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
                'label'=>'Preset / Shift',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

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
                        if(isset($v['shift_group'])){
                            $sh = $v['shift_group'];
                        }
                    }

                    return $tg.' / '.$sh;
                },
            ],
            [
                'label'=>'WR / Shift',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

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
                        if(isset($v['shift_group'])){
                            $sh = $v['shift_group'];
                        }
                    }

                    return $tg.' / '.$sh;
                },
            ],
            [
                'label'=>'C WR / Shift',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

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
                        if(isset($v['shift_group'])){
                            $sh = $v['shift_group'];
                        }
                    }

                    return $tg.' / '.$sh;
                },
            ],
            [
                'label'=>'DYEING / Shift',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesPrinting*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPrintingProcess::tableName())
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
                'label'=>'SCUTCHER DYEING / Shift',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesPrinting*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPrintingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>9])
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
                'label'=>'SETTING / Shift',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesPrinting*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPrintingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>10])
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
                'label'=>'RESIN FINISH / Shift',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesPrinting*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPrintingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>11])
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
                'label'=>'Panjang Jadi',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    $r = 0;
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPrintingProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>5])
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
                'label'=>'Proses Ulang',
                'value'=>function($data){
                    $resp = '';
                    /* @var $data TrnKartuProsesPrinting*/
                    foreach ($data->kartuProcessPrintingProcesses as $kartuProcessPrintingProcess) {
                        $dataProcess = Json::decode($kartuProcessPrintingProcess->value);
                        if(isset($dataProcess['pengulangan']) && !empty($dataProcess['pengulangan'])){
                            //$headers = [];
                            $attrs = $kartuProcessPrintingProcess->process->attributes;
                            unset($attrs['id']); unset($attrs['order']); unset($attrs['created_at']); unset($attrs['created_by']); unset($attrs['updated_at']); unset($attrs['updated_by']); unset($attrs['max_pengulangan']);
                            foreach ($attrs as $key=>$attr) {
                                if($key === 'nama_proses'){
                                    unset($attrs['nama_proses']);
                                    $resp .= '<strong>'.$attr.'</strong>'.'<br>';
                                }
                            }

                            foreach ($dataProcess['pengulangan'] as $pengulangan) {
                                $resp .= $pengulangan['time'].'<br>';
                                $resp .= $pengulangan['memo'].'<br>';
                                $resp .= '------------'.'<br>';
                            }
                            $resp .= '<br>';
                        }
                    }

                    return $resp;
                },
                'format'=>'html'
            ],
            [
                'label'=>'Pack',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPrinting*/
                    return $data->approved_at;
                },
                'format'=>'datetime'
            ],
        ],
    ]); ?>
</div>
