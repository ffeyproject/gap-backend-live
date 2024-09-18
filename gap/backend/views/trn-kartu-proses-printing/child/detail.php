<?php

use common\models\ar\MstHandling;
use common\models\ar\TrnKartuProsesPrinting;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnKartuProsesPrinting */
?>

<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        //'wo_id',
                        [
                            'label'=>'Nomor WO',
                            'value'=>Html::a($model->wo->no, ['/trn-wo/view', 'id'=>$model->wo_id], ['title'=>'Detail WO', 'target'=>'blank']),
                            'format'=>'raw'
                        ],
                        [
                            'label'=>'Nomor Kartu Proses PG',
                            'value'=>$model->kartu_proses_id !== null ? Html::a($model->kartuProses->no, ['/trn-kartu-proses-printing/view', 'id'=>$model->kartu_proses_id], ['title'=>'Detail Kartu Proses PG', 'target'=>'blank']) : null,
                            'format'=>'raw'
                        ],
                        'no_proses',
                        'no',
                        [
                            'attribute'=>'asal_greige',
                            'value'=>TrnStockGreige::asalGreigeOptions()[$model->asal_greige]
                        ],
                        'dikerjakan_oleh',
                        'lusi',
                        'pakan',
                        [
                            'label'=>'Catatan Pross',
                            'value'=>$model->note,
                            'contentOptions'=>['id'=>'CatatanProsesValue']
                        ],
                        [
                            'label'=>'Nomor Design',
                            'value'=>$model->mo->design,
                        ],
                        'kombinasi',
                        'nomor_kartu',
                        //'reject_notes'
                    ],
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                            'label'=>'Color',
                            'value'=>$model->woColor->moColor->color
                        ],
                        [
                            'attribute'=>'status',
                            'value'=>$model::statusOptions()[$model->status]
                        ],
                        'date:date',
                        'created_at:datetime',
                        'created_by',
                        'updated_at:datetime',
                        'updated_by',
                        'delivered_at:datetime',
                        'delivered_by',
                        'approved_at:datetime',
                        'approved_by',
                        'no_limit_item:boolean',
                        [
                            'label'=>'Ket. Washing',
                            'value'=>function($data){
                                /* @var $data TrnKartuProsesPrinting*/
                                return $data->wo->handling->ket_washing;
                            },
                            'format'=>'boolean'
                        ],
                        [
                            'label'=>'Ket. WR',
                            'value'=>function($data){
                                /* @var $data TrnKartuProsesPrinting*/
                                return $data->wo->handling->ket_wr;
                            },
                            'format'=>'boolean'
                        ],
                        [
                            'label'=>'Keterangan',
                            'value'=>function($data){
                                /* @var $data TrnKartuProsesPrinting*/
                                return $data->note;
                            }
                        ]
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>