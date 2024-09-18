<?php

use backend\components\ajax_modal\AjaxModal;
use common\models\ar\TrnSc;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model TrnSc */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Approval Kontrak Pemesanan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>

<div class="sc-view">
    <p>
        <?=Html::a('Setujui', ['approve', 'id' => $model->id], [
            'class' => 'btn btn-success',
            'onclick' => 'approveSc(event);',
            'title' => 'Approve SC: '.$model->id
        ]);?>
        <?=Html::a('Tolak', ['reject', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'onclick' => 'rejectSc(event);',
            'title' => 'Reject SC: '.$model->id
        ]);?>
    </p>

    <iframe src="<?=Url::to(['/trn-sc/print-sc', 'id'=>$model->id])?>" style="width: 100%; height: 500px;"></iframe>

    <?php echo $this->render('/trn-sc/child/_persetujuan', ['model' => $model]);?>

    <?php echo $this->render('/trn-sc/child/_sc_agens', ['model' => $model]);?>

    <?php echo $this->render('/trn-sc/child/_sc_komisis', ['model' => $model]);?>

    <?php
    if($model->status == $model::STATUS_APPROVED){
        echo $this->render('child/_memo_perubahan', ['model' => $model]);
    }
    ?>
</div>

<?php
echo AjaxModal::widget([
    'id' => 'trnScModal',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">...</h4>',
]);

$indexUrl = Url::to(['index']);
$jsStr = <<<JS
var indexUrl = "{$indexUrl}";
JS;

$js = $jsStr.$this->renderFile(__DIR__.'/js/view.js');
$this->registerJs($js, $this::POS_END);
