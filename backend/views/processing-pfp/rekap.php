<?php
use common\models\ar\TrnKartuProsesPfp;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesPfpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rekap Processing PFP';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kartu-proses-dyeing-index">
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
                'label'=>'No. PFP',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPfp*/
                    return $data->orderPfp->no;
                },
            ],
            [
                'label'=>'Qty',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPfp*/
                    return $data->orderPfp->qty;
                },
                'format' => 'decimal',
            ],
            [
                'label'=>'Dasar PFP',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPfp*/
                    return $data->orderPfp->dasar_warna;
                },
            ],
            [
                'label'=>'Hand',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPfp*/
                    return $data->orderPfp->handling->name;
                },
            ],
            [
                'label'=>'Note',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPfp*/
                    return $data->note;
                },
            ],
            [
                'label'=>'NK',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPfp*/
                    return $data->no;
                },
            ],
            [
                'label'=>'Panjang Greige',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPfp*/
                    $panjangTotal = $data->getTrnKartuProsesPfpItems()->sum('panjang_m');
                    return $panjangTotal === null ? 0 : $panjangTotal;
                },
                'format'=>'decimal'
            ],
            [
                'label'=>'Berat Greige',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPfp*/
                    return $data->berat;
                },
                //'format'=>'decimal'
            ],
            [
                'label'=>'Pcs',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPfp*/
                    return $data->getTrnKartuProsesPfpItems()->count();
                },
                'format'=>'decimal'
            ],
            [
                'label'=>'Tgl. Buka / Shift',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesPfp*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPfpProcess::tableName())
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
                'label'=>'SCOURING / Shift',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesPfp*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPfpProcess::tableName())
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

                    /* @var $data TrnKartuProsesPfp*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPfpProcess::tableName())
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
                'label'=>'Scutcher / Shift',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesPfp*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPfpProcess::tableName())
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

                    /* @var $data TrnKartuProsesPfp*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPfpProcess::tableName())
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

                    /* @var $data TrnKartuProsesPfp*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPfpProcess::tableName())
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
                'label'=>'W.HIRANO / Shift',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesPfp*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPfpProcess::tableName())
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
                'label'=>'HEAT SETT / STENTER NO / Shift',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesPfp*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPfpProcess::tableName())
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
                'label'=>'CUCI WR / Shift',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesPfp*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPfpProcess::tableName())
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
                'label'=>'GREIGE SET / Shift',
                'value'=>function($data){
                    $tg = '-';
                    $sh = '-';

                    /* @var $data TrnKartuProsesPfp*/
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPfpProcess::tableName())
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
                'label'=>'Panjang Jadi',
                'value'=>function($data){
                    /* @var $data TrnKartuProsesPfp*/
                    $r = 0;
                    /*$pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessPfpProcess::tableName())
                        ->where(['kartu_process_id'=>$data->id, 'process_id'=>11])
                        ->one()
                    ;
                    if($pc !== false){
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if(isset($v['panjang_jadi'])){
                            $r = $v['panjang_jadi'];
                        }
                    }*/

                    return $r;
                },
            ],
            [
                'label'=>'Proses Ulang',
                'value'=>function($data){
                    $resp = '';
                    /* @var $data TrnKartuProsesPfp*/
                    foreach ($data->kartuProcessPfpProcesses as $kpps) {
                        $dataProcess = Json::decode($kpps->value);
                        if(isset($dataProcess['pengulangan']) && !empty($dataProcess['pengulangan'])){
                            //$headers = [];
                            $attrs = $kpps->process->attributes;
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
        ],
    ]); ?>
</div>
