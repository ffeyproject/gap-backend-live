<?php

use backend\components\ajax_modal\AjaxModal;
use common\models\ar\MstProcessDyeing;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesCelup */
/* @var $attrsLabels array*/
/* @var $processModels MstProcessDyeing[]*/
/* @var $processesUlang array*/

$this->title = 'Processing Celup - '.$model->no;
$this->params['breadcrumbs'][] = ['label' => 'Processing Celup', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$formatter = Yii::$app->formatter;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-kartu-proses-celup-view">

    <p>
        <?php if($model->status == $model::STATUS_DELIVERED):?>
            <?= Html::a('Buat Memo Penggantian Greige', ['ganti-greige', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'onclick' => 'memoPg(event, "Memo Penggantian Greige");',
                'title' => 'Buat Memo Penggantian Greige'
            ]) ?>
            <?= Html::a('Selesai Dan Masukan Ke Gudag PFP', ['approve', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Are you sure you want to approve this item?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif;?>
    </p>

    <?php echo $this->render('/trn-kartu-proses-celup/child/detail', ['model' => $model]);?>

    <?php echo $this->render('/trn-kartu-proses-celup/child/items', ['model' => $model]);?>

    <?php echo $this->render('/trn-kartu-proses-celup/child/persetujuan', ['model' => $model]);?>

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
    'id' => 'kartuProsesCelupModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);

if($model->status == $model::STATUS_DELIVERED){
    $indexUrl = Url::to(['index']);
    $jsStr = <<<JS
var indexUrl = "{$indexUrl}";
JS;

    $js = $jsStr.$this->renderFile(Yii::$app->controller->viewPath.'/js/view.js');
    $this->registerJs($js, $this::POS_END);
}
