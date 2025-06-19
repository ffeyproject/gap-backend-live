<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\modules\rawdata\models\TrnKirimBuyerItem $model */

$this->title = 'Ubah Kirim Buyer Item: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Kirim Buyer Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Detail Item #' . $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="trn-kirim-buyer-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>