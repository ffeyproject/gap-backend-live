<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\GudangJadiMutasiItem */

$this->title = 'Update Gudang Jadi Mutasi Item: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Gudang Jadi Mutasi Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="gudang-jadi-mutasi-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
