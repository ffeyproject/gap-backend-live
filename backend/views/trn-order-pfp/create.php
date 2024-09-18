<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnOrderPfp */

$this->title = 'Create Order PFP';
$this->params['breadcrumbs'][] = ['label' => 'Order PFP', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-order-pfp-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
