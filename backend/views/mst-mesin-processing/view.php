<?php

use kartik\dialog\Dialog;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use common\models\ar\MstGreige;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstMesinProcessing */

$this->title = $model->nama_mesin;
$this->params['breadcrumbs'][] = ['label' => 'Mesin Processing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

// Get already forbidden greige IDs to exclude them from dropdown
$alreadyForbiddenIds = ArrayHelper::getColumn($model->forbiddenGreiges, 'greige_id');
$availableGreiges = MstGreige::find()
    ->where(['not in', 'id', $alreadyForbiddenIds])
    ->andWhere(['aktif' => true])
    ->orderBy('nama_kain')
    ->all();
$greigesData = ArrayHelper::map($availableGreiges, 'id', 'nama_kain');
?>

<div class="mst-mesin-processing-view">

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-md-5">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Detail Mesin</h3>
                </div>
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'nama_mesin',
                            'jenis_mesin',
                            'jenis_nozzle',
                            'ukuran_nozzle',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-ban text-danger"></i> Greige yang Tidak Boleh Digunakan</h3>
                </div>
                <div class="box-body">
                    
                    <!-- Form Multi-Insert Forbidden Greiges -->
                    <div class="well">
                        <h4>Tambah Greige Terlarang</h4>
                        <?= Html::beginForm(['view', 'id' => $model->id], 'post') ?>
                        <div class="form-group">
                            <?= Select2::widget([
                                'name' => 'forbidden_greige_ids',
                                'data' => $greigesData,
                                'options' => [
                                    'placeholder' => 'Pilih satu atau beberapa kain greige...',
                                    'multiple' => true,
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                            ]) ?>
                        </div>
                        <div class="form-group">
                            <?= Html::submitButton('<i class="glyphicon glyphicon-plus"></i> Tambahkan ke Daftar Terlarang', ['class' => 'btn btn-danger btn-block']) ?>
                        </div>
                        <?= Html::endForm() ?>
                    </div>

                    <!-- Daftar Greige Terlarang -->
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr class="danger">
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Nama Kain Greige</th>
                                <th>Alias</th>
                                <th style="width: 80px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($model->forbiddenGreiges)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Semua kain greige diperbolehkan untuk mesin ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($model->forbiddenGreiges as $forbidden): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><?= Html::encode($forbidden->greige->nama_kain) ?></td>
                                        <td><?= Html::encode($forbidden->greige->alias) ?></td>
                                        <td class="text-center">
                                            <?= Html::a('<i class="glyphicon glyphicon-trash"></i>', ['delete-forbidden-greige', 'id' => $forbidden->id], [
                                                'class' => 'btn btn-danger btn-xs',
                                                'title' => 'Hapus dari daftar terlarang',
                                                'data-method' => 'post',
                                                'data-confirm' => 'Yakin ingin memperbolehkan kain greige ini kembali pada mesin?',
                                            ]) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

</div>
