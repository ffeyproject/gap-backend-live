<?php
use backend\components\ajax_modal\AjaxModal;
use backend\modules\user\models\User;
use common\models\ar\MstProcessDyeing;
use common\models\ar\TrnKartuProsesDyeing;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrnKartuProsesDyeing */
/* @var $attrsLabels array*/
/* @var $processModels MstProcessDyeing[]*/
/* @var $processesUlang array*/

$this->title = 'Kartu Proses Dyeing - '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Kartu Proses Dyeing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
$formatter = Yii::$app->formatter;
?>
<div class="kartu-proses-dyeing-view">

    <p>
        <?= Html::a('Buat Baru', ['create'], ['class' => 'btn btn-success']) ?>

        <?php if($model->status == $model::STATUS_DRAFT):?>
            <?php //echo Html::a('Ubah', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
            <?= Html::a('Hapus', ['delete', 'id' => $model->id], [
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

        <?php echo Html::a('Ganti WO', ['ganti-wo', 'id' => $model->id], ['class' => 'btn btn-default']); ?>
    </p>

    <?php echo $this->render('child/detail', ['model' => $model]);?>

    <?php echo $this->render('child/items', ['model' => $model]);?>

    <?php echo $this->render('child/persetujuan', ['model' => $model]);?>

    <?php if(in_array($model->status, [$model::STATUS_GANTI_GREIGE, $model::STATUS_GANTI_GREIGE_LINKED])):?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><strong>MEMO PENGGANTIAN GREIGE</strong></h3>
            <div class="box-tools pull-right">
                <strong><?=Yii::$app->formatter->asDatetime($model->memo_pg_at)?> | <?=User::findOne($model->memo_pg_by)->full_name?></strong>
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
            echo $this->render('/processing-dyeing/child/proses_disabled', ['model' => $model, 'attrsLabels'=>$attrsLabels, 'processModels'=>$processModels, 'processesUlang'=>$processesUlang, 'formatter'=>$formatter]);
            break;
        case $model::STATUS_DRAFT:
            echo AjaxModal::widget([
                'id' => 'kartuProsesDyeingModal',
                'size' => 'modal-lg',
                'header' => '<h4 class="modal-title">...</h4>',
            ]);
            break;
        default:
            echo '';
    }
    ?>
</div>