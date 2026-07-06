<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\ar\TrnGudangJadi;
use common\models\ar\MstGreigeGroup;

/* @var $this yii\web\View */
/* @var $models common\models\ar\TrnGudangJadi[] */
/* @var $ids array */
/* @var $isSuccess boolean */
/* @var $postedData array */

$isSuccess = $isSuccess ?? false;
$postedData = $postedData ?? [];

$this->title = 'Form Mutasi Ke Processing';
$this->params['breadcrumbs'][] = ['label' => 'Gudang Jadi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$jenisGudangOptions = TrnGudangJadi::jenisGudangOptions();
$sourceOptions = TrnGudangJadi::sourceOptions();
$unitOptions = MstGreigeGroup::unitOptions();

?>
<style>
    @media print {
        .no-print, .no-print * {
            display: none !important;
        }
        .main-header, .main-sidebar, .left-side, .main-footer, .content-header, .control-sidebar, h4 {
            display: none !important;
        }
        .content-wrapper, .right-side, .main-footer {
            margin-left: 0 !important;
            padding: 0 !important;
            min-height: auto !important;
        }
        body {
            background-color: white !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .box {
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .box-header {
            display: none !important;
        }
    }
</style>

<div class="trn-gudang-jadi-mutasi-processing">

    <div class="box box-primary">
        <div class="box-header with-border no-print">
            <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
        </div>

        <form action="<?= Url::to(['mutasi-processing']) ?>" method="POST">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
            
            <?php foreach ($ids as $id): ?>
                <input type="hidden" name="data[ids][]" value="<?= $id ?>" />
            <?php endforeach; ?>

            <div class="box-body">
                <div class="row no-print">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="noRef">No Referensi</label>
                            <input type="text" class="form-control" id="noRef" name="data[ref]" value="<?= Html::encode($postedData['ref'] ?? '') ?>" <?= $isSuccess ? 'readonly' : 'required' ?>>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="pemohon">Pemohon</label>
                            <input type="text" class="form-control" id="pemohon" name="data[pemohon]" value="<?= Html::encode($postedData['pemohon'] ?? '') ?>" <?= $isSuccess ? 'readonly' : 'required' ?>>
                        </div>
                    </div>
                </div>
                <div class="row no-print">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Note:</label>
                            <textarea class="form-control" rows="3" name="data[note]" <?= $isSuccess ? 'readonly' : '' ?>><?= Html::encode($postedData['note'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <h4>Detail Item Mutasi</h4>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Jenis Gudang</th>
                                    <th>Marketing</th>
                                    <th>Buyer</th>
                                    <th>No. SC</th>
                                    <th>No. WO</th>
                                    <th>Color</th>
                                    <th>Source</th>
                                    <th>Source Ref.</th>
                                    <th>Unit</th>
                                    <th>Qty</th>
                                    <th>Grade</th>
                                    <th>Motif</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($models as $i => $model): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= $model->id ?></td>
                                    <td><?= $jenisGudangOptions[$model->jenis_gudang] ?? $model->jenis_gudang ?></td>
                                    <td><?= $model->wo->mo->scGreige->sc->marketing->full_name ?? '-' ?></td>
                                    <td><?= $model->wo->mo->scGreige->sc->customerName ?? '-' ?></td>
                                    <td><?= $model->wo->mo->scGreige->sc->no ?? '-' ?></td>
                                    <td><?= $model->wo->no ?? '-' ?></td>
                                    <td><?= $model->color ?></td>
                                    <td><?= $sourceOptions[$model->source] ?? $model->source ?></td>
                                    <td><?= $model->source_ref ?></td>
                                    <td><?= $unitOptions[$model->unit] ?? $model->unit ?></td>
                                    <td><?= Yii::$app->formatter->asDecimal($model->qty) ?></td>
                                    <td><?= $model->gradeName ?></td>
                                    <td><?= $model->wo->greigeNamaKain ?? '-' ?></td>
                                    <td><?= $model->date ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="box-footer no-print">
                <?php if (!$isSuccess): ?>
                    <button type="submit" class="btn btn-primary">Submit</button>
                <?php endif; ?>
                <button type="button" class="btn btn-info" onclick="window.print(); return false;"><i class="fa fa-print"></i> Print</button>
                <?= Html::a('Batal', ['index'], ['class' => 'btn btn-default']) ?>
            </div>
        </form>
    </div>
</div>
