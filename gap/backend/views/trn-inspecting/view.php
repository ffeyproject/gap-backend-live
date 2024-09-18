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
        switch ($model->status){
            case $model::STATUS_DRAFT:
                echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']).' ';
                echo Html::a('Delete', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ]).' ';
                echo Html::a('Posting', ['posting', 'id' => $model->id], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => 'Are you sure you want to posting this item?',
                        'method' => 'post',
                    ],
                ]);
                break;
            /*case $model::STATUS_POSTED:
                echo Html::a('Approve', ['approve', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to approve this item?',
                        'method' => 'post',
                    ],
                ]);
                break;
            case $model::STATUS_APPROVED:
                echo '';
                break;*/
        }

        echo ' '.Html::a('Print', ['print', 'id' => $model->id], ['class' => 'btn btn-default']);
        ?>
    </p>

    <?php
    echo $this->render('child/_header', ['model'=>$model, 'kartuProses'=>$kartuProses, 'kartuProsesUrl'=>$kartuProsesUrl, 'greige'=>$greige]);
    echo $this->render('child/_items', ['model'=>$model, 'greige'=>$greige, 'formatter'=>$formatter]);
    ?>
</div>

<?php
$warnaList = $model->wo->getTrnWoColors()->joinWith('moColor')->asArray()->all();
$indexUrl = \yii\helpers\Url::to(['index']);

$this->registerJsVar('indexUrl', $indexUrl);
$this->registerJsVar('warnaList', $warnaList);
$this->registerJs($this->renderFile(Yii::$app->controller->viewPath.'/js/view.js'), $this::POS_END);