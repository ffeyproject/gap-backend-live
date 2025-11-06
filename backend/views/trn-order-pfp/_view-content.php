<?php

use common\models\ar\TrnOrderPfpQtyLog;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\ar\TrnStockGreige;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnOrderPfp */
?>

<div class="box">
    <div class="box-header with-border">
        <!--<h3 class="box-title"><strong></strong></h3>
        <div class="box-tools pull-right"></div>-->
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        [
                            'label'=>'Greige Group',
                            'value'=>$model->greigeGroup->nama_kain
                        ],
                        [
                            'label'=>'Handling',
                            'value'=>$model->handling->name
                        ],
                        [
                            'label'=>'Greige',
                            'value'=>$model->greige->nama_kain
                        ],
                        'no_urut',
                        'no',
                        'qty:decimal',
                        'note:ntext',
                        [
                            'label'=>'Status',
                            'value'=>$model::statusOptions()[$model->status]
                        ],
                        'dasar_warna',
                        [
                            'attribute'=>'proses_sampai',
                            'value'=>$model->proses_sampai != null ? $model::prosesSampaiOptions()[$model->proses_sampai] : null
                        ],
                        [   
                            'attribute'=>'validasi_stock',
                            'value'=>$model->validasi_stock != true ? "YA" : "TIDAK"
                        ],
                        [   
                            'attribute'=>'jenis_gudang',
                            'value'=>$model->jenis_gudang != null ? TrnStockGreige::jenisGudangOptions()[$model->jenis_gudang] : "YA"
                        ],
                    ],
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'date:date',
                        'created_at:datetime',
                        [
                            'label'=>'Dibuat Oleh',
                            'value'=>$model->createdBy->full_name
                        ],
                        'updated_at:datetime',
                        [
                            'label'=>'Diubah Oleh',
                            'value'=>$model->updatedBy->full_name
                        ],
                        'approved_at:datetime',
                        [
                            'attribute'=>'approved_by',
                            'value'=>$model->approvedBy->full_name
                        ],
                        'approved_at:datetime',
                        'approval_note',
                        'batal_at:datetime',
                        [
                            'attribute'=>'batal_by',
                            'value' => $model->rejectBy ? $model->rejectBy->full_name : $model->batal_by

                        ],
                        'batal_note',
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>

<?php if($model->status != $model::STATUS_APPROVED): ?>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Stock Grige Didalam Group "<?=$model->greigeGroup->nama_kain?>"</h3>
        <div class="box-tools pull-right">
            <span class="label label-info">Satuan: <?=$model->greigeGroup->unitName?></span>
        </div>
    </div>
    <div class="box-body">
        <p>
            Menampilkan stok greige didalam group "<?=$model->greigeGroup->nama_kain?>" di semua gudang, untuk membantu
            anda mengambil keputusan memposting order PFP ini.
        </p>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th rowspan="2" style="vertical-align: middle;">NO</th>
                    <th rowspan="2" style="vertical-align: middle;">GREIGE</th>
                    <th colspan="3" class="text-center">FRESH</th>
                    <th colspan="3" class="text-center">WIP</th>
                    <th colspan="3" class="text-center">PFP</th>
                    <th colspan="3" class="text-center">EX FINISH</th>
                </tr>
                <tr>
                    <th class="text-right">STOCK</th>
                    <th class="text-right">BOOKED</th>
                    <th class="text-right">AVAILABLE</th>

                    <th class="text-right">STOCK</th>
                    <th class="text-right">BOOKED</th>
                    <th class="text-right">AVAILABLE</th>

                    <th class="text-right">STOCK</th>
                    <th class="text-right">BOOKED</th>
                    <th class="text-right">AVAILABLE</th>

                    <th class="text-right">STOCK</th>
                    <th class="text-right">BOOKED</th>
                    <th class="text-right">AVAILABLE</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $model->greigeGroup->mstGreiges as $i=>$greige):?>
                <?php
                $stockFresh = $greige->stock;
                $bookedFresh = $greige->booked;
                $availableFresh = $greige->available;

                $stockWip = $greige->stock_wip;
                $bookedWip = $greige->booked_wip;
                $availableWip = $stockWip - $bookedWip;

                $stockPfp = $greige->stock_pfp;
                $bookedPfp = $greige->booked_pfp;
                $availablePfp = $stockPfp - $bookedPfp;

                $stockEf = $greige->stock_ef;
                $bookedEf = $greige->booked_ef;
                $availableEf = $stockEf - $bookedEf;
                ?>
                <tr>
                    <td><?=$i+1?></td>
                    <td><?=$greige->nama_kain?></td>
                    <td class="text-right"><?=Yii::$app->formatter->asDecimal($stockFresh)?></td>
                    <td class="text-right"><?=Yii::$app->formatter->asDecimal($bookedFresh)?></td>
                    <td class="text-right"><?=Yii::$app->formatter->asDecimal($availableFresh)?></td>

                    <td class="text-right"><?=Yii::$app->formatter->asDecimal($stockWip)?></td>
                    <td class="text-right"><?=Yii::$app->formatter->asDecimal($bookedWip)?></td>
                    <td class="text-right"><?=Yii::$app->formatter->asDecimal($availableWip)?></td>

                    <td class="text-right"><?=Yii::$app->formatter->asDecimal($stockPfp)?></td>
                    <td class="text-right"><?=Yii::$app->formatter->asDecimal($bookedPfp)?></td>
                    <td class="text-right"><?=Yii::$app->formatter->asDecimal($availablePfp)?></td>

                    <td class="text-right"><?=Yii::$app->formatter->asDecimal($stockEf)?></td>
                    <td class="text-right"><?=Yii::$app->formatter->asDecimal($bookedEf)?></td>
                    <td class="text-right"><?=Yii::$app->formatter->asDecimal($availableEf)?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="box-footer"></div>
</div>

<?php else:?>
<?= $this->render('_view-print', ['model'=>$model])?>
<?php endif;?>
<!-- RIWAYAT TAMBAH & KURANG QTY -->
<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Riwayat Perubahan Qty Batch</h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-sm table-striped">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>User</th>
                    <th class="text-right">Qty (Batch)</th>
                    <th class="text-right">Total Greige</th>
                    <th>Alasan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $logs = TrnOrderPfpQtyLog::find()
                    ->where(['order_pfp_id' => $model->id])
                    ->orderBy(['created_at' => SORT_DESC])
                    ->all();

                if ($logs):
                    foreach ($logs as $log):
                        $isMinus = $log->qty_tambah < 0;
                        $rowClass = $isMinus ? 'danger' : 'success';
                ?>
                <tr class="<?= $rowClass ?>">
                    <td><?= Yii::$app->formatter->asDatetime($log->created_at) ?></td>
                    <td><?= Html::encode($log->user->username ?? 'Unknown') ?></td>
                    <td class="text-right">
                        <?= $isMinus ? '(' . Yii::$app->formatter->asDecimal(abs($log->qty_tambah)) . ')' : Yii::$app->formatter->asDecimal($log->qty_tambah) ?>
                    </td>
                    <td class="text-right">
                        <?= $isMinus ? '(' . Yii::$app->formatter->asDecimal(abs($log->total_meter)) . ')' : Yii::$app->formatter->asDecimal($log->total_meter) ?>
                    </td>
                    <td><?= Html::encode($log->keterangan) ?></td>
                </tr>
                <?php
                    endforeach;
                else:
                ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">Belum ada riwayat perubahan qty.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <small><i>Keterangan: <span class="text-success">Hijau = Penambahan</span>, <span class="text-danger">Merah =
                    Pengurangan</span></i></small>
    </div>
</div>