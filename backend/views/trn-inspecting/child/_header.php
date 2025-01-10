<?php

use common\models\ar\{ MstGreige, MstGreigeGroup, TrnInspecting, TrnScGreige };
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnInspecting */
/* @var $greige MstGreige */
/* @var $kartuProses */
/* @var $kartuProsesUrl string */
?>

<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-md-4">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'no',
                        'date:date',
                        [
                            'label'=>'No. WO',
                            'value'=>Html::a($model->wo->no, ['trn-wo/view', 'id'=>$model->wo->id], ['title'=>'Detail WO', 'target'=>'_blank']).
                                ' '.
                                Html::a('ganti', ['ganti-wo', 'id' => $model->id], [
                                    'class' => 'label label-default',
                                    'onclick' => 'gantiWo(event);',
                                    'title' => 'Ganti WO Kartu Proses: '.$model->id
                                ]),
                            'format'=>'raw'
                        ],
                        [
                            'label'=>'No. Kartu',
                            'value'=>Html::a($kartuProses->no, [$kartuProsesUrl.'/view', 'id'=>$kartuProses->id], ['title'=>'Detail Kartu Proses', 'target'=>'_blank']),
                            'format'=>'raw'
                        ],
                        /*[
                            'label'=>'Buyer',
                            'value'=>Html::a($cust->name, ['mst-customer/view', 'id'=>$cust->id], ['title'=>'Detail Buyer', 'target'=>'_blank']),
                            'format'=>'raw'
                        ],*/
                        [
                            'label'=>'Unit',
                            'value'=>MstGreigeGroup::unitOptions()[$model->unit],
                        ],
                    ],
                ]) ?>
            </div>

            <div class="col-md-4">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'tanggal_inspeksi:date',
                        'no_lot',
                        [
                            'label'=>'Motif',
                            'value'=>Html::a($greige->nama_kain, ['mst-greige/view', 'id'=>$greige->id], ['title'=>'Detail Greige', 'target'=>'_blank']),
                            'format'=>'raw'
                        ],
                        [
                            'attribute'=>'jenis_proses',
                            'value'=>TrnScGreige::processOptions()[$model->jenis_process],
                            'format'=>'raw'
                        ],
                        [
                            'attribute'=>'k3l_code',
                            'value'=>$model->k3l_code,
                            'format'=>'raw'
                        ]
                        /*[
                            'label'=>'Design',
                            'value'=>Html::a($mo->design, ['trn-mo/view', 'id'=>$mo->id], ['title'=>'Detail MO', 'target'=>'_blank']),
                            'format'=>'raw'
                        ],
                        [
                            'label'=>'Orientasi',
                            'value'=>Html::a($sc::tipeKontrakOptions()[$sc->tipe_kontrak], ['trn-sc/view', 'id'=>$mo->id], ['title'=>'Detail SC', 'target'=>'_blank']),
                            'format'=>'raw'
                        ],*/
                    ],
                ]) ?>
            </div>

            <div class="col-md-4">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                            'attribute'=>'kombinasi',
                            'value'=>$model->kombinasi. ' '. Html::a(' ganti', ['ganti-warna', 'id' => $model->id], [
                                'class' => 'label label-default',
                                'onclick' => 'gantiWarna(event);',
                                'title' => 'Ganti Warna Kartu Proses: '.$model->id
                            ]),
                            'format'=>'raw'
                        ],
                        /*[
                            'label'=>'Stamping',
                            'value'=>Html::a($mo->face_stamping, ['trn-mo/view', 'id'=>$cust->id], ['title'=>'Detail MO', 'target'=>'_blank']),
                            'format'=>'raw'
                        ],
                        [
                            'label'=>'Piece Length',
                            'value'=>Html::a($mo->piece_length, ['trn-mo/view', 'id'=>$cust->id], ['title'=>'Detail MO', 'target'=>'_blank']),
                            'format'=>'raw'
                        ],
                        [
                            'label'=>'Jenis Order',
                            'value'=>Html::a($scGreigeGroup::processOptions()[$scGreigeGroup->process], ['trn-sc/view', 'id'=>$sc->id], ['title'=>'Detail SC', 'target'=>'_blank']),
                            'format'=>'raw'
                        ],*/
                        [
                            'attribute'=>'status',
                            'value'=>TrnInspecting::statusOptions()[$model->status],
                        ],
                        [
                            'attribute'=>'Jenis Inspek',
                            'value'=>TrnInspecting::jenisInspeksiOptions()[$model->jenis_inspek],
                        ],
                        [
                            'attribute'=>'Kode Defect',
                            'value'=>$model->defect,
                            'format'=>'raw'
                        ],
                        'created_at:datetime',
                        [
                            'label'=>'Created By',
                            'value'=>$model->createdBy->full_name,
                            'format'=>'raw'
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>