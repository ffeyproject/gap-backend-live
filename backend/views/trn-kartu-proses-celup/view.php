<?php

use backend\components\ajax_modal\AjaxModal;
use common\models\ar\MstProcessDyeing;
use kartik\dialog\Dialog;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesCelup */
/* @var $attrsLabels array*/
/* @var $processModels MstProcessDyeing[]*/
/* @var $processesUlang array*/

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Kartu Proses Celup', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
$formatter = Yii::$app->formatter;
?>
<div class="trn-kartu-proses-celup-view">

    <p>
        <?= Html::a('Buat Baru', ['create'], ['class' => 'btn btn-default']) ?>

        <?php if($model->status == $model::STATUS_DRAFT):?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Posting', ['posting', 'id' => $model->id], [
                'class' => 'btn btn-info',
                'data' => [
                    'confirm' => 'Anda yakin akan memposting item ini?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Set No Limit Item', ['set-unlimit-item', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Jumlah item pada kartu proses tidak lagi dibatasi, lanjutkan?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif;?>
    </p>

    <?php echo $this->render('child/detail', ['model' => $model]);?>

    <?php echo $this->render('child/items', ['model' => $model]);?>

    <?php echo $this->render('child/persetujuan', ['model' => $model]);?>

    <?php
    switch ($model->status){
        case $model::STATUS_DELIVERED:
        case $model::STATUS_APPROVED:
        case $model::STATUS_INSPECTED:
            echo $this->render('/processing-celup/child/proses_disabled', ['model' => $model, 'attrsLabels'=>$attrsLabels, 'processModels'=>$processModels, 'processesUlang'=>$processesUlang, 'formatter'=>$formatter]);
            break;
        default:
            echo '';
    }
    ?>

    <?php
    /*echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'greige_group_id',
            'greige_id',
            'order_celup_id',
            'no_urut',
            'no',
            'no_proses',
            'asal_greige',
            'dikerjakan_oleh',
            'lusi',
            'pakan',
            'note:ntext',
            'date',
            'posted_at',
            'approved_at',
            'approved_by',
            'status',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'delivered_at',
            'delivered_by',
            'reject_notes:ntext',
            'berat',
            'lebar',
            'k_density_lusi',
            'k_density_pakan',
            'gramasi',
            'lebar_preset',
            'lebar_finish',
            'berat_finish',
            't_density_lusi',
            't_density_pakan',
            'handling',
        ],
    ]);*/
    ?>

</div>

<?php
echo AjaxModal::widget([
    'id' => 'kartuProsesCelupModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);