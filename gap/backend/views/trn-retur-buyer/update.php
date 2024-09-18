<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnReturBuyer */
/* @var $modelsItem common\models\ar\TrnReturBuyerItem[] */

$this->title = 'Ubah Retur Buyer: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Retur Buyer', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="trn-retur-buyer-update">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>
</div>
