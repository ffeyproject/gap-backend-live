<?php

use common\models\ar\MstHandling;
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnKartuProsesDyeing */

$panjangTotal = $model->getTrnKartuProsesDyeingItems()->sum('panjang_m');
$panjangTotal = $panjangTotal === null ? 0 : $panjangTotal;
$jumlahRoll = $model->getTrnKartuProsesDyeingItems()->count('id');
$jumlahRoll = $jumlahRoll === null ? 0 : $jumlahRoll;

$greige = $model->wo->greige;
$greigeGroup = $greige->group;

/* @var $handling MstHandling*/
$handling = MstHandling::find()->where(['greige_id'=>$model->wo->greige_id, 'name'=>$model->handling])->one();
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
                    <div class="col-md-9">Panjang:
                        <?=Yii::$app->formatter->asDecimal($panjangTotal)?><?=$greigeGroup->unitName?></div>

                    <div class="col-md-3"><?=Yii::$app->formatter->asDecimal($jumlahRoll)?> PCS</div>
                </div>

                <hr>

                <p>Berat : <?=$model->berat?></p>
                <p><strong>Tunggu Marketing : </strong><?=$model->tunggu_marketing ? "Ya" : "Tidak"?></p>
                <p><strong>Toping Matching : </strong><?=$model->toping_matching ? "Ya" : "Tidak"?></p>
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
                                /* @var $data TrnKartuProsesDyeing*/
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
                                /* @var $data TrnKartuProsesDyeing*/
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
                                /* @var $data TrnKartuProsesDyeing*/
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
            <div class="box-header with-border">
                <h3 class="box-title">Hasil Tes Gosok</h3>
            </div>
            <div class="box-body">
                <p id="HasilTesGosokValue"><?=$model->hasil_tes_gosok?></p>

                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                            'label'=>'Motif',
                            'value'=>$model->wo->greigeNamaKain
                        ],
                        [
                            'label'=>'No Do',
                            'value'=>$model->wo->no
                        ],
                        [
                            'label'=>'Warna',
                            'value'=>$model->woColor->moColor->color
                        ],
                        [
                            'label'=>'Order Date',
                            'value'=>$model->mo->date,
                            'format'=>'date'
                        ],
                        [
                            'label'=>'Buyer',
                            'value'=>$model->sc->customerName
                            //'value'=>$model->wo->mo->sc->customerName
                        ],
                        [
                            'label'=>'Delivery Date',
                            'value'=>$model->sc->delivery_date,
                            'format'=>'date'
                        ],
                        'nomor_kartu',
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>

<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        //'wo_id',
                        'nomor_kartu',
                        [
                            'label'=>'Nomor WO',
                            'value'=>Html::a($model->wo->no, ['/trn-wo/view', 'id'=>$model->wo_id], ['title'=>'Detail WO', 'target'=>'blank']),
                            'format'=>'raw'
                        ],
                        [
                            'label'=>'Nomor Kartu Proses PG',
                            'value'=>$model->kartu_proses_id !== null ? Html::a($model->kartuProses->no, ['/trn-kartu-proses-dyeing/view', 'id'=>$model->kartu_proses_id], ['title'=>'Detail Kartu Proses PG', 'target'=>'blank']) : null,
                            'format'=>'raw'
                        ],
                        'no',
                        [
                            'attribute'=>'asal_greige',
                            'value'=>TrnStockGreige::asalGreigeOptions()[$model->asal_greige]
                        ],
                        'dikerjakan_oleh',
                        [
                            'attribute'=>'status',
                            'value'=>$model::statusOptions()[$model->status]
                        ],
                        'date:date',
                        'no_limit_item:boolean'
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
                        'created_at:datetime',
                        'created_by',
                        'updated_at:datetime',
                        'updated_by',
                        'delivered_at:datetime',
                        'delivered_by',
                        'approved_at:datetime',
                        'approved_by'
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>