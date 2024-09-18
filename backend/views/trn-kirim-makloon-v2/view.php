<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKirimMakloonV2 */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pengiriman Ke Makloon V2', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-kirim-makloon-v2-view">
    <p>
        <?php if($model->status === $model::STATUS_DRAFT):?>
            <?= Html::a('Ubah', ['update', 'id'=>$model->id], ['class' => 'btn btn-primary']) ?>

            <?= Html::a('Hapus', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Posting', ['posting', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Anda yakin akan memposting item ini?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif;?>

        <?= Html::a('Buat Baru', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="row">
        <div class="col-md-8">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">DETAIL</h3>
                </div>
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            [
                                'label'=>'Nomor SC',
                                'value'=>$model->sc->no
                            ],
                            [
                                'label'=>'Nama Kain',
                                'value'=>$model->wo->greige->nama_kain
                            ],
                            [
                                'label'=>'Vendor',
                                'value'=>$model->vendor->name
                            ],
                            [
                                'label'=>'Nomor MO',
                                'value'=>$model->mo->no
                            ],
                            [
                                'label'=>'Nomor WO',
                                'value'=>$model->wo->no
                            ],
                            'date:date',
                            //'no_urut',
                            'no',
                            [
                                'label'=>'Unit',
                                'value'=>\common\models\ar\MstGreigeGroup::unitOptions()[$model->unit]
                            ],
                            'note:ntext',
                            'status',
                            'created_at:datetime',
                            'created_by',
                            'updated_at:datetime',
                            'updated_by',
                            'penerima',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">ITEMS</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Qty</th>
                            <th>Note</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($model->trnKirimMakloonItems as $i=>$item):?>
                        <tr>
                            <td><?=$i+1?></td>
                            <td><?=Yii::$app->formatter->asDecimal($item->qty)?></td>
                            <td><?=$item->note?></td>
                        </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
