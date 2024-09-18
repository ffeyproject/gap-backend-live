<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnMixedGreigeItem */

$this->title = 'Update Trn Mixed Greige Item: ' . $model->mix_id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Mixed Greige Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->mix_id, 'url' => ['view', 'mix_id' => $model->mix_id, 'stock_greige_id' => $model->stock_greige_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-mixed-greige-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
