<?php

use backend\components\ajax_modal\AjaxModal;
use common\models\ar\MstProcessPrinting;
use common\models\ar\TrnKartuProsesPrinting;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnKartuProsesPrinting */
/* @var $attrsLabels array*/
/* @var $processModels MstProcessPrinting[]*/
/* @var $processesUlang array*/

$this->title = 'Kartu Proses Printing - '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Kartu Proses Printing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

$formatter = Yii::$app->formatter;
?>
<div class="trn-kartu-proses-printing-view">

    <p>
        <?= Html::a('Buat Baru', ['create'], ['class' => 'btn btn-default']) ?>

        <?php if($model->status == $model::STATUS_DRAFT):?>
            <?= Html::a('Hapus', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Posting', ['posting', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Anda yakin akan memposting item ini?',
                    'method' => 'post',
                ],
            ]) ?>
            <?php
            if(!$model->no_limit_item){
                echo Html::a('Set No Limit Item', ['set-unlimit-item', 'id' => $model->id], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => 'Jumlah item pada kartu proses tidak lagi dibatasi, lanjutkan?',
                        'method' => 'post',
                    ],
                ]);
            }
            ?>
        <?php endif;?>
    </p>

    <?php echo $this->render('child/detail', ['model' => $model]);?>
    <?php echo $this->render('child/items', ['model' => $model]);?>
    <?php echo $this->render('child/persetujuan', ['model' => $model]);?>

    <?php if(in_array($model->status, [$model::STATUS_GANTI_GREIGE, $model::STATUS_GANTI_GREIGE_LINKED])):?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><strong>MEMO PENGGANTIAN GREIGE</strong></h3>
                <div class="box-tools pull-right">
                    <strong><?=Yii::$app->formatter->asDatetime($model->memo_pg_at)?> | <?=\backend\modules\user\models\User::findOne($model->memo_pg_by)->full_name?></strong>
                </div>
            </div>
            <div class="box-body">
                <p><?=$model->memo_pg?></p>
            </div>
        </div>
    <?php endif;?>

    <?php
    switch ($model->status){
        case $model::STATUS_DELIVERED:
        case $model::STATUS_APPROVED:
        case $model::STATUS_INSPECTED:
        case $model::STATUS_GANTI_GREIGE:
        case $model::STATUS_GANTI_GREIGE_LINKED:
            echo $this->render('/processing-printing/child/proses_disabled', ['model' => $model, 'attrsLabels'=>$attrsLabels, 'processModels'=>$processModels, 'processesUlang'=>$processesUlang, 'formatter'=>$formatter]);
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
