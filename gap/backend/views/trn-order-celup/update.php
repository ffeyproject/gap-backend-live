<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnOrderCelup */

$this->title = 'Ubah Order Celup: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Order Celup', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="trn-order-celup-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
