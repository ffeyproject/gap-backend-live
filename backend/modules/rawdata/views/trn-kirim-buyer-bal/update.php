<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\modules\rawdata\models\TrnKirimBuyerBal $model */

$this->title = "Edit Bal #$model->id";
$this->params['breadcrumbs'][] = ['label' => 'Kirim Buyer Bal', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => "Detail Bal #$model->id", 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="trn-kirim-buyer-bal-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>