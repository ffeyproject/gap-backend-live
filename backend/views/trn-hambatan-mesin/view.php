<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\dialog\Dialog;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnHambatanMesin */

$this->title = 'Detail Hambatan: Shift ' . $model->shift . ' (' . $model->tanggal . ')';
$this->params['breadcrumbs'][] = ['label' => 'Hambatan Per Mesin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-hambatan-mesin-view">
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Apakah Anda yakin ingin menghapus data hambatan ini?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Tambah Hambatan Baru', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="row">
        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Informasi Shift & Tanggal</h3>
                </div>
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'shift',
                            'tanggal:date',
                            'created_at:datetime',
                            'updated_at:datetime',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Daftar Set Hambatan</h3>
        </div>
        <div class="box-body no-padding">
            <table class="table table-bordered table-striped" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 100px;">Start</th>
                        <th style="width: 100px;">Stop</th>
                        <th style="width: 250px;">Mesin</th>
                        <th>Keterangan</th>
                        <th style="width: 200px;">WO (jika ada)</th>
                        <th style="width: 200px;">NK (jika ada)</th>
                        <th style="width: 250px;">Jenis Hambatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($model->trnHambatanMesinItems as $item): ?>
                        <tr>
                            <td><?= Html::encode($item->start_time) ?></td>
                            <td><?= Html::encode($item->stop_time) ?></td>
                            <td><?= Html::encode($item->mstMesinProses->nama_mesin ?? '-') ?></td>
                            <td><?= Html::encode($item->keterangan ?? '-') ?></td>
                            <td><?= Html::encode($item->no_wo ?? '-') ?></td>
                            <td><?= Html::encode($item->no_kartu ?? '-') ?></td>
                            <td>
                                <?php 
                                    $names = [];
                                    foreach ($item->mstJenisHambatans as $jh) {
                                        $names[] = $jh->nama;
                                    }
                                    echo Html::encode(implode(', ', $names));
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
