<?php

use backend\components\Converter;
use common\models\ar\TrnInspecting;
use common\models\ar\TrnScGreige;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnInspecting */

$formatter = Yii::$app->formatter;

$this->title = 'Inspecting, ID: '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inspectings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

if($model->kartu_process_dyeing_id !== null){
    $kartuProses = $model->kartuProcessDyeing;
    $kartuProsesUrl = 'trn-kartu-proses-dyeing';
    $isDyeing = true;
    $isPfp = false;
    $isPrinting = false;
    $isMaklon = false;
    $jenis = 'dyeing';

    $wo = $model->wo;
    $greige = $wo->greige;
    $mo = $model->mo;
    $scGreigeGroup = $model->scGreige;
    $sc = $model->sc;
    $cust = $sc->cust;
}else if($model->kartu_process_printing_id !== null){
    $kartuProses = $model->kartuProcessPrinting;
    $kartuProsesUrl = 'trn-kartu-proses-printing';
    $isDyeing = false;
    $isPfp = false;
    $isPrinting = true;
    $isMaklon = false;
    $jenis = 'printing';

    $wo = $model->wo;
    $greige = $wo->greige;
    $mo = $model->mo;
    $scGreigeGroup = $model->scGreige;
    $sc = $model->sc;
    $cust = $sc->cust;
}else if($model->memo_repair_id !== null){
    $kartuProses = $model->memoRepair;
    $kartuProsesUrl = 'trn-memo-repair';
    $isDyeing = $kartuProses->scGreige->process == TrnScGreige::PROCESS_DYEING;
    $isPfp = false;
    $isPrinting = $kartuProses->scGreige->process == TrnScGreige::PROCESS_PRINTING;
    $isMaklon = false;
    $jenis = TrnScGreige::processOptions()[$kartuProses->scGreige->process];

    $wo = $model->wo;
    $greige = $wo->greige;
    $mo = $model->mo;
    $scGreigeGroup = $model->scGreige;
    $sc = $model->sc;
    $cust = $sc->cust;
}
?>
<div class="inspecting-view">
    <p>
        <?php
        $hasDraftItems = \common\models\ar\InspectingItem::find()->where(['inspecting_id' => $model->id, 'is_posted' => false])->exists();

        switch ($model->status){
            case $model::STATUS_DRAFT:
                echo Html::a('Upgrade', ['upgrade', 'id' => $model->id], ['class' => 'btn btn-success']).' ';
                echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']).' ';
                echo Html::a('Delete', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ]).' ';
                break;
        }

        if ($model->status == $model::STATUS_DRAFT || $model->status == $model::STATUS_APPROVED) {
            if ($hasDraftItems) {
                $isAnyPrinted = \common\models\ar\InspectingItem::find()->where(['inspecting_id' => $model->id, 'is_posted' => false])->andWhere(['not', ['qr_print_at' => null]])->exists();
                if($isAnyPrinted){
                    echo Html::button('Posting', [
                        'class' => 'btn btn-warning',
                        'onclick' => 'postingItems()'
                    ]).' ';
                }else{
                    echo Html::a('Posting', 'javascript:void(0)', [
                        'class' => 'btn btn-warning',
                        'disabled' => 'disabled',
                        'title' => 'Cetak QR Code terlebih dahulu untuk dapat melakukan posting.',
                        'onclick' => 'alert("Cetak QR Code terlebih dahulu untuk dapat melakukan posting."); return false;'
                    ]).' ';
                }
            }
        }

        if ($model->status == $model::STATUS_DRAFT) {
            echo Html::a('Hapus Semua Kode Defect', ['hapus-semua-defect', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Apakah Anda yakin ingin menghapus semua kode defect dari inspeksi ini?',
                    'method' => 'post',
                ],
            ]);
        }

        echo ' '.Html::a('Print', ['print', 'id' => $model->id], ['class' => 'btn btn-default']);
        ?>
    </p>

    <?= Html::beginForm(['posting', 'id' => $model->id], 'post', ['id' => 'posting-form']) ?>
    <?php
    echo $this->render('child/_header', ['model'=>$model, 'kartuProses'=>$kartuProses, 'kartuProsesUrl'=>$kartuProsesUrl, 'greige'=>$greige]);
    echo $this->render('child/_items', ['model'=>$model, 'greige'=>$greige, 'formatter'=>$formatter]);
    ?>
    <?= Html::endForm() ?>
</div>

<?php
$warnaList = $model->wo->getTrnWoColors()->joinWith('moColor')->asArray()->all();
$indexUrl = \yii\helpers\Url::to(['index']);

$this->registerJsVar('inspectionId', $model->id);
$this->registerJsVar('indexUrl', $indexUrl);
$this->registerJsVar('warnaList', $warnaList);
$this->registerJs('
window.postingItems = function() {
    if ($(".check-item:checked").length === 0) {
        alert("Pilih setidaknya satu item untuk dikirim.");
        return;
    }
    if (confirm("Apakah Anda yakin ingin memposting item yang dipilih?")) {
        // Clear saved selections after posting
        localStorage.removeItem("checked_items_" + inspectionId);
        $("#posting-form").submit();
    }
};

function updateLocalStorage() {
    var checkedIds = [];
    $(".check-item:checked").each(function() {
        checkedIds.push($(this).val());
    });
    localStorage.setItem("checked_items_" + inspectionId, JSON.stringify(checkedIds));
}

// Load saved states on page load
var saved = localStorage.getItem("checked_items_" + inspectionId);
if (saved) {
    var ids = JSON.parse(saved);
    ids.forEach(function(id) {
        var cb = $(".check-item[value=\"" + id + "\"]");
        if(cb.length){
            cb.show().prop("checked", true);
            cb.closest("tr").find(".label-print-dulu").hide();
        }
    });
}

$(document).on("click", "#qrPrintLink", function(e) {
    e.preventDefault();

    var param1Value = $("#param1Checkbox").is(":checked") ? "1" : "0";
    var param2Value = $("#param2Checkbox").is(":checked") ? "1" : "0";
    var param6Value = $("#param6Checkbox").is(":checked") ? "1" : "0";
    var param8Value = $("#param8Checkbox").is(":checked") ? "1" : "0";

    var theView = (param1Value == 0 && param2Value == 0) ? "qr-all-without-attribute" : "qr-all";
    var url = $(this).attr("href") + "&param1=" + param1Value + "&param2=" + param2Value + "&param6=" + param6Value + "&param8=" + param8Value;
    var replacedUrl = url.replace(/replace/, theView);

    window.open(replacedUrl, "_blank");
});

$(document).on("click", ".qrPrint", function(e) {
    e.preventDefault();
    var row = $(this).closest("tr");
    
    row.find(".check-item").show().prop("checked", true);
    row.find(".label-print-dulu").hide();
    updateLocalStorage();

    var param3Value = $("#param3Checkbox").is(":checked") ? "1" : "0";
    var param4Value = $("#param4Checkbox").is(":checked") ? "1" : "0";
    var param5Value = $("#param5Checkbox").is(":checked") ? "1" : "0";

    var url = $(this).attr("href") + "&param3=" + param3Value + "&param4=" + param4Value + "&param5=" + param5Value;

    window.open(url, "_blank");
});

$(document).on("change", ".check-item", function() {
    updateLocalStorage();
});
');
$this->registerJs($this->renderFile(Yii::$app->controller->viewPath.'/js/view.js'), $this::POS_END);