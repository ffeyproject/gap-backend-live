<?php

use common\models\ar\TrnReturBuyer;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model TrnReturBuyer */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Retur Buyer', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="trn-retur-buyer-view">
    <p>
        <?php if($model->status == $model::STATUS_DRAFT):?>
            <?= Html::a('Ubah', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Hapus', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Posting', ['posting', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'onclick' => 'posting(event);',
                'title' => 'Posting Retur Buyer: '.$model->id
            ]) ?>
        <?php else:?>
            <?php
                if($model->keputusan_qc == $model::QC_REPAIR){
                    if($model->status == $model::STATUS_POSTED){
                        echo Html::a('Re Dyeing', ['re-dyeing', 'id' => $model->id], [
                            'class' => 'btn btn-warning',
                            'onclick' => 'setReDyeing(event);',
                            'title' => 'Lanjutkan Perbaikan Redyeing'
                        ]);
                        echo ' ';
                        echo Html::a('Repair', ['repair', 'id' => $model->id], [
                            'class' => 'btn btn-warning',
                            'onclick' => 'setRepair(event);',
                            'title' => 'Lanjutkan Perbaikan repair'
                        ]);
                    }
                }
            ?>
        <?php endif;?>

        <?= Html::a('Buat Baru', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo $this->render('child/detail', ['model' => $model]);?>

    <?php echo $this->render('child/items', ['model' => $model]);?>

    <?php echo $this->render('child/print', ['model' => $model]);?>
</div>

<?php
$JGForm = [
    '<form action="" class="formName">',
    '<div class="form-group">',
    '<label>Tentukan Keputusan QC:</label>',
    '<select class="QCOpt form-control">',
];

$strOpt = ['<option value="">--Pilih--</option>'];
foreach ( TrnReturBuyer::keputusanQcOptions() as $key=>$keputusanQcOption) {
    $strOpt[] = '<option value="'.$key.'">'.$keputusanQcOption.'</option>';
}
$JGForm[] = implode(' ', $strOpt);
$JGForm[] = '</select><div class="err-block text-danger"></div></div></form>';
$kpOptions = implode(' ', $JGForm);

$js = <<<JS
var kpOptions = '{$kpOptions}';
JS;

$this->registerJs($js.$this->renderFile(__DIR__.'/js/view.js'), View::POS_END);