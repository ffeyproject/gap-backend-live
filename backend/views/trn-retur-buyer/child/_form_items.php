<?php

use common\models\ar\TrnReturBuyerItem;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWo;
use common\widgets\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnReturBuyer */
/* @var $modelsItem TrnReturBuyerItem[] */
/* @var $form ActiveForm */

// input baru: WO dipilih per item (banyak WO sekaligus), ubah: WO mengikuti dokumen
$multiWo = $model->isNewRecord;
?>

<?php
DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
    'widgetBody' => '.container-items', // required: css class selector
    'widgetItem' => '.item', // required: css class
    //'limit' => 4, // the maximum times, an element can be cloned (default 999)
    //'min' => 0, // 0 or 1 (default 1)
    'insertButton' => '.add-item', // css class
    'deleteButton' => '.remove-item', // css class
    'model' => $modelsItem[0],
    'formId' => 'dynamic-form',
    'formFields' => $multiWo ? ['wo_id', 'kirim_buyer_id', 'stock_id', 'qty', 'note', 'grade'] : ['kirim_buyer_id', 'stock_id', 'qty', 'note', 'grade'],
]);
?>

<?php
$grdOptsA = [''=>'--'];
$grdOptsB = TrnStockGreige::gradeOptions();
$grdOpts = ArrayHelper::merge($grdOptsA, $grdOptsB);
?>

<?php // Panel input banyak roll/qty untuk satu WO sekaligus. Input di sini tidak punya name (tidak ikut disubmit), hanya untuk membuat baris item. ?>
<div class="well well-sm retur-bulk">
    <strong>Tambah Banyak Roll / Qty untuk 1 WO</strong>
    <div class="row" style="margin-top: 8px">
        <?php if($multiWo):?>
        <div class="col-md-3">
            <label class="control-label">WO</label>
            <select class="form-control retur-bulk-wo"><option value=""></option></select>
        </div>
        <?php endif;?>
        <div class="col-md-<?= $multiWo ? 4 : 5 ?>">
            <label class="control-label">Surat Jalan</label>
            <?= Html::dropDownList('', null, $multiWo ? [] : TrnReturBuyerItem::kirimBuyerOptions($model->wo_id), ['prompt' => '-- Tidak diketahui --', 'class' => 'form-control retur-bulk-sj']) ?>
        </div>
        <div class="col-md-2">
            <label class="control-label">Grade</label>
            <?= Html::dropDownList('', null, $grdOpts, ['class' => 'form-control retur-bulk-grade']) ?>
        </div>
        <div class="col-md-<?= $multiWo ? 3 : 5 ?>">
            <label class="control-label">Qty roll tidak diketahui</label>
            <?= Html::textarea('', '', ['class' => 'form-control retur-bulk-qty', 'rows' => 1, 'placeholder' => 'Contoh: 50 48 52 47']) ?>
        </div>
    </div>
    <div class="retur-bulk-rolls" style="margin-top: 8px; max-height: 220px; overflow-y: auto"></div>
    <p class="help-block" style="margin-bottom: 6px">
        Centang roll dari surat jalan (qty &amp; grade ikut roll), dan/atau ketik qty roll yang tidak diketahui dipisah spasi / koma / enter.
        Setiap roll / qty menjadi 1 baris item.
    </p>
    <button type="button" class="btn btn-primary btn-sm retur-bulk-add"><i class="fa fa-plus"></i> Tambahkan ke Item</button>
</div>

<table class="table table-bordered">
    <thead>
    <tr>
        <th style="width: 12%">Scan QR / ID Roll</th>
        <?php if($multiWo):?><th style="width: 20%">WO</th><?php endif;?>
        <th style="width: <?= $multiWo ? 19 : 25 ?>%">Surat Jalan</th>
        <th style="width: <?= $multiWo ? 19 : 25 ?>%">Roll Asal</th>
        <th style="width: 8%">Qty</th>
        <th style="width: 8%">Grade</th>
        <th>Note</th>
        <th><button type="button" class="pull-right add-item btn btn-success btn-xs"><i class="fa fa-plus"></i></button></th>
    </tr>
    </thead>
    <tbody class="container-items">
    <?php foreach ($modelsItem as $index => $modelItem): ?>
        <?php
        $woId = $multiWo ? $modelItem->wo_id : $model->wo_id;
        $sjOpts = TrnReturBuyerItem::kirimBuyerOptions($woId);

        $rollOpts = [];
        $rollOptAttrs = [];
        foreach (TrnReturBuyerItem::rollList($modelItem->kirim_buyer_id, $model->id) as $roll) {
            $rollOpts[$roll['id']] = $roll['text'].($roll['returned'] ? ' (sudah diretur)' : '');
            $rollOptAttrs[$roll['id']] = ['data-qty' => $roll['qty'], 'data-grade' => $roll['grade'], 'disabled' => $roll['returned']];
        }
        ?>
        <tr class="item">
            <td>
                <?php
                // necessary for update action.
                if (! $modelItem->isNewRecord) {
                    echo Html::activeHiddenInput($modelItem, "[{$index}]id");
                }

                echo Html::textInput('', '', ['class' => 'form-control retur-scan', 'placeholder' => 'Scan lalu Enter', 'autocomplete' => 'off']);
                ?>
            </td>
            <?php if($multiWo):?>
            <td>
                <?php
                // select biasa, select2 diinisialisasi di js/form-items.js (widget Select2 kartik tidak tampil pada baris dynamic form)
                $wo = empty($modelItem->wo_id) ? null : TrnWo::findOne($modelItem->wo_id);
                echo $form->field($modelItem, "[{$index}]wo_id")->dropDownList($wo !== null ? [$wo->id => $wo->no] : [], ['prompt' => '', 'class' => 'form-control retur-wo'])->label(false);
                ?>
            </td>
            <?php endif;?>
            <td>
                <?= $form->field($modelItem, "[{$index}]kirim_buyer_id")->dropDownList($sjOpts, ['prompt' => '-- Tidak diketahui --', 'class' => 'form-control retur-sj'])->label(false) ?>
            </td>
            <td>
                <?= $form->field($modelItem, "[{$index}]stock_id")->dropDownList($rollOpts, ['prompt' => '-- Tidak diketahui --', 'class' => 'form-control retur-roll', 'options' => $rollOptAttrs])->label(false) ?>
            </td>
            <td><?= $form->field($modelItem, "[{$index}]qty")->textInput(['class' => 'form-control retur-qty'])->label(false) ?></td>
            <td><?= $form->field($modelItem, "[{$index}]grade")->dropDownList($grdOpts, ['class' => 'form-control retur-grade'])->label(false)?></td>
            <td><?= $form->field($modelItem, "[{$index}]note")->textInput()->label(false) ?></td>
            <td><button type="button" class="pull-right remove-item btn btn-danger btn-xs"><i class="fa fa-minus"></i></button></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

<?php DynamicFormWidget::end();?>

<?php
$config = Json::htmlEncode([
    'multiWo' => $multiWo,
    'woId' => $model->wo_id,
    'returId' => $model->id,
    'urlWo' => Url::to(['ajax/lookup-wo']),
    'urlSj' => Url::to(['ajax/retur-kirim-buyer-options']),
    'urlRoll' => Url::to(['ajax/retur-roll-list']),
    'urlScan' => Url::to(['ajax/retur-scan-roll']),
]);
$this->registerJs("var returForm = {$config};\n".$this->renderFile(Yii::$app->controller->viewPath.'/js/form-items.js'), View::POS_END);
?>
