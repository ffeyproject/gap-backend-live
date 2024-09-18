<?php

use backend\components\ajax_modal\AjaxModal;
use common\models\ar\MstProcessPrinting;
use common\models\ar\TrnKartuProsesPrinting;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnKartuProsesPrinting */
/* @var $attrsLabels array*/
/* @var $processModels MstProcessPrinting[]*/
/* @var $processesUlang array*/

$this->title = 'Processing Printing - '.$model->no;
$this->params['breadcrumbs'][] = ['label' => 'Processing Printing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$formatter = Yii::$app->formatter;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-kartu-proses-printing-view">

    <p>
        <?php if($model->status == $model::STATUS_DELIVERED):?>
            <?= Html::a('Buat Memo Penggantian Greige', ['ganti-greige', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'onclick' => 'memoPg(event, "Memo Penggantian Greige");',
                'title' => 'Buat Memo Penggantian Greige'
            ]) ?>

            <?= Html::a('Selesai Dan Teruskan Ke Inspecting', ['approve', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Are you sure you want to approve this item?',
                    'method' => 'post',
                ],
            ]) ?>

            <?= Html::a('Batalkan Kartu Proses', ['batal', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to batalkan this item?',
                    'method' => 'post',
                ],
            ]) ?>

            <?=Html::a('Ganti WO', ['ganti-wo', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'onclick' => 'gantiWo(event);',
                'title' => 'Ganti WO Kartu Proses: '.$model->id
            ]);?>

            <?=Html::a('Ganti Warna', ['ganti-warna', 'id' => $model->id], [
                'class' => 'btn btn-info',
                'onclick' => 'gantiWarna(event);',
                'title' => 'Ganti Warna Kartu Proses: '.$model->id
            ]);?>
        <?php endif;?>
    </p>

    <?php echo $this->render('/trn-kartu-proses-printing/child/detail', ['model' => $model]);?>

    <?php echo $this->render('/trn-kartu-proses-printing/child/items', ['model' => $model]);?>

    <?php echo $this->render('/trn-kartu-proses-printing/child/persetujuan', ['model' => $model]);?>

    <?php
    switch ($model->status){
        case $model::STATUS_DELIVERED:
            echo $this->render('child/proses', ['model' => $model, 'attrsLabels'=>$attrsLabels, 'processModels'=>$processModels, 'processesUlang'=>$processesUlang, 'formatter'=>$formatter]);
            break;
        case $model::STATUS_APPROVED:
        case $model::STATUS_INSPECTED:
            echo $this->render('child/proses_disabled', ['model' => $model, 'attrsLabels'=>$attrsLabels, 'processModels'=>$processModels, 'processesUlang'=>$processesUlang, 'formatter'=>$formatter]);
            break;
        default:
            echo '';
    }
    ?>
</div>

<?php
echo AjaxModal::widget([
    'id' => 'kartuProsesPrintingModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);

if($model->status == $model::STATUS_DELIVERED){
    $warnaList = $model->wo->getTrnWoColors()->joinWith('moColor')->asArray()->all();
    $this->registerJsVar('warnaList', $warnaList);
    $this->registerJsVar('indexUrl', Url::to(['index']));

    $js = $this->renderFile(Yii::$app->controller->viewPath.'/js/view.js');
    $this->registerJs($js, $this::POS_END);
}
