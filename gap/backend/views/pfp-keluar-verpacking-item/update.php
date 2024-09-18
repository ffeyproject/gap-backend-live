<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ar\PfpKeluarVerpackingItem */

$this->title = 'Update Pfp Keluar Verpacking Item: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pfp Keluar Verpacking Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pfp-keluar-verpacking-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
