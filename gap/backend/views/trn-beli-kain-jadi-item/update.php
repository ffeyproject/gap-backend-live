<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnBeliKainJadiItem */

$this->title = 'Update Trn Beli Kain Jadi Item: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Trn Beli Kain Jadi Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="trn-beli-kain-jadi-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
