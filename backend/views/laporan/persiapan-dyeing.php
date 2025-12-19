<?php
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Laporan Persiapan Dyeing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kartu-proses-dyeing-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'showPageSummary'=>true,
        'panel' => [
            'type' => 'default',
            'before'=>Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['persiapan-dyeing'], ['class' => 'btn btn-default']).
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success']),
                ['class'=>'btn-group', 'role'=>'group']
            ),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            //['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            // [
            //     'label' => 'Tanggal',
            //     'value' => function($data){
            //         /* @var $data TrnKartuProsesDyeing*/
            //         try {
            //             if($data->tanggalKartuProcessDyeingProcess == null){
            //                 return '-';
            //             }
            //             return $data->tanggalKartuProcessDyeingProcess;
            //         }catch (Throwable $t){
            //             return null;
            //         }
            //     },
            // ],
            [
                'attribute' => 'openDateRange',
                'label' => 'TANGGAL',
                'value' => function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    try {
                        return $data->tanggalKartuProcessDyeingProcess;
                    }catch (Throwable $t){
                        return null;
                    }
                },
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
                'attribute'=>'woNo',
                'label'=>'Nomor WO',
                'value'=>'wo.no'
            ],
             [
                'label' => 'Motif',
                'value' => function ($data) {
                    /* @var $data TrnKartuProsesDyeing */

                    $lusi  = $data->lusi ?? '';
                    $motif = $data->wo->greigeNamaKain ?? '';
                    $pakan = $data->pakan ?? '';

                    return trim($lusi . ' ' . $motif . ' ' . $pakan);
                }
            ],
            [
                'label' => 'Warna',
                'value' => function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return $data->woColor->moColor->color;
                }
            ],
            'nomor_kartu',
            [
                'label' => 'Panjang',
                'value' => function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    $panjangTotal = $data->getTrnKartuProsesDyeingItems()->sum('panjang_m');
                    return $panjangTotal === null ? 0 : $panjangTotal;
                },
                'format' => 'decimal',
                'pageSummary' => true
            ],
            [
                'label' => 'Berat',
                'value' => function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    return $data->berat;
                },
                //'format' => 'decimal'
            ],
             [
                'label' => 'Gul',
                'value' => function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    $jumlahRoll = $data->getTrnKartuProsesDyeingItems()->count('id');
                    return $jumlahRoll === null ? 0 : $jumlahRoll;
                },
                'format' => 'decimal',
                'pageSummary' => true
            ],
            [
                'attribute' => 'shift', // ✅ WAJIB
                'label' => 'Shift',
                'value' => function ($data) {
                    $model = $data->getKartuProcessDyeingProcesses()
                        ->where(['process_id' => 1])
                        ->one();

                    if ($model !== null) {
                        try {
                            $json = \yii\helpers\Json::decode($model->value);
                            return $json['shift_group'] ?? '-';
                        } catch (\Throwable $t) {
                            return '-';
                        }
                    }
                    return '-';
                }
            ],
            [
                'label' => 'MC',
                'value' => function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    $model = $data->getKartuProcessDyeingProcesses()->where(['process_id'=>1])->one();
                    if($model !== null){
                        try {
                            $model = \yii\helpers\Json::decode($model['value']);
                            return $model['no_mesin'];
                        }catch (Throwable $t){
                            return '-';
                        }
                    }

                    return '-';
                }
            ],
             'delivered_at:datetime',
        ],
    ]); ?>


</div>