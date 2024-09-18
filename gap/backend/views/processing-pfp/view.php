<?php

use backend\components\ajax_modal\AjaxModal;
use common\models\ar\MstProcessPfp;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesPfp */
/* @var $attrsLabels array*/
/* @var $processModels MstProcessPfp[]*/
/* @var $processesUlang array*/

$this->title = 'Processing Pfp - '.$model->no;
$this->params['breadcrumbs'][] = ['label' => 'Processing Pfp', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$formatter = Yii::$app->formatter;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-kartu-proses-pfp-view">

    <p>
        <?php
        if($model->status == $model::STATUS_DELIVERED){
            echo Html::a('Buat Memo Penggantian Greige', ['ganti-greige', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'onclick' => 'memoPg(event, "Memo Penggantian Greige");',
                'title' => 'Buat Memo Penggantian Greige'
            ]).' ';

            echo Html::a('Ganti Kartu Dyeing', ['ganti-dyeing', 'id' => $model->id], [
                    'class' => 'btn btn-info',
                    'onclick' => 'gantiDyeing(event, "Ganti Kartu Dyeing");',
                    'title' => 'Ganti Kartu Dyeing'
                ]).' ';

            echo Html::a('Selesai Dan Masukan Ke Gudag PFP', ['approve', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'title' => 'Add Items',
                'data-toggle'=>"modal",
                'data-target'=>"#kartuProsesPfpModal",
                'data-title' => 'Add Items'
            ]);
        }elseif ($model->status == $model::STATUS_POSTED){
            echo Html::a('Ganti Kartu Dyeing', ['ganti-dyeing', 'id' => $model->id], [
                    'class' => 'btn btn-info',
                    'onclick' => 'gantiDyeing(event, "Ganti Kartu Dyeing");',
                    'title' => 'Ganti Kartu Dyeing'
                ]).' ';
        }elseif ($model->status == $model::STATUS_INSPECTED){
            echo Html::a('Selesai Dan Masukan Ke Gudag PFP', ['approve', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'title' => 'Add Items',
                'data-toggle'=>"modal",
                'data-target'=>"#kartuProsesPfpModal",
                'data-title' => 'Add Items'
            ]);
        }
        ?>

        <?=Html::a('Ganti Motif', ['ganti-motif', 'id' => $model->id], [
            'class' => 'btn btn-default',
            'onclick' => 'gantiMotif(event, "Ganti Motif");',
            'title' => 'Ganti Motif'
        ])?>
    </p>

    <?php echo $this->render('/trn-kartu-proses-pfp/child/detail', ['model' => $model]);?>

    <?php echo $this->render('/trn-kartu-proses-pfp/child/items', ['model' => $model]);?>

    <?php echo $this->render('/trn-kartu-proses-pfp/child/persetujuan', ['model' => $model]);?>

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
    'id' => 'kartuProsesPfpModal',
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
