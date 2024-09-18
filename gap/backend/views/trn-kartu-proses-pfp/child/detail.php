<?php

use common\models\ar\MstHandling;
use common\models\ar\TrnKartuProsesPfp;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesPfp */

$panjangTotal = $model->getTrnKartuProsesPfpItems()->sum('panjang_m');
$panjangTotal = $panjangTotal === null ? 0 : $panjangTotal;
$jumlahRoll = $model->getTrnKartuProsesPfpItems()->count('id');
$jumlahRoll = $jumlahRoll === null ? 0 : $jumlahRoll;

/* @var $handling MstHandling*/
$handling = MstHandling::find()->where(['greige_id'=>$model->greige_id, 'name'=>$model->handling])->one();
?>

<div class="row">
    <div class="col-md-3">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">CATATAN PROSES</h3>
            </div>
            <div class="box-body">
                <p id="CatatanProsesValue"><?=$model->note?></p>

                <hr>

                <div class="row">
                    <div class="col-md-9">Panjang: <?=Yii::$app->formatter->asDecimal($panjangTotal)?>M</div>

                    <div class="col-md-3"><?=Yii::$app->formatter->asDecimal($jumlahRoll)?> PCS</div>
                </div>

                <hr>

                <p>Berat : <?=$model->berat?></p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">KONSTRUKSI GREIGE</h3>
            </div>
            <div class="box-body">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'lebar',
                        'lusi',
                        'pakan',
                        [
                            'attribute'=>'k_density_lusi',
                            'label'=>'Density Lusi',
                        ],
                        [
                            'attribute'=>'k_density_pakan',
                            'label'=>'Density Pakan',
                        ],
                        'gramasi'
                    ],
                ]) ?>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">TARGET HASIL JADI</h3>
            </div>
            <div class="box-body">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'lebar_preset',
                        'lebar_finish',
                        'berat_finish',
                        [
                            'attribute'=>'t_density_lusi',
                            'label'=>'Densiti Lusi'
                        ],
                        [
                            'attribute'=>'t_density_pakan',
                            'label'=>'Densiti Pakan'
                        ],
                        [
                            'label'=>'Handling',
                            'value'=>$model->handling
                        ],
                        [
                            'label'=>'Ket. Washing',
                            'value'=>function($data) use($handling){
                                /* @var $data TrnKartuProsesPfp*/
                                if($handling !== null){
                                    return $handling->ket_washing;
                                }
                                return '';
                            },
                            'format'=>'boolean'
                        ],
                        [
                            'label'=>'Ket. WR',
                            'value'=>function($data) use($handling){
                                /* @var $data TrnKartuProsesPfp*/
                                if($handling !== null){
                                    return $handling->ket_wr;
                                }
                                return '';
                            },
                            'format'=>'boolean'
                        ],
                        [
                            'label'=>'Keterangan',
                            'value'=>function($data){
                                /* @var $data TrnKartuProsesPfp*/
                                return $data->note;
                            }
                        ]
                    ],
                ]) ?>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="box">
            <div class="box-body">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                            'label'=>'Motif Kain',
                            'value'=>$model->greige->nama_kain
                        ],
                        [
                            'label'=>'Order Date',
                            'value'=>$model->orderPfp->date,
                            'format'=>'date'
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><strong>DETAIL</strong></h3>
        <div class="box-tools pull-right"><strong></strong></div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        [
                            'label'=>'Nomor Order PFP',
                            'value'=>Html::a($model->orderPfp->no, ['/trn-order-pfp/view', 'id'=>$model->order_pfp_id], ['title'=>'Detail Order PFP', 'target'=>'blank']),
                            'format'=>'raw'
                        ],
                        [
                            'label'=>'Greige Group',
                            'value'=>$model->greigeGroup->nama_kain,
                        ],
                        [
                            'label'=>'Greige',
                            'value'=>$model->greige->nama_kain,
                        ],
                        'no_urut',
                        'no',
                        //'no_proses',
                        [
                            'attribute'=>'asal_greige',
                            'value'=>TrnStockGreige::asalGreigeOptions()[$model->asal_greige],
                        ],
                        'date:date',
                        'no_limit_item:boolean',
                        [
                            'attribute'=>'status',
                            'value'=>$model::statusOptions()[$model->status]
                        ],
                        'nomor_kartu',
                    ],
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        //'note:ntext',
                        'posted_at:datetime',
                        'approved_at:datetime',
                        'approved_by',
                        'created_at:datetime',
                        'created_by',
                        'updated_at:datetime',
                        'updated_by',
                        'delivered_at:datetime',
                        'delivered_by',
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>
