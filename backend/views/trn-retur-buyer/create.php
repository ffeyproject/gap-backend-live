<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnReturBuyer */
/* @var $modelsItem common\models\ar\TrnReturBuyerItem[] */

$this->title = 'Tambah Retur Buyer';
$this->params['breadcrumbs'][] = ['label' => 'Retur Buyer', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-retur-buyer-create">
    <div class="clearfix" style="margin-bottom: 15px;">
        <?= Html::button('<i class="fa fa-question-circle"></i> Petunjuk &amp; Instruksi Penggunaan', [
            'class' => 'btn btn-info pull-right',
            'data-toggle' => 'modal',
            'data-target' => '#modalPetunjukRetur',
        ]) ?>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>
</div>
