<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnReturBuyer */
/* @var $modelsItem common\models\ar\TrnReturBuyerItem[] */

$this->title = 'Tambah Retur Buyer';
$this->params['breadcrumbs'][] = ['label' => 'Retur Buyer', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-retur-buyer-create">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsItem' => $modelsItem
    ]) ?>
</div>
