<?php

use common\models\ar\InspectingDyeingReject;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\InspectingDyeingReject */
/* @var $form yii\widgets\ActiveForm */
/* @var $items common\models\ar\TrnKartuProsesDyeingItem[] */

$items = $items ?? [];
$formatter = Yii::$app->formatter;
$kartuProses = $model->kartuProses;
$nomorKartu = $kartuProses !== null ? $kartuProses->nomor_kartu : '-';
$nomorKartuBaru = $kartuProses !== null ? InspectingDyeingReject::nextNomorKartuReject($kartuProses->nomor_kartu) : '-';
$selectedIds = array_map('strval', (array)$model->selected_items);
$inputName = Html::getInputName($model, 'selected_items');

$this->registerCss(<<<CSS
.idr-form { --idr-red: #dd4b39; --idr-red-soft: #fdf1ef; --idr-green: #00a65a; --idr-green-soft: #eefaf3; --idr-border: #e3e7ec; --idr-muted: #7a8694; }
.idr-form .idr-meta { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; }
.idr-form .idr-meta-item { flex: 1 1 150px; background: #f7f9fb; border: 1px solid var(--idr-border); border-radius: 6px; padding: 8px 12px; }
.idr-form .idr-meta-label { font-size: 10px; text-transform: uppercase; letter-spacing: .5px; color: var(--idr-muted); font-weight: 600; }
.idr-form .idr-meta-val { font-size: 14px; font-weight: 700; color: #2c3e50; word-break: break-word; }

.idr-form .idr-summary { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px; }
.idr-form .idr-tile { flex: 1 1 180px; border-radius: 6px; padding: 10px 14px; border: 1px solid var(--idr-border); transition: transform .15s ease; }
.idr-form .idr-tile.bump { transform: scale(1.03); }
.idr-form .idr-tile-reject { background: var(--idr-red-soft); border-color: #f5c6bf; }
.idr-form .idr-tile-keep { background: var(--idr-green-soft); border-color: #bfe8d1; }
.idr-form .idr-tile-label { font-size: 11px; text-transform: uppercase; font-weight: 600; color: var(--idr-muted); }
.idr-form .idr-tile-count { font-size: 22px; font-weight: 800; line-height: 1.2; }
.idr-form .idr-tile-reject .idr-tile-count { color: var(--idr-red); }
.idr-form .idr-tile-keep .idr-tile-count { color: var(--idr-green); }
.idr-form .idr-tile-sub { font-size: 12px; color: #55606c; }

.idr-form .idr-toolbar { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; margin-bottom: 8px; }
.idr-form .idr-toolbar .idr-search { flex: 1 1 200px; position: relative; }
.idr-form .idr-toolbar .idr-search .glyphicon { position: absolute; left: 10px; top: 10px; color: var(--idr-muted); }
.idr-form .idr-toolbar .idr-search input { padding-left: 30px; border-radius: 4px; }

.idr-form .idr-table-wrap { max-height: 280px; overflow-y: auto; border: 1px solid var(--idr-border); border-radius: 6px; }
.idr-form .idr-table { margin: 0; }
.idr-form .idr-table thead th { position: sticky; top: 0; background: #f7f9fb; z-index: 1; font-size: 12px; text-transform: uppercase; color: #55606c; border-bottom: 1px solid var(--idr-border) !important; }
.idr-form .idr-table td { vertical-align: middle !important; }
.idr-form .idr-row { cursor: pointer; transition: background-color .15s ease; }
.idr-form .idr-row:hover { background: #f4f6f8; }
.idr-form .idr-row.is-selected { background: var(--idr-red-soft); }
.idr-form .idr-row.is-selected:hover { background: #fbe4e0; }
.idr-form .idr-row input[type=checkbox] { width: 16px; height: 16px; margin: 0; cursor: pointer; }
.idr-form .idr-chip { display: inline-block; padding: 3px 9px; border-radius: 12px; font-size: 11px; font-weight: 700; white-space: nowrap; }
.idr-form .idr-chip-reject { background: var(--idr-red); color: #fff; }
.idr-form .idr-chip-keep { background: var(--idr-green-soft); color: var(--idr-green); border: 1px solid #bfe8d1; }
.idr-form .idr-empty { display: none; padding: 16px; text-align: center; color: var(--idr-muted); }

.idr-form .idr-notice { border-radius: 6px; padding: 10px 14px; margin: 12px 0; font-size: 13px; border-left: 4px solid; }
.idr-form .idr-notice-full { background: #fff8e6; border-color: #f39c12; }
.idr-form .idr-notice-partial { background: #eef6fc; border-color: #3c8dbc; }
.idr-form .idr-notice-none { background: var(--idr-red-soft); border-color: var(--idr-red); }

.idr-form .idr-section-title { font-size: 13px; font-weight: 700; text-transform: uppercase; color: #55606c; margin: 16px 0 8px; letter-spacing: .4px; }
.idr-form .idr-auto { font-size: 10px; font-weight: 600; color: #3c8dbc; margin-left: 4px; }
.idr-form .idr-actions { display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid var(--idr-border); padding-top: 12px; margin-top: 4px; }
CSS
);
?>

<div class="idr-form">
    <?php $form = ActiveForm::begin(['id' => 'InspectingDyeingRejectForm']); ?>

    <?php if ($kartuProses !== null): ?>
        <div class="idr-meta">
            <div class="idr-meta-item">
                <div class="idr-meta-label">Nomor Kartu</div>
                <div class="idr-meta-val"><?= Html::encode($nomorKartu) ?></div>
            </div>
            <div class="idr-meta-item">
                <div class="idr-meta-label">Nomor WO</div>
                <div class="idr-meta-val"><?= Html::encode($kartuProses->wo->no ?? '-') ?></div>
            </div>
            <div class="idr-meta-item">
                <div class="idr-meta-label">Motif</div>
                <div class="idr-meta-val"><?= Html::encode($kartuProses->wo->greige->nama_kain ?? '-') ?></div>
            </div>
            <div class="idr-meta-item">
                <div class="idr-meta-label">Warna</div>
                <div class="idr-meta-val"><?= Html::encode($kartuProses->woColor->moColor->color ?? '-') ?></div>
            </div>
        </div>
    <?php endif; ?>

    <div class="idr-summary">
        <div class="idr-tile idr-tile-reject" id="idrTileReject">
            <div class="idr-tile-label"><span class="glyphicon glyphicon-share-alt"></span> Kembali ke Processing</div>
            <div class="idr-tile-count"><span id="idrRejectCount">0</span> <small>roll</small></div>
            <div class="idr-tile-sub">Qty: <strong id="idrRejectQty">0</strong></div>
        </div>
        <div class="idr-tile idr-tile-keep" id="idrTileKeep">
            <div class="idr-tile-label"><span class="glyphicon glyphicon-ok"></span> Tetap di Inspecting</div>
            <div class="idr-tile-count"><span id="idrKeepCount">0</span> <small>roll</small></div>
            <div class="idr-tile-sub">Qty: <strong id="idrKeepQty">0</strong></div>
        </div>
    </div>

    <div class="idr-toolbar">
        <div class="idr-search">
            <span class="glyphicon glyphicon-search"></span>
            <input type="text" class="form-control input-sm" id="idrSearch" placeholder="Cari roll, grade, tube, lot...">
        </div>
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-default" data-idr-action="all"><span class="glyphicon glyphicon-check"></span> Semua</button>
            <button type="button" class="btn btn-default" data-idr-action="none"><span class="glyphicon glyphicon-unchecked"></span> Kosongkan</button>
            <button type="button" class="btn btn-default" data-idr-action="invert"><span class="glyphicon glyphicon-transfer"></span> Balik</button>
        </div>
    </div>

    <div class="idr-table-wrap">
        <?= Html::hiddenInput($inputName, '') ?>
        <table class="table table-condensed idr-table">
            <thead>
            <tr>
                <th class="text-center" style="width: 40px;">
                    <input type="checkbox" id="idrCheckAll" title="Pilih semua yang tampil">
                </th>
                <th>Roll</th>
                <th>Tube</th>
                <th>Grade</th>
                <th>Lot Lusi / Pakan</th>
                <th class="text-right">Qty</th>
                <th class="text-center">Tujuan</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <?php
                $stock = $item->stock;
                $qty = (float)($item->panjang_m ?? $stock->panjang_m);
                $tube = $item::tubeOptions()[$item->tube] ?? '-';
                $lot = $stock->lot_lusi . ' / ' . $stock->lot_pakan;
                $checked = in_array((string)$item->id, $selectedIds, true);
                ?>
                <tr class="idr-row" data-search="<?= Html::encode(strtolower(implode(' ', [$stock->id, $tube, $stock->gradeName, $lot]))) ?>">
                    <td class="text-center">
                        <?= Html::checkbox($inputName . '[]', $checked, [
                            'value' => $item->id,
                            'class' => 'idr-check',
                            'data-qty' => $qty,
                        ]) ?>
                    </td>
                    <td><strong><?= Html::encode($stock->id) ?></strong></td>
                    <td><?= Html::encode($tube) ?></td>
                    <td><span class="label label-default"><?= Html::encode($stock->gradeName) ?></span></td>
                    <td><?= Html::encode($lot) ?></td>
                    <td class="text-right"><strong><?= $formatter->asDecimal($qty) ?></strong></td>
                    <td class="text-center"><span class="idr-chip"></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="idr-empty" id="idrEmpty">Tidak ada roll yang cocok dengan pencarian.</div>
    </div>
    <?= $form->field($model, 'selected_items', [
        'template' => '{error}',
        'parts' => ['{input}' => ''],
        'enableClientValidation' => false,
    ]) ?>

    <div class="idr-notice" id="idrNotice"></div>

    <div class="idr-section-title">Surat Pengantar</div>

    <div class="row">
        <div class="col-md-6"><?= $form->field($model, 'untuk_bagian')->textInput(['maxlength' => true]) ?></div>

        <div class="col-md-3"><?= $form->field($model, 'pcs', ['enableClientValidation' => false])
                ->input('number', ['maxlength' => true])
                ->label($model->getAttributeLabel('pcs') . '<span class="idr-auto">AUTO</span>') ?></div>

        <div class="col-md-3"><?= $form->field($model, 'total_qty', ['enableClientValidation' => false])
                ->input('number', ['step' => 'any'])
                ->label($model->getAttributeLabel('total_qty') . '<span class="idr-auto">AUTO</span>') ?></div>
    </div>

    <div class="row">
        <div class="col-md-4"><?= $form->field($model, 'penerima')->textInput(['maxlength' => true]) ?></div>

        <div class="col-md-4"><?= $form->field($model, 'mengetahui')->textInput(['maxlength' => true]) ?></div>

        <div class="col-md-4"><?= $form->field($model, 'pengirim')->textInput(['maxlength' => true]) ?></div>
    </div>

    <?= $form->field($model, 'keterangan')->textarea(['rows' => 3]) ?>

    <div class="idr-actions">
        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
        <?= Html::submitButton('<span class="glyphicon glyphicon-share-alt"></span> <span id="idrSubmitLabel">Kembalikan</span>', [
            'class' => 'btn btn-danger',
            'id' => 'idrSubmit',
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$idrConfig = \yii\helpers\Json::htmlEncode([
    'nomorKartu' => $nomorKartu,
    'nomorKartuBaru' => $nomorKartuBaru,
]);
$this->registerJs("var idrConfig = {$idrConfig};\n" . $this->renderFile(__DIR__.'/js/form.js'), \yii\web\View::POS_END, 'inspectingDyeingRejectFormJs');
