<?php
/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnOrderCelup */

$this->title = 'Buat Order Celup';
$this->params['breadcrumbs'][] = ['label' => 'Order Celup', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trn-order-celup-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
