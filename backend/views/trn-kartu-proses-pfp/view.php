<?php

use backend\components\ajax_modal\AjaxModal;
use common\models\ar\MstProcessPfp;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesPfp */
/* @var $attrsLabels array*/
/* @var $processModels MstProcessPfp[]*/
/* @var $processesUlang array*/

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Kartu Proses PFP', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);

$formatter = Yii::$app->formatter;
?>
<div class="trn-kartu-proses-pfp-view">
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

        <?php
            if (!$model->no_limit_item){
                echo Html::a('Set No Limit Item', ['set-unlimit-item', 'id' => $model->id], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => 'Jumlah item pada kartu proses tidak lagi dibatasi, lanjutkan?',
                        'method' => 'post',
                    ],
                ]);
            }else{
                echo Html::a('Set Limited Item', ['set-limited-item', 'id' => $model->id], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => 'Jumlah item pada kartu proses akan dibatasi, lanjutkan?',
                        'method' => 'post',
                    ],
                ]);
            }
            ?>
        <?php endif;?>

        <?php if(in_array($model->status, [$model::STATUS_DRAFT, $model::STATUS_POSTED, $model::STATUS_DELIVERED])):?>
        <?= Html::a('Edit Nomor Kartu',
        ['trn-kartu-proses-pfp/edit-nomor-kartu', 'id'=>$model->id],
        [
            'class'=>'btn btn-primary',
            'data-toggle'=>'ajaxModal',
            'data-target'=>'#kartuProsesPfpModalNomor'
        ]) ?>

    <div class="modal fade" id="kartuProsesPfpModalNomor" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Nomor Kartu</h4>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span>&times;</span></button>
                </div>
                <div class="modal-body">Loading...</div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    </p>

    <?php echo $this->render('child/detail', ['model' => $model]);?>

    <?php echo $this->render('child/items', ['model' => $model]);?>

    <?php echo $this->render('child/persetujuan', ['model' => $model]);?>

    <?php
    switch ($model->status){
        case $model::STATUS_DELIVERED:
        case $model::STATUS_APPROVED:
        case $model::STATUS_INSPECTED:
            echo $this->render('/processing-pfp/child/proses_disabled', ['model' => $model, 'attrsLabels'=>$attrsLabels, 'processModels'=>$processModels, 'processesUlang'=>$processesUlang, 'formatter'=>$formatter]);
            break;
        default:
            echo '';
    }
    ?>
</div>

<?php
echo AjaxModal::widget([
    'id' => 'kartuProsesPfpModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);

?>

<?php
$js = <<<JS
$(document).on('click', '[data-toggle="ajaxModal"]', function (e) {
    e.preventDefault();
    var target = $(this).data('target');
    var url = $(this).attr('href');
    var modal = $(target);

    modal.find('.modal-body').html('<div class="p-3">Loading...</div>');
    modal.modal('show');

    $.get(url, function (data) {
        modal.find('.modal-body').html(data);
    });
});
JS;
$this->registerJs($js);
?>