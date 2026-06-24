<?php

use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnInspecting;
use common\models\ar\TrnInspectingSearch;
use common\models\ar\TrnScGreige;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel TrnInspectingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Penerimaan Packing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inspecting-index">
    <p>
        <?= Html::button('<i class="fa fa-refresh"></i> Sync Data Stuck', [
            'class' => 'btn btn-warning',
            'title' => 'Perbaiki data yang sudah di-receive semua tapi masih muncul',
            'data-toggle' => 'modal',
            'data-target' => '#syncModal'
        ]) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'before'=>Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['index'], ['class' => 'btn btn-default']),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar'=>false,
        'showPageSummary'=>true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            ['class' => 'kartik\grid\ActionColumn', 'template'=>'{view}'],

            //'id',
            [
                'attribute' => 'dateRange',
                'label' => 'Tanggal Kirim',
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
            'no',
            //'wo_id',
            'woNo',
            'kombinasi',
            [
                'label' => 'Warna',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    return '-';
                }
            ],
            [
                'label' => 'No. Design',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    return $data->mo->design;
                }
            ],
            [
                'label' => 'Article',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    return $data->mo->article;
                }
            ],
            [
                'attribute'=>'jenis_process',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    if($data->kartu_process_dyeing_id !== null){
                        $scGreige = $data->kartuProcessDyeing->scGreige;
                        return $scGreige::processOptions()[$scGreige->process];
                    }else if($data->kartu_process_printing_id !== null){
                        $scGreige = $data->kartuProcessPrinting->scGreige;
                        return $scGreige::processOptions()[$scGreige->process];
                    }else if($data->memo_repair_id !== null){
                        $scGreige = $data->memoRepair->scGreige;
                        return $scGreige::processOptions()[$scGreige->process];
                    }
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TrnScGreige::processOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            'kpdNo',
            'kppNo',
            'memoRepairNo',
            [
                'attribute' => 'no_lot',
                'label' => 'No. Lot',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    return $data->no_lot;
                }
            ],
            [
                'label' => 'Total Qty',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    $qTotal = (new \yii\db\Query())->from(\common\models\ar\InspectingItem::tableName())
                        ->where(['inspecting_id'=>$data->id])
                        ->sum('qty');
                    return $qTotal > 0 ? $qTotal : 0;
                },
                'format' => 'decimal'
            ],
            //'process_dyeing_id',
            //'process_printing_id',
            //'no_urut',
            [
                'label' => 'Satuan',
                'value'=>function($data){
                    /* @var $data TrnInspecting*/
                    return MstGreigeGroup::unitOptions()[$data->unit];
                }
            ],
            //'tanggal_inspeksi',
            //'no_lot',
            //'kombinasi',
            //'note:ntext',
            /*[
                'attribute'=>'status',
                'value'=>function($data){
                    ///* @var $data Inspecting
                    return $data::statusOptions()[$data->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => Inspecting::statusOptions(),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],*/
            //'grade_a_pcs',
            //'grade_b_pcs',
            //'grade_c_pcs',
            //'piece_kecil_pcs',
            //'sample_pcs',
            //'grade_a_roll',
            //'grade_b_roll',
            //'grade_c_roll',
            //'piece_kecil_roll',
            //'sample_roll',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'approved_at',
            //'approved_by',
        ],
    ]); ?>


</div>

<!-- Modal Sync -->
<div class="modal fade" id="syncModal" tabindex="-1" role="dialog" aria-labelledby="syncModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <?php $form = \yii\widgets\ActiveForm::begin(['action' => ['sync-status'], 'method' => 'get']); ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="syncModalLabel">Sync Data Stuck</h4>
      </div>
      <div class="modal-body">
        <p>Proses sinkronisasi akan mengecek semua data yang sudah di-receive namun statusnya masih nyangkut di Penerimaan. Karena proses ini membutuhkan waktu, silakan filter berdasarkan bulan dan tahun.</p>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Bulan</label>
                    <?= Html::dropDownList('bulan', date('m'), [
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ], ['class' => 'form-control']) ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tahun</label>
                    <?= Html::dropDownList('tahun', date('Y'), array_combine(range(date('Y')-5, date('Y')), range(date('Y')-5, date('Y'))), ['class' => 'form-control']) ?>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-warning"><i class="fa fa-refresh"></i> Mulai Sync</button>
      </div>
      <?php \yii\widgets\ActiveForm::end(); ?>
    </div>
  </div>
</div>
