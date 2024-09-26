<?php
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use common\models\ar\MstProcessDyeing;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rekap Dyeing Siap Kirim';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kartu-proses-dyeing-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsiveWrap' => false,
        'panel' => [
            'type' => 'default',
            'before'=>Html::tag(
                'div',
                Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['siap-kirim'], ['class' => 'btn btn-default']).
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success']),
                ['class'=>'btn-group', 'role'=>'group']
            ),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/processing-dyeing/view/', 'id' => $model->id], [
                            'title' => Yii::t('yii', 'View'),
                            'data-pjax' => '0',
                            'target' => '_blank',
                        ]);
                    },
                ],
            ],

            'id',
            //'wo_id',
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
                'attribute'=>'woNo',
                'label'=>'Nomor WO',
                'value'=>'wo.no'
            ],
            'nomor_kartu',
            'no',
            'lusi',
            'pakan',
            //'note:ntext',
            [
                'attribute' => 'dateRange',
                'label' => 'TANGGAL KARTU',
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
                'attribute' => 'openDateRange',
                'label' => 'TANGGAL BUKA',
                'value' => 'tanggalKartuProcessDyeingProcess',
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
                'label'=>'Tanggal Siap Kirim',
                'value'=> function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                    $tanggalBuka = $data->tanggalKartuProcessDyeingProcess;
                    //add 14 days after $tanggalBuka
                    if($tanggalBuka !== null){
                        $newDate = date('j F Y', strtotime($tanggalBuka . ' + 14 days'));
                        return $newDate;
                    }
                    return null;
                },
                'format' => 'raw',
            ],
            [
                'label'=>'Terakhir Proses',
                'value'=> function($data){
                    /* @var $data TrnKartuProsesDyeing*/
                   $process_id = $data->latestKartuProcessDyeingProcess['process_id'];
                   $tanggal = $data->latestKartuProcessDyeingProcess['tanggal'];
                    if($process_id !== null || $tanggal !== null){
                        $proses = MstProcessDyeing::find()->where(['id' => $process_id])->one()->nama_proses;
                        $tgl = date('j F Y', strtotime($tanggal));
                        return $proses.' - '.$tgl;
                    }
                    return null;
                },
                'format' => 'raw',
            ],
            // [
            //     'label'=>'Panjang',
            //     'value'=>function($data){
            //         // /* @var $data TrnKartuProsesDyeing*/
            //         // $totalPanjang = 0;
            //         // foreach ($data->trnKartuProsesDyeingItems as $trnKartuProsesDyeingItem) {
            //         //     $stockGreige = $trnKartuProsesDyeingItem->stock->toArray();
            //         //     $totalPanjang += $stockGreige['panjang_m'];
            //         // }
            //         $panjangTotal = $data->getTrnKartuProsesDyeingItems()->sum('panjang_m');
            //         $panjangTotal = $panjangTotal === null ? 0 : $panjangTotal;
            //         return $panjangTotal;
            //     },
            //     'format'=>'decimal',
            //     'pageSummary' => true,
            //     'hAlign' => 'right'
            // ],
            //'created_at:datetime',
            //'created_by',
            //'updated_at:datetime',
            //'updated_by',
        ],
        'rowOptions' => function($model) {
            // Get the current date
            $currentDate = new \DateTime();
            // Get the tanggalKartuProcessDyeingProcess date
            $tanggalBuka = $model->tanggalKartuProcessDyeingProcess;
            if ($tanggalBuka !== null) {
                // Add 14 days to the tanggalKartuProcessDyeingProcess date
                $targetDate = new \DateTime($tanggalBuka);
                $targetDate->modify('+14 days');
    
                // Get the date 3 days before the target date
                $yellowDate = clone $targetDate;
                $yellowDate->modify('-3 days');
    
                // Compare dates
                if ($currentDate >= $targetDate) {
                    // If current date is greater than or equal to target date, make the row red
                    return ['style' => 'background-color:red; color:yellow'];
                } elseif ($currentDate >= $yellowDate && $currentDate < $targetDate) {
                    // If current date is within 3 days of the target date, make the row yellow
                    return ['style' => 'background-color:yellow'];
                }
            }
    
            // Default row style
            return [];
        },
    ]); ?>


</div>
