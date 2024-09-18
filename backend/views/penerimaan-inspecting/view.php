<?php

use backend\components\ajax_modal\AjaxModal;
use common\models\ar\TrnInspecting;
use common\models\ar\TrnScGreige;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnInspecting */

$formatter = Yii::$app->formatter;

$this->title = 'Penerimaan Packing: '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Penerimaan Packing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

if($model->kartu_process_dyeing_id !== null){
    $jenis = 'dyeing';
}else{
    $jenis = 'printing';
}

$inspectingItems = $model->getInspectingItems()->asArray()->all();

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
    $isPfp = $kartuProses->scGreige->process == TrnScGreige::PROCESS_PFP;
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
        if($model->status == $model::STATUS_APPROVED){
            echo Html::a('Terima', ['terima', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'title' => 'Penerimaan Packing',
                'data-toggle'=>"modal",
                'data-target'=>"#penerimaanPackingModal",
                'data-title' => 'Penerimaan Packing'
            ]);
            echo ' ';

            echo Html::a('Tolak', ['tolak', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'onclick' => 'rejectInspect(event);',
                'title' => 'Reject Inspecting: '.$model->id
            ]);

            $indexUrl = Url::to(['index']);
            $jsStr = 'var indexUrl = "'.$indexUrl.'";';
            $js = $jsStr.$this->renderFile(Yii::$app->controller->viewPath.'/js/view.js');
            $this->registerJs($js, $this::POS_END);
        }
        ?>
    </p>

    <?=$this->render('/trn-inspecting/child/_header', ['model'=>$model, 'kartuProses'=>$kartuProses, 'kartuProsesUrl'=>$kartuProsesUrl, 'greige'=>$greige])?>
    <?=$this->render('child/_items', ['model'=>$model, 'greige'=>$greige, 'formatter'=>$formatter])?>

</div>

<?php
echo AjaxModal::widget([
    'id' => 'penerimaanPackingModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);